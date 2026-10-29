<?= $this->extend('layout/metronic') ?>

<?= $this->section('content') ?>
<div class="d-flex flex-column flex-center flex-column-fluid">
    <div class="d-flex flex-column flex-center text-center p-10">
        <div class="card card-flush w-lg-650px py-5 shadow-sm">
            <div class="card-body py-15 py-lg-20">
                
                <!-- Title -->
                <h1 class="fw-bolder fs-2hx text-gray-900 mb-4">Akses Ditolak!</h1>
                
                <!-- Icon/Ilustrasi -->
                <div class="mb-10">
                    <i class="ki-duotone ki-shield-cross text-danger" style="font-size: 10rem">
                        <span class="path1"></span><span class="path2"></span><span class="path3"></span>
                    </i>
                </div>
                
                <!-- Message -->
                <div class="fw-semibold fs-6 text-gray-500 mb-7">
                    Maaf, Akun Anda (Role: <?= session()->get('role_id') ?>) tidak memiliki izin untuk mengakses halaman ini. 
                    Silahkan hubungi Administrator jika ini adalah kesalahan.
                </div>
                
                <!-- Button Kembali -->
                <div class="mb-0">
                    <a href="<?= base_url('dashboard') ?>" class="btn btn-sm btn-primary">Kembali ke Dashboard</a>
                </div>
                
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>