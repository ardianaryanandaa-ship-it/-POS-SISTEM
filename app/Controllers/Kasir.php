<?php
namespace App\Controllers;

use App\Models\MasterBarangModel;
use App\Models\SalesModel;
use App\Models\SalesDetailModel;

class Kasir extends BaseController
{
    protected $barangModel;
    protected $salesModel;
    protected $detailModel;
    protected $db;

    public function __construct() {
        $this->barangModel = new MasterBarangModel();
        $this->salesModel = new SalesModel();
        $this->detailModel = new SalesDetailModel();
        $this->db = \Config\Database::connect();
    }

    public function index() {
    $selectedUnit = session()->get('selected_unit');
    $listPelanggan = $this->db->table('pelanggan')->get()->getResultArray();
    $categories = $this->db->table('item_kategori')->get()->getResultArray();

    if (!$selectedUnit) {
        return view('operasional/kasir_view', [
            'title' => 'Pilih Unit', 
            'units' => $this->db->table('bisnis_unit')->get()->getResultArray(), 
            'pelanggan' => $listPelanggan,
            'categories' => $categories
        ]);
    }

    // PERBAIKAN: Gunakan MAX() pada kolom promo agar tidak error 1055
    $products = $this->db->table('master_barang mb')
        ->select('mb.kode_barang, mb.nama_barang, mb.nama_varian, mb.parent_kode, mb.harga_jual, mb.foto, mb.id_kategori, su.jumlah as stok, 
                  MAX(p.nama_promo) as nama_promo, 
                  MAX(p.jenis_promo) as jenis_promo, 
                  MAX(p.nilai_promo) as nilai_promo')
        ->join('stok_unit su', 'su.kode_barang = mb.kode_barang')
        ->join('promo_mapping pm', 'pm.kode_barang = mb.kode_barang OR pm.id_kategori = mb.id_kategori', 'left')
        ->join('master_promo p', 'p.id_promo = pm.id_promo AND p.status = "Aktif" 
            AND CURDATE() BETWEEN p.tgl_mulai AND p.tgl_selesai 
            AND CURTIME() BETWEEN p.jam_mulai AND p.jam_selesai', 'left')
        ->where('su.kode_unit', $selectedUnit)
        ->groupBy('mb.kode_barang, mb.nama_barang, mb.nama_varian, mb.parent_kode, mb.harga_jual, mb.id_kategori, su.jumlah') 
        ->get()->getResultArray();

    foreach ($products as &$p) {
        $p['harga_promo'] = $this->hitungHargaPromo($p['harga_jual'], $p);
    }

    $unitRow = $this->db->table('bisnis_unit')->where('kode_unit', $selectedUnit)->get()->getRow();
    
    $data = [
        'title'    => 'Kasir POS',
        'products' => $products,
        'categories' => $categories,
        'kodeUnit' => $selectedUnit,
        'namaUnit' => $unitRow ? $unitRow->nama_unit : 'Unknown',
        'pelanggan' => $listPelanggan
    ];
    return view('operasional/kasir_view', $data);
}

    public function set_unit($kode) {
        session()->set('selected_unit', $kode);
        return redirect()->to(base_url('kasir'));
    }

    public function ganti_unit() {
        session()->remove('selected_unit');
        return redirect()->to(base_url('kasir'));
    }

    public function search() {
        $keyword = $this->request->getGet('keyword');
        $kodeUnit = session()->get('selected_unit'); 

        $products = $this->db->table('master_barang mb')
            ->select('mb.kode_barang, mb.nama_barang, mb.nama_varian, mb.parent_kode, mb.harga_jual, su.jumlah as stok, p.nama_promo, p.jenis_promo, p.nilai_promo')
            ->join('stok_unit su', 'su.kode_barang = mb.kode_barang')
            ->join('promo_mapping pm', 'pm.kode_barang = mb.kode_barang OR pm.id_kategori = mb.id_kategori', 'left')
            ->join('master_promo p', 'p.id_promo = pm.id_promo AND p.status = "Aktif" 
                AND CURDATE() BETWEEN p.tgl_mulai AND p.tgl_selesai 
                AND CURTIME() BETWEEN p.jam_mulai AND p.jam_selesai', 'left')
            ->where('su.kode_unit', $kodeUnit)
            ->groupStart()
                ->like('mb.nama_barang', $keyword)
                ->orLike('mb.kode_barang', $keyword)
            ->groupEnd()
            ->groupBy('mb.kode_barang, mb.nama_varian, mb.parent_kode')
            ->get()->getResultArray();

        foreach ($products as &$p) {
            $p['harga_promo'] = $this->hitungHargaPromo($p['harga_jual'], $p);
        }

        return $this->response->setJSON($products);
    }

    /**
     * Helper Function untuk menghitung harga setelah diskon
     */
    private function hitungHargaPromo($hargaNormal, $promoData) {
        if (empty($promoData['nama_promo'])) return $hargaNormal;

        if ($promoData['jenis_promo'] == 'Persen') {
            return $hargaNormal - ($hargaNormal * ($promoData['nilai_promo'] / 100));
        } else {
            return $hargaNormal - $promoData['nilai_promo'];
        }
    }

    public function save_pelanggan_ajax() {
    $model = new \App\Models\PelangganModel();
    $data = [
        'nama_pelanggan' => $this->request->getPost('nama_pelanggan'),
        'telepon'        => $this->request->getPost('telepon'),
        'alamat'         => '-',
        'poin'           => 0
    ];
    
    $id = $model->insert($data); 
    
    return $this->response->setJSON([
        'status'    => 'success',
        'id'        => $id,
        'nama'      => $data['nama_pelanggan'],
        'telp'      => $data['telepon'],
        'csrf_hash' => csrf_hash() // KIRIMKAN HASH TERBARU KE VIEW
    ]);
}

    public function checkout() {
    $db = \Config\Database::connect();
    $userId = session()->get('id');
    $kodeUnit = session()->get('selected_unit');

    $idPelanggan = $this->request->getPost('id_pelanggan');
    $idPelanggan = ($idPelanggan == "") ? null : $idPelanggan;

    $shiftModel = new \App\Models\ShiftModel();
    $activeShift = $shiftModel->where(['user_id' => $userId, 'status' => 'open'])->first();

    if (!$activeShift) return $this->response->setJSON(['status' => 'error', 'message' => 'Silakan Buka Shift Terlebih Dahulu!']);

    $cart = $this->request->getPost('cart');
    $totalPrice = $this->request->getPost('total_price');
    $discount = (float)$this->request->getPost('discount'); // Pastikan angka

    // --- PERBAIKAN NAMA PROMO (Agar Diskon Member tidak hilang namanya) ---
    $namaPromo = $this->request->getPost('nama_promo');
    if (empty($namaPromo) && $discount > 0) {
        // Jika ada idPelanggan, berarti ini diskon member
        $namaPromo = (!empty($idPelanggan)) ? 'Diskon Member 5%' : 'Potongan Langsung';
    }

    $cashReceived = $this->request->getPost('cash_received');
    $totalBayar = $totalPrice - $discount; 
    $cashKembali = $cashReceived - $totalBayar;

    $db->transBegin();
    try {
        $invoiceNo = 'INV/' . date('Ymd') . '/' . strtoupper(substr(uniqid(), -4));

        $dataSales = [
            'invoice_no'     => $invoiceNo,
            'kode_unit'      => $kodeUnit, 
            'total_price'    => $totalPrice, 
            'discount'       => $discount,   
            'nama_promo'     => $namaPromo,
            'shift_id'       => $activeShift['id'],
            'payment_method' => $this->request->getPost('payment_method'),
            'cash_received'  => $cashReceived, 
            'cash_change'    => $cashKembali,  
            'id_pelanggan'   => $idPelanggan,
            'user_id'        => $userId,
            'status'         => 'Lunas' 
        ];

        if (!$this->salesModel->save($dataSales)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Gagal simpan header sales']);
        }
        $saleId = $this->salesModel->getInsertID();

        // Update Poin Pelanggan
        if ($idPelanggan != null) {
            $poinBaru = floor($totalBayar / 10000); 
            $db->table('pelanggan')->where('id', $idPelanggan)->set('poin', "poin + $poinBaru", false)->update();
        }

        // --- PERBAIKAN VARIABEL $barang ---
        foreach ($cart as $item) {
            // Kita ambil dulu data barangnya agar dapat 'harga_beli' (modal)
            $rowBarang = $this->barangModel->find($item['kode_barang']);
            $hargaModal = ($rowBarang) ? $rowBarang['harga_beli'] : 0;

            $this->detailModel->insert([
                'sale_id'       => $saleId, 
                'kode_barang'   => $item['kode_barang'],
                'qty'           => $item['qty'], 
                'price_at_sale' => $item['harga'], 
                'cost_price'    => $hargaModal, // SEKARANG SUDAH ADA (Fixed)
                'subtotal'      => $item['qty'] * $item['harga']
            ]);
            
            $db->table('stok_unit')
               ->where(['kode_barang' => $item['kode_barang'], 'kode_unit' => $kodeUnit])
               ->set('jumlah', "jumlah - {$item['qty']}", false)
               ->update();
        }

        if ($db->transStatus() === FALSE) { 
            $db->transRollback(); 
            return $this->response->setJSON(['status' => 'error', 'message' => 'Database error']); 
        }
        
        $db->transCommit();
        return $this->response->setJSON(['status' => 'success', 'invoice' => $invoiceNo, 'sale_id' => $saleId]);

    } catch (\Exception $e) { 
        $db->transRollback(); 
        return $this->response->setJSON(['status' => 'error', 'message' => $e->getMessage()]); 
    }
}

    // Tambahkan fungsi ini di Kasir.php untuk dipanggil via AJAX
public function cek_diskon() {
    $id_pelanggan = $this->request->getGet('id_pelanggan');
    $total_belanja = $this->request->getGet('total_belanja');
    
    $diskon = 0;

    // Skenario 1: Jika dia Member (Bukan Umum), beri diskon 5%
    if (!empty($id_pelanggan)) {
        $diskon = $total_belanja * 0.05; // 5% diskon
    }

    // Skenario 2: Tambahan diskon jika belanja > 10 Juta
    if ($total_belanja >= 10000000) {
        $diskon += 25000; // Bonus potongan 25rb
    }

    return $this->response->setJSON([
        'diskon' => $diskon,
        'total_akhir' => $total_belanja - $diskon
    ]);
}

    public function print_receipt($saleId)
    {

$sale = $this->salesModel
        ->select('sales.*, users.full_name as nama_kasir, bisnis_unit.nama_unit')
        ->join('users', 'users.id = sales.user_id')
        ->join('bisnis_unit', 'bisnis_unit.kode_unit = sales.kode_unit', 'left')
        ->where('sales.id', $saleId)
        ->first();

        $sale = $this->salesModel->find($saleId);
        if (!$sale) return "Data transaksi tidak ditemukan.";

        $details = $this->detailModel
            ->select('sales_details.*, master_barang.nama_barang')
            ->join('master_barang', 'master_barang.kode_barang = sales_details.kode_barang')
            ->where('sale_id', $saleId)
            ->findAll();

        $data = ['sale' => $sale, 'details' => $details];
        return view('operasional/print_receipt', $data);
    }

    public function get_price($kode_barang) {
    // 1. Ambil harga asli
    $barang = $this->barangModel->find($kode_barang);
    $harga = $barang['harga_jual'];

    // 2. Cek apakah ada promo yang aktif sekarang
    $promo = $this->db->table('master_promo p')
        ->join('promo_mapping pm', 'p.id_promo = pm.id_promo')
        ->where('pm.kode_barang', $kode_barang)
        ->orWhere('pm.id_kategori', $barang['id_kategori'])
        ->where('p.status', 'Aktif')
        ->where('CURDATE() BETWEEN tgl_mulai AND tgl_selesai')
        ->where('CURTIME() BETWEEN jam_mulai AND jam_selesai')
        ->get()->getRow();

    if ($promo) {
        if ($promo->jenis_promo == 'Persen') {
            $harga = $harga - ($harga * $promo->nilai_promo / 100);
        } else {
            $harga = $harga - $promo->nilai_promo;
        }
    }
    
    return $harga;
}

public function upload_gambar_barang() {
    $kode = $this->request->getPost('kode_barang');
    $file = $this->request->getFile('foto');

    if ($file && $file->isValid() && !$file->hasMoved()) {
        $uploadPath = FCPATH . 'uploads/produk';
        if (!is_dir($uploadPath)) mkdir($uploadPath, 0777, true);

        $newName = $file->getRandomName();
        $file->move($uploadPath, $newName);

        $db = \Config\Database::connect();
        
        // UPDATE FOTO DI TABEL 'sales'
        $db->table('master_barang')->where('kode_barang', $kode)->update(['foto' => $newName]);

        return $this->response->setJSON([
            'status' => 'success',
            'new_file' => $newName,
            'csrf_hash' => csrf_hash()
        ]);
    }
    return $this->response->setJSON(['status' => 'error', 'message' => 'Gagal simpan']);
}
}