<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // Mengecek ke dalam session user melalui helper
        if (!is_logged_in()) {
            return redirect()->to(base_url('login'))->with('errors', [
                'username' => 'Silahkan login terlebih dahulu untuk mengakses sistem.'
            ]);
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }
}