<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\ShiftModel;

class ShiftFilter implements FilterInterface
{
    /**
     * Mengecek apakah kasir sudah melakukan "Buka Shift" sebelum akses menu transaksi.
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        $shiftModel = new ShiftModel();
        $userId = session()->get('user_id'); // Ambil ID kasir dari session

        // Cari shift yang statusnya masih 'open' untuk user ini
        $activeShift = $shiftModel->where('user_id', $userId)
                                  ->where('status', 'open')
                                  ->first();

        // Jika tidak ada shift yang open, arahkan ke halaman manajemen shift (untuk input modal awal)
        if (!$activeShift) {
            return redirect()->to('/shift')->with('error', 'Silakan buka shift (input modal awal) terlebih dahulu!');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Tidak perlu logika tambahan setelah request
    }
}