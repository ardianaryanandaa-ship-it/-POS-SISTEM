<?php

namespace App\Controllers;

use App\Models\PromoModel;
use App\Models\PromoMappingModel;
use App\Models\MasterBarangModel;
use CodeIgniter\Controller;

class Promo extends BaseController
{
    protected $db;

    public function __construct() {
        $this->db = \Config\Database::connect();
    }

    public function index()
    {
        $data = [
            'title' => 'Manajemen Promo & Diskon',
            'promos' => $this->db->table('master_promo')->orderBy('id_promo', 'DESC')->get()->getResultArray(),
            'kategori' => $this->db->table('item_kategori')->get()->getResultArray(),
            'barang' => $this->db->table('master_barang')->get()->getResultArray()
        ];
        return view('marketing/promo_view', $data);
    }

    public function save()
{
    $promoModel = new PromoModel();
    $mappingModel = new PromoMappingModel();
    
    $target_tipe = $this->request->getPost('target_tipe');
    $hari = $this->request->getPost('hari_aktif'); // Ambil checkbox hari

    $this->db->transStart();

    // 1. Simpan Aturan Utama
    $dataPromo = [
        'nama_promo'   => $this->request->getPost('nama_promo'),
        'jenis_promo'  => $this->request->getPost('jenis_promo'),
        'nilai_promo'  => $this->request->getPost('nilai_promo'),
        'minimal_beli' => $this->request->getPost('minimal_beli') ?: 1,
        'tgl_mulai'    => $this->request->getPost('tgl_mulai'),
        'tgl_selesai'  => $this->request->getPost('tgl_selesai'),
        'jam_mulai'    => $this->request->getPost('jam_mulai') ?: '00:00:00',
        'jam_selesai'  => $this->request->getPost('jam_selesai') ?: '23:59:59',
        // PENTING: Ubah array hari menjadi string dipisahkan koma untuk tipe data SET
        'hari_aktif'   => $hari ? implode(',', $hari) : 'Senin,Selasa,Rabu,Kamis,Jumat,Sabtu,Minggu',
        'status'       => 'Aktif'
    ];
    
    $promoModel->insert($dataPromo);
    $id_promo = $promoModel->getInsertID();

    // 2. Simpan Mapping (Multiple Target)
    if ($target_tipe == 'Barang') {
        $items = $this->request->getPost('kode_barang');
        if($items){
            foreach ($items as $kode) {
                $mappingModel->insert([
                    'id_promo' => $id_promo,
                    'kode_barang' => $kode,
                    'id_kategori' => null
                ]);
            }
        }
    } else {
        $categories = $this->request->getPost('id_kategori');
        if($categories){
            foreach ($categories as $id_kat) {
                $mappingModel->insert([
                    'id_promo' => $id_promo,
                    'kode_barang' => null,
                    'id_kategori' => $id_kat
                ]);
            }
        }
    }

    $this->db->transComplete();
    return redirect()->to(base_url('promo'))->with('success', 'Promo berhasil diaktifkan!');
}

    public function delete($id)
    {
        $this->db->table('master_promo')->where('id_promo', $id)->delete();
        return redirect()->to(base_url('promo'))->with('success', 'Promo dihapus.');
    }
}
