<?php

namespace App\Models;

use CodeIgniter\Model;

class BarangMasukDetailModel extends Model
{
    protected $table            = 'barang_masuk_detail';
    protected $primaryKey       = 'id_detail';
    
    // TAMBAHKAN 'jumlah_faktur', 'alasan_hilang', dan 'tgl_kadaluwarsa' di bawah ini:
    protected $allowedFields    = [
        'id_masuk', 
        'kode_barang', 
        'jumlah', 
        'jumlah_faktur', 
        'alasan_hilang', 
        'tgl_kadaluwarsa'
    ];
}