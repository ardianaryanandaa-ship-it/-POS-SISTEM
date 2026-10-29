<?php

namespace App\Controllers;
use App\Models\ContactModel;

class ContactController extends BaseController
{
    protected $contactModel;

    public function __construct() {
        $this->contactModel = new ContactModel();
    }

    public function index()
    {
        $data = [
            'title'   => 'Kontak Kami',
            'contact' => $this->contact, // Mengambil data dari BaseController
            'meta'    => $this->meta     // Juga tersedia data meta jika dibutuhkan
        ];
        return view('admin/contact_view', $data);
    }

    public function update()
    {
        $posts = $this->request->getPost('contact');

        if ($posts) {
            foreach ($posts as $key => $value) {
                $cek = $this->contactModel->where('contact_key', $key)->first();
                if ($cek) {
                    $this->contactModel->where('contact_key', $key)
                                       ->set(['contact_value' => $value])
                                       ->update();
                } else {
                    $this->contactModel->insert([
                        'contact_key'   => $key,
                        'contact_value' => $value
                    ]);
                }
            }
        }

        return redirect()->back()->with('success', 'Informasi Kontak Berhasil Diperbarui!');
    }
}