<?php

namespace App\Controllers;

use App\Models\MasterBarangModel;
use App\Models\ItemKategoriModel;

class MasterBarang extends BaseController
{
    protected $barangModel;
    protected $kategoriModel;

    public function __construct() {
        $this->barangModel = new MasterBarangModel();
        $this->kategoriModel = new ItemKategoriModel();
    }

    public function index()
    {
        $data = [
            'title'    => 'Master Barang',
            'kategori' => $this->kategoriModel->findAll(),
            // Tambahkan ini untuk pilihan "Induk" saat tambah varian di View
            'induk_barang' => $this->barangModel->where('parent_kode', null)->findAll()
        ];
        return view('master_barang/index', $data);
    }

    /**
     * Ambil data untuk DataTables (AJAX)
     */
    public function list()
    {
        // Pastikan getBarangWithKategori() di Model menyertakan parent_kode dan nama_varian
        return $this->response->setJSON($this->barangModel->getBarangWithKategori());
    }

    /**
     * Ambil data "Anak" dari suatu produk (Untuk melihat list varian)
     */
    public function get_variants($parent_kode)
    {
        $data = $this->barangModel->where('parent_kode', $parent_kode)->findAll();
        return $this->response->setJSON($data);
    }

    /**
     * Ambil satu data untuk Edit (AJAX)
     */
    public function get($id)
    {
        return $this->response->setJSON($this->barangModel->find($id));
    }

    /**
     * Simpan Barang Baru (Termasuk Varian)
     */
    public function save()
{
    $db = \Config\Database::connect();
    $db->transStart(); // Gunakan transaksi agar data tidak parsial jika error

    try {
        $kodeUtama   = $this->request->getPost('kode_barang');
        $namaUtama   = $this->request->getPost('nama_barang');
        $parentKode  = $this->request->getPost('parent_kode') ?: null;
        $namaVarian  = $this->request->getPost('nama_varian') ?: null;

        if ($parentKode) {
            // Simpan varian baru yang langsung ditambahkan ke produk induk
            $dataVarianLangsung = [
                'kode_barang'    => $kodeUtama,
                'nama_barang'    => $namaUtama,
                'id_kategori'    => $this->request->getPost('id_kategori'),
                'satuan_standar' => $this->request->getPost('satuan_standar'),
                'harga_beli'     => $this->request->getPost('harga_beli'),
                'harga_jual'     => $this->request->getPost('harga_jual'),
                'parent_kode'    => $parentKode,
                'nama_varian'    => $namaVarian,
            ];
            $this->barangModel->insert($dataVarianLangsung);
        } else {
            // Simpan produk utama terlebih dahulu
            $dataInduk = [
                'kode_barang'    => $kodeUtama,
                'nama_barang'    => $namaUtama,
                'id_kategori'    => $this->request->getPost('id_kategori'),
                'satuan_standar' => $this->request->getPost('satuan_standar'),
                'harga_beli'     => $this->request->getPost('harga_beli'),
                'harga_jual'     => $this->request->getPost('harga_jual'),
                'parent_kode'    => null,
                'nama_varian'    => null
            ];
            $this->barangModel->insert($dataInduk);

            // Proses varian tambahan jika ada
            $v_nama  = $this->request->getPost('v_nama');
            $v_kode  = $this->request->getPost('v_kode');
            $v_harga = $this->request->getPost('v_harga');

            if (!empty($v_nama)) {
                foreach ($v_nama as $index => $namaVarian) {
                    $dataVarian = [
                        'kode_barang'    => $v_kode[$index],
                        'parent_kode'    => $kodeUtama,
                        'nama_barang'    => $namaUtama,
                        'nama_varian'    => $namaVarian,
                        'id_kategori'    => $this->request->getPost('id_kategori'),
                        'satuan_standar' => $this->request->getPost('satuan_standar'),
                        'harga_beli'     => $this->request->getPost('harga_beli'),
                        'harga_jual'     => $v_harga[$index],
                    ];
                    $this->barangModel->insert($dataVarian);
                }
            }
        }

        $db->transComplete();
        return $this->response->setJSON(['status' => true, 'message' => 'Produk dan Varian berhasil disimpan']);

    } catch (\Exception $e) {
        $db->transRollback();
        return $this->response->setJSON(['status' => false, 'message' => $e->getMessage()]);
    }
}

    /**
     * Perbarui Data Barang
     */
    public function update($id)
    {
        $parentKodePost = $this->request->getPost('parent_kode') ?: null;

        $data = [
            'parent_kode'    => $parentKodePost,
            'nama_barang'    => $this->request->getPost('nama_barang'),
            'nama_varian'    => $this->request->getPost('nama_varian') ?: null,
            'id_kategori'    => $this->request->getPost('id_kategori'),
            'satuan_standar' => $this->request->getPost('satuan_standar'),
            'harga_beli'     => $this->request->getPost('harga_beli'),
            'harga_jual'     => $this->request->getPost('harga_jual'),
        ];

        // Lakukan update pada record yang dipilih
        $updated = $this->barangModel->update($id, $data);

        if (! $updated) {
            return $this->response->setJSON(['status' => false, 'message' => 'Gagal memperbarui data']);
        }

        // Jika record yang di-update adalah produk induk (parent_kode = null), proses juga input varian baru
        // (form mengirimkan array v_nama[], v_kode[], v_harga[] saat menambah varian di modal edit)
        $v_nama  = $this->request->getPost('v_nama');
        $v_kode  = $this->request->getPost('v_kode');
        $v_harga = $this->request->getPost('v_harga');

        if (empty($parentKodePost) && ! empty($v_nama) && is_array($v_nama)) {
            foreach ($v_nama as $index => $namaVarian) {
                $kodeVar = $v_kode[$index] ?? null;
                $hargaVar = $v_harga[$index] ?? 0;
                if (! $kodeVar) continue;

                // Jika kode varian belum ada di DB, buat baru. Jika sudah ada, lewati.
                if (! $this->barangModel->find($kodeVar)) {
                    $insertVar = [
                        'kode_barang'    => $kodeVar,
                        'parent_kode'    => $id,
                        'nama_barang'    => $this->request->getPost('nama_barang'),
                        'nama_varian'    => $namaVarian,
                        'id_kategori'    => $this->request->getPost('id_kategori'),
                        'satuan_standar' => $this->request->getPost('satuan_standar'),
                        'harga_beli'     => $this->request->getPost('harga_beli'),
                        'harga_jual'     => $hargaVar,
                    ];
                    $this->barangModel->insert($insertVar);
                }
            }
        }

        return $this->response->setJSON(['status' => true, 'message' => 'Data berhasil diperbarui']);
    }
    /**
     * Hapus Barang
     */
    public function delete($id)
    {
        if ($this->barangModel->delete($id)) {
            return $this->response->setJSON(['status' => true, 'message' => 'Barang berhasil dihapus']);
        } else {
            return $this->response->setJSON(['status' => false, 'message' => 'Gagal menghapus data']);
        }
    }
}