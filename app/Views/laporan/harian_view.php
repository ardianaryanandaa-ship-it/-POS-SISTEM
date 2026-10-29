<?= $this->extend('layout/metronic'); ?>
<?= $this->section('content'); ?>


<div class="row g-5 mb-5">
    <div class="col-md-4">
        <div class="card bg-success p-5">
            <span class="text-white fw-bold">TOTAL OMZET TUNAI HARI INI</span>
            <h2 class="text-white">Rp <?= number_format($total_omzet_harian) ?></h2>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-danger p-5">
            <span class="text-white fw-bold">TOTAL PENGELUARAN (GALON/DLL)</span>
            <h2 class="text-white">- Rp <?= number_format($total_biaya_harian) ?></h2>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-primary p-5">
            <span class="text-white fw-bold">TOTAL UANG WAJIB SETOR</span>
            <h2 class="text-white">Rp <?= number_format($total_fisik_harian) ?></h2>
        </div>
    </div>
</div>

<div class="card card-flush shadow-sm mb-5">
    <div class="card-header pt-5">
        <h3 class="card-title fw-bold">Filter Tanggal</h3>
    </div>
    <div class="card-body">
        <form action="" method="get" class="row g-3">
            <div class="col-md-4">
                <input type="date" name="tanggal" class="form-control" value="<?= $filter_tgl ?>">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">Cari Data</button>
            </div>
        </form>
    </div>
</div>

<!-- Tambahkan ini di atas tabel Laporan Harian Anda -->


<div class="card card-flush shadow-sm">
    <div class="card-body py-5">
        <div class="table-responsive">
            <table class="table align-middle table-row-dashed fs-6 gy-5">
                <thead>
                    <tr class="text-start text-gray-400 fw-bold fs-7 text-uppercase">
                        <th>Kasir</th>
                        <th>Status</th>
                        <th class="text-end">Modal</th>
                        <th class="text-end">Penjualan</th>
                        <th class="text-end text-danger">Biaya (Galon/dll)</th>
                        <th class="text-end">Harus Ada</th>
                        <th class="text-end">Uang Fisik</th>
                        <th class="text-end">Selisih</th>
                    </tr>
                </thead>
                <tbody class="fw-semibold text-gray-600">
    <?php if (empty($results)) : ?>
        <tr>
            <td colspan="8" class="text-center py-10">
                <div class="d-flex flex-column">
                    <i class="ki-duotone ki-information-5 fs-2x text-muted mb-3"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                    <span class="text-muted fw-bold">Tidak ada aktivitas shift pada tanggal <?= date('d M Y', strtotime($filter_tgl)) ?></span>
                </div>
            </td>
        </tr>

    <?php else : ?>
        <?php foreach ($results as $r) : ?>
            <tr>
                <td class="text-gray-800 fw-bold"><?= $r['nama_kasir'] ?></td>
                <td>
                    <span class="badge badge-<?= $r['status'] == 'closed' ? 'light-success' : 'light-warning' ?>">
                        <?= strtoupper($r['status']) ?>
                    </span>
                </td>
                <td class="text-end">Rp <?= number_format($r['starting_cash'], 0, ',', '.') ?></td>
                <td class="text-end">Rp <?= number_format($r['total_sales_cash'], 0, ',', '.') ?></td>
                <td class="text-end text-danger">-<?= number_format($r['total_expenses'], 0, ',', '.') ?></td>
                <td class="text-end fw-bold">Rp <?= number_format($r['expected_cash'], 0, ',', '.') ?></td>
                <td class="text-end text-dark fw-boldest">Rp <?= number_format($r['actual_cash'], 0, ',', '.') ?></td>
                <td class="text-end">
                    <?php $selisih = $r['actual_cash'] - $r['expected_cash']; ?>
                    <span class="badge badge-<?= $selisih < 0 ? 'danger' : ($selisih > 0 ? 'success' : 'light') ?>">
                        <?= $selisih ?>
                    </span>
                </td>
            </tr>
        <?php endforeach; ?>
    <?php endif; ?>
</tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>