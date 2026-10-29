<?php namespace App\Models;
use CodeIgniter\Model;

class ReturBarangModel extends Model {
    protected $table = 'retur_barang';
    protected $primaryKey = 'id_retur';
    
    // PERBAIKAN: Pastikan 'user' ada agar bisa mencatat siapa yang edit/input
    protected $allowedFields = [
        'no_retur', 
        'id_masuk', 
        'nama_vendor', 
        'kode_unit', 
        'tanggal_retur', 
        'status', 
        'user' // <--- HARUS ADA
    ];

    public function getFullDetail() {
        return $this->select('retur_barang.*, bisnis_unit.nama_unit')
                    ->join('bisnis_unit', 'bisnis_unit.kode_unit = retur_barang.kode_unit')
                    ->orderBy('tanggal_retur', 'DESC')->findAll();
    }
}