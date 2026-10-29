<?php

namespace App\Controllers;

use App\Models\UserModel;

class UserController extends BaseController
{
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    public function index()
    {
        $model = new UserModel();
        
        // JOIN untuk mengambil role_name agar tampilan tabel user lebih informatif
        $users = $model->select('users.*, users_role.role_name')
                       ->join('users_role', 'users_role.id = users.role_id', 'left') 
                       ->findAll();

        $roles = $this->db->table('users_role')->get()->getResultArray();

        $data = [
            'title' => 'Manajemen User',
            'users' => $users,
            'roles' => $roles
        ];

        return view('users/index', $data);
    }

    public function get_json($id)
    {
        $model = new UserModel();
        return $this->response->setJSON($model->find($id));
    }

    public function store()
    {
        $model = new UserModel();

        // 1. Tambahkan Validasi agar tidak ada username ganda
        $rules = [
            'username'  => 'required|is_unique[users.username]|min_length[3]',
            'full_name' => 'required',
            'role_id'   => 'required',
            'password'  => 'required|min_length[5]',
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'status' => 'error',
                'pesan'  => 'Gagal simpan: Username sudah ada atau data tidak lengkap.',
                'token'  => csrf_hash()
            ]);
        }

        $data = [
            'username'  => $this->request->getPost('username'),
            'full_name' => $this->request->getPost('full_name'),
            'email'     => $this->request->getPost('email'),
            'role_id'   => $this->request->getPost('role_id'),
            'status'    => $this->request->getPost('status') ?? 'Active',
            'password'  => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'created_at' => date('Y-m-d H:i:s')
        ];

        if ($model->insert($data)) {
            return $this->response->setJSON([
                'status' => 'success', 
                'pesan'  => 'User baru berhasil dibuat! Silahkan atur akses unit di menu Hak Akses.', 
                'token'  => csrf_hash()
            ]);
        }

        return $this->response->setJSON(['status' => 'error', 'pesan' => 'Terjadi kesalahan database.']);
    }

    public function update($id)
    {
        $model = new UserModel();
        
        $data = [
            'username'  => $this->request->getPost('username'),
            'full_name' => $this->request->getPost('full_name'),
            'email'     => $this->request->getPost('email'),
            'role_id'   => $this->request->getPost('role_id'),
            'status'    => $this->request->getPost('status'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        // Jika password diisi (tidak kosong), maka update passwordnya
        if ($this->request->getPost('password')) {
            $data['password'] = password_hash($this->request->getPost('password'), PASSWORD_DEFAULT);
        }

        if ($model->update($id, $data)) {
            return $this->response->setJSON(['status' => 'success', 'pesan' => 'Data user diperbarui', 'token' => csrf_hash()]);
        }
        
        return $this->response->setJSON(['status' => 'error', 'pesan' => 'Gagal memperbarui data.']);
    }

    // Tambahkan ini di UserController.php

public function reset_password($id)
{
    $model = new UserModel();
    $password = $this->request->getPost('password');

    // Validasi input
    if (empty($password) || strlen($password) < 5) {
        return $this->response->setJSON([
            'status' => 'error', 
            'pesan' => 'Password minimal 5 karakter.', 
            'token' => csrf_hash()
        ]);
    }

    $data = [
        'password'   => password_hash($password, PASSWORD_DEFAULT),
        'updated_at' => date('Y-m-d H:i:s')
    ];

    if ($model->update($id, $data)) {
        return $this->response->setJSON([
            'status' => 'success', 
            'pesan' => 'Password berhasil diperbarui!', 
            'token' => csrf_hash()
        ]);
    }

    return $this->response->setJSON(['status' => 'error', 'pesan' => 'Gagal mereset password.']);
}
}