<?php

namespace App\Models;

use CodeIgniter\Model;

class SalesModel extends Model
{
    protected $table            = 'sales';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $allowedFields    = [
        'invoice_no', 
        'kode_unit',      // Pastikan ada ini
        'total_price',
        'discount',
        'nama_promo', 
        'shift_id',       // Pastikan ada ini
        'payment_method', 
        'cash_received', 
        'cash_change', 
        'user_id', 
        'id_pelanggan',   // Pastikan ada ini
        'status',
        'foto'
    ];

    protected $useTimestamps = true; // Karena di tabel sales Anda ada created_at & updated_at
// Karena kita punya created_at dan updated_at

    public function getSalesWithUser()
    {
        return $this->select('sales.*, users.full_name as nama_kasir, bisnis_unit.nama_unit, pelanggan.nama_pelanggan')
                    ->join('users', 'users.id = sales.user_id', 'left')
                    ->join('bisnis_unit', 'bisnis_unit.kode_unit = sales.kode_unit', 'left')
                     ->leftJoin('pelanggan', 'pelanggan.id = sales.id_pelanggan') // Gunakan leftJoin agar Umum tetap muncul
                    ->orderBy('sales.created_at', 'DESC')
                    ->findAll();
}
}