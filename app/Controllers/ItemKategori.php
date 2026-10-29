<?php

namespace App\Controllers;

use App\Models\ItemKategoriModel;

class ItemKategori extends BaseController
{
    protected $kategoriModel;

    public function __construct() {
        $this->kategoriModel = new ItemKategoriModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Kategori Barang',
            // Data tidak dikirim dari sini lagi karena akan dipanggil via AJAX
        ];
        return view('kategori/index', $data);
    }

    // --- TAMBAHAN UNTUK AMBIL SEMUA DATA (AJAX) ---
    public function list()
    {
        return $this->response->setJSON($this->kategoriModel->findAll());
    }

    // --- TAMBAHAN UNTUK AMBIL SATU DATA (AJAX EDIT) ---
    public function get($id)
    {
        return $this->response->setJSON($this->kategoriModel->find($id));
    }

    public function save()
    {
        $this->kategoriModel->save([
            'nama_kategori' => $this->request->getPost('nama_kategori'),
            'deskripsi'     => $this->request->getPost('deskripsi'),
        ]);
        // Berubah dari redirect ke setJSON
        return $this->response->setJSON(['status' => true, 'message' => 'Kategori berhasil ditambahkan']);
    }

    public function update($id)
    {
        $this->kategoriModel->update($id, [
            'nama_kategori' => $this->request->getPost('nama_kategori'),
            'deskripsi'     => $this->request->getPost('deskripsi'),
        ]);
        // Berubah dari redirect ke setJSON
        return $this->response->setJSON(['status' => true, 'message' => 'Kategori berhasil diperbarui']);
    }

    public function delete($id)
    {
        $this->kategoriModel->delete($id);
        // Berubah dari redirect ke setJSON
        return $this->response->setJSON(['status' => true, 'message' => 'Kategori berhasil dihapus']);
    }
}