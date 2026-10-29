<?php

namespace App\Controllers;

use App\Models\BarangModel;

class Barang extends BaseController
{
    protected $barangModel;

    public function __construct()
    {
        $this->barangModel = new BarangModel();
        helper(['barang']);
    }

    public function index()
    {
        $data = [
            'title'  => 'Daftar Barang',
            'barang' => get_list_barang()
        ];

        return view('barang/index', $data);
    }

    public function simpan()
    {
        $data = ['nama_barang' => $this->request->getPost('nama_barang')];

        if (db_simpan_barang($data)) {
            return $this->response->setJSON([
                'status' => 'success', 
                'pesan'  => 'Barang berhasil ditambahkan',
                'token'  => csrf_hash() // Penting untuk keamanan AJAX selanjutnya
            ]);
        }
        return $this->response->setJSON(['status' => 'error', 'pesan' => 'Gagal simpan', 'token' => csrf_hash()]);
    }

    public function ubah($id)
    {
        $data = ['nama_barang' => $this->request->getPost('nama_barang')];

        if (db_update_barang($id, $data)) {
            return $this->response->setJSON([
                'status' => 'success', 
                'pesan'  => 'Barang berhasil diubah',
                'token'  => csrf_hash()
            ]);
        }
        return $this->response->setJSON(['status' => 'error', 'pesan' => 'Gagal ubah', 'token' => csrf_hash()]);
    }

    public function hapus($id)
    {
        if (db_delete_barang($id)) {
            return $this->response->setJSON([
                'status' => 'success', 
                'pesan'  => 'Barang berhasil dihapus',
                'token'  => csrf_hash()
            ]);
        }
        return $this->response->setJSON(['status' => 'error', 'pesan' => 'Gagal hapus', 'token' => csrf_hash()]);
    }
}