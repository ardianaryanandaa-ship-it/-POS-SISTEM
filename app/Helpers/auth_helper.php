<?php

/**
 * MENGKRIPSI ISI ARRAY / NILAI SATU PER SATU
 * Termasuk angka pada array dan nested array.
 */
if (!function_exists('encrypt_array_values')) {
    function encrypt_array_values($value) {
        $encrypter = \Config\Services::encrypter();

        if (is_array($value)) {
            $result = [];
            foreach ($value as $item) {
                $result[bin2hex(random_bytes(6))] = encrypt_array_values($item);
            }
            return $result;
        }

        return base64_encode($encrypter->encrypt((string)$value));
    }
}

if (!function_exists('canAny')) {
    function canAny(array $menu_slugs) {
        foreach ($menu_slugs as $s) {
            if (can($s)) return true;
        }
        return false;
    }
}

/**
 * DEKRIPSI NILAI / ARRAY
 */
if (!function_exists('decrypt_value')) {
    function decrypt_value($value) {
        if (is_array($value)) {
            return array_map('decrypt_value', $value);
        }

        if (empty($value) || !is_string($value)) return $value;
        try {
            $encrypter = \Config\Services::encrypter();
            return $encrypter->decrypt(base64_decode($value));
        } catch (\Exception $e) {
            return $value;
        }
    }
}

/**
 * CEK IZIN AKSES (Looping Array Session & Decrypt)
 */
if (!function_exists('can')) {
    function can($menu_slug) {
        if (session()->get('role_id') == 3) return true; // Dev bypass

        $permissions = session()->get('permissions'); 
        if (!is_array($permissions)) return false;

        $menu_slug = strtoupper(trim($menu_slug));
        
        foreach ($permissions as $p) {
            if (strtoupper(decrypt_value($p)) === $menu_slug) {
                return true;
            }
        }
        return false;
    }
}

/**
 * AMBIL KODE BU PERTAMA (Didekripsi)
 */
if (!function_exists('user_bu')) {
    function user_bu() {
        $bu = session()->get('bu');
        if (is_array($bu)) {
            $first = reset($bu);
            return $first !== false ? decrypt_value($first) : null;
        }
        return null;
    }
}

/**
 * AMBIL SEMUA DATA AUTH (Didekripsi semua)
 */
if (!function_exists('get_auth_data')) {
    function get_auth_data() {
        $permissions = (array)session()->get('permissions');
        $bu          = (array)session()->get('bu');

        return [
            'permissions' => array_map(fn($p) => decrypt_value($p), $permissions),
            'bu'          => array_map(fn($b) => decrypt_value($b), $bu)
        ];
    }
}

/**
 * LOGIKA LOGIN
 */
if (!function_exists('attempt_login')) {
    function attempt_login($username, $password) {
        $model = new \App\Models\UserModel();
        $user = $model->select('users.*, users_role.role_name')
                    ->join('users_role', 'users_role.id = users.role_id')
                    ->where('username', $username)
                    ->orWhere('email', $username)
                    ->first();

        if (!$user) return ['status' => 'error', 'field' => 'username', 'pesan' => 'User tidak ditemukan!'];
        if (!password_verify($password, $user['password'])) return ['status' => 'error', 'field' => 'password', 'pesan' => 'Password salah!'];
        if ($user['status'] !== 'Active') return ['status' => 'error', 'field' => 'username', 'pesan' => 'Akun tidak aktif!'];

        return ['status' => 'success', 'data' => $user];
    }
}

if (!function_exists('is_logged_in')) {
    function is_logged_in() { return session()->get('logged_in') === true; }
}

if (!function_exists('user')) {
    function user($field = null) { return $field ? session()->get($field) : session()->get(); }
}

if (!function_exists('is_admin')) { function is_admin() { return session()->get('role_id') == 1; } }
if (!function_exists('is_user')) { function is_user() { return session()->get('role_id') == 2; } }
if (!function_exists('is_developer')) { function is_developer() { return session()->get('role_id') == 3; } }