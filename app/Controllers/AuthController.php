<?php

namespace App\Controllers;

class AuthController extends BaseController
{
    public function index()
    {
        if (is_logged_in()) {
            return redirect()->to('/dashboard');
        }
        return view('auth/mitro_login'); 
    }

    public function login()
    {
        // 1. Panggil helper agar fungsi enkripsi tersedia
        helper('auth'); 

        $userCaptcha    = $this->request->getPost('captcha');
    $sessionCaptcha = session()->get('captcha_code');

    if (!$userCaptcha || $userCaptcha !== $sessionCaptcha) {
        // Hapus session captcha segera setelah gagal agar tidak bisa ditebak ulang
        session()->remove('captcha_code'); 
        return redirect()->back()->withInput()->with('errors', [
            'captcha' => 'Kode Captcha salah atau kadaluarsa!'
        ]);
    }

        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');

        $result = attempt_login($username, $password);

        if ($result['status'] === 'success') {
            $user = $result['data'];
            $db = \Config\Database::connect();
            
            // --- 2. AMBIL PERMISSIONS ---
            $permissions = $db->table('role_permissions')
                            ->select('menu_slug')
                            ->where('role_id', $user['role_id'])
                            ->distinct()
                            ->get()->getResultArray();
            $permission_list = array_column($permissions, 'menu_slug'); 

            // --- 3. AMBIL BISNIS UNIT (BU) ---
            if ($user['role_id'] == 3) {
                // JIKA DEVELOPER: Ambil SEMUA unit yang terdaftar di sistem
                $allUnits = $db->table('bisnis_unit')->get()->getResultArray();
                $bu_raw = array_column($allUnits, 'kode_unit');
            } else {
                // JIKA USER BIASA: Ambil dari tabel user_units berdasarkan user_id
                $userUnits = $db->table('user_units')
                                ->where('user_id', $user['id'])
                                ->get()->getResultArray();
                
                $bu_raw = array_column($userUnits, 'kode_unit');

                // Jika tidak punya akses khusus di user_units, ambil kode_unit default dari profil
                if (empty($bu_raw)) {
                    $bu_raw = [$user['kode_unit']];
                }
            }

            // Pastikan data BU bersih & unik (menghindari duplikat)
            $bu_processed = array_unique(array_map(function($val) {
                return trim((string)$val);
            }, $bu_raw));

            // --- 4. SET SESSION DENGAN ENKRIPSI ---
            // Gunakan \ didepan fungsi untuk menghindari error namespace
            session()->set([
                'id'          => $user['id'],
                'username'    => $user['username'],
                'full_name'   => $user['full_name'],
                'email'       => $user['email'], 
                'avatar'      => $user['avatar'], 
                'role_id'     => $user['role_id'],
                
                // Menggunakan fungsi dari helper yang sudah kita buat sebelumnya
                'permissions' => \encrypt_array_values($permission_list),
                'bu'          => \encrypt_array_values($bu_processed), 
                
                'logged_in'   => true               
            ]);

            return redirect()->to('/dashboard'); 
        }

        // Jika login gagal
        return redirect()->back()->withInput()->with('errors', [
            $result['field'] => $result['pesan']
        ]);
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login');
    }
}