<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\HTTP\ResponseInterface;

class BaseApiController extends BaseController
{
    use ResponseTrait;

    /**
     * Format Response standar untuk Mobile App
     */
    protected $format = 'json';

    /**
     * Helper untuk mempermudah respon sukses
     */
    protected function apiResponse($data = [], string $message = "Success", int $code = ResponseInterface::HTTP_OK)
    {
        return $this->respond([
            'status'  => $code,
            'error'   => null,
            'message' => $message,
            'data'    => $data
        ], $code);
    }

    /**
     * Helper untuk mempermudah respon error/gagal
     */
    protected function apiError(string $message = "Internal Server Error", int $code = ResponseInterface::HTTP_INTERNAL_SERVER_ERROR)
    {
        return $this->respond([
            'status'  => $code,
            'error'   => $code,
            'message' => $message,
            'data'    => null
        ], $code);
    }

    /**
     * Helper untuk validasi input yang gagal
     */
    protected function apiValidationError($errors)
    {
        return $this->respond([
            'status'  => 400,
            'error'   => 400,
            'message' => 'Validation Error',
            'data'    => $errors // Berisi list error field
        ], 400);
    }
}