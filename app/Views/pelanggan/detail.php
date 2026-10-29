<?= $this->extend('layout/metronic'); ?>
<?= $this->section('content'); ?>
<div class="row g-5">
    <!-- INFO RINGKASAN -->
    <div class="col-md-4">
        <div class="card shadow-sm mb-5">
            <div class="card-body">
                <div class="d-flex flex-column align-items-center">
                    <div class="symbol symbol-100px symbol-circle mb-3">
                        <span class="symbol-label bg-light-danger text-danger fs-1 fw-boldest"><?= substr($p['nama_pelanggan'],0,1) ?></span>
                    </div>
                    <h3 class="fw-bold"><?= $p['nama_pelanggan'] ?></h3>
                    <span class="text-muted"><?= $p['telepon'] ?></span>
                </div>
                <div class="separator my-5"></div>
                <div class="d-flex flex-stack">
                    <span class="text-gray-600">Total Poin:</span>
                    <span class="fw-boldest text-success fs-4"><?= number_format($p['poin']) ?></span>
                </div>
            </div>
        </div>
        
        <div class="card bg-primary">
            <div class="card-body">
                <h4 class="text-white">Belanja Tahun Ini (<?= date('Y') ?>)</h4>
                <div class="text-white fw-boldest fs-1">Rp <?= number_format($total_tahunan, 0, ',', '.') ?></div>
                <p class="text-white opacity-75 mt-2">*Gunakan data ini untuk memberikan diskon khusus jika belanja tahunan > 50jt.</p>
            </div>
        </div>
    </div>

    <!-- TABEL RIWAYAT -->
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header"><h3 class="card-title">Riwayat Belanja</h3></div>
            <div class="card-body">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>No. Invoice</th>
                            <th>Tanggal</th>
                            <th>Total Belanja</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($transaksi as $t): ?>
                        <tr>
                            <td><?= $t['invoice_no'] ?></td>
                            <td><?= date('d/m/Y', strtotime($t['created_at'])) ?></td>
                            <td>Rp <?= number_format($t['total_price'],0,',','.') ?></td>
                            <td><span class="badge badge-success"><?= $t['status'] ?></span></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>