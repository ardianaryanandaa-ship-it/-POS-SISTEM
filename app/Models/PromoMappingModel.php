<?php

namespace App\Models;

use CodeIgniter\Model;

class PromoMappingModel extends Model
{
    protected $table            = 'promo_mapping';
    protected $primaryKey       = 'id_mapping';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['id_promo', 'kode_barang', 'id_kategori'];
}