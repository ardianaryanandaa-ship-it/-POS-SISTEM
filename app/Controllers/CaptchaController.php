<?php

namespace App\Controllers;

// GUNAKAN Controller bawaan CodeIgniter, BUKAN BaseController Anda
use CodeIgniter\Controller; 
use Gregwar\Captcha\CaptchaBuilder;

class CaptchaController extends Controller
{
    // Hapus fungsi index() karena tidak diperlukan di sini

    public function generate()
    {
        // 1. Bersihkan semua output buffer agar gambar tidak korup/pecah
        while (ob_get_level()) {
            ob_end_clean();
        }

        $builder = new CaptchaBuilder;
        $builder->build();

        // 2. Simpan kode ke session (Gunakan service session secara manual)
        $session = \Config\Services::session();
        $session->set('captcha_code', $builder->getPhrase());

        // 3. Kirim header gambar
        header('Content-Type: image/jpeg');
        header("Cache-Control: no-cache, must-revalidate");
        
        // 4. Output gambar
        $builder->output();
        
        // 5. Wajib EXIT agar tidak menjalankan sisa script sistem yang bisa merusak gambar
        exit; 
    }
}