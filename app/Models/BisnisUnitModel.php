<?php

namespace App\Models;

use CodeIgniter\Model;

class BisnisUnitModel extends Model
{
    protected $table            = 'bisnis_unit';
    protected $primaryKey       = 'kode_unit';
    protected $useAutoIncrement = false; 
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = ['kode_unit', 'nama_unit', 'jenis', 'alamat', 'jenis_unit', 'wilayah'];

    /**
     * Mengambil unit yang hanya diizinkan untuk user tertentu
     */
    public function getAuthorizedUnits($allowed_codes)
    {
        if (empty($allowed_codes)) {
            return [];
        }
        
        // Melakukan query WHERE kode_unit IN ('KDS-001', 'BU02', ...)
        return $this->whereIn('kode_unit', $allowed_codes)->findAll();
    }
}