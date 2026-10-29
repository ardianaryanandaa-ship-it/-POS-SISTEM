<?php
namespace App\Controllers;
use App\Models\ReturBarangModel;
use App\Models\MasterBarangModel;
use App\Models\BisnisUnitModel;

class ReturBarang extends BaseController {
    protected $returModel;

    public function __construct() {
        $this->returModel = new ReturBarangModel();
    }

    private function logAksi($id_retur, $aksi, $keterangan) {
    $db = \Config\Database::connect();
    $db->table('retur_barang_log')->insert([
        'id_retur'   => $id_retur,
        'aksi'       => $aksi,
        'keterangan' => $keterangan,
        'user'       => session()->get('username') ?? '',
        'created_at' => date('Y-m-d H:i:s')
    ]);
}

    public function index() {
        $db = \Config\Database::connect();
        $data = [
            'retur' => $this->returModel->getFullDetail(),
            'list_barang' => (new MasterBarangModel())->findAll(),
            'list_unit' => (new BisnisUnitModel())->findAll(),
            'list_nota' => $db->table('barang_masuk')->where('status', 1)->get()->getResultArray()
        ];
        return view('logistik/retur_index', $data);
    }

    public function save() {
    $db = \Config\Database::connect();

    // 1. DEFINISI ATURAN VALIDASI CI4
    $rules = [
        'id_masuk_asal' => [
            'label'  => 'Nota Masuk',
            'rules'  => 'required',
            'errors' => ['required' => '{field} harus dipilih agar ketersediaan stok valid.']
        ],
        'kode_barang.*' => [
            'label'  => 'Barang',
            'rules'  => 'required',
            'errors' => ['required' => '{field} pada baris ini harus dipilih.']
        ],
        'jumlah.*' => [
            'label'  => 'Jumlah',
            'rules'  => 'required|numeric|greater_than[0]',
            'errors' => [
                'required' => '{field} harus diisi.',
                'numeric'  => '{field} harus berupa angka.',
                'greater_than' => '{field} harus minimal 1.'
            ]
        ],
        'alasan.*' => [
            'label'  => 'Alasan',
            'rules'  => 'required',
            'errors' => ['required' => '{field} retur tidak boleh kosong.']
        ],
    ];

    // Jalankan Validasi Dasar
    if (!$this->validate($rules)) {
        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Validasi gagal!',
            'errors' => $this->validator->getErrors()]);
    }

    // 2. VALIDASI LOGIKA STOK (SERVER-SIDE)
    // Cek apakah jumlah yang diinput melebihi stok yang ada di nota asal
    $id_masuk_asal = $this->request->getPost('id_masuk_asal');
    $kode_barang   = $this->request->getPost('kode_barang');
    $jumlah        = $this->request->getPost('jumlah');

    foreach ($kode_barang as $key => $kode) {
        $stokNota = $db->table('barang_masuk_detail')
                       ->where(['id_masuk' => $id_masuk_asal, 'kode_barang' => $kode])
                       ->get()->getRowArray();
        
        $maxStok = $stokNota['jumlah'] ?? 0;

        if ($jumlah[$key] > $maxStok) {
            return redirect()->back()->withInput()->with('error', "Gagal Simpan! Jumlah retur untuk [{$kode}] melebihi stok di nota (Maks: {$maxStok}).");
        }
    }

    // 3. PROSES SIMPAN / UPDATE
    $db->transStart();
    $id_retur = $this->request->getPost('id_retur'); 

    $dataHeader = [
        'id_masuk'      => $id_masuk_asal,
        'nama_vendor'   => $this->request->getPost('nama_vendor'),
        'kode_unit'     => $this->request->getPost('kode_unit'),
        'tanggal_retur' => date('Y-m-d H:i:s'),
        'user'          => session()->get('username') ?? '',
        'status'        => 0
    ];

    if (!empty($id_retur)) {
        $this->returModel->update($id_retur, $dataHeader);
        $this->logAksi($id_retur, 'Edit', 'Melakukan perubahan data retur');
        $db->table('retur_barang_detail')->where('id_retur', $id_retur)->delete();
    } else {
        $dataHeader['no_retur'] = 'RTN-' . date('YmdHis');
        $id_retur = $this->returModel->insert($dataHeader);
    }

    $alasan = $this->request->getPost('alasan');
    foreach ($kode_barang as $key => $kode) {
        $db->table('retur_barang_detail')->insert([
            'id_retur' => $id_retur,
            'kode_barang' => $kode,
            'jumlah' => $jumlah[$key],
            'alasan_retur' => $alasan[$key]
        ]);
    }

    // Ganti bagian akhir transComplete di Controller Anda
    $db->transComplete();

    if ($db->transStatus() === false) {
        return $this->response->setJSON([
            'status'  => 'error',
            'message' => 'Terjadi kesalahan pada database saat menyimpan.'
        ]);
    }

    return $this->response->setJSON([
        'status'  => 'success',
        'message' => 'Data Retur berhasil disimpan.'
    ]);
}
    public function verify($id) {
        $db = \Config\Database::connect();
        $header = $this->returModel->find($id);
        $items = $db->table('retur_barang_detail')->where('id_retur', $id)->get()->getResultArray();

        $db->transStart();
        foreach ($items as $it) {
            $qty = (int)$it['jumlah']; $kb = $it['kode_barang'];
            $db->table('master_barang')->where('kode_barang', $kb)->set('stok_akhir', 'stok_akhir - ' . $qty, false)->update();
            $db->table('stok_unit')->where(['kode_barang' => $kb, 'kode_unit' => $header['kode_unit']])->set('jumlah', 'jumlah - ' . $qty, false)->update();

            if (!empty($header['id_masuk'])) {
                $db->table('barang_masuk_detail')->where(['id_masuk' => $header['id_masuk'], 'kode_barang' => $kb])
                   ->set('jumlah_retur', 'jumlah_retur + ' . $qty, false)->update();
            }
        }
        $this->returModel->update($id, ['status' => 1]);
        $db->transComplete();
        return redirect()->to(base_url('retur'))->with('success', 'Retur Berhasil diverifikasi!');
    }

    // FUNGSI INI DIGUNAKAN UNTUK DETAIL DAN JUGA EDIT (KARENA ROUTE YANG TERSEDIA HANYA INI)
    public function get_detail($id) {
    $db = \Config\Database::connect();
    
    // Ambil Header
    $header = $this->returModel->find($id);
    if (!$header) return $this->response->setJSON(['status' => 'error']);

    // Ambil Detail (Pastikan Join ke Master Barang agar ada NAMA BARANG)
    $items = $db->table('retur_barang_detail')
                ->select('retur_barang_detail.*, master_barang.nama_barang')
                ->join('master_barang', 'master_barang.kode_barang = retur_barang_detail.kode_barang')
                ->where('id_retur', $id)
                ->get()->getResultArray();

    return $this->response->setJSON([
        'status' => 'success',
        'header' => $header,
        'items'  => $items
    ]);
}
    }
