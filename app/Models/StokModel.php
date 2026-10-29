<?php
namespace App\Models;
use CodeIgniter\Model;

class StokModel extends Model
{
    protected $table            = 'stok';
    protected $primaryKey       = 'id_stok';
    protected $useAutoIncrement = true;
    protected $allowedFields    = ['jumlah_stok_keluar', 'id_penjualan'];
}