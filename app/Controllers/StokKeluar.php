<?php

namespace App\Controllers;

use App\Models\StokKeluarModel;

class StokKeluar extends BaseController
{
    protected $stokKeluarModel;

    public function __construct()
    {
        $this->stokKeluarModel = new StokKeluarModel();
    }

    public function index()
    {
        $data = [
            'title'       => 'Daftar Stok Keluar',
            'stok_keluar' => $this->stokKeluarModel->findAll()
        ];

        return view('stok_keluar/index', $data);
    }

    // --- TAMBAHKAN FUNGSI INI AGAR TOMBOL DETAIL BERFUNGSI ---
    public function get_detail($id_penjualan)
    {
        $db = \Config\Database::connect();
        
        // Mengambil data dari tabel penjualan, join ke barang, dan join ke stok
        $data = $db->table('penjualan')
            ->select('penjualan.*, barang.nama_barang, stok.jumlah_stok_keluar')
            ->join('barang', 'barang.id_barang = penjualan.id_barang', 'left')
            ->join('stok', 'stok.id_penjualan = penjualan.id_penjualan', 'left')
            ->where('penjualan.id_penjualan', $id_penjualan)
            ->get()->getRowArray();

        return $this->response->setJSON($data);
    }
}