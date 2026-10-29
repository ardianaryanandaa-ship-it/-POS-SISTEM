<?php

namespace App\Controllers;

use App\Models\SalesModel;
use App\Models\SalesDetailModel;

class Penjualan extends BaseController
{
    protected $salesModel;
    protected $detailModel;

    public function __construct() {
        $this->salesModel = new SalesModel();
        $this->detailModel = new SalesDetailModel();
    }

    public function index() {
        return view('operasional/penjualan_history', ['title' => 'Riwayat Penjualan']);
    }

    public function list() {
        // Tambahkan Join ke tabel pelanggan dan ambil kolom discount
        $data = $this->salesModel
            ->select('sales.*, users.full_name as nama_kasir, bisnis_unit.nama_unit, pelanggan.nama_pelanggan')
            ->join('users', 'users.id = sales.user_id', 'left')
            ->join('bisnis_unit', 'bisnis_unit.kode_unit = sales.kode_unit', 'left')
            ->join('pelanggan', 'pelanggan.id = sales.id_pelanggan', 'left') // Join Pelanggan
            ->orderBy('sales.created_at', 'DESC')
            ->findAll();
        return $this->response->setJSON($data);
    }

    public function get_detail_with_sales($saleId) {
        // 1. Ambil data Header (Sales)
        $sale = $this->salesModel
            ->select('sales.*, users.full_name as nama_kasir, bisnis_unit.nama_unit, pelanggan.nama_pelanggan')
            ->join('users', 'users.id = sales.user_id', 'left')
            ->join('bisnis_unit', 'bisnis_unit.kode_unit = sales.kode_unit', 'left')
            ->join('pelanggan', 'pelanggan.id = sales.id_pelanggan', 'left')
            ->find($saleId);

             $items = $this->detailModel
            ->select('sales_details.*, master_barang.nama_barang')
            ->join('master_barang', 'master_barang.kode_barang = sales_details.kode_barang')
            ->where('sale_id', $saleId)
            ->findAll();

            return $this->response->setJSON([
            'sales' => $sale,
            'items' => $items
        ]);
    }

    public function get_detail($id) {
        $data = $this->detailModel
            ->select('sales_details.*, master_barang.nama_barang')
            ->join('master_barang', 'master_barang.kode_barang = sales_details.kode_barang')
            ->where('sale_id', $id)
            ->findAll();
        return $this->response->setJSON($data);
    }

    public function void($id) {
        $db = \Config\Database::connect();
        $db->transBegin();

        try {
            $sale = $this->salesModel->find($id);
            if (!$sale || $sale['status'] == 'Void') {
                return $this->response->setJSON(['status' => false, 'message' => 'Transaksi sudah dibatalkan sebelumnya.']);
            }

            // 1. KEMBALIKAN STOK KE UNIT ASAL
            $items = $this->detailModel->where('sale_id', $id)->findAll();
            foreach ($items as $it) {
                $db->table('stok_unit')
                   ->where('kode_barang', $it['kode_barang'])
                   ->where('kode_unit', $sale['kode_unit'])
                   ->set('jumlah', 'jumlah + ' . $it['qty'], false)
                   ->update();
            }

            // 2. BATALKAN POIN MEMBER (Jika transaksi menggunakan member)
            if (!empty($sale['id_pelanggan'])) {
                // Gunakan rumus yang sama saat checkout (Misal: 10rb = 1 poin)
                $poinBatal = floor($sale['total_price'] / 10000); 
                
                $db->table('pelanggan')
                   ->where('id', $sale['id_pelanggan'])
                   ->set('poin', "poin - $poinBatal", false)
                   ->update();
            }

            // 3. Update status nota jadi Void
            $this->salesModel->update($id, [
                'status' => 'Void',
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            if ($db->transStatus() === FALSE) {
                $db->transRollback();
                return $this->response->setJSON(['status' => false, 'message' => 'Gagal memproses ke database']);
            }

            $db->transCommit();
            return $this->response->setJSON(['status' => true, 'message' => 'Transaksi Berhasil Dibatalkan & Stok Dikembalikan']);
            
        } catch (\Exception $e) {
            $db->transRollback();
            return $this->response->setJSON(['status' => false, 'message' => $e->getMessage()]);
        }
    }
}