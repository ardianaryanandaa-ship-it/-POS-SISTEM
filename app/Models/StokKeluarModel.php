<?php

namespace App\Models;

use CodeIgniter\Model;

class StokKeluarModel extends Model
{
    // GANTI 'stok_keluar' di bawah ini dengan nama tabel asli di phpMyAdmin Anda
    protected $table            = 'stok'; 
    
    protected $primaryKey       = 'id_stok';
    protected $useAutoIncrement = true;
    protected $allowedFields    = ['jumlah_stok_keluar', 'id_penjualan'];
}