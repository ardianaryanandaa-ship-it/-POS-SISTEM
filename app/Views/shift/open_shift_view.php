<?= $this->extend('layout/metronic') ?> <!-- Sesuaikan dengan nama layout utama Metronic Anda -->

<?= $this->section('content') ?>
<div class="d-flex flex-column flex-column-fluid">
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-xxl">
            
            <div class="card shadow-sm col-md-6 mx-auto">
                <div class="card-header">
                    <h3 class="card-title">Mulai Shift Baru</h3>
                </div>
                <form action="<?= base_url('shift/open') ?>" method="post">
                    <?= csrf_field() ?>
                    <div class="card-body">
                        <div class="text-center mb-10">
                            <img src="<?= base_url('assets/media/illustrations/sigma-1/17.png') ?>" class="mw-150px mb-5" alt="" />
                            <div class="text-gray-600 fw-semibold fs-5">Silakan masukkan modal awal di laci kasir hari ini.</div>
                        </div>

                        <div class="fv-row mb-7">
                            <label class="required fw-semibold fs-6 mb-2">Modal Awal (Cash)</label>
                            <div class="input-group input-group-solid">
                                <span class="input-group-text">Rp</span>
                                <input type="number" name="starting_cash" class="form-control form-control-solid mb-3 mb-lg-0" placeholder="Contoh: 200000" required />
                            </div>
                            <div class="form-text">Uang ini digunakan untuk kembalian pelanggan di awal shift.</div>
                        </div>
                    </div>

                    <div class="card-footer d-flex justify-content-end py-6 px-9">
                        <button type="submit" class="btn btn-primary">
                            <i class="ki-duotone ki-entrance-right fs-2"><span class="path1"></span><span class="path2"></span></i>
                            Buka Shift Sekarang
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>
<?= $this->endSection() ?>