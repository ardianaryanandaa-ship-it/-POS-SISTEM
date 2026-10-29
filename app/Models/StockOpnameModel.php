<?php

namespace App\Models;

use CodeIgniter\Model;

class StockOpnameModel extends Model
{
    protected $table            = 'stock_opname';
    protected $primaryKey       = 'id_opname';
    protected $allowedFields    = ['kode_unit', 'kode_barang', 'stok_sistem', 'stok_fisik', 'selisih', 'keterangan', 'status'];

    // Fungsi untuk memanggil data Join
    public function getOpnameWithJoin()
    {
        return $this->db->table($this->table)
            ->select('stock_opname.*, master_barang.nama_barang, bisnis_unit.nama_unit')
            ->join('master_barang', 'master_barang.kode_barang = stock_opname.kode_barang')
            ->join('bisnis_unit', 'bisnis_unit.kode_unit = stock_opname.kode_unit')
            ->get()->getResultArray();
    }
}