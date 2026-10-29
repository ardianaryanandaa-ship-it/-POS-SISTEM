<?php

namespace App\Models;

use CodeIgniter\Model;

class ExpenseModel extends Model
{
    protected $table            = 'pengeluaran_kas';
    protected $primaryKey       = 'id';
    protected $allowedFields    = ['shift_id', 'nominal', 'keterangan', 'created_at'];
}