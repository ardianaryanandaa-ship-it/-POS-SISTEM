<?= $this->extend('layout/metronic') ?>
<?= $this->section('content') ?>

<div class="d-flex flex-column mb-5">
    <h1 class="text-dark fw-bold fs-2qx">Settingan APP</h1>
    <ul class="breadcrumb fw-semibold text-gray-600 fs-7">
        <li class="breadcrumb-item">Setting</li>
        <li class="breadcrumb-item text-gray-500">Setting App</li>
    </ul>
</div>

<div class="card shadow-sm">
    <div class="card-header">
        <h3 class="card-title fw-bold"><i class="ki-duotone ki-setting-2 fs-2 me-3"><span class="path1"></span><span class="path2"></span></i> Setting App</h3>
    </div>
    <div class="card-body">
        <?php if(session()->getFlashdata('success')): ?>
            <div class="alert alert-success p-5 mb-10"><?= session()->getFlashdata('success') ?></div>
        <?php endif; ?>

        <form action="<?= base_url('meta/update') ?>" method="post">
            <?= csrf_field() ?>
            <div class="row mb-7">
                <label class="col-lg-2 col-form-label fw-semibold fs-6">Nama</label>
                <div class="col-lg-10">
                    <input type="text" name="meta[nama]" class="form-control" value="<?= $meta['nama'] ?? '' ?>" />
                </div>
            </div>
            <div class="row mb-7">
                <label class="col-lg-2 col-form-label fw-semibold fs-6">Deskripsi</label>
                <div class="col-lg-10">
                    <textarea name="meta[deskripsi]" class="form-control" rows="4"><?= $meta['deskripsi'] ?? '' ?></textarea>
                </div>
            </div>
            <div class="row mb-7">
                <label class="col-lg-2 col-form-label fw-semibold fs-6">Author</label>
                <div class="col-lg-10">
                    <input type="text" name="meta[author]" class="form-control" value="<?= $meta['author'] ?? '' ?>" />
                </div>
            </div>
            <div class="row mb-7">
                <label class="col-lg-2 col-form-label fw-semibold fs-6">Developer</label>
                <div class="col-lg-10">
                    <input type="text" name="meta[developer]" class="form-control" value="<?= $meta['developer'] ?? '' ?>" />
                </div>
            </div>
            <button type="submit" class="btn btn-primary px-10">Update</button>
        </form>
    </div>
</div>
<?= $this->endSection() ?>