<!DOCTYPE html>
<html lang="en">
<head>
    <!-- 1. TITLE DINAMIS -->
    <title><?= $title ?? 'Dashboard' ?> | <?= $meta['nama'] ?? 'POS SYSTEM' ?></title>
    
    <meta name="X-CSRF-TOKEN" content="<?= csrf_hash() ?>" />
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    
    <!-- 2. SEO META TAGS -->
    <meta name="description" content="<?= $meta['deskripsi'] ?? 'Sistem Manajemen Penjualan Terintegrasi' ?>" />
    <meta name="keywords" content="<?= $meta['nama'] ?? 'POS' ?>, Sistem Penjualan, Kasir Online, POS System, <?= $meta['author'] ?? '' ?>" />
    <meta name="author" content="<?= $meta['author'] ?? 'Admin' ?>" />
    <link rel="canonical" href="<?= current_url() ?>" />

    <!-- 3. OPEN GRAPH / SOCIAL MEDIA -->
    <meta property="og:locale" content="id_ID" />
    <meta property="og:type" content="website" />
    <meta property="og:title" content="<?= $title ?? 'Dashboard' ?> | <?= $meta['nama'] ?? 'POS SYSTEM' ?>" />
    <meta property="og:description" content="<?= $meta['deskripsi'] ?? 'Kelola stok dan transaksi secara real-time.' ?>" />
    <meta property="og:url" content="<?= current_url() ?>" />
    <meta property="og:site_name" content="<?= $meta['nama'] ?? 'POS SYSTEM' ?>" />
    <meta property="og:image" content="<?= base_url('Gambar 1.png') ?>" /> 

    <meta name="<?= csrf_token() ?>" content="<?= csrf_hash() ?>" class="csrf_token">

    <!-- 4. FAVICON -->
    <link rel="shortcut icon" href="<?= base_url('Gambar 2.png') ?>" />

    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700" />
    
    <!-- CSS (Metronic Core) -->
    <link href="<?= base_url('template/assets/plugins/global/plugins.bundle.css') ?>" rel="stylesheet" type="text/css" />
    <link href="<?= base_url('template/assets/css/style.bundle.css') ?>" rel="stylesheet" type="text/css" />
    <link href="<?= base_url('template/assets/plugins/custom/datatables/datatables.bundle.css') ?>" rel="stylesheet" type="text/css" />
    
    <!-- JQUERY -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        if (localStorage.getItem("data-bs-theme")) {
            document.documentElement.setAttribute("data-bs-theme", localStorage.getItem("data-bs-theme"));
        }
    </script>
</head> 

<body id="kt_body" class="header-fixed header-tablet-and-mobile-fixed aside-enabled aside-fixed">
    <div class="d-flex flex-column flex-root">
        <div class="page d-flex flex-row flex-column-fluid">

            <div class="wrapper d-flex flex-column flex-row-fluid" id="kt_wrapper">
                
                <!-- HEADER & NAVBAR -->
                <?= $this->include('partials/header'); ?>
                <?= $this->include('partials/navbar'); ?>

                <!-- MAIN CONTENT -->
                <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
                    <div class="container-xxl mt-10" id="ajax-content-target">
                        <?= $this->renderSection('content') ?>
                    </div>
                </div>

                <?= $this->include('partials/footer'); ?>
            </div>
        </div>
    </div>

    <!-- JS Global Bundle -->
    <!-- ... (bagian atas code Anda tetap sama) ... -->

    <!-- JS Global Bundle -->
    <!-- PERBAIKAN: Memastikan library dimuat dengan benar -->
    <script src="<?= base_url('template/assets/plugins/global/plugins.bundle.js') ?>"></script>
    <script src="<?= base_url('template/assets/js/scripts.bundle.js') ?>"></script>
    <script src="<?= base_url('template/assets/plugins/custom/datatables/datatables.bundle.js') ?>"></script>
    

    <script>
        $.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="X-CSRF-TOKEN"]').attr('content')
    }
});
$(document).ready(function() {
    // 1. Fungsi Utama Klik Menu
    $(document).on('click', '.menu-link, .ajax-link, .menu-item a', function(e) {
        let url = $(this).attr('href');
        
        if (!url || url === '#' || url.includes('javascript') || url.includes('logout') || $(this).attr('data-bs-toggle') === 'modal') {
            return;
        }

        e.preventDefault(); 
        $('#ajax-content-target').animate({ opacity: 0.5 }, 100);

        $.ajax({
            url: url,
            type: 'GET',
            dataType: 'html',
            success: function(response) {
                // Update Title
                let titleMatch = response.match(/<title>(.*?)<\/title>/);
                if (titleMatch) document.title = titleMatch[1];

                // Parse response
                let tempDom = $('<div>').append($.parseHTML(response, document, true)); // 'true' agar script ikut terbaca
                let htmlContent = tempDom.find('#ajax-content-target').html();

                if (htmlContent) {
                    // Update Konten
                    $('#ajax-content-target').html(htmlContent).animate({ opacity: 1 }, 100);
                    window.history.pushState({path: url}, '', url);
                    
                    $('.menu-link').removeClass('active');
                    $(`a[href="${url}"]`).addClass('active');

                    // --- KUNCI: RE-INITIALIZE SEMUANYA ---
                    setTimeout(function() {
                        try {
                            // 1. Inisialisasi Ulang Komponen Dasar Metronic
                            if (typeof KTMenu !== 'undefined') { KTMenu.createInstances(); }
                            if (typeof KTToggle !== 'undefined') { KTToggle.init(); }
                            if (typeof KTSwapper !== 'undefined') { KTSwapper.init(); }
                            if (typeof KTApp !== 'undefined' && KTApp.init) { KTApp.init(); }
                            if (typeof KTDrawer !== 'undefined') { KTDrawer.init(); }

                            // 2. RE-INIT PLUGINS (Select2, Tooltip, dll)
                            if ($.fn.select2) { $('[data-control="select2"]').select2(); }
                            
                            // 3. PAKSA JALANKAN SCRIPT DI HALAMAN TUJUAN
                            // Mencari fungsi inisialisasi yang mungkin ada di view (Master Barang, Kategori, dll)
                            if (typeof loadData === 'function') { loadData(); }
                            if (typeof initDataTableBarang === 'function') { initDataTableBarang(); }
                            if (typeof initHalamanBarang === 'function') { initHalamanBarang(); }

                            // 4. Tutup Sidebar otomatis jika di Mobile
                            let asideEl = document.querySelector("#kt_aside");
                            if (asideEl && typeof KTDrawer !== 'undefined') {
                                let drawer = KTDrawer.getInstance(asideEl);
                                if (drawer && drawer.isShown()) { drawer.hide(); }
                            }
                        } catch (err) {
                            console.warn("Re-init error: ", err);
                        }
                    }, 100); 

                } else {
                    window.location.href = url;
                }
            },
            error: function() { window.location.href = url; }
        });
    });

    window.onpopstate = function() { location.reload(); };

    $(document).on('click', '#kt_theme_mode_toggle', function(e) {
        e.preventDefault();
        let currentTheme = document.documentElement.getAttribute("data-bs-theme");
        let newTheme = (currentTheme === "dark") ? "light" : "dark";
        document.documentElement.setAttribute("data-bs-theme", newTheme);
        localStorage.setItem("data-bs-theme", newTheme);
    });
});

</script>
</body>
</html>