<?php

namespace App\Controllers;

use App\Models\UserModel;

class ProfileController extends BaseController
{
    public function index()
    {
        // Mencoba mengambil 'id' atau fallback ke 'user_id' jika kunci berbeda
        $id = session()->get('id') ?: session()->get('user_id');
        
        if (!$id) {
            return redirect()->to('/login');
        }

        $model = new UserModel();
        // MENGGUNAKAN 'left' join agar data tetap muncul meskipun role_id bermasalah
        $userData = $model->select('users.*, users_role.role_name')
                          ->join('users_role', 'users_role.id = users.role_id', 'left')
                          ->find($id);

        // Jika user tetap tidak ditemukan di database
        if (!$userData) {
            return redirect()->to('/login')->with('error', 'User tidak ditemukan di database.');
        }

        // --- SYNC SESSION OTOMATIS ---
        session()->set([
            'id'        => $userData['id'],
            'full_name' => $userData['full_name'] ?? '',
            'email'     => $userData['email'] ?? '',
            'avatar'    => $userData['avatar'] ?? 'default.jpg'
        ]);

        $data = [
            'title' => 'User Profile',
            'user'  => $userData
        ];
        return view('profile/index', $data);
    }

    public function update()
    {
        $rules = [
            'full_name' => 'required|min_length[3]',
            'email'     => 'required|valid_email'
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'status' => 'error', 
                'pesan'  => implode("<br>", $this->validator->getErrors()), 
                'token'  => csrf_hash()
            ]);
        }

        $model = new UserModel();
        $id = session()->get('id') ?: session()->get('user_id');
        
        $full_name = $this->request->getPost('full_name');
        $email = $this->request->getPost('email');

        $data = [
            'full_name' => $full_name,
            'email'     => $email,
        ];

        $file = $this->request->getFile('avatar');
        $currentAvatar = session()->get('avatar'); 

        if ($file && $file->isValid() && !$file->hasMoved()) {
            $newName = $file->getRandomName();
            $file->move('uploads/avatars', $newName);
            $data['avatar'] = $newName;
            $currentAvatar = $newName; 
            session()->set('avatar', $newName); 
        }

        $model->update($id, $data);
        session()->set('full_name', $full_name); 
        session()->set('email', $email); 

        return $this->response->setJSON([
            'status' => 'success', 
            'pesan'  => 'Update profil berhasil', 
            'data'   => [
                'full_name'  => $full_name,
                'email'      => $email,
                'avatar_url' => base_url('uploads/avatars/' . ($currentAvatar ?: 'default.jpg'))
            ],
            'token'  => csrf_hash()
        ]);
    }

    public function changePassword()
    {
        $rules = [
            'old_password'     => 'required',
            'new_password'     => 'required|min_length[8]|regex_match[/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/]',
            'confirm_password' => 'required|matches[new_password]'
        ];

        $messages = [
            'new_password' => [
                'regex_match' => 'Password baru harus mengandung Huruf Besar, Huruf Kecil, dan Angka.'
            ]
        ];

        if (!$this->validate($rules, $messages)) {
            return $this->response->setJSON([
                'status' => 'error', 
                'pesan'  => implode("<br>", $this->validator->getErrors()), 
                'token'  => csrf_hash()
            ]);
        }

        $model = new UserModel();
        $id = session()->get('id') ?: session()->get('user_id');
        $user = $model->find($id);

        if (!$user) {
            return $this->response->setJSON(['status' => 'error', 'pesan' => 'User tidak ditemukan!', 'token' => csrf_hash()]);
        }

        $oldPass = $this->request->getPost('old_password');
        $newPass = $this->request->getPost('new_password');

        if (!password_verify($oldPass, $user['password'])) {
            return $this->response->setJSON(['status' => 'error', 'pesan' => 'Password lama salah!', 'token' => csrf_hash()]);
        }

        $model->update($id, [
            'password' => password_hash($newPass, PASSWORD_DEFAULT)
        ]);

        return $this->response->setJSON(['status' => 'success', 'pesan' => 'Ubah password berhasil', 'token' => csrf_hash()]);
    }
}