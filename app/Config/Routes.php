<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->get('captcha', 'CaptchaController::index');
$routes->get('captcha/generate', 'CaptchaController::generate');
$routes->post('captcha/verify', 'CaptchaController::verify');

// --- 1. ROUTES PUBLIC (Tanpa Login) ---
$routes->get('/', 'AuthController::index');
$routes->get('login', 'AuthController::index');
$routes->post('auth/login', 'AuthController::login');
$routes->get('logout', 'AuthController::logout');

// --- 2. ROUTES PRIVATE (Wajib Login) ---
$routes->group('', ['filter' => 'auth'], function($routes) {

    // Akses Dashboard & Profile (Semua Role: 1, 2, 3)
    $routes->get('dashboard', 'DashboardController::index');

    $routes->get('hak-akses', 'AccessController::index'); 
        $routes->post('hak-akses/update', 'AccessController::update');
        $routes->post('hak-akses/update-unit', 'AccessController::update_unit');
        $routes->post('hak-akses/delete-unit-user', 'AccessController::delete_unit_user');

        // Manajemen User
        $routes->group('users', function($routes) {
            $routes->get('/', 'UserController::index');
            $routes->post('/', 'UserController::store'); // FIX: Menangani POST ke /users
            $routes->get('get_json/(:num)', 'UserController::get_json/$1');
            $routes->post('store', 'UserController::store');
            $routes->post('update/(:num)', 'UserController::update/$1');
        });

        // Modul Meta & Contact
        $routes->group('meta', function($routes) {
            $routes->get('/', 'MetaController::index');
            $routes->post('update', 'MetaController::update');
        });
        $routes->group('contact', function($routes) {
            $routes->get('/', 'ContactController::index');
            $routes->post('update', 'ContactController::update');
        });

        // --- MODUL KASIR (POS) ---
    $routes->group('kasir', ['filter' => 'permission:VIEW_POS'], function($routes) {
        $routes->get('/', 'Kasir::index'); 
        $routes->get('set_unit/(:any)', 'Kasir::set_unit/$1'); // Pilih unit
        $routes->get('ganti_unit', 'Kasir::ganti_unit'); // Ganti unit
        $routes->get('search', 'Kasir::search'); 
        $routes->post('checkout', 'Kasir::checkout'); 
        $routes->get('cetak/(:num)', 'Kasir::print_receipt/$1');
        $routes->post('save_pelanggan_ajax', 'Kasir::save_pelanggan_ajax');
        $routes->get('cek_diskon', 'Kasir::cek_diskon');
        // Tambahkan ini agar URL /kasir/upload_gambar_barang bisa diakses
        $routes->post('upload_gambar_barang', 'Kasir::upload_gambar_barang');
    });

        // Gunakan group agar lebih rapi
        

        // Halaman Manajemen Shift sendiri JANGAN diberi filter 'shift' 
        // agar kasir bisa akses menu tersebut untuk Buka Shift.
        $routes->group('shift', function($routes) {
            $routes->get('/', 'ShiftController::index');
            $routes->post('open', 'ShiftController::open');
            $routes->post('close', 'ShiftController::close');
            $routes->post('addExpense', 'ShiftController::addExpense');

            $routes->get('history', 'ShiftController::history'); // Daftar semua shift lampau
            $routes->get('summary/(:num)', 'ShiftController::summary/$1'); // Detail rekap per ID
        });

        $routes->group('penjualan', function($routes) {
            $routes->get('/', 'Penjualan::index');             // Tampilan halaman utama
            $routes->get('list', 'Penjualan::list');           // Data untuk tabel (AJAX)
            $routes->get('get_detail_with_sales/(:num)', 'Penjualan::get_detail_with_sales/$1'); // Isi detail nota
            $routes->post('void/(:num)', 'Penjualan::void/$1'); // Batalkan transaksi
        });

        // Route untuk Fitur Stock Opname
$routes->group('stockopname', function($routes) {
    $routes->get('/', 'StockOpname::index');
    $routes->post('save', 'StockOpname::save');
    $routes->post('update', 'StockOpname::update'); // Untuk simpan editan
    $routes->get('delete/(:num)', 'StockOpname::delete/$1'); // Untuk hapus
    $routes->get('get_stok_sistem/(:any)/(:any)', 'StockOpname::get_stok_sistem/$1/$2');
    $routes->get('get_detail/(:num)', 'StockOpname::get_detail/$1'); // Untuk ambil data via AJAX
});

        // Route untuk Fitur Stok Adjustment
        $routes->group('stockopname', function($routes) {
    // ... rute sebelumnya ...
    $routes->get('approve/(:num)', 'StockOpname::approve/$1');
    $routes->get('reject/(:num)', 'StockOpname::reject/$1');
});

// --- MODUL MARKETING & PROMO ---
$routes->group('promo', ['filter' => 'auth'], function($routes) {
    // Tampilan Utama (List Promo)
    $routes->get('/', 'Promo::index');
    
    // Proses Simpan Promo Baru
    $routes->post('save', 'Promo::save');
    
    // Hapus Promo
    $routes->get('delete/(:num)', 'Promo::delete/$1');
    
    // Opsional: Ubah Status Aktif/Nonaktif secara cepat
    $routes->get('toggle/(:num)', 'Promo::toggle/$1');
});

$routes->group('laporan', ['filter' => 'auth'], function($routes) {
    $routes->get('harian', 'ReportController::daily');
    $routes->get('bulanan', 'ReportController::monthly');
    $routes->get('stok', 'ReportController::stock');
});
    
    $routes->group('profile', function($routes) {
        $routes->get('/', 'ProfileController::index');
        $routes->post('update', 'ProfileController::update');
        $routes->post('change-password', 'ProfileController::changePassword');
    });

    // --- 3. MODUL PENJUALAN ---
    

    // --- 4. MODUL ADMIN & OPERASIONAL ---
    $routes->group('', function($routes) {
        
        // Modul Barang
        $routes->group('barang', ['filter' => 'permission:VIEW_BARANG'], function($routes) {
            $routes->get('/', 'Barang::index');
            $routes->post('simpan', 'Barang::simpan', ['filter' => 'permission:CREATE_BARANG']);
            $routes->post('ubah/(:num)', 'Barang::ubah/$1', ['filter' => 'permission:UPDATE_BARANG']);
            $routes->get('hapus/(:num)', 'Barang::hapus/$1', ['filter' => 'permission:DELETE_BARANG']);
        });

        // Modul Stok Keluar
        $routes->group('stok-keluar', ['filter' => 'permission:VIEW_STOK_KELUAR'], function($routes) {
            $routes->get('/', 'StokKeluar::index');
            $routes->get('get_detail/(:num)', 'StokKeluar::get_detail/$1');
        });

        // Modul Bisnis Unit
        $routes->group('bisnisunit', ['filter' => 'permission:VIEW_BISNIS_UNIT'], function($routes) {
            $routes->get('/', 'BisnisUnit::index');
            $routes->get('list', 'BisnisUnit::list');            
            $routes->get('get/(:any)', 'BisnisUnit::get/$1');    
            $routes->post('save', 'BisnisUnit::save', ['filter' => 'permission:CREATE_BISNIS_UNIT']);
            $routes->post('update', 'BisnisUnit::update', ['filter' => 'permission:UPDATE_BISNIS_UNIT']);       
            $routes->get('delete/(:any)', 'BisnisUnit::delete/$1', ['filter' => 'permission:DELETE_BISNIS_UNIT']); 
        });     

        // Modul Kategori
        $routes->group('kategori', ['filter' => 'permission:VIEW_KATEGORI'], function($routes) {
            $routes->get('/', 'ItemKategori::index');
            $routes->get('list', 'ItemKategori::list');
            $routes->get('get/(:any)', 'ItemKategori::get/$1');
            $routes->post('save', 'ItemKategori::save', ['filter' => 'permission:CREATE_KATEGORI']);
            $routes->post('update/(:any)', 'ItemKategori::update/$1', ['filter' => 'permission:UPDATE_KATEGORI']); 
            $routes->get('delete/(:any)', 'ItemKategori::delete/$1', ['filter' => 'permission:DELETE_KATEGORI']);
        });

        // Modul Master Barang
        $routes->group('master-barang', ['filter' => 'permission:VIEW_MASTER_BARANG'], function($routes) {
            $routes->get('/', 'MasterBarang::index');
            $routes->get('list', 'MasterBarang::list');
            $routes->get('get/(:any)', 'MasterBarang::get/$1');
            $routes->post('save', 'MasterBarang::save', ['filter' => 'permission:CREATE_MASTER_BARANG']);
            $routes->post('update/(:any)', 'MasterBarang::update/$1', ['filter' => 'permission:UPDATE_MASTER_BARANG']);
            $routes->get('delete/(:any)', 'MasterBarang::delete/$1', ['filter' => 'permission:DELETE_MASTER_BARANG']);
        });

        // Modul Barang Masuk
        $routes->group('barangmasuk', ['filter' => 'permission:VIEW_BARANG_MASUK'], function($routes) {
            $routes->get('/', 'BarangMasuk::index');
            $routes->post('save', 'BarangMasuk::save', ['filter' => 'permission:CREATE_BARANG_MASUK']); 
            $routes->post('verify/(:num)', 'BarangMasuk::verify/$1', ['filter' => 'permission:UPDATE_BARANG_MASUK']); 
            $routes->get('get_detail/(:num)', 'BarangMasuk::get_detail/$1');
            $routes->post('rollback/(:num)', 'BarangMasuk::rollback/$1', ['filter' => 'permission:DELETE_BARANG_MASUK']);
            $routes->post('update_item', 'BarangMasuk::update_item', ['filter' => 'permission:UPDATE_BARANG_MASUK']);
            $routes->post('add_item_to_nota', 'BarangMasuk::add_item_to_nota', ['filter' => 'permission:CREATE_BARANG_MASUK']);
            $routes->post('delete_item/(:num)/(:num)', 'BarangMasuk::delete_item/$1/$2', ['filter' => 'permission:DELETE_BARANG_MASUK']);
            $routes->get('cetak_nota/(:num)', 'BarangMasuk::cetak_nota/$1');
        });

        // Modul Stok Adjustment
        $routes->group('stokadjustment', ['filter' => 'permission:VIEW_STOK_ADJUSTMENT'], function($routes) {
            $routes->get('/', 'StokAdjustment::index');
            $routes->post('save', 'StokAdjustment::save', ['filter' => 'permission:CREATE_STOK_ADJUSTMENT']);
            $routes->get('apply/(:num)', 'StokAdjustment::apply/$1', ['filter' => 'permission:UPDATE_STOK_ADJUSTMENT']);
            $routes->get('get_detail/(:num)', 'StokAdjustment::get_detail/$1');
        });

        // Modul Stok Transfer
        $routes->group('stoktransfer', ['filter' => 'permission:VIEW_STOK_TRANSFER'], function($routes) {
            $routes->get('/', 'StokTransfer::index');
            $routes->post('save', 'StokTransfer::save', ['filter' => 'permission:CREATE_STOK_TRANSFER']);
            $routes->get('receive/(:num)', 'StokTransfer::receive/$1', ['filter' => 'permission:UPDATE_STOK_TRANSFER']);
            $routes->get('get_detail/(:num)', 'StokTransfer::get_detail/$1');
            $routes->get('cetak_sit/(:num)', 'StokTransfer::cetak_sit/$1');
            $routes->get('get_stok_per_unit/(:any)', 'StokTransfer::get_stok_per_unit/$1');
        });

        // Modul Retur
        $routes->group('retur', ['filter' => 'permission:VIEW_RETUR'], function($routes) {
            $routes->get('/', 'ReturBarang::index');
            $routes->post('save', 'ReturBarang::save', ['filter' => 'permission:CREATE_RETUR']);
            $routes->get('verify/(:num)', 'ReturBarang::verify/$1', ['filter' => 'permission:UPDATE_RETUR']);
            $routes->get('get_detail/(:num)', 'ReturBarang::get_detail/$1');
        });
    });

    $routes->group('pelanggan', function($routes) {
    $routes->get('/', 'Pelanggan::index');
    $routes->get('detail/(:num)', 'Pelanggan::detail/$1');
    $routes->post('save', 'Pelanggan::save');
});

    // --- 5. AKSES UMUM ---
    $routes->get('inventory', 'Inventory::index', ['filter' => 'permission:VIEW_INVENTORY']);

    // --- 6. SUPER RESTRICTED (Hanya Developer Role 3) ---
    $routes->group('', ['filter' => 'role:3'], function($routes) {
        
        // Manajemen Hak Akses (Menu & Unit)
        
    });
});

// Route untuk Mobile App
$routes->group('api', function($routes) {
    $routes->post('login', '\App\Controllers\Api\AuthApi::login');
    
    // Nanti tambahkan route lain di sini
    // $routes->get('barang', '\App\Controllers\Api\BarangApi::index');
});

$routes->group('api', function($routes) {
    // Auth
    $routes->post('login', 'Api\AuthApi::login');

    // Barang Masuk API
    $routes->get('barang-masuk', 'Api\BarangMasukApi::index');
    $routes->get('barang-masuk/(:num)', 'Api\BarangMasukApi::show/$1');
    $routes->post('barang-masuk', 'Api\BarangMasukApi::create');
    $routes->post('barang-masuk/verify/(:num)', 'Api\BarangMasukApi::verify/$1');
});