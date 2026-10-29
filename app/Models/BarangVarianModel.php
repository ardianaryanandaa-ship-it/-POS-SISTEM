<?php

namespace App\Models;

use CodeIgniter\Model;

class BarangVarianModel extends Model
{
    protected $table            = 'm_barang_varian';
    protected $primaryKey       = 'id_varian';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'parent_kode', 'nama_varian', 'satuan_standar', 
        'harga_beli', 'harga_jual', 'stok_akhir'
    ];

    /**
     * Mendapatkan semua varian berdasarkan kode bapaknya
     */
    public function getByParent($parent_kode)
    {
        return $this->where('parent_kode', $parent_kode)->findAll();
    }

    /**
     * Total stok semua varian dari satu bapak
     */
    public function getTotalStokByParent($parent_kode)
    {
        return $this->selectSum('stok_akhir')
                    ->where('parent_kode', $parent_kode)
                    ->first();
    }
}