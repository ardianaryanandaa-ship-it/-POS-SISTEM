<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class RoleFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
{
    helper('auth');
    if (!is_logged_in()) {
        return redirect()->to('/login');
    }

    $userRole = user('role_id'); // Helper user() otomatis dekripsi
    $allowedRoles = $arguments;

    if (!in_array($userRole, $allowedRoles)) {
        return redirect()->to('/dashboard')->with('errors', ['role' => 'Akses ditolak untuk role Anda!']);
    }
}

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null) {}
}