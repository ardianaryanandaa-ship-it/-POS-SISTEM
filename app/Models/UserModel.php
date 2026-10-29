<?php

namespace App\Models;

use CodeIgniter\Model;
use Config\Services;

class UserModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    // Pastikan 'permissions' dan 'bu' ada di sini
    protected $allowedFields = ['username', 'email', 'password', 'role_id', 'full_name', 'status', 'avatar', 'permissions', 'bu'];

    // OTOMATISASI:
    protected $beforeInsert = ['encodeData'];
    protected $beforeUpdate = ['encodeData'];
    protected $afterFind    = ['decodeData'];

    // Fungsi untuk Enkripsi sebelum masuk ke Database
    protected function encodeData(array $data)
    {
        $encrypter = Services::encrypter();
        $fields = ['permissions', 'bu']; // Kolom yang berisi array

        foreach ($fields as $field) {
            if (isset($data['data'][$field]) && is_array($data['data'][$field])) {
                $json = json_encode($data['data'][$field]);
                $data['data'][$field] = base64_encode($encrypter->encrypt($json));
            }
        }
        return $data;
    }

    // Fungsi untuk Deskripsi setelah diambil dari Database
    protected function decodeData(array $data)
    {
        $encrypter = Services::encrypter();
        $fields = ['permissions', 'bu'];

        // Jika data tunggal (first())
        if (isset($data['data']['id'])) {
            foreach ($fields as $field) {
                if (!empty($data['data'][$field]) && is_string($data['data'][$field])) {
                    try {
                        $decrypted = $encrypter->decrypt(base64_decode($data['data'][$field]));
                        $data['data'][$field] = json_decode($decrypted, true);
                    } catch (\Exception $e) {
                        $data['data'][$field] = [];
                    }
                }
            }
        } 
        return $data;
    }
}