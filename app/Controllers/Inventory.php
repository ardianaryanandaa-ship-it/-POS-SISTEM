<?php

namespace App\Controllers;

use App\Models\MasterBarangModel;
use App\Models\BisnisUnitModel;

class Inventory extends BaseController
{
    protected $barangModel;
    protected $unitModel;

    public function __construct()
    {
        $this->barangModel = new MasterBarangModel();
        $this->unitModel   = new BisnisUnitModel();
    }

    public function index()
{
    $db = \Config\Database::connect();

    // 1. Ambil data Wilayah untuk filter
    $filter_wilayah = $this->request->getGet('wilayah');
    $list_wilayah = $this->unitModel->select('wilayah')->distinct()->findAll();

    // 2. Ambil data Unit (PT Lancar, PT Polytron, dll)
    $unitQuery = $this->unitModel->orderBy('jenis_unit', 'ASC');
    if ($filter_wilayah && $filter_wilayah != 'all') {
        $unitQuery->where('wilayah', $filter_wilayah);
    }
    $units = $unitQuery->findAll();

    // 3. Ambil data Barang (Master & Varian)
    $items = $this->barangModel->findAll();

    // --- LOGIKA SINKRONISASI STOK (MASTER & VARIAN) ---
    // Ambil data mentah dari tabel stok_unit
    $raw_stok = $db->table('stok_unit')->get()->getResultArray();
    
    // Kita buat mapping: Barang ini Parent-nya siapa?
    $parentMap = [];
    foreach($items as $it) {
        if(!empty($it['parent_kode'])) {
            $parentMap[$it['kode_barang']] = $it['parent_kode'];
        }
    }

    $stok_matrix = [];
    foreach ($raw_stok as $s) {
        $kb = $s['kode_barang'];
        $ku = $s['kode_unit'];
        $jumlah = (int)$s['jumlah'];

        // Masukkan stok ke barang itu sendiri
        $stok_matrix[$kb][$ku] = ($stok_matrix[$kb][$ku] ?? 0) + $jumlah;

        // JIKA ini adalah varian, tambahkan juga stoknya ke si PARENT (Master)
        // Agar baris Master menampilkan total stok dari semua variannya
        if (isset($parentMap[$kb])) {
            $pk = $parentMap[$kb];
            $stok_matrix[$pk][$ku] = ($stok_matrix[$pk][$ku] ?? 0) + $jumlah;
        }
    }
    // --------------------------------------------------

    $data = [
        'units'           => $units,
        'items'           => $items,
        'stok_matrix'     => $stok_matrix,
        'list_wilayah'    => $list_wilayah,
        'wilayah_aktif'   => $filter_wilayah
    ];

    return view('logistik/monitoring_stok', $data);
}

    public function cek_stok_unit($kode_barang, $kode_unit)
    {
        $db = \Config\Database::connect();
        $res = $db->table('stok_unit')
                  ->where(['kode_barang' => $kode_barang, 'kode_unit' => $kode_unit])
                  ->get()->getRowArray();

        return $this->response->setJSON([
            'kode_barang' => $kode_barang,
            'kode_unit'   => $kode_unit,
            'stok'        => $res['jumlah'] ?? 0
        ]);
    }
}