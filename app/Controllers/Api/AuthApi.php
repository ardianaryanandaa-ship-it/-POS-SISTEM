<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;

class AuthApi extends ResourceController
{
    use ResponseTrait;

    public function login()
    {
        helper(['auth']); // Memanggil helper Anda

        // 1. Ambil input JSON
        $username = $this->request->getVar('username');
        $password = $this->request->getVar('password');

        // 2. Login Attempt (Menggunakan fungsi yang sudah Anda punya)
        $result = attempt_login($username, $password);

        if ($result['status'] === 'success') {
            $user = $result['data'];
            $db = \Config\Database::connect();
            
            // --- 3. AMBIL PERMISSIONS ---
            $permissions = $db->table('role_permissions')
                            ->select('menu_slug')
                            ->where('role_id', $user['role_id'])
                            ->distinct()
                            ->get()->getResultArray();
            $permission_list = array_column($permissions, 'menu_slug'); 

            // --- 4. AMBIL BISNIS UNIT (BU) ---
            if ($user['role_id'] == 3) {
                $allUnits = $db->table('bisnis_unit')->get()->getResultArray();
                $bu_raw = array_column($allUnits, 'kode_unit');
            } else {
                $userUnits = $db->table('user_units')
                                ->where('user_id', $user['id'])
                                ->get()->getResultArray();
                $bu_raw = array_column($userUnits, 'kode_unit');

                if (empty($bu_raw)) {
                    $bu_raw = [$user['kode_unit']];
                }
            }

            $bu_processed = array_unique(array_map('trim', $bu_raw));

            // --- 5. RESPONSE JSON (Tanpa Session & Tanpa Redirect) ---
            return $this->respond([
                'status'  => 'success',
                'message' => 'Login Berhasil',
                'data'    => [
                    'id'          => $user['id'],
                    'username'    => $user['username'],
                    'full_name'   => $user['full_name'],
                    'role_id'     => $user['role_id'],
                    'permissions' => $permission_list, // Di API jangan di-encrypt agar mobile bisa baca
                    'bu'          => $bu_processed,
                    'token'       => base64_encode("USER_".$user['id']) // Contoh token sederhana
                ]
            ], 200);
        }

        // Jika Gagal
        return $this->respond([
            'status'  => 'error',
            'message' => $result['pesan']
        ], 401);
    }
}