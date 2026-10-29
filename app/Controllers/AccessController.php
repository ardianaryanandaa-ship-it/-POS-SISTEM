<?php
namespace App\Controllers;

class AccessController extends BaseController
{
    public function index()
    {
        helper('auth');

        $db = \Config\Database::connect();
        $roles = $db->table('users_role')->get()->getResultArray();
        $existingPermissions = $db->table('role_permissions')->get()->getResultArray();
        
        $current = [];
        foreach ($existingPermissions as $p) {
            $current[] = $p['role_id'] . '_' . $p['menu_slug'];
        }

        $users = $db->table('users')->where('role_id !=', 3)->get()->getResultArray();
        $bisnisUnits = $db->table('bisnis_unit')->get()->getResultArray();
        
        $rawUserUnits = $db->table('user_units')->get()->getResultArray();
        $unitAksesMap = [];
        foreach ($rawUserUnits as $uu) {
            $unitAksesMap[$uu['user_id']][] = $uu['kode_unit'];
        }

        $data = [
            'title' => 'Manajemen Hak Akses URL',
            'current_permissions' => $current,
            'roles' => $roles,
            'all_users' => $users,
            'all_bisnis_unit' => $bisnisUnits,
            'unit_akses_map' => $unitAksesMap,
            'rules' => [
                ['menu' => 'Dashboard', 'slug' => 'VIEW_DASHBOARD', 'url' => '/dashboard'],
                ['menu' => '-- GRUP OPERASIONAL --', 'slug' => '', 'url' => '#'],
                ['menu' => 'Tampil Data Barang', 'slug' => 'VIEW_BARANG', 'url' => '/barang'],
                ['menu' => 'Tambah Barang', 'slug' => 'CREATE_BARANG', 'url' => '/barang/simpan'],
                ['menu' => 'Ubah Barang', 'slug' => 'UPDATE_BARANG', 'url' => '/barang/ubah'],
                ['menu' => 'Hapus Barang', 'slug' => 'DELETE_BARANG', 'url' => '/barang/hapus'],
                ['menu' => 'Tampil Data Penjualan', 'slug' => 'VIEW_PENJUALAN', 'url' => '/penjualan'],
                ['menu' => 'Tambah Penjualan', 'slug' => 'CREATE_PENJUALAN', 'url' => '/penjualan/simpandata'],
                ['menu' => 'Ubah Penjualan', 'slug' => 'UPDATE_PENJUALAN', 'url' => '/penjualan/updatedata'],
                ['menu' => 'Hapus Penjualan', 'slug' => 'DELETE_PENJUALAN', 'url' => '/penjualan/delete'],
                ['menu' => 'Hapus Banyak Penjualan', 'slug' => 'DELETE_PENJUALAN', 'url' => '/penjualan/delete_batch'],
                ['menu' => 'Stok Keluar', 'slug' => 'VIEW_STOK_KELUAR', 'url' => '/stok-keluar'],
                ['menu' => 'Stok Adjustment', 'slug' => 'VIEW_STOK_ADJUSTMENT', 'url' => '/stokadjustment'],
                ['menu' => 'Tambah Stok Adjustment', 'slug' => 'CREATE_STOK_ADJUSTMENT', 'url' => '/stokadjustment/save'],
                ['menu' => 'Apply Stok Adjustment', 'slug' => 'UPDATE_STOK_ADJUSTMENT', 'url' => '/stokadjustment/apply'],
                ['menu' => '-- GRUP LOGISTIK --', 'slug' => '', 'url' => '#'],
                ['menu' => 'Tampil Data Bisnis Unit', 'slug' => 'VIEW_BISNIS_UNIT', 'url' => '/bisnisunit'],
                ['menu' => 'Tambah Bisnis Unit', 'slug' => 'CREATE_BISNIS_UNIT', 'url' => '/bisnisunit/save'],
                ['menu' => 'Ubah Bisnis Unit', 'slug' => 'UPDATE_BISNIS_UNIT', 'url' => '/bisnisunit/update'],
                ['menu' => 'Hapus Bisnis Unit', 'slug' => 'DELETE_BISNIS_UNIT', 'url' => '/bisnisunit/delete'],
                ['menu' => 'Tampil Data Item Kategori', 'slug' => 'VIEW_KATEGORI', 'url' => '/kategori'],
                ['menu' => 'Tambah Item Kategori', 'slug' => 'CREATE_KATEGORI', 'url' => '/kategori/save'],
                ['menu' => 'Ubah Item Kategori', 'slug' => 'UPDATE_KATEGORI', 'url' => '/kategori/update'],
                ['menu' => 'Hapus Item Kategori', 'slug' => 'DELETE_KATEGORI', 'url' => '/kategori/delete'],
                ['menu' => 'Tampil Data Master Barang', 'slug' => 'VIEW_MASTER_BARANG', 'url' => '/master-barang'],
                ['menu' => 'Tambah Master Barang', 'slug' => 'CREATE_MASTER_BARANG', 'url' => '/master-barang/save'],
                ['menu' => 'Ubah Master Barang', 'slug' => 'UPDATE_MASTER_BARANG', 'url' => '/master-barang/update'],
                ['menu' => 'Hapus Master Barang', 'slug' => 'DELETE_MASTER_BARANG', 'url' => '/master-barang/delete'],
                ['menu' => 'Tampil Data Barang Masuk', 'slug' => 'VIEW_BARANG_MASUK', 'url' => '/barangmasuk'],
                ['menu' => 'Tambah Barang Masuk', 'slug' => 'CREATE_BARANG_MASUK', 'url' => '/barangmasuk/save'],
                ['menu' => 'Verifikasi Barang Masuk', 'slug' => 'UPDATE_BARANG_MASUK', 'url' => '/barangmasuk/verify'],
                ['menu' => 'Rollback Barang Masuk', 'slug' => 'DELETE_BARANG_MASUK', 'url' => '/barangmasuk/rollback'],
                ['menu' => 'Tambah Item Barang Masuk', 'slug' => 'CREATE_BARANG_MASUK', 'url' => '/barangmasuk/add_item_to_nota'],
                ['menu' => 'Ubah Item Barang Masuk', 'slug' => 'UPDATE_BARANG_MASUK', 'url' => '/barangmasuk/update_item'],
                ['menu' => 'Hapus Item Barang Masuk', 'slug' => 'DELETE_BARANG_MASUK', 'url' => '/barangmasuk/delete_item'],
                ['menu' => 'Tampil Data Stok Transfer', 'slug' => 'VIEW_STOK_TRANSFER', 'url' => '/stoktransfer'],
                ['menu' => 'Tambah Stok Transfer', 'slug' => 'CREATE_STOK_TRANSFER', 'url' => '/stoktransfer/save'],
                ['menu' => 'Receive Stok Transfer', 'slug' => 'UPDATE_STOK_TRANSFER', 'url' => '/stoktransfer/receive'],
                ['menu' => 'Tampil Data Retur Barang', 'slug' => 'VIEW_RETUR', 'url' => '/retur'],
                ['menu' => 'Tambah Retur Barang', 'slug' => 'CREATE_RETUR', 'url' => '/retur/save'],
                ['menu' => 'Verifikasi Retur Barang', 'slug' => 'UPDATE_RETUR', 'url' => '/retur/verify'],
                ['menu' => 'Inventory Map', 'slug' => 'VIEW_INVENTORY', 'url' => '/inventory'],
                ['menu' => '-- GRUP PENGATURAN --', 'slug' => '', 'url' => '#'],
                ['menu' => 'User Management', 'slug' => 'VIEW_USERS', 'url' => '/users'],
                ['menu' => 'Setting App (Meta)', 'slug' => 'VIEW_META', 'url' => '/meta'],
                ['menu' => 'Contact Info', 'slug' => 'VIEW_CONTACT', 'url' => '/contact'],
                ['menu' => 'Hak Akses', 'slug' => 'VIEW_HAK_AKSES', 'url' => '/hak-akses'],
            ]
        ];

        return view('admin/access_view', $data);
    }

    public function update()
    {
        helper('auth'); 
        $db = \Config\Database::connect();
        $permissions = $this->request->getPost('permissions');

        $db->table('role_permissions')->where('role_id !=', 3)->delete();

        if (!empty($permissions)) {
            $insertData = [];
            foreach ($permissions as $p) {
                if (strpos($p, '|') !== false) {
                    list($role_id, $slug) = explode('|', $p);
                    $insertData[] = [
                        'role_id'   => $role_id,
                        'menu_slug' => strtoupper(trim($slug)) 
                    ];
                }
            }
            if (!empty($insertData)) {
                $db->table('role_permissions')->insertBatch($insertData);
            }
        }

        $this->autoRefreshMySession();
        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Hak akses berhasil diperbarui!'
            ]);
        }

        return redirect()->back()->with('success', 'Hak akses berhasil diperbarui!');
    }

// ... di dalam AccessController.php ...

public function update_unit()
{
    helper('auth');
    $db = \Config\Database::connect();

    $user_id = $this->request->getPost('user_id');
    $units = $this->request->getPost('units');

    if (empty($user_id)) {
        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Silahkan pilih user terlebih dahulu!']);
        }
        return redirect()->back()->with('error', 'Silahkan pilih user terlebih dahulu!');
    }

    if (!empty($units)) {
        foreach ($units as $kode) {
            $exists = $db->table('user_units')
                         ->where('user_id', $user_id)
                         ->where('kode_unit', $kode)
                         ->countAllResults();

            if ($exists == 0) {
                $db->table('user_units')->insert([
                    'user_id'   => $user_id,
                    'kode_unit' => $kode 
                ]);
            }
        }
    }

    $this->autoRefreshMySession();

    if ($this->request->isAJAX()) {
        return $this->response->setJSON(['success' => true, 'message' => 'Akses unit berhasil ditambahkan!']);
    }

    return redirect()->back()->with('success', 'Akses unit berhasil ditambahkan!');
}

public function delete_unit_user()
{
    $db = \Config\Database::connect();
    $user_id = $this->request->getPost('user_id');

    if ($user_id) {
        $db->table('user_units')->where('user_id', $user_id)->delete();
        $this->autoRefreshMySession();

        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['success' => true, 'message' => 'Semua akses unit user berhasil dihapus!']);
        }
        return redirect()->back()->with('success', 'Semua akses unit user berhasil dihapus!');
    }

    if ($this->request->isAJAX()) {
        return $this->response->setJSON(['success' => false, 'message' => 'Gagal menghapus data.']);
    }
    return redirect()->back()->with('error', 'Gagal menghapus data.');
}
    private function autoRefreshMySession()
    {
        helper('auth'); // Memastikan helper dimuat
        $db = \Config\Database::connect();
        $my_role = session()->get('role_id');
        $my_id   = session()->get('id');

        if (!$my_id) return;

        // Ambil data terbaru
        $new_perms = $db->table('role_permissions')->where('role_id', $my_role)->get()->getResultArray();
        $permission_list = array_column($new_perms, 'menu_slug');
        
        // GUNAKAN BACKSLASH (\) DI DEPAN NAMA FUNGSI
        session()->set('permissions', \encrypt_array_values($permission_list));

        $userUnits = $db->table('user_units')->where('user_id', $my_id)->get()->getResultArray();
        $bu_raw_list = array_column($userUnits, 'kode_unit');

        if (empty($bu_raw_list)) {
            $userData = $db->table('users')->where('id', $my_id)->get()->getRowArray();
            if ($userData && $userData['kode_unit']) {
                $bu_raw_list = [$userData['kode_unit']];
            }
        }

        // GUNAKAN BACKSLASH (\) DI DEPAN NAMA FUNGSI
        session()->set('bu', \encrypt_array_values($bu_raw_list));
    }
}