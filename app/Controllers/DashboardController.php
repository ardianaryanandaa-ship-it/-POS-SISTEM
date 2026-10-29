<?php
namespace App\Controllers;
use App\Models\PenjualanModel;
use App\Models\BisnisUnitModel;

class DashboardController extends BaseController {
    public function index()
    {
        helper('auth');
        $db = \Config\Database::connect();
        $buModel = new BisnisUnitModel();
        $today = date('Y-m-d');

        // --- 1. LOGIKA KEUANGAN KESELURUHAN (ALL TIME) ---
        
        // A. Hitung Omzet Bersih (Uang Masuk - Diskon)
        $sales_res = $db->table('sales')
            ->selectSum('total_price')
            ->selectSum('discount')
            ->where('status', 'Lunas')
            ->get()->getRow();
        
        $total_omzet_kotor = $sales_res->total_price ?? 0;
        $total_diskon      = $sales_res->discount ?? 0;
        $total_omzet_netto = $total_omzet_kotor - $total_diskon;

        // B. Hitung Total Modal (HPP) dari sales_details
        // --- 2. HITUNG TOTAL MODAL (HPP) ---
// Gunakan select() dengan parameter kedua FALSE agar CI4 tidak menambahkan backtick otomatis
$hpp_res = $db->table('sales_details sd')
    ->select('SUM(sd.qty * sd.cost_price) as total_hpp', false) 
    ->join('sales s', 's.id = sd.sale_id')
    ->where('s.status', 'Lunas')
    ->get()->getRow();

$total_hpp = $hpp_res->total_hpp ?? 0;

        // C. Hitung Total Biaya Operasional (Tabel pengeluaran_kas / galon, dll)
        $expense_res = $db->table('pengeluaran_kas')
            ->selectSum('nominal')
            ->get()->getRow();
        $total_expense = $expense_res->nominal ?? 0;

        // --- RUMUS KEUANGAN YANG BENAR ---
        $laba_kotor   = $total_omzet_netto - $total_hpp; 
        $laba_bersih  = $laba_kotor - $total_expense; // Laba bersih HARUS lebih kecil dari laba kotor

        // --- 2. DATA OPERASIONAL HARIAN ---
        $authData = get_auth_data();
        $my_bu    = $authData['bu'];

        // Omzet Hari Ini
        $omzet_today_res = $db->table('sales')
            ->selectSum('total_price')->selectSum('discount')
            ->where(['DATE(created_at)' => $today, 'status' => 'Lunas'])
            ->get()->getRow();
        $omzet_today = ($omzet_today_res->total_price ?? 0) - ($omzet_today_res->discount ?? 0);

        // Stok Kritis
        $stok_kritis = $db->table('stok_unit su')
            ->select('su.*, mb.nama_barang, bu.nama_unit')
            ->join('master_barang mb', 'mb.kode_barang = su.kode_barang')
            ->join('bisnis_unit bu', 'bu.kode_unit = su.kode_unit')
            ->where('su.jumlah <', 10)->limit(5)->get()->getResultArray();

        // Top 5 Produk
        $top_products = $db->table('sales_details sd')
            ->select('mb.nama_barang, SUM(sd.qty) as total_qty')
            ->join('master_barang mb', 'mb.kode_barang = sd.kode_barang')
            ->groupBy('sd.kode_barang')->orderBy('total_qty', 'DESC')
            ->limit(5)->get()->getResultArray();

        // Payment Stats
        $payment_stats = $db->table('sales')
            ->select('payment_method, COUNT(id) as total')
            ->where('DATE(created_at)', $today)->groupBy('payment_method')->get()->getResultArray();

        // Recent Sales
        $recent_sales = $db->table('sales')
            ->select('sales.*, users.full_name as nama_kasir')
            ->join('users', 'users.id = sales.user_id')
            ->orderBy('sales.created_at', 'DESC')->limit(5)->get()->getResultArray();

        $data = [
            'title'          => 'Dashboard Overview',
            'username'       => session()->get('username'),
            'total_trx'      => $db->table('sales')->where('DATE(created_at)', $today)->countAllResults(),
            'omzet_today'    => $omzet_today,
            'total_member'   => $db->table('pelanggan')->countAll(),
            'pending_opname' => $db->table('stock_opname')->where('status', 'Pending')->countAllResults(),
            'top_products'   => $top_products, 
            'stok_kritis'    => $stok_kritis,
            'payment_stats'  => $payment_stats,
            'recent_sales'   => $recent_sales,
            'bu_list'        => !empty($my_bu) ? $buModel->whereIn('kode_unit', $my_bu)->findAll() : [],
            
            // Variabel Keuangan Final
            'total_uang'     => $total_omzet_netto,
            'laba_kotor'     => $laba_kotor,
            'laba_bersih'    => $laba_bersih,
            'total_expense'  => $total_expense,
            'meta'           => $this->meta ?? [],
        ];

        return view('dashboard', $data);
    }
}