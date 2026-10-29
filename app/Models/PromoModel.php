<?php

namespace App\Models;

use CodeIgniter\Model;

class PromoModel extends Model
{
    protected $table            = 'master_promo';
    protected $primaryKey       = 'id_promo';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'nama_promo', 'jenis_promo', 'nilai_promo', 'minimal_beli', 
        'tgl_mulai', 'tgl_selesai', 'jam_mulai', 'jam_selesai', 
        'hari_aktif', 'status'
    ];

    /**
     * Mendapatkan semua promo beserta list barang/kategorinya (untuk Index)
     */
    public function getAllPromoWithMapping()
    {
        return $this->select('master_promo.*, 
                GROUP_CONCAT(DISTINCT promo_mapping.kode_barang) as list_barang, 
                GROUP_CONCAT(DISTINCT promo_mapping.id_kategori) as list_kategori')
            ->join('promo_mapping', 'promo_mapping.id_promo = master_promo.id_promo', 'left')
            ->groupBy('master_promo.id_promo')
            ->orderBy('master_promo.id_promo', 'DESC')
            ->findAll();
    }
}