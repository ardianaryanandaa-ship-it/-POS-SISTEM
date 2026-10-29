<?php

namespace App\Models;

use CodeIgniter\Model;

class UserUnitModel extends Model
{
    protected $table      = 'user_units';
    protected $primaryKey = 'id';
    protected $allowedFields = ['user_id', 'kode_unit'];

    // JANGAN ADA beforeInsert, beforeUpdate, atau afterFind di sini!
}