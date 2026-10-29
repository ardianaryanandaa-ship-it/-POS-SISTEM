<?= $this->extend('layout/metronic') ?>

<?= $this->section('content') ?>
<div class="d-flex flex-column flex-column-fluid">
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-xxl">
            
            <!-- Summary Widgets -->
            <div class="row g-5 g-xl-10 mb-5 mb-xl-10">
                <div class="col-md-4">
                    <div class="card card-flush h-md-100" style="background-color: #F1416C;">
                        <div class="card-body d-flex flex-column justify-content-between p-6">
                            <div class="d-flex align-items-center mb-2 text-white">
                                <i class="ki-solid ki-wallet text-white fs-2x me-3"></i>
                                <span class="fw-semibold fs-5">Modal Awal</span>
                            </div>
                            <span class="fs-2hx fw-bold text-white">Rp <?= number_format($shift['starting_cash'], 0, ',', '.') ?></span>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card card-flush h-md-100" style="background-color: #50CD89;">
                        <div class="card-body d-flex flex-column justify-content-between p-6">
                            <div class="d-flex align-items-center mb-2 text-white">
                                <i class="ki-solid ki-basket text-white fs-2x me-3"></i>
                                <span class="fw-semibold fs-5">Total Penjualan (Tunai)</span>
                            </div>
                            <span class="fs-2hx fw-bold text-white">Rp <?= number_format($total_sales, 0, ',', '.') ?></span>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card card-flush h-md-100" style="background-color: #7239EA;">
                        <div class="card-body d-flex flex-column justify-content-between p-6">
                            <div class="d-flex align-items-center mb-2 text-white">
                                <i class="ki-solid ki-abstract-18 text-white fs-2x me-3"></i>
                                <span class="fw-semibold fs-5">Total Pengeluaran Kas</span>
                            </div>
                            <span class="fs-2hx fw-bold text-white">Rp <?= number_format($total_expenses, 0, ',', '.') ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-5">
                <!-- Kolom Kiri: Form Pengeluaran -->
                <div class="col-md-6">
                    <div class="card shadow-sm">
                        <div class="card-header"><h3 class="card-title">Input Pengeluaran Kecil</h3></div>
                        <form action="<?= base_url('shift/addExpense') ?>" method="post">
                            <?= csrf_field() ?>
                            <div class="card-body">
                                <div class="mb-5">
                                    <label class="form-label">Nominal</label>
                                    <input type="number" name="nominal" class="form-control form-control-solid" required />
                                </div>
                                <div class="mb-5">
                                    <label class="form-label">Keterangan</label>
                                    <textarea name="keterangan" class="form-control form-control-solid" rows="2" required></textarea>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-light-primary btn-sm">Simpan Pengeluaran</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Kolom Kanan: Form Tutup Shift -->
                <div class="col-md-6">
                    <div class="card shadow-sm border border-danger">
                        <div class="card-header bg-light-danger">
                            <h3 class="card-title text-danger">Tutup Kasir (End of Shift)</h3>
                        </div>
                        <form action="<?= base_url('shift/close') ?>" method="post" id="form-tutup-shift">
                            <?= csrf_field() ?>
                            <div class="card-body">
                                <div class="alert alert-dismissible bg-light-info d-flex flex-column p-5 mb-10">
                                    <h4 class="fw-bold">Estimasi Uang di Laci:</h4>
                                    <?php $expected = ($shift['starting_cash'] + $total_sales) - $total_expenses; ?>
                                    <span class="fs-2 fw-bolder text-primary">Rp <?= number_format($expected, 0, ',', '.') ?></span>
                                </div>

                                <div class="fv-row mb-7">
                                    <label class="required fw-bold fs-6 mb-2">Uang Fisik yang Ada di Laci</label>
                                    <div class="input-group input-group-solid">
                                        <span class="input-group-text">Rp</span>
                                        <input type="number" name="actual_cash" id="actual_cash" class="form-control form-control-lg form-control-solid" placeholder="0" required />
                                    </div>
                                    <div class="form-text text-danger">*Pastikan perhitungan uang fisik akurat untuk menghindari selisih.</div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="button" id="btn-tutup-shift" class="btn btn-danger w-100 py-4">
                                    Konfirmasi & Tutup Kasir
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- Hapus blok script lama, ganti dengan ini -->
<script>
    // Gunakan $(document).on agar event tetap menempel meskipun halaman dimuat via AJAX
    $(document).off('click', '#btn-tutup-shift').on('click', '#btn-tutup-shift', function(e) {
        e.preventDefault();
        
        const actualCash = $('#actual_cash').val();
        const formTutup = $('#form-tutup-shift');

        if (actualCash === "" || actualCash < 0) {
            Swal.fire({
                title: 'Input Salah',
                text: 'Silakan masukkan jumlah uang fisik di laci!',
                icon: 'error',
                confirmButtonClass: "btn btn-primary"
            });
            return;
        }

        Swal.fire({
            title: 'Tutup Kasir Sekarang?',
            text: "Uang fisik Rp " + parseInt(actualCash).toLocaleString('id-ID') + " akan dicatat ke sistem.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#F1416C', // Warna merah Metronic
            confirmButtonText: 'Ya, Tutup!',
            cancelButtonText: 'Batal',
            customClass: {
                confirmButton: "btn btn-danger",
                cancelButton: "btn btn-light"
            }
        }).then((result) => {
            if (result.isConfirmed) {
                // Tampilkan loading saat proses tutup
                Swal.fire({
                    title: 'Memproses...',
                    text: 'Sedang menutup shift dan menyinkronkan data.',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                formTutup.submit(); 
            }
        });
    });
</script>
<?= $this->endSection() ?>