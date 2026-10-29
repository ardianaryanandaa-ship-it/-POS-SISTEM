<?= $this->extend('layout/metronic') ?>
<?= $this->section('content') ?>

<div class="d-flex flex-column mb-5">
    <h1 class="text-dark fw-bold fs-2qx">INFORMASI KONTAK</h1>
    <ul class="breadcrumb fw-semibold text-gray-600 fs-7">
        <li class="breadcrumb-item">Informasi Kontak</li>
        <li class="breadcrumb-item text-gray-500">Kontak Kami</li>
    </ul>
</div>

<div class="card shadow-sm">
    <div class="card-header">
        <h3 class="card-title fw-bold">
            <i class="ki-duotone ki-user fs-2 me-3"><span class="path1"></span><span class="path2"></span></i> Kontak Kami
        </h3>
    </div>
    <div class="card-body">
        <?php if(session()->getFlashdata('success')): ?>
            <div class="alert alert-success p-5 mb-10"><?= session()->getFlashdata('success') ?></div>
        <?php endif; ?>

        <form action="<?= base_url('contact/update') ?>" method="post">
            <?= csrf_field() ?>
            
            <div class="row mb-7">
                <label class="col-lg-2 col-form-label fw-semibold fs-6">Nama Instansi</label>
                <div class="col-lg-10">
                    <input type="text" name="contact[nama_instansi]" class="form-control" value="<?= $contact['nama_instansi'] ?? '' ?>" />
                </div>
            </div>

            <div class="row mb-7">
                <label class="col-lg-2 col-form-label fw-semibold fs-6">Alamat</label>
                <div class="col-lg-10">
                    <textarea name="contact[alamat]" class="form-control" rows="4"><?= $contact['alamat'] ?? '' ?></textarea>
                </div>
            </div>

            <div class="row mb-7">
                <label class="col-lg-2 col-form-label fw-semibold fs-6">No. Telp</label>
                <div class="col-lg-10">
                    <input type="text" name="contact[telepon]" class="form-control" value="<?= $contact['telepon'] ?? '' ?>" />
                </div>
            </div>

            <div class="row mb-7">
                <label class="col-lg-2 col-form-label fw-semibold fs-6">Email</label>
                <div class="col-lg-10">
                    <input type="email" name="contact[email]" class="form-control" value="<?= $contact['email'] ?? '' ?>" />
                </div>
            </div>

            <div class="row mb-7">
                <label class="col-lg-2 col-form-label fw-semibold fs-6">Website</label>
                <div class="col-lg-10">
                    <input type="text" name="contact[website]" class="form-control" value="<?= $contact['website'] ?? '' ?>" />
                </div>
            </div>

            <div class="row">
                <div class="col-lg-2"></div>
                <div class="col-lg-10">
                    <button type="submit" class="btn btn-success px-10">Update</button>
                </div>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>