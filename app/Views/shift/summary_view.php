<?= $this->extend('layout/metronic') ?>

<?= $this->section('content') ?>
<div class="card shadow-sm col-md-8 mx-auto">
    <div class="card-header bg-light">
        <h3 class="card-title">Laporan Penutupan Kasir</h3>
        <div class="card-toolbar">
            <button onclick="window.print()" class="btn btn-sm btn-secondary me-2">Cetak Laporan</button>
            <a href="<?= base_url('kasir') ?>" class="btn btn-sm btn-primary">Kembali ke POS</a>
        </div>
    </div>
    <div class="card-body">
        <div class="text-center mb-10">
            <h2 class="fw-bold">REKAP SHIFT #<?= $shift['id'] ?></h2>
            <span class="text-muted">Waktu: <?= date('d M Y H:i', strtotime($shift['start_at'])) ?> s/d <?= date('H:i', strtotime($shift['end_at'])) ?></span>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered align-middle gs-7 gy-4">
                <tr class="bg-light">
                    <td class="fw-bold">Modal Awal (A)</td>
                    <td class="text-end">Rp <?= number_format($shift['starting_cash'], 0, ',', '.') ?></td>
                </tr>
                <tr>
                    <td class="fw-bold">Total Penjualan Tunai (B)</td>
                    <td class="text-end">Rp <?= number_format($shift['total_sales_cash'], 0, ',', '.') ?></td>
                </tr>
                <tr>
                    <td class="fw-bold">Total Pengeluaran Kas (C)</td>
                    <td class="text-end text-danger">- Rp <?= number_format($shift['total_expenses'], 0, ',', '.') ?></td>
                </tr>
                <tr class="bg-light-primary">
                    <td class="fw-bold fs-4">Estimasi Uang di Laci (A + B - C)</td>
                    <td class="text-end fw-boldest fs-4 text-primary">Rp <?= number_format($shift['expected_cash'], 0, ',', '.') ?></td>
                </tr>
                <tr class="bg-light-info">
                    <td class="fw-bold">Uang Fisik yang Diinput Kasir</td>
                    <td class="text-end fw-boldest">Rp <?= number_format($shift['actual_cash'], 0, ',', '.') ?></td>
                </tr>
            </table>
        </div>

        <!-- TAMPILAN SELISIH -->
        <?php $selisih = $shift['actual_cash'] - $shift['expected_cash']; ?>
        <div class="alert alert-<?= $selisih == 0 ? 'success' : 'danger' ?> d-flex align-items-center p-5 mt-5">
            <i class="ki-duotone ki-information-5 fs-2hx me-4 text-<?= $selisih == 0 ? 'success' : 'danger' ?>"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
            <div class="d-flex flex-column">
                <h4 class="mb-1 text-<?= $selisih == 0 ? 'success' : 'danger' ?>">
                    <?= $selisih == 0 ? 'Status: Balance (Sesuai)' : 'Status: Selisih (Unbalance)' ?>
                </h4>
                <span class="fs-2 fw-bold">
                    <?= $selisih >= 0 ? '+' : '' ?> Rp <?= number_format($selisih, 0, ',', '.') ?>
                </span>
                <?php if($selisih < 0): ?>
                    <small>Kasir wajib mempertanggungjawabkan kekurangan uang sebesar Rp <?= number_format(abs($selisih), 0, ',', '.') ?></small>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>