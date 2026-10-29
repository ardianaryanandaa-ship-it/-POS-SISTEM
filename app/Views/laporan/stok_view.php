<?= $this->extend('layout/metronic'); ?>
<?= $this->section('content'); ?>

<div class="card card-flush shadow-sm mb-5">
    <div class="card-header pt-5">
        <h3 class="card-title fw-bold">Filter Unit Bisnis</h3>
    </div>
    <div class="card-body">
        <form action="" method="get" class="row g-3">
            <div class="col-md-4">
                <select name="unit" class="form-select">
                    <option value="">-- Semua Unit --</option>
                    <?php foreach($units as $u): ?>
                        <option value="<?= $u['kode_unit'] ?>" <?= $filter_unit == $u['kode_unit'] ? 'selected' : '' ?>><?= $u['nama_unit'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">Filter Stok</button>
            </div>
        </form>
    </div>
</div>

<div class="card card-flush shadow-sm">
    <div class="card-body py-5">
        <!-- Ringkasan Nilai Aset -->
        <?php 
            $grand_total_qty = array_sum(array_column($results, 'jumlah'));
            $grand_total_rp = array_sum(array_column($results, 'total_nilai_aset'));
        ?>
        <div class="alert bg-light-info d-flex flex-stack p-5 mb-10 border border-info border-dashed">
            <div class="d-flex align-items-center">
                <i class="ki-duotone ki-delivery-2 fs-2hx text-info me-4"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span><span class="path6"></span><span class="path7"></span><span class="path8"></span><span class="path9"></span></i>
                <div class="d-flex flex-column">
                    <h4 class="fw-bold text-info">Total Nilasi Aset Saat Ini</h4>
                    <span>Berdasarkan harga beli terakhir dari master data</span>
                </div>
            </div>
            <div class="text-end">
                <span class="fs-2hx fw-boldest text-info">Rp <?= number_format($grand_total_rp) ?></span>
                <div class="fs-7 text-muted fw-bold">Dari total <?= number_format($grand_total_qty) ?> pcs barang</div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table align-middle table-row-dashed fs-6 gy-5">
                <thead>
                    <tr class="text-start text-gray-400 fw-bold fs-7 text-uppercase">
                        <th>Unit Lokasi</th>
                        <th>Barang</th>
                        <th class="text-center">Sisa Qty</th>
                        <th class="text-end">Harga Modal</th>
                        <th class="text-end">Total Nilai Aset</th>
                    </tr>
                </thead>
                <tbody class="fw-semibold text-gray-600">
                    <?php foreach($results as $r): ?>
                    <tr>
                        <td>
                            <div class="text-gray-800 fw-bold"><?= $r['nama_unit'] ?></div>
                            <span class="badge badge-light-<?= $r['jenis_unit'] == 'GUDANG' ? 'primary' : 'success' ?> fs-9"><?= $r['jenis_unit'] ?></span>
                        </td>
                        <td>
                            <div class="text-dark fw-bold"><?= $r['nama_barang'] ?></div>
                            <div class="text-muted fs-8"><?= $r['kode_barang'] ?></div>
                        </td>
                        <td class="text-center"><?= number_format($r['jumlah']) ?></td>
                        <td class="text-end">Rp <?= number_format($r['harga_beli']) ?></td>
                        <td class="text-end fw-boldest text-dark">
                            Rp <?= number_format($r['total_nilai_aset']) ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>