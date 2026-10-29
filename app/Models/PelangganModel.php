<?php
namespace App\Models;
use CodeIgniter\Model;

class PelangganModel extends Model {
    protected $table = 'pelanggan';
    protected $primaryKey = 'id';
    protected $allowedFields = ['nama_pelanggan', 'telepon', 'alamat', 'poin'];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = '';

    // Menghitung total belanja pelanggan dalam tahun berjalan
    public function getTotalBelanjaTahunan($id_pelanggan) {
        return $this->db->table('sales')
            ->selectSum('total_price')
            ->where('id_pelanggan', $id_pelanggan)
            ->where('YEAR(created_at)', date('Y'))
            ->where('status', 'Lunas')
            ->get()->getRow()->total_price ?? 0;
    }

    // Mendapatkan riwayat transaksi per pelanggan
    public function getRiwayatTransaksi($id_pelanggan) {
        return $this->db->table('sales')
            ->where('id_pelanggan', $id_pelanggan)
            ->orderBy('created_at', 'DESC')
            ->get()->getResultArray();
    }
}