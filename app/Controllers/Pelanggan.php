<?php
namespace App\Controllers;
use App\Models\PelangganModel;

class Pelanggan extends BaseController {
    protected $pelangganModel;

    public function __construct() {
        $this->pelangganModel = new PelangganModel();
    }

    public function index() {
        $data = [
            'title' => 'Manajemen Pelanggan',
            'pelanggan' => $this->pelangganModel->findAll()
        ];
        return view('pelanggan/index', $data);
    }

    public function detail($id) {
        $pelanggan = $this->pelangganModel->find($id);
        if (!$pelanggan) throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();

        $data = [
            'title' => 'Profil Member: ' . $pelanggan['nama_pelanggan'],
            'p' => $pelanggan,
            'total_tahunan' => $this->pelangganModel->getTotalBelanjaTahunan($id),
            'transaksi' => $this->pelangganModel->getRiwayatTransaksi($id)
        ];
        return view('pelanggan/detail', $data);
    }

    public function save() {
        $this->pelangganModel->save([
            'nama_pelanggan' => $this->request->getPost('nama_pelanggan'),
            'telepon' => $this->request->getPost('telepon'),
            'alamat' => $this->request->getPost('alamat'),
            'poin' => 0
        ]);
        return redirect()->to('/pelanggan')->with('success', 'Member berhasil didaftarkan');
    }
}