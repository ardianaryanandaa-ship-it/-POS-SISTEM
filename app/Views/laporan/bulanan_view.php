<?= $this->extend('layout/metronic'); ?>
<?= $this->section('content'); ?>

<div class="card card-flush shadow-sm mb-5">
    <div class="card-header pt-5">
        <h3 class="card-title fw-bold">Analisis Periode</h3>
    </div>
    <div class="card-body">
        <form action="" method="get" class="row g-3">
            <div class="col-md-3">
                <select name="bulan" class="form-select">
                    <?php for($i=1; $i<=12; $i++): ?>
                        <option value="<?= sprintf('%02d', $i) ?>" <?= $filter_bulan == $i ? 'selected' : '' ?>><?= date('F', mktime(0,0,0,$i,1)) ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-success w-100">Lihat Laporan</button>
            </div>
        </form>
    </div>
</div>

<div class="card card-flush shadow-sm">
    <div class="card-body py-5">
        <div class="table-responsive">
            <table class="table align-middle table-row-dashed fs-6 gy-5">
                <thead>
                    <tr class="text-start text-gray-400 fw-bold fs-7 text-uppercase">
                        <th>Tanggal</th>
                        <th class="text-end">Omzet Kotor</th>
                        <th class="text-end text-danger">Diskon</th>
                        <th class="text-end text-warning">HPP (Modal)</th>
                        <th class="text-end text-danger">Biaya Op.</th>
                        <th class="text-end text-success">Laba Bersih</th>
                    </tr>
                </thead>
                <tbody class="fw-semibold text-gray-600">
                    <?php foreach($results as $r): ?>
                    <tr>
                        <td><?= date('d M Y', strtotime($r['tanggal'])) ?></td>
                        <td class="text-end">Rp <?= number_format($r['omzet_bruto']) ?></td>
                        <td class="text-end text-danger">-<?= number_format($r['total_diskon']) ?></td>
                        <td class="text-end text-warning"><?= number_format($r['total_hpp']) ?></td>
                        <td class="text-end text-danger"><?= number_format($r['biaya_operasional']) ?></td>
                        <td class="text-end">
                            <span class="badge badge-light-success fs-6 fw-boldest">Rp <?= number_format($r['laba_bersih']) ?></span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>