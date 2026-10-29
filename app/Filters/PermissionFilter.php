<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class PermissionFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        helper('auth');

        if (!is_logged_in()) {
            return redirect()->to('/login');
        }

        if (user('role_id') == 3) {
            return; 
        }

        $requiredSlug = $arguments[0] ?? null;

        if ($requiredSlug && !can($requiredSlug)) {
            
            if ($request->isAJAX()) {
                return service('response')->setJSON([
                    'status'  => 'error',
                    'message' => 'Anda tidak memiliki akses (Forbidden).'
                ])->setStatusCode(403);
            }

            try {
                return service('response')
                    ->setStatusCode(403)
                    ->setBody(view('errors/html/error_403'));
            } catch (\Exception $e) {
                return redirect()->to('/dashboard')->with('errors', [
                    'access' => 'Maaf, Anda tidak memiliki izin untuk mengakses halaman tersebut.'
                ]);
            }
        }

        return; 
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }
}