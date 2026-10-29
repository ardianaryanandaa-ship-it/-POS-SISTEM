<?php

namespace App\Models;

use CodeIgniter\Model; // <--- Pastikan ini CodeIgniter, bukan Illuminate

class PenjualanModel extends Model
{
    protected $table      = 'penjualan'; // Sesuaikan dengan nama tabel di DB
    protected $primaryKey = 'id';

    protected $allowedFields = ['nama_produk', 'jumlah', 'harga']; // Sesuaikan kolomnya
}