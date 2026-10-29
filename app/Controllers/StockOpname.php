<?php

namespace App\Controllers;

use App\Models\StockOpnameModel;
use App\Models\MasterBarangModel;
use App\Models\BisnisUnitModel;

class StockOpname extends BaseController
{
    protected $soModel;
    protected $barangModel;
    protected $unitModel;

    public function __construct()
    {
        $this->soModel = new StockOpnameModel();
        $this->barangModel = new MasterBarangModel();
        $this->unitModel = new BisnisUnitModel();
    }

    public function index()
    {
        $data = [
            'title'   => 'Riwayat Stock Opname',
            'history' => $this->soModel->select('stock_opname.*, master_barang.nama_barang, bisnis_unit.nama_unit')
                ->join('master_barang', 'master_barang.kode_barang = stock_opname.kode_barang')
                ->join('bisnis_unit', 'bisnis_unit.kode_unit = stock_opname.kode_unit')
                ->orderBy('tanggal_opname', 'DESC')
                ->findAll(),
            'barang'  => $this->barangModel->findAll(),
            'unit'    => $this->unitModel->findAll()
        ];
        return view('stock_opname/index', $data);
    }

    public function save()
    {
        $kode_barang = $this->request->getPost('kode_barang');
        $kode_unit   = $this->request->getPost('kode_unit');
        $stok_fisik  = (int)$this->request->getPost('stok_fisik');
        $stok_sistem = (int)$this->request->getPost('stok_sistem');
        $selisih     = $stok_fisik - $stok_sistem;

        $db = \Config\Database::connect();
        $db->transStart();

        // A. Catat Riwayat Opname
        $this->soModel->save([
            'kode_unit'      => $kode_unit,
            'kode_barang'    => $kode_barang,
            'stok_sistem'    => $stok_sistem,
            'stok_fisik'     => $stok_fisik,
            'selisih'        => $selisih,
            'keterangan'     => $this->request->getPost('keterangan'),
            'status'         => 'Pending',
            'tanggal_opname' => date('Y-m-d H:i:s')
        ]);

        // B. ALUR ADJUSTMENT PER UNIT (PENTING!)
        // Update jumlah di tabel yang menampung stok per unit (misal nama tabelnya: stok_unit)
        $db->table('stok_unit') // SESUAIKAN dengan nama tabel matriks stok Anda
           ->where('kode_unit', $kode_unit)
           ->where('kode_barang', $kode_barang)
           ->update(['jumlah' => $stok_fisik]);

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->with('error', 'Gagal melakukan penyesuaian stok.');
        }

        return redirect()->to('/stockopname')->with('success', 'Data opname disimpan. Menunggu persetujuan Manager.');
    }

    public function approve($id)
{
    $data = $this->soModel->find($id);
    
    if (!$data) return redirect()->back()->with('error', 'Data tidak ditemukan.');

    $db = \Config\Database::connect();
    $db->transStart();

    // A. Update Status di Riwayat
    $this->soModel->update($id, ['status' => 'Approved']);

    // B. EKSEKUSI ADJUSTMENT STOK (Hanya saat di-approve)
    $db->table('stok_unit')
       ->where('kode_unit', $data['kode_unit'])
       ->where('kode_barang', $data['kode_barang'])
       ->update(['jumlah' => $data['stok_fisik']]);

    $db->transComplete();

    return redirect()->to('/stockopname')->with('success', 'Opname Berhasil di-Approve. Stok telah diperbarui.');
}

// 3. FUNGSI REJECT (Membatalkan Opname)
public function reject($id)
{
    $this->soModel->update($id, ['status' => 'Rejected']);
    return redirect()->to('/stockopname')->with('success', 'Data opname ditolak.');
}

    // Tambahkan di dalam class StockOpname

// 1. Ambil data detail untuk Edit/Detail Modal (AJAX)
public function get_detail($id)
{
    $data = $this->soModel->select('stock_opname.*, master_barang.nama_barang, bisnis_unit.nama_unit')
        ->join('master_barang', 'master_barang.kode_barang = stock_opname.kode_barang')
        ->join('bisnis_unit', 'bisnis_unit.kode_unit = stock_opname.kode_unit')
        ->where('id_opname', $id)
        ->first();

    return json_encode($data);
}

// 2. Proses Update Data
public function update()
{
    $id = $this->request->getPost('id_opname');
    $kode_unit = $this->request->getPost('kode_unit');
    $kode_barang = $this->request->getPost('kode_barang');
    $stok_fisik = (int)$this->request->getPost('stok_fisik');
    $stok_sistem = (int)$this->request->getPost('stok_sistem');

    $db = \Config\Database::connect();
    $db->transStart();

    // Update Riwayat
    $this->soModel->update($id, [
        'stok_fisik' => $stok_fisik,
        'selisih'    => $stok_fisik - $stok_sistem,
        'keterangan' => $this->request->getPost('keterangan'),
    ]);

    // Sinkronkan ulang stok unit
    $db->table('stok_unit')
       ->where('kode_unit', $kode_unit)
       ->where('kode_barang', $kode_barang)
       ->update(['jumlah' => $stok_fisik]);

    $db->transComplete();
    return redirect()->to('/stockopname')->with('success', 'Data Opname berhasil diperbarui.');
}

// 3. Proses Hapus
public function delete($id)
{
    // Catatan: Menghapus riwayat tidak mengembalikan stok ke angka sebelumnya (Audit Trail)
    $this->soModel->delete($id);
    return redirect()->to('/stockopname')->with('success', 'Riwayat Opname berhasil dihapus.');
}

    // AJAX: Ambil stok berdasarkan Unit dan Barang
    public function get_stok_sistem($kode_unit, $kode_barang)
    {
        $db = \Config\Database::connect();
        $query = $db->table('stok_unit') // SESUAIKAN dengan nama tabel matriks stok Anda
                    ->where('kode_unit', $kode_unit)
                    ->where('kode_barang', $kode_barang)
                    ->get()
                    ->getRow();

        $stok = ($query) ? $query->jumlah : 0;
        return json_encode(['stok' => $stok]);
    }
}