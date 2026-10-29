<?php

namespace App\Controllers;

use App\Models\MetaModel;

class MetaController extends BaseController
{
    protected $metaModel;

    public function __construct() {
        $this->metaModel = new MetaModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Setting App',
            'meta'  => $this->meta // Ambil data dari BaseController ($this->meta)
        ];
        return view('admin/meta_view', $data);
    }

    public function update()
    {
        $posts = $this->request->getPost('meta');

        if ($posts) {
            foreach ($posts as $key => $value) {
                $cek = $this->metaModel->where('meta_key', $key)->first();
                if ($cek) {
                    $this->metaModel->where('meta_key', $key)
                                     ->set(['meta_value' => $value])
                                     ->update();
                } else {
                    $this->metaModel->insert([
                        'meta_key'   => $key,
                        'meta_value' => $value
                    ]);
                }
            }
        }

        return redirect()->back()->with('success', 'Data Setting App Berhasil Diperbarui!');
    }
}