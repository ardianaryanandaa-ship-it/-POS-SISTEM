<?php

namespace App\Controllers;

use App\Models\BisnisUnitModel;

class BisnisUnit extends BaseController
{
    protected $bisnisUnitModel;

    public function __construct() {
        $this->bisnisUnitModel = new BisnisUnitModel();
        // Memastikan helper auth dimuat di seluruh fungsi
        helper('auth');
    }

    public function index()
    {
        return view('bisnis_unit/index', ['title' => 'Daftar Bisnis Unit']);
    }

    public function list()
    {
        $role_id = session()->get('role_id');
        $bu_encrypted = session()->get('bu') ?? [];

        // --- PROSES DEKRIPSI ARRAY BU ---
        $bu_allowed = array_map(function($item) {
            return decrypt_value($item);
        }, $bu_encrypted);

        // Jika Developer (Role 3), tampilkan semua
        if ($role_id == 3) {
            $data = $this->bisnisUnitModel->findAll();
        } else {
            // Gunakan hasil dekripsi ($bu_allowed) untuk query database
            $data = !empty($bu_allowed) ? $this->bisnisUnitModel->whereIn('kode_unit', $bu_allowed)->findAll() : [];
        }

        return $this->response->setJSON($data);
    }

    public function get($id)
    {
        $role_id = session()->get('role_id');
        $bu_encrypted = session()->get('bu') ?? [];
        
        // Dekripsi untuk pengecekan in_array
        $bu_allowed = array_map(fn($item) => decrypt_value($item), $bu_encrypted);

        // Proteksi: Bandingkan ID yang diminta dengan daftar BU yang sudah didekripsi
        if ($role_id != 3 && !in_array($id, $bu_allowed)) {
            return $this->response->setJSON(['status' => false, 'message' => 'Akses ditolak']);
        }

        return $this->response->setJSON($this->bisnisUnitModel->find($id));
    }

    public function save()
    {
        $this->bisnisUnitModel->save([
            'kode_unit'  => $this->request->getPost('kode_unit'),
            'nama_unit'  => $this->request->getPost('nama_unit'),
            'jenis'      => $this->request->getPost('jenis'),
            'alamat'     => $this->request->getPost('alamat'),
            'jenis_unit' => $this->request->getPost('jenis_unit'),
            'wilayah'    => $this->request->getPost('wilayah'),
        ]);
        return $this->response->setJSON(['status' => true, 'message' => 'Data berhasil disimpan']);
    }

    public function update()
    {
        $id = $this->request->getPost('kode_unit');
        $role_id = session()->get('role_id');
        $bu_encrypted = session()->get('bu') ?? [];
        
        // Dekripsi untuk validasi akses update
        $bu_allowed = array_map(fn($item) => decrypt_value($item), $bu_encrypted);

        if ($role_id != 3 && !in_array($id, $bu_allowed)) {
            return $this->response->setJSON(['status' => false, 'message' => 'Akses ditolak']);
        }

        $this->bisnisUnitModel->update($id, [
            'nama_unit'  => $this->request->getPost('nama_unit'),
            'jenis'      => $this->request->getPost('jenis'),
            'alamat'     => $this->request->getPost('alamat'),
            'jenis_unit' => $this->request->getPost('jenis_unit'),
            'wilayah'    => $this->request->getPost('wilayah'),
        ]);
        return $this->response->setJSON(['status' => true, 'message' => 'Data berhasil diperbarui']);
    }

    public function delete($id)
    {
        $role_id = session()->get('role_id');
        $bu_encrypted = session()->get('bu') ?? [];
        
        // Dekripsi untuk validasi akses delete
        $bu_allowed = array_map(fn($item) => decrypt_value($item), $bu_encrypted);

        if ($role_id != 3 && !in_array($id, $bu_allowed)) {
            return $this->response->setJSON(['status' => false, 'message' => 'Akses ditolak']);
        }

        $this->bisnisUnitModel->delete($id);
        return $this->response->setJSON(['status' => true, 'message' => 'Data berhasil dihapus']);
    }
}