<?php

namespace App\Models;

use CodeIgniter\Model;

class BarangMasukModel extends Model
{
    protected $table            = 'barang_masuk';
    protected $primaryKey       = 'id_masuk';
    
    // PERBAIKAN: Pastikan semua field yang dibutuhkan terdaftar
    protected $allowedFields    = ['no_referensi', 'nama_vendor', 'kode_unit', 'tanggal_masuk', 'penerima', 'status', 'tgl_verifikasi', 'verifikator'];

    public function getFullDetail()
    {
        // PERBAIKAN: Menambahkan SUM(barang_masuk_detail.jumlah_retur) agar Retur terhitung di halaman utama
        return $this->select('barang_masuk.*, bisnis_unit.nama_unit, 
                    SUM(barang_masuk_detail.jumlah_faktur) as total_faktur, 
                    SUM(barang_masuk_detail.jumlah) as total_terima,
                    SUM(barang_masuk_detail.jumlah_retur) as total_retur, 
                    GROUP_CONCAT(master_barang.nama_barang SEPARATOR ", ") as nama_barang')
                    ->join('bisnis_unit', 'bisnis_unit.kode_unit = barang_masuk.kode_unit')
                    ->join('barang_masuk_detail', 'barang_masuk_detail.id_masuk = barang_masuk.id_masuk', 'left')
                    ->join('master_barang', 'master_barang.kode_barang = barang_masuk_detail.kode_barang', 'left')
                    ->groupBy('barang_masuk.id_masuk')
                    ->orderBy('barang_masuk.tanggal_masuk', 'DESC')
                    ->findAll();
    }
}