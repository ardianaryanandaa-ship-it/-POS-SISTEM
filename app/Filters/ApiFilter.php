<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Services;

class ApiFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $authHeader = $request->getHeaderLine('Authorization');
        
        // Cek apakah header Authorization ada
        if (!$authHeader || !str_contains($authHeader, 'Bearer')) {
            $response = Services::response();
            $response->setStatusCode(401);
            return $response->setJSON(['message' => 'Token diperlukan (Unauthorized)']);
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null) {}
}