<?php
namespace App\Controllers;
use App\Models\MasterBarangModel;
use App\Models\BisnisUnitModel;

class StokTransfer extends BaseController {
    public function index() {
        $db = \Config\Database::connect();
        
        $transfers = $db->table('stok_transfer')
            ->select('stok_transfer.*, u_asal.nama_unit as nama_asal, u_tujuan.nama_unit as nama_tujuan')
            ->join('bisnis_unit u_asal', 'u_asal.kode_unit = stok_transfer.unit_asal', 'left')
            ->join('bisnis_unit u_tujuan', 'u_tujuan.kode_unit = stok_transfer.unit_tujuan', 'left')
            ->orderBy('tanggal','DESC')
            ->get()->getResultArray();

        $semua_unit = $db->table('bisnis_unit')->get()->getResultArray();
        
        $units_asal = [];
        $units_tujuan = [];
        foreach ($semua_unit as $u) {
            $units_asal[$u['jenis_unit']][] = $u;
            $units_tujuan[$u['jenis_unit']][] = $u;
        }

        $data = [
            'transfers'    => $transfers,
            'units_asal'   => $units_asal,
            'units_tujuan' => $units_tujuan,
            'list_barang'  => (new MasterBarangModel())->findAll()
        ];
        return view('logistik/transfer', $data);
    }

    // Mengambil data tabel saja untuk update tanpa reload
    public function get_table_data() {
        $db = \Config\Database::connect();
        $transfers = $db->table('stok_transfer')
            ->select('stok_transfer.*, u_asal.nama_unit as nama_asal, u_tujuan.nama_unit as nama_tujuan')
            ->join('bisnis_unit u_asal', 'u_asal.kode_unit = stok_transfer.unit_asal', 'left')
            ->join('bisnis_unit u_tujuan', 'u_tujuan.kode_unit = stok_transfer.unit_tujuan', 'left')
            ->orderBy('tanggal','DESC')
            ->get()->getResultArray();
            
        return view('logistik/partial_table_sit', ['transfers' => $transfers]);
    }

    public function get_stok_per_unit($kode_unit) {
        $db = \Config\Database::connect();
        $stok = $db->table('stok_unit')
                   ->where('kode_unit', $kode_unit)
                   ->get()->getResultArray();
        return $this->response->setJSON($stok);
    }

    public function save() {
        $db = \Config\Database::connect();
        $db->transStart();
        
        $unit_asal = $this->request->getPost('unit_asal');
        $unit_tujuan = $this->request->getPost('unit_tujuan');

        if ($unit_asal == $unit_tujuan) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Unit asal dan tujuan tidak boleh sama!']);
        }

        $db->table('stok_transfer')->insert([
            'no_trf' => 'SIT-' . date('YmdHis'), 
            'unit_asal' => $unit_asal, 
            'unit_tujuan' => $unit_tujuan,
            'tanggal' => date('Y-m-d H:i:s'), 
            'penerima' => $this->request->getPost('penerima'), 
            'status' => 0
        ]);
        
        $id_trf = $db->insertID();
        $kode_barang = $this->request->getPost('kode_barang'); 
        $jumlah = $this->request->getPost('jumlah');

        foreach($kode_barang as $key => $kode) {
            $qty = (int)$jumlah[$key];
            $db->table('stok_transfer_detail')->insert([
                'id_trf' => $id_trf, 
                'kode_barang' => $kode, 
                'jumlah' => $qty
            ]);
            
            $db->table('stok_unit')
               ->where(['kode_barang' => $kode, 'kode_unit' => $unit_asal])
               ->set('jumlah', 'jumlah - ' . $qty, false)
               ->update();
        }

        $db->transComplete();
        
        if ($db->transStatus() === FALSE) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Gagal memproses transfer.']);
        }
        
        return $this->response->setJSON([
            'status' => 'success', 
            'message' => 'Transfer berhasil diproses!'
            ]);
    }

    public function get_detail($id) {
        $db = \Config\Database::connect();
        $data['header'] = $db->table('stok_transfer')
                            ->select('stok_transfer.*, u_asal.nama_unit as nama_asal, u_tujuan.nama_unit as nama_tujuan')
                            ->join('bisnis_unit u_asal', 'u_asal.kode_unit = stok_transfer.unit_asal', 'left')
                            ->join('bisnis_unit u_tujuan', 'u_tujuan.kode_unit = stok_transfer.unit_tujuan', 'left')
                            ->where('id_trf', $id)->get()->getRowArray();
                            
        $data['items']  = $db->table('stok_transfer_detail')->select('stok_transfer_detail.*, master_barang.nama_barang')->join('master_barang', 'master_barang.kode_barang = stok_transfer_detail.kode_barang')->where('id_trf', $id)->get()->getResultArray();
        return $this->response->setJSON($data);
    }

    public function receive($id) {
        $db = \Config\Database::connect();
        $trf = $db->table('stok_transfer')->where('id_trf', $id)->get()->getRowArray();
        
        if(!$trf || $trf['status'] == 1) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Data tidak ditemukan atau sudah diproses.']);
        }

        $db->transStart();
        $items = $db->table('stok_transfer_detail')->where('id_trf', $id)->get()->getResultArray();
        foreach($items as $it) {
            $kb = $it['kode_barang']; $qty = (int)$it['jumlah'];
            $db->query("INSERT INTO stok_unit (kode_barang, kode_unit, jumlah) 
                        VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE jumlah = jumlah + ?", 
                        [$kb, $trf['unit_tujuan'], $qty, $qty]);
        }
        $db->table('stok_transfer')->where('id_trf', $id)->update(['status' => 1]);
        $db->transComplete();

        if ($db->transStatus() === FALSE) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Gagal melakukan konfirmasi.']);
        }

        return $this->response->setJSON([
            'status' => 'success', 
            'message' => 'Konfirmasi Berhasil!'
            ]);
    }

    public function cetak_sit($id) {
        $db = \Config\Database::connect();
        $data['header'] = $db->table('stok_transfer')->select('stok_transfer.*, u_asal.nama_unit as nama_asal, u_tujuan.nama_unit as nama_tujuan')->join('bisnis_unit u_asal', 'u_asal.kode_unit = stok_transfer.unit_asal', 'left')->join('bisnis_unit u_tujuan', 'u_tujuan.kode_unit = stok_transfer.unit_tujuan', 'left')->where('id_trf', $id)->get()->getRowArray();
        $data['items']  = $db->table('stok_transfer_detail')->select('stok_transfer_detail.*, master_barang.nama_barang')->join('master_barang', 'master_barang.kode_barang = stok_transfer_detail.kode_barang')->where('id_trf', $id)->get()->getResultArray();
        return view('logistik/cetak_sit', $data);
    }
}