<?php

namespace App\Controllers;

class ReportController extends BaseController
{
    protected $db;

    public function __construct() {
        $this->db = \Config\Database::connect();
    }

    // --- 1. LAPORAN HARIAN (SHIFT & AUDIT KAS) ---
   public function daily()
{
    $tgl = $this->request->getGet('tanggal') ?: date('Y-m-d');

    $builder = $this->db->table('shifts s');
    $builder->select('s.*, u.full_name as nama_kasir');
    $builder->join('users u', 'u.id = s.user_id', 'left');
    $builder->where('DATE(s.start_at)', $tgl);
    $builder->orderBy('s.id', 'DESC');
    
    $results = $builder->get()->getResultArray();

    // --- HITUNG TOTAL UNTUK WIDGET (PENTING!) ---
    $total_omzet   = 0;
    $total_biaya   = 0;
    $total_fisik   = 0;
    $total_selisih = 0;

    foreach ($results as $r) {
        $total_omzet   += $r['total_sales_cash'];
        $total_biaya   += $r['total_expenses'];
        $total_fisik   += $r['actual_cash'];
        $total_selisih += ($r['actual_cash'] - $r['expected_cash']);
    }

    $data = [
        'title'               => 'Laporan Operasional Harian',
        'results'             => $results,
        'filter_tgl'          => $tgl,
        
        // KIRIMKAN VARIABEL YANG TADI ERROR KE VIEW
        'total_omzet_harian'   => $total_omzet,
        'total_biaya_harian'   => $total_biaya,
        'total_fisik_harian'   => $total_fisik,
        'total_selisih_harian' => $total_selisih
    ];

    return view('laporan/harian_view', $data);
}
    // --- 2. LAPORAN BULANAN (LABA RUGI / P&L) ---
    public function monthly()
    {
        $bulan = $this->request->getGet('bulan') ?: date('m');
        $tahun = $this->request->getGet('tahun') ?: date('Y');

        $query = "
            SELECT 
                sales_harian.tanggal,
                sales_harian.omzet_bruto,
                sales_harian.total_diskon,
                hpp_harian.total_hpp,
                COALESCE(exp_harian.total_biaya, 0) as biaya_operasional,
                ((sales_harian.omzet_bruto - sales_harian.total_diskon) - hpp_harian.total_hpp - COALESCE(exp_harian.total_biaya, 0)) as laba_bersih
            FROM (
                SELECT DATE(created_at) as tanggal, SUM(total_price) as omzet_bruto, SUM(discount) as total_diskon
                FROM sales WHERE status = 'Lunas' GROUP BY DATE(created_at)
            ) sales_harian
            JOIN (
                SELECT DATE(s.created_at) as tanggal, SUM(sd.qty * sd.cost_price) as total_hpp
                FROM sales_details sd
                JOIN sales s ON s.id = sd.sale_id
                WHERE s.status = 'Lunas' GROUP BY DATE(s.created_at)
            ) hpp_harian ON sales_harian.tanggal = hpp_harian.tanggal
            LEFT JOIN (
                SELECT DATE(created_at) as tanggal, SUM(nominal) as total_biaya
                FROM pengeluaran_kas GROUP BY DATE(created_at)
            ) exp_harian ON sales_harian.tanggal = exp_harian.tanggal
            WHERE MONTH(sales_harian.tanggal) = ? AND YEAR(sales_harian.tanggal) = ?
            ORDER BY sales_harian.tanggal DESC
        ";

        $data = [
            'title' => 'Laporan Laba Rugi Bulanan',
            'results' => $this->db->query($query, [$bulan, $tahun])->getResultArray(),
            'filter_bulan' => $bulan,
            'filter_tahun' => $tahun
        ];

        return view('laporan/bulanan_view', $data);
    }

    public function stock()
{
    // Mengambil filter unit dari URL jika ada
    $filter_unit = $this->request->getGet('unit');

    $builder = $this->db->table('stok_unit su');
    $builder->select('su.*, mb.nama_barang, mb.harga_beli, bu.nama_unit, bu.jenis_unit, (su.jumlah * mb.harga_beli) as total_nilai_aset');
    $builder->join('master_barang mb', 'mb.kode_barang = su.kode_barang');
    $builder->join('bisnis_unit bu', 'bu.kode_unit = su.kode_unit');
    
    if ($filter_unit) {
        $builder->where('su.kode_unit', $filter_unit);
    }

    $builder->where('su.jumlah >', 0); // Hanya tampilkan yang ada stoknya
    $builder->orderBy('bu.nama_unit', 'ASC');
    $builder->orderBy('total_nilai_aset', 'DESC');

    $data = [
        'title' => 'Laporan Nilai Aset Stok',
        'results' => $builder->get()->getResultArray(),
        'units' => $this->db->table('bisnis_unit')->get()->getResultArray(),
        'filter_unit' => $filter_unit
    ];

    return view('laporan/stok_view', $data);
}
}