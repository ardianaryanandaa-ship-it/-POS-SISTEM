<?php

namespace App\Models;

use CodeIgniter\Model;

class MasterBarangModel extends Model
{
    protected $table            = 'master_barang';
    protected $primaryKey       = 'kode_barang';
    protected $useAutoIncrement = false; 
    protected $returnType       = 'array';
    protected $protectFields    = true;
    
    // TAMBAHKAN 'stok_akhir' DI BARIS INI:
    protected $allowedFields    = ['parent_kode','nama_varian','kode_barang', 'nama_barang', 'id_kategori', 'satuan_standar', 'harga_beli', 'harga_jual', 'stok_akhir', 'foto'];

    /**
     * Mengambil data barang lengkap dengan nama kategorinya
     */
    public function getBarangWithKategori()
    {
        return $this->select('master_barang.*, item_kategori.nama_kategori')
                    ->join('item_kategori', 'item_kategori.id_kategori = master_barang.id_kategori')
                    ->findAll();
    }

    public function getBarangUntukPOS($kodeUnit)
{
    return $this->db->table($this->table . ' mb')
        // Pastikan mb.foto, mb.nama_varian, mb.parent_kode, dan mb.id_kategori ikut terbawa
        ->select('mb.kode_barang, mb.nama_barang, mb.nama_varian, mb.parent_kode, mb.id_kategori, mb.harga_jual, mb.foto, ik.nama_kategori, su.jumlah as stok')
        ->join('item_kategori ik', 'ik.id_kategori = mb.id_kategori')
        ->join('stok_unit su', 'su.kode_barang = mb.kode_barang')
        ->where('su.kode_unit', $kodeUnit)
        ->where('su.jumlah >', 0)
        ->get()->getResultArray();
}
}
