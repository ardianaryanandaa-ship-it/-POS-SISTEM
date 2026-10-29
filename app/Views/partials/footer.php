<!--begin::Footer-->
<div class="footer py-4 d-flex flex-lg-column" id="kt_footer">
    <!--begin::Container-->
    <div class="container-xxl d-flex flex-column flex-md-row align-items-center justify-content-between">
        <!--begin::Copyright-->
        <div class="text-gray-900 order-2 order-md-1">
            <span class="text-muted fw-semibold me-1"><?= date('Y') ?>&copy;</span>
            <!-- Nama Aplikasi -->
            <span class="text-gray-800 fw-bold me-1"><?= $meta['nama'] ?? 'Penjualan' ?></span>
            <!-- Deskripsi Aplikasi -->
            <span class="text-muted fs-8 fw-semibold me-2">- <?= $meta['deskripsi'] ?? '' ?></span>
            <!-- Developer -->
            <span class="text-muted fw-semibold">- Developed by <?= $meta['developer'] ?? 'Team Developer' ?></span>
        </div>
        <!--end::Copyright-->
        
        <!--begin::Menu-->
        <ul class="menu menu-gray-600 menu-hover-primary fw-semibold order-1">
            <li class="menu-item">
                <span class="menu-link px-2">Email: <?= $contact['email'] ?? '-' ?></span>
            </li>
            <li class="menu-item">
                <span class="menu-link px-2">Telp: <?= $contact['telepon'] ?? '-' ?></span>
            </li>
            <li class="menu-item">
                <span class="menu-link px-2">Web: <?= $contact['website'] ?? '-' ?></span>
            </li>
        </ul>
        <!--end::Menu-->
    </div>
    <!--end::Container-->
</div>
<!--end::Footer-->

<!--begin::Scrolltop-->
<div id="kt_scrolltop" class="scrolltop" data-kt-scrolltop="true">
    <i class="ki-duotone ki-arrow-up">
        <span class="path1"></span>
        <span class="path2"></span>
    </i>
</div>
<!--end::Scrolltop-->

<!--begin::Javascript-->
<script>var hostUrl = "<?= base_url('template/assets/') ?>";</script>

<!--begin::Global Javascript Bundle-->
<script src="<?= base_url('template/assets/plugins/global/plugins.bundle.js') ?>"></script>
<script src="<?= base_url('template/assets/js/scripts.bundle.js') ?>"></script>

<!--begin::Vendors Javascript-->
<script src="<?= base_url('template/assets/plugins/custom/datatables/datatables.bundle.js') ?>"></script>

<!--begin::Custom Javascript-->
<script src="<?= base_url('template/assets/js/widgets.bundle.js') ?>"></script>
<script src="<?= base_url('template/assets/js/custom/widgets.js') ?>"></script>

<script>
$(document).ready(function() {
    // RE-INITIALIZE CHART & COUNTUP SAAT PINDAH HALAMAN VIA AJAX
    $(document).ajaxComplete(function() {
        // Jalankan Chart Dashboard jika fungsi initChart ada
        if (typeof initChart === 'function') {
            initChart();
        }
        
        // Jalankan ulang CountUp Metronic (Angka bergerak)
        $('[data-kt-countup="true"]').each(function() {
            if (!$(this).hasClass('countup-initialized')) {
                const value = $(this).attr('data-kt-countup-value');
                const options = { startVal: 0, duration: 2 };
                const countUp = new countUp.CountUp(this, value, options);
                countUp.start();
                $(this).addClass('countup-initialized');
            }
        });

        // Re-init dropdown Metronic
        KTMenu.createInstances();
    });
});
</script>
<!--end::Javascript-->