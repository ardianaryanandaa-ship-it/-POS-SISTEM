<?php

namespace App\Controllers\Api;

use App\Models\BarangMasukModel;
use App\Models\BarangMasukDetailModel;
use App\Models\MasterBarangModel;
use App\Models\BisnisUnitModel;

class BarangMasukApi extends BaseApiController
{
    protected $barangMasukModel;
    protected $barangDetailModel;
    protected $barangModel;
    protected $unitModel;

    public function __construct()
    {
        $this->barangMasukModel  = new BarangMasukModel();
        $this->barangDetailModel = new BarangMasukDetailModel();
        $this->barangModel       = new MasterBarangModel();
        $this->unitModel         = new BisnisUnitModel();
    }

    // GET api/barang-masuk
    public function index()
    {
        $data = $this->barangMasukModel->getFullDetail();
        return $this->apiResponse($data, "Daftar barang masuk berhasil diambil");
    }

    // GET api/barang-masuk/(:num)
    public function show($id = null)
    {
        $db = \Config\Database::connect();
        
        $header = $this->barangMasukModel
                       ->select('barang_masuk.*, bisnis_unit.nama_unit')
                       ->join('bisnis_unit', 'bisnis_unit.kode_unit = barang_masuk.kode_unit')
                       ->where('id_masuk', $id)
                       ->first();

        if (!$header) return $this->apiError("Nota tidak ditemukan", 404);

        $items = $this->barangDetailModel
                      ->select('barang_masuk_detail.*, master_barang.nama_barang')
                      ->join('master_barang', 'master_barang.kode_barang = barang_masuk_detail.kode_barang')
                      ->where('id_masuk', $id)
                      ->findAll();

        // Logika Retur & Neto (Sama dengan controller web Anda)
        $retur = $db->table('retur_barang_detail rd')
                    ->select('rd.kode_barang, SUM(rd.jumlah) as total_retur')
                    ->join('retur_barang rh', 'rh.id_retur = rd.id_retur')
                    ->where('rh.id_masuk', $id)->where('rh.status', 1)
                    ->groupBy('rd.kode_barang')->get()->getResultArray();

        $returMap = array_column($retur, 'total_retur', 'kode_barang');

        foreach ($items as &$item) {
            $jml_r = $returMap[$item['kode_barang']] ?? 0;
            $item['jumlah_retur'] = (int)$jml_r;
            $item['jumlah_neto'] = (int)$item['jumlah'] - (int)$jml_r;
        }

        $data = [
            'header' => $header,
            'items'  => $items,
            'logs'   => $db->table('barang_masuk_log')->where('id_masuk', $id)->orderBy('created_at', 'DESC')->get()->getResultArray()
        ];

        return $this->apiResponse($data, "Detail nota berhasil diambil");
    }

    // POST api/barang-masuk (Simpan Nota Baru dari Mobile)
    public function create()
    {
        $db = \Config\Database::connect();
        $db->transStart();

        // Mobile mengirim JSON
        $json = $this->request->getJSON(); 

        $id_masuk = $this->barangMasukModel->insert([
            'no_referensi'  => $json->no_referensi,
            'nama_vendor'   => $json->nama_vendor,
            'kode_unit'     => $json->kode_unit,
            'tanggal_masuk' => date('Y-m-d H:i:s'),
            'penerima'      => $json->penerima, // Diambil dari data login di mobile
            'status'        => 0
        ]);

        foreach ($json->items as $item) {
            $this->barangDetailModel->save([
                'id_masuk'      => $id_masuk,
                'kode_barang'   => $item->kode_barang,
                'jumlah_faktur' => $item->jumlah,
                'jumlah'        => $item->jumlah,
            ]);
        }

        // Log Aksi (User diganti dari session ke nama dari mobile)
        $db->table('barang_masuk_log')->insert([
            'id_masuk'   => $id_masuk,
            'aksi'       => 'Input',
            'keterangan' => 'Input Nota Baru via Mobile',
            'user'       => $json->penerima ?? 'Admin Mobile',
            'created_at' => date('Y-m-d H:i:s')
        ]);

        $db->transComplete();

        if ($db->transStatus() === false) {
            return $this->apiError("Gagal menyimpan transaksi");
        }

        return $this->apiResponse(['id_masuk' => $id_masuk], "Nota Berhasil Disimpan", 201);
    }

    // POST api/barang-masuk/verify/(:num)
    public function verify($id)
    {
        // Logika verifikasi sama dengan controller web Anda, 
        // tapi kembalikan $this->apiResponse() bukan JSON mentah.
        // (Saran: Copy logika verify Anda ke sini dan sesuaikan return-nya)
        
        // ... (Logika Verify Anda) ...

        return $this->apiResponse(null, "Nota Berhasil Diverifikasi");
    }
}
