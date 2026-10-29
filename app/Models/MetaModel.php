<?php

namespace App\Models;

use CodeIgniter\Model;

class MetaModel extends Model
{
    protected $table            = 'web_meta';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $allowedFields    = ['meta_key', 'meta_value'];
    protected $useTimestamps    = false;

    public function getMetaArray()
    {
        $data   = $this->findAll();
        $result = [];

        foreach ($data as $row) {
            $result[$row['meta_key']] = $row['meta_value'];
        }

        return $result;
    }
}
