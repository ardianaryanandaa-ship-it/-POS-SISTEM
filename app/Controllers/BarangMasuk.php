<?php
namespace App\Controllers;
use App\Models\BarangMasukModel;
use App\Models\BarangMasukDetailModel;
use App\Models\MasterBarangModel;
use App\Models\BisnisUnitModel;

class BarangMasuk extends BaseController {
    protected $barangMasukModel;
    protected $barangDetailModel;
    protected $barangModel;
    protected $unitModel;

    public function __construct() {
        $this->barangMasukModel  = new BarangMasukModel();
        $this->barangDetailModel = new BarangMasukDetailModel();
        $this->barangModel       = new MasterBarangModel();
        $this->unitModel         = new BisnisUnitModel();
    }

    public function index() {
        $data = [
            'barang_masuk'  => $this->barangMasukModel->getFullDetail(), 
            'list_barang'   => $this->barangModel->findAll(), 
            'list_unit'     => $this->unitModel->findAll()     
        ];
        return view('barang_masuk/index', $data);
    }

    private function logAksi($id_masuk, $aksi, $keterangan) {
        $db = \Config\Database::connect();
        $db->table('barang_masuk_log')->insert([
            'id_masuk'   => $id_masuk,
            'aksi'       => $aksi,
            'keterangan' => $keterangan,
            'user'       => session()->get('nama_lengkap') ?? 'Admin',
            'created_at' => date('Y-m-d H:i:s')
        ]);
    }

    public function save() {
        $db = \Config\Database::connect();
        $db->transStart();
        $unitInput = $this->request->getPost('kode_unit');
        $unit = $this->unitModel->where('kode_unit', $unitInput)->orWhere('nama_unit', $unitInput)->first();
        $kode_unit = $unit ? $unit['kode_unit'] : $unitInput;

        $id_masuk = $this->barangMasukModel->insert([
            'no_referensi'  => $this->request->getPost('no_referensi'),
            'nama_vendor'   => $this->request->getPost('nama_vendor'),
            'kode_unit'     => $kode_unit,
            'tanggal_masuk' => date('Y-m-d H:i:s'), 
            'penerima'      => $this->request->getPost('penerima'),
            'status'        => 0 
        ]);

        $kode_barang_input = $this->request->getPost('kode_barang');
        $jumlah            = $this->request->getPost('jumlah');

        foreach ($kode_barang_input as $key => $val) {
            $brg = $this->barangModel->where('kode_barang', $val)->orWhere('nama_barang', $val)->first();
            $kb = $brg ? $brg['kode_barang'] : $val;

            $this->barangDetailModel->save([
                'id_masuk'      => $id_masuk,
                'kode_barang'   => $kb,
                'jumlah_faktur' => $jumlah[$key],
                'jumlah'        => $jumlah[$key], 
            ]);
        }
        $this->logAksi($id_masuk, 'Input', 'Input Nota Baru');
        $db->transComplete();
        return redirect()->to(base_url('barangmasuk'))->with('success', 'Nota disimpan.');
    }

    public function update_item() {
        $id_detail = $this->request->getPost('id_detail');
        $id_masuk  = $this->request->getPost('id_masuk');
        $this->barangDetailModel->update($id_detail, [
            'jumlah_faktur' => $this->request->getPost('faktur'),
            'jumlah'        => $this->request->getPost('terima'), 
            'alasan_hilang' => $this->request->getPost('alasan')
        ]);
        $this->logAksi($id_masuk, 'Update Item', 'Sinkronisasi Kroscek Fisik');
        return $this->response->setJSON(['status' => 'success']);
    }

    public function verify($id) {
        $db = \Config\Database::connect();
        $header = $this->barangMasukModel->find($id);
        $db->transStart();
        $details = $this->barangDetailModel->where('id_masuk', $id)->findAll();
        
        foreach ($details as $item) {
            $kb = $item['kode_barang']; 
            
            // PERBAIKAN: Ambil jumlah retur dari tabel retur secara dinamis
            $retur = $db->table('retur_barang_detail rd')
                        ->selectSum('rd.jumlah')
                        ->join('retur_barang rh', 'rh.id_retur = rd.id_retur')
                        ->where(['rh.id_masuk' => $id, 'rd.kode_barang' => $kb, 'rh.status' => 1])
                        ->get()->getRowArray();
            
            $jml_retur = (int)($retur['jumlah'] ?? 0);
            
            // Stok yang masuk ke gudang adalah angka Terima Bersih (Neto)
            $qty = (int)$item['jumlah'] - $jml_retur; 

            // Update Master Barang
            $db->table('master_barang')->where('kode_barang', $kb)->set('stok_akhir', 'COALESCE(stok_akhir, 0) + ' . $qty, false)->update();
            
            // Update Stok Unit (Gudang) - Ini yang akan menghilangkan angka minus di dashboard
            $db->query("INSERT INTO stok_unit (kode_barang, kode_unit, jumlah) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE jumlah = jumlah + ?", [$kb, $header['kode_unit'], $qty, $qty]);
        }
        
        $this->barangMasukModel->update($id, ['status' => 1, 'tgl_verifikasi' => date('Y-m-d H:i:s'), 'verifikator' => session()->get('nama_lengkap') ?? 'Admin']);
        $this->logAksi($id, 'Verify', 'Verifikasi Selesai');
        $db->transComplete();
        return $this->response->setJSON(['status' => 'success']);
    }

    public function rollback($id) {
        $db = \Config\Database::connect();
        $header = $this->barangMasukModel->find($id);
        $db->transStart();
        $details = $this->barangDetailModel->where('id_masuk', $id)->findAll();
        
        foreach ($details as $item) {
            $kb = $item['kode_barang']; 
            
            // PERBAIKAN: Ambil jumlah retur dari tabel retur secara dinamis agar rollback akurat
            $retur = $db->table('retur_barang_detail rd')
                        ->selectSum('rd.jumlah')
                        ->join('retur_barang rh', 'rh.id_retur = rd.id_retur')
                        ->where(['rh.id_masuk' => $id, 'rd.kode_barang' => $kb, 'rh.status' => 1])
                        ->get()->getRowArray();
            
            $jml_retur = (int)($retur['jumlah'] ?? 0);
            $qty = (int)$item['jumlah'] - $jml_retur;

            $db->table('master_barang')->where('kode_barang', $kb)->set('stok_akhir', 'stok_akhir - ' . $qty, false)->update();
            $db->table('stok_unit')->where(['kode_barang' => $kb, 'kode_unit' => $header['kode_unit']])->set('jumlah', 'jumlah - ' . $qty, false)->update();
        }
        
        $this->barangMasukModel->update($id, ['status' => 0, 'tgl_verifikasi' => null, 'verifikator' => null]);
        $this->logAksi($id, 'Rollback', 'Batal Verifikasi');
        $db->transComplete();
        return $this->response->setJSON(['status' => 'success']);
    }

    public function get_detail($id) {
        $db = \Config\Database::connect();
        
        $items = $this->barangDetailModel->select('barang_masuk_detail.*, master_barang.nama_barang')
                          ->join('master_barang', 'master_barang.kode_barang = barang_masuk_detail.kode_barang')
                          ->where('id_masuk', $id)->findAll();

        $retur = $db->table('retur_barang_detail rd')
                    ->select('rd.kode_barang, SUM(rd.jumlah) as total_retur')
                    ->join('retur_barang rh', 'rh.id_retur = rd.id_retur')
                    ->where('rh.id_masuk', $id)
                    ->where('rh.status', 1)
                    ->groupBy('rd.kode_barang')
                    ->get()->getResultArray();

        $returMap = [];
        foreach ($retur as $r) { $returMap[$r['kode_barang']] = $r['total_retur']; }

        foreach ($items as &$item) {
            $jml_r = $returMap[$item['kode_barang']] ?? 0;
            $item['jumlah_retur'] = $jml_r;
            $item['jumlah_neto'] = (int)$item['jumlah'] - (int)$jml_r;
        }

        $data['items']  = $items;
        $data['header'] = $this->barangMasukModel->select('barang_masuk.*, bisnis_unit.nama_unit')->join('bisnis_unit', 'bisnis_unit.kode_unit = barang_masuk.kode_unit')->where('id_masuk', $id)->first();
        $data['logs']   = $db->table('barang_masuk_log')->where('id_masuk', $id)->orderBy('created_at', 'DESC')->get()->getResultArray();
        
        return $this->response->setJSON($data);
    }
}