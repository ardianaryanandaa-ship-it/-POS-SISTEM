<?php
namespace App\Controllers;
use App\Models\MasterBarangModel;

class StokAdjustment extends BaseController {
    
    public function index() {
        $db = \Config\Database::connect();
        $data = [
            'adjustments' => $db->table('stok_adj')->orderBy('tanggal','DESC')->get()->getResultArray(),
            'list_barang' => (new MasterBarangModel())->findAll()
        ];
        return view('logistik/adjustment', $data);
    }

    public function save() {
        $db = \Config\Database::connect();
        $db->transStart();

        // 1. Simpan Header
        $db->table('stok_adj')->insert([
            'no_adj'  => 'ADJ-' . date('YmdHis'),
            'tanggal' => date('Y-m-d H:i:s'),
            'user'    => session()->get('nama_lengkap') ?? 'Admin',
            'status'  => 0 // Draft
        ]);
        $id_adj = $db->insertID();

        // 2. Simpan Detail (Array dari Form)
        $kode_barang = $this->request->getPost('kode_barang');
        $stok_fisik  = $this->request->getPost('stok_fisik');
        $alasan      = $this->request->getPost('alasan');

        foreach($kode_barang as $key => $kode) {
            $barang = $db->table('master_barang')->where('kode_barang', $kode)->get()->getRowArray();
            $sistem = $barang['stok_akhir'] ?? 0;
            $fisik  = (int)$stok_fisik[$key];
            
            $db->table('stok_adj_detail')->insert([
                'id_adj'      => $id_adj,
                'kode_barang' => $kode,
                'stok_sistem' => $sistem,
                'stok_fisik'  => $fisik,
                'selisih'     => $fisik - $sistem,
                'alasan'      => $alasan[$key] ?? 'Stok Opname Rutin'
            ]);
        }

        $db->transComplete();
        return redirect()->to(base_url('stokadjustment'))->with('success', 'Draft Adjustment berhasil dibuat.');
    }

    // FUNGSI UNTUK MODAL DETAIL (READ)
    public function get_detail($id) {
        $db = \Config\Database::connect();
        $data['header'] = $db->table('stok_adj')->where('id_adj', $id)->get()->getRowArray();
        $data['items']  = $db->table('stok_adj_detail')
                            ->select('stok_adj_detail.*, master_barang.nama_barang')
                            ->join('master_barang', 'master_barang.kode_barang = stok_adj_detail.kode_barang')
                            ->where('id_adj', $id)
                            ->get()->getResultArray();
        return $this->response->setJSON($data);
    }

    public function apply($id) {
    $db = \Config\Database::connect();
    $db->transStart();
    
    // Tentukan Kode Unit Gudang Utama (PT Lancar Jaya)
    // Silakan ganti 'KDS-001' sesuai kode PT Lancar Jaya di database Anda
    $kodeUnitGudang = 'KDS-001'; 

    $items = $db->table('stok_adj_detail')->where('id_adj', $id)->get()->getResultArray();
    
    foreach($items as $it) {
        // 1. UPDATE STOK MASTER (Untuk kolom "Sisa di Gudang")
        $db->table('master_barang')
           ->where('kode_barang', $it['kode_barang'])
           ->update(['stok_akhir' => $it['stok_fisik']]);

        // 2. UPDATE STOK UNIT GUDANG (Untuk kolom "PT Lancar Jaya")
        // Cek apakah data stok di unit tersebut sudah ada
        $cekStokUnit = $db->table('stok_unit')
                          ->where(['kode_barang' => $it['kode_barang'], 'kode_unit' => $kodeUnitGudang])
                          ->get()->getRow();

        if ($cekStokUnit) {
            // Jika ada, update jadi sama dengan hasil fisik opname
            $db->table('stok_unit')
               ->where(['kode_barang' => $it['kode_barang'], 'kode_unit' => $kodeUnitGudang])
               ->update(['jumlah' => $it['stok_fisik']]);
        } else {
            // Jika belum ada recordnya, insert baru
            $db->table('stok_unit')->insert([
                'kode_barang' => $it['kode_barang'],
                'kode_unit'   => $kodeUnitGudang,
                'jumlah'      => $it['stok_fisik']
            ]);
        }
    }

    // Update status adjustment jadi Selesai
    $db->table('stok_adj')->where('id_adj', $id)->update(['status' => 1]);
    
    $db->transComplete();

    if ($db->transStatus() === FALSE) {
        return redirect()->back()->with('error', 'Gagal menyinkronkan stok.');
    }

    return redirect()->to(base_url('stokadjustment'))->with('success', 'Stok Master & Gudang Berhasil Disinkronkan!');
}

    public function delete($id) {
        $db = \Config\Database::connect();
        // Hanya bisa hapus jika status masih DRAFT (0)
        $db->table('stok_adj')->where(['id_adj' => $id, 'status' => 0])->delete();
        $db->table('stok_adj_detail')->where('id_adj', $id)->delete();
        return redirect()->to(base_url('stokadjustment'))->with('success', 'Draft Adjustment dihapus.');
    }
}