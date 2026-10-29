<?php

namespace App\Models;

use CodeIgniter\Model;

class SalesDetailModel extends Model
{
    protected $table            = 'sales_details';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'sale_id', 
        'kode_barang', 
        'qty', 
        'price_at_sale', 
        'cost_price',
        'subtotal'
    ];
}