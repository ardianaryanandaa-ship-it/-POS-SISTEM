<?php

namespace App\Models;

use CodeIgniter\Model;

class ShiftModel extends Model
{
    protected $table            = 'shifts';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'user_id', 'start_at', 'end_at', 'starting_cash', 
        'total_sales_cash', 'total_expenses', 'expected_cash', 
        'actual_cash', 'status'
    ];

    // Fungsi untuk cek apakah kasir sedang memiliki shift aktif
    public function getActiveShift($userId)
    {
        return $this->where('user_id', $userId)
                    ->where('status', 'open')
                    ->first();
    }
}