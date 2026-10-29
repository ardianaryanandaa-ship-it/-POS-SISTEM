<?= $this->extend('layout/metronic') ?>

<?= $this->section('content') ?>

    <!-- 1. BAGIAN BANNER WELCOME -->
    <div class="card border-0 bgi-no-repeat mb-10" style="background-color: #009EF7; background-position: 100% 50%; background-size: 30% auto; background-image:url('<?= base_url('template/assets/media/svg/shapes/abstract-2.svg') ?>')">
        <div class="card-body">
            <div class="row align-items-center h-100">
                <div class="col-lg-7 ps-xl-13">
                    <div class="text-white mb-6 pt-6">
                        <span class="fs-4 fw-semibold me-2 d-block lh-1 pb-2 opacity-75">
                            Selamat Datang, <?= user('full_name') ?> 
                            <span class="badge badge-light-success fs-9 px-2 py-1 ms-2 text-uppercase">
                                <?= is_admin() ? 'Admin' : (is_developer() ? 'Developer' : 'Kasir') ?>
                            </span>
                        </span>
                        <span class="fs-2qx fw-bold">Optimalkan <?= $meta['nama'] ?? 'Sistem POS' ?></span>
                    </div>
                    <span class="fw-semibold text-white fs-6 mb-8 d-block opacity-75">Kelola unit bisnis, pantau stok fisik, dan analisis penjualan secara real-time.</span>
                    <div class="d-flex align-items-center flex-wrap d-grid gap-2 mb-10">
                        <div class="d-flex align-items-center me-5 me-xl-13">
                            <div class="symbol symbol-35px symbol-circle me-3">
                                <span class="symbol-label" style="background: rgba(255, 255, 255, 0.2)">
                                    <i class="ki-duotone ki-abstract-41 fs-3 text-white"><span class="path1"></span><span class="path2"></span></i>
                                </span>
                            </div>
                            <div class="text-white">
                                <span class="fw-semibold d-block fs-8 opacity-75">Unit Terdaftar</span>
                                <span class="fw-bold fs-7"><?= count($bu_list ?? []) ?> Cabang</span>
                            </div>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="symbol symbol-35px symbol-circle me-3">
                                <span class="symbol-label" style="background: rgba(255, 255, 255, 0.2)">
                                    <i class="ki-duotone ki-profile-user fs-3 text-white"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></i>
                                </span>
                            </div>
                            <div class="text-white">
                                <span class="fw-semibold opacity-75 d-block fs-8">Member Loyal</span>
                                <span class="fw-bold fs-7"><?= $total_member ?? 0 ?> Orang</span>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex flex-column flex-sm-row d-grid gap-2">
                        <a href="<?= base_url('kasir') ?>" class="btn btn-success fw-bold px-8">Buka Kasir (POS)</a>
                        <a href="<?= base_url('stockopname') ?>" class="btn btn-primary fw-bold" style="background: rgba(255, 255, 255, 0.2)">Cek Stok Fisik</a>
                    </div>
                </div>
                <div class="col-lg-5 pt-10 d-none d-lg-block">
                    <div class="bgi-no-repeat bgi-size-contain bgi-position-x-end h-225px" style="background-image:url('<?= base_url('template/assets/media/svg/illustrations/easy/5.svg') ?>')"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. CARD STATISTIK OPERASIONAL (DATA HARI INI) -->
    <div class="row g-5 mb-10">
        <div class="col-md-3">
            <div class="card card-flush shadow-sm h-100 py-5 bg-primary">
                <div class="card-body text-center">
                    <div class="text-white opacity-75 fs-7 fw-bold text-uppercase mb-2">Omzet Hari Ini</div>
                    <div class="fs-2qx fw-boldest text-white">Rp <?= number_format($omzet_today ?? 0,0,',','.') ?></div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-flush shadow-sm h-100 py-5">
                <div class="card-body text-center">
                    <div class="text-muted fs-7 fw-bold text-uppercase mb-2">Transaksi Hari Ini</div>
                    <div class="fs-2qx fw-boldest text-dark" data-kt-countup="true" data-kt-countup-value="<?= $total_trx ?? 0 ?>"><?= $total_trx ?? 0 ?></div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-flush shadow-sm h-100 py-5">
                <div class="card-body text-center">
                    <div class="text-muted fs-7 fw-bold text-uppercase mb-2">Stok Rendah</div>
                    <div class="fs-2qx fw-boldest text-warning"><?= count($stok_kritis ?? []) ?> <small class="fs-6 text-muted">Item</small></div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-flush shadow-sm h-100 py-5 <?= (($pending_opname ?? 0) > 0) ? 'bg-light-danger border border-danger' : '' ?>">
                <div class="card-body text-center">
                    <div class="text-muted fs-7 fw-bold text-uppercase mb-2">Approval Opname</div>
                    <div class="fs-2qx fw-boldest <?= (($pending_opname ?? 0) > 0) ? 'text-danger' : 'text-gray-400' ?>"><?= $pending_opname ?? 0 ?></div>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. INFORMASI KEUANGAN (ADMIN & DEVELOPER ONLY) -->
    <!-- 3. INFORMASI KEUANGAN (ADMIN & DEVELOPER ONLY) -->
    <?php if (!is_user()) : ?>
    <div class="row g-5 mb-10">
        <!-- Card 1: Total Omzet -->
        <div class="col-md-4">
            <div class="card card-flush shadow-sm h-100 py-5 bg-primary">
                <div class="card-body d-flex flex-column">
                    <span class="text-white opacity-75 fs-7 fw-bold text-uppercase">Total Omzet Bersih</span>
                    <span class="fs-2hx fw-boldest text-white">Rp <?= number_format($total_uang, 0, ',', '.') ?></span>
                    <span class="text-white opacity-50 fs-9 mt-1">Uang masuk setelah diskon</span>
                </div>
            </div>
        </div>
        <!-- Card 2: Laba Kotor -->
        <div class="col-md-4">
            <div class="card card-flush shadow-sm h-100 py-5 border border-dashed border-success">
                <div class="card-body d-flex flex-column">
                    <span class="text-success fs-7 fw-bold text-uppercase">Laba Kotor (Gross)</span>
                    <span class="fs-2hx fw-boldest text-success">Rp <?= number_format($laba_kotor, 0, ',', '.') ?></span>
                    <span class="text-muted fs-9 mt-1">Laba barang sebelum biaya operasional</span>
                </div>
            </div>
        </div>
        <!-- Card 3: Laba Bersih -->
        <div class="col-md-4">
            <div class="card card-flush shadow-sm h-100 py-5 bg-light-success border border-success">
                <div class="card-body d-flex flex-column">
                    <span class="text-gray-800 fs-7 fw-bold text-uppercase">Laba Bersih (Net)</span>
                    <span class="fs-2hx fw-boldest text-dark">Rp <?= number_format($laba_bersih, 0, ',', '.') ?></span>
                    <span class="text-muted fs-9 mt-1">Setelah dipotong biaya pengeluaran kasir</span>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
    <!-- 4. WIDGET ANALITIK (PRODUK TERLARIS & METODE PEMBAYARAN) -->
    <div class="row g-5 g-xl-10 mb-10">
        <!-- 5 PRODUK TERLARIS -->
        <div class="col-xl-6">
            <div class="card card-flush shadow-sm h-xl-100">
                <div class="card-header pt-7">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bold text-gray-800">5 Produk Terlaris</span>
                        <span class="text-muted mt-1 fw-semibold fs-7">Volume penjualan terbanyak bulan ini</span>
                    </h3>
                </div>
                <div class="card-body pt-5">
                    <?php if(!empty($top_products)): ?>
                        <?php foreach($top_products as $index => $tp): ?>
                        <div class="d-flex flex-stack mb-5">
                            <div class="d-flex align-items-center me-2">
                                <div class="symbol symbol-35px me-4">
                                    <span class="symbol-label bg-light-primary text-primary fw-bold"><?= $index + 1 ?></span>
                                </div>
                                <div class="d-flex flex-column">
                                    <span class="text-gray-800 fw-bold fs-6"><?= $tp['nama_barang'] ?></span>
                                    <span class="text-muted fs-8">Item Terjual</span>
                                </div>
                            </div>
                            <span class="badge badge-light-success fs-7 fw-bold"><?= $tp['total_qty'] ?> Qty</span>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="text-center py-10 text-muted italic">Belum ada data penjualan produk.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- DISTRIBUSI PEMBAYARAN (PIE CHART) -->
        <div class="col-xl-6">
            <div class="card card-flush shadow-sm h-xl-100">
                <div class="card-header pt-7">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bold text-gray-800">Metode Pembayaran</span>
                        <span class="text-muted mt-1 fw-semibold fs-7">Distribusi transaksi masuk hari ini</span>
                    </h3>
                </div>
                <div class="card-body d-flex flex-column justify-content-center">
                    <div id="kt_payment_chart" style="height: 300px;"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- 5. WIDGET STOK & AKTIVITAS -->
    <div class="row g-5 g-xl-10 mb-10">
        <div class="col-xl-4">
            <div class="card card-flush shadow-sm h-xl-100">
                <div class="card-header pt-7">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bold text-gray-800">Peringatan Stok</span>
                        <span class="text-muted mt-1 fw-semibold fs-7">Item dengan stok di bawah 10 pcs</span>
                    </h3>
                </div>
                <div class="card-body pt-5">
                    <?php if(empty($stok_kritis)): ?>
                        <div class="text-center py-10 text-muted">Semua stok unit aman.</div>
                    <?php else: ?>
                        <?php foreach($stok_kritis as $sk): ?>
                        <div class="d-flex flex-stack mb-5">
                            <div class="d-flex align-items-center me-2">
                                <div class="symbol symbol-40px me-3">
                                    <span class="symbol-label bg-light-danger text-danger fw-bold"><?= substr($sk['nama_barang'],0,1) ?></span>
                                </div>
                                <div class="d-flex flex-column">
                                    <span class="text-gray-800 fw-bold fs-7"><?= $sk['nama_barang'] ?></span>
                                    <span class="text-muted fs-8"><?= $sk['nama_unit'] ?></span>
                                </div>
                            </div>
                            <span class="badge badge-light-danger fw-bold"><?= $sk['jumlah'] ?> pcs</span>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-xl-8">
            <div class="card card-flush shadow-sm h-xl-100">
                <div class="card-header pt-7">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bold text-dark">Aktivitas Penjualan Terakhir</span>
                        <span class="text-muted mt-1 fw-semibold fs-7">5 Transaksi lunas terbaru</span>
                    </h3>
                    <div class="card-toolbar">
                        <a href="<?= base_url('penjualan') ?>" class="btn btn-sm btn-light-primary fw-bold ajax-link">Detail Riwayat</a>
                    </div>
                </div>
                <div class="card-body pt-0">
                    <div class="table-responsive">
                        <table class="table align-middle table-row-dashed fs-6 gy-3">
                            <thead>
                                <tr class="text-start text-gray-400 fw-bold fs-7 text-uppercase gs-0">
                                    <th>Invoice</th>
                                    <th>Kasir</th>
                                    <th class="text-end">Metode</th>
                                    <th class="text-end">Total Bayar</th>
                                </tr>
                            </thead>
                            <tbody class="fw-semibold text-gray-600">
                                <?php if(empty($recent_sales)): ?>
                                    <tr><td colspan="4" class="text-center py-5">Belum ada transaksi.</td></tr>
                                <?php else: ?>
                                    <?php foreach($recent_sales as $rs) : ?>
                                    <tr>
                                        <td><span class="text-gray-800 fw-bold">#<?= substr($rs['invoice_no'], -8) ?></span></td>
                                        <td><span class="fs-7"><?= $rs['nama_kasir'] ?? 'System' ?></span></td>
                                        <td class="text-end"><span class="badge badge-light-primary fw-bold"><?= $rs['payment_method'] ?></span></td>
                                        <td class="text-end text-dark fw-bold">Rp <?= number_format($rs['total_price'] - $rs['discount'], 0,',','.') ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Script Chart -->
    <script>
    var initDashboardComponents = function() {
        // 1. Re-init CountUp (Metronic Function)
        if (typeof KTComponents !== 'undefined') { KTComponents.init(); }
        
        // 2. PIE CHART METODE PEMBAYARAN
        var paymentElement = document.getElementById("kt_payment_chart");
        if (paymentElement && !paymentElement.getAttribute('data-kt-initialized')) {
            <?php 
                $labels = []; $values = [];
                foreach($payment_stats ?? [] as $ps) {
                    $labels[] = $ps['payment_method'];
                    $values[] = (int)$ps['total'];
                }
            ?>
            
            // Hanya render jika ada data
            if (<?= count($values) ?> > 0) {
                var options = {
                    series: <?= json_encode($values) ?>,
                    chart: { type: 'donut', height: 300 },
                    labels: <?= json_encode($labels) ?>,
                    colors: [KTUtil.getCssVariableValue('--bs-primary'), KTUtil.getCssVariableValue('--bs-success'), KTUtil.getCssVariableValue('--bs-info')],
                    legend: { position: 'bottom' },
                    dataLabels: { enabled: false },
                    plotOptions: { pie: { donut: { size: '70%' } } }
                };
                var chart = new ApexCharts(paymentElement, options);
                chart.render();
                paymentElement.setAttribute('data-kt-initialized', 'true');
            } else {
                paymentElement.innerHTML = '<div class="text-center text-muted py-10">Data pembayaran belum tersedia hari ini.</div>';
            }
        }
    }

    // Jalankan inisialisasi
    if (document.readyState === "complete") { 
        setTimeout(initDashboardComponents, 100); 
    } else { 
        document.addEventListener("DOMContentLoaded", initDashboardComponents); 
    }
</script>
<?= $this->endSection() ?>