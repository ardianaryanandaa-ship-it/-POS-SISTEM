<?php

namespace App\Models;

use CodeIgniter\Model;

class ItemKategoriModel extends Model
{
    protected $table            = 'item_kategori';
    protected $primaryKey       = 'id_kategori';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = ['nama_kategori', 'deskripsi'];

    protected $useTimestamps = false;
}