<?php

use App\Models\BarangModel;

/* ==========================================================================
   CRUD BARANG
   ========================================================================== */

/**
 * Mengambil semua data barang
 */
if (!function_exists('get_list_barang')) {
    function get_list_barang() {
        $model = new \App\Models\BarangModel();
        return $model->findAll();
    }
}

/**
 * Simpan barang baru
 */
if (!function_exists('db_simpan_barang')) {
    function db_simpan_barang($data) {
        $model = new \App\Models\BarangModel();
        return $model->save($data);
    }
}

/**
 * Update data barang
 */
if (!function_exists('db_update_barang')) {
    function db_update_barang($id, $data) {
        $model = new \App\Models\BarangModel();
        return $model->update($id, $data);
    }
}

/**
 * Hapus barang
 */
if (!function_exists('db_delete_barang')) {
    function db_delete_barang($id) {
        $model = new \App\Models\BarangModel();
        return $model->delete($id);
    }
}