<?= $this->extend('layout/metronic') ?>

<?= $this->section('content') ?>
<div class="card shadow-sm">
    <div class="card-header border-0 pt-5">
        <h3 class="card-title align-items-start flex-column">
            <span class="card-label fw-bold fs-3 mb-1">Riwayat Shift Kasir</span>
            <span class="text-muted mt-1 fw-semibold fs-7">Pantau saldo laci dan selisih setiap pergantian shift</span>
        </h3>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
                <thead>
                    <tr class="fw-bold text-muted bg-light text-center">
                        <th class="ps-4 rounded-start text-start">Kasir</th>
                        <th>Waktu Buka</th>
                        <th>Waktu Tutup</th>
                        <th>Modal</th>
                        <th>Uang Fisik</th>
                        <th>Selisih</th>
                        <th class="text-end pe-4 rounded-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($shifts as $s): ?>
                    <tr class="text-center">
                        <td class="text-start ps-4">
                            <div class="fw-bold text-gray-800"><?= $s['full_name'] ?></div>
                            <span class="badge badge-light-<?= $s['status'] == 'open' ? 'success' : 'secondary' ?> fs-10">
                                <?= strtoupper($s['status']) ?>
                            </span>
                        </td>
                        <td><?= date('d/m/y H:i', strtotime($s['start_at'])) ?></td>
                        <td><?= $s['end_at'] ? date('d/m/y H:i', strtotime($s['end_at'])) : '-' ?></td>
                        <td>Rp <?= number_format($s['starting_cash'], 0, ',', '.') ?></td>
                        <td>Rp <?= number_format($s['actual_cash'], 0, ',', '.') ?></td>
                        <td>
                            <?php 
                                $diff = $s['actual_cash'] - $s['expected_cash'];
                                if($s['status'] == 'open'): echo '-';
                                elseif($diff == 0): echo '<span class="text-success fw-bold">Balance</span>';
                                else: echo '<span class="text-danger fw-bold">Rp '.number_format($diff, 0, ',', '.').'</span>';
                                endif;
                            ?>
                        </td>
                        <td class="text-end pe-4">
                            <a href="<?= base_url('shift/summary/' . $s['id']) ?>" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm">
                                <i class="ki-duotone ki-eye fs-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>