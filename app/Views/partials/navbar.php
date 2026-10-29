<?php 
/** 
 * Memastikan helper 'auth' dimuat. 
 */
helper('auth'); 
?>

<!--begin::Separator-->
<div class="separator"></div>
<!--end::Separator-->

<!--begin::Navbar Container-->
<div class="header-menu-container container-xxl d-flex flex-stack h-lg-75px w-100" id="kt_header_nav">
    
    <div class="header-menu flex-column flex-lg-row" 
         data-kt-drawer="true" 
         data-kt-drawer-name="header-menu" 
         data-kt-drawer-activate="{default: true, lg: false}" 
         data-kt-drawer-overlay="true" 
         data-kt-drawer-width="{default:'200px', '300px': '250px'}" 
         data-kt-drawer-direction="start" 
         data-kt-drawer-toggle="#kt_header_menu_toggle" 
         data-kt-swapper="true" 
         data-kt-swapper-mode="prepend" 
         data-kt-swapper-parent="{default: '#kt_body', lg: '#kt_header_nav'}">
        
        <div class="menu menu-rounded menu-column menu-lg-row menu-root-here-bg-desktop menu-active-bg menu-state-primary menu-title-gray-800 menu-arrow-gray-500 align-items-stretch flex-grow-1 my-5 my-lg-0 px-2 px-lg-0 fw-semibold fs-6" id="#kt_header_menu" data-kt-menu="true">
            
            <!-- DASHBOARD -->
            <div class="menu-item me-0 me-lg-2">
                <a href="<?= base_url('dashboard') ?>" class="menu-link py-3 <?= (url_is('dashboard*') ? 'active' : '') ?>">
                    <span class="menu-icon">
                        <i class="ki-duotone ki-element-11 fs-2">
                            <span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span>
                        </i>
                    </span>
                    <span class="menu-title">Dashboard</span>
                </a>
            </div>

           <!-- ===================================================================
     GROUP: OPERASIONAL (Didesain Lebih Rapi dengan Sub-Section)
     =================================================================== -->
<?php if (canAny(['VIEW_BARANG','VIEW_STOK_KELUAR','VIEW_STOK_ADJUSTMENT','VIEW_PENJUALAN', 'VIEW_SHIFT'])) : ?>
<div data-kt-menu-trigger="click" data-kt-menu-placement="bottom-start" class="menu-item menu-lg-down-accordion me-0 me-lg-2">
    <span class="menu-link py-3 <?= (url_is('barang*') || url_is('stok-keluar*') || url_is('penjualan*') || url_is('stokadjustment*') || url_is('shift*') || url_is('kasir*') || url_is('stockopname*') ? 'active' : '') ?>">
        <span class="menu-title fw-bold fs-6">Operasional</span>
        <span class="menu-arrow d-lg-none"></span>
    </span>
    
    <div class="menu-sub menu-sub-lg-down-accordion menu-sub-lg-dropdown menu-rounded-0 py-lg-4 w-lg-250px">
        
        <!-- SECTION: CORE TRANSAKSI -->
        <div class="menu-item px-3">
            <div class="menu-content pb-2 px-3 align-items-center text-uppercase fs-9 fw-bold text-muted ls-1">Core Transaksi</div>
        </div>

        <!-- MENU: Kasir POS (Dibuat Menonjol) -->
        <div class="menu-item px-3">
            <a class="menu-link px-5 <?= (uri_string() == 'kasir') ? 'active' : '' ?>" href="<?= base_url('kasir') ?>">
                <span class="menu-icon">
                    <i class="ki-duotone ki-calculator fs-2 text-primary"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></i>
                </span>
                <span class="menu-title fw-bold text-primary">Kasir (Point of Sale)</span>
                <span class="menu-badge"><span class="badge badge-light-primary badge-circle fw-bold fs-9">HOT</span></span>
            </a>
        </div>

        <!-- MENU: Manajemen Shift -->
        
        <div class="menu-item px-3">
            <a class="menu-link px-5 <?= (url_is('shift*') && !url_is('shift/history')) ? 'active' : '' ?>" href="<?= base_url('shift') ?>">
                <span class="menu-icon"><i class="ki-duotone ki-wallet fs-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></i></span>
                <span class="menu-title">Buka/Tutup Shift</span>
            </a>
        </div>
        

        <div class="separator mb-3 opacity-75"></div>

        <!-- SECTION: KONTROL STOK -->
        <div class="menu-item px-3">
            <div class="menu-content pb-2 px-3 align-items-center text-uppercase fs-9 fw-bold text-muted ls-1">Inventori & Stok</div>
        </div>

        <!-- MENU: Stock Opname Retail -->
        <div class="menu-item px-3">
            <a class="menu-link px-5 <?= (url_is('stockopname*')) ? 'active' : '' ?>" href="<?= base_url('stockopname') ?>">
                <span class="menu-icon">
                    <i class="ki-duotone ki-delivery-3 fs-2 text-success"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                </span>
                <span class="menu-title">Stock Opname Retail</span>
            </a>
        </div>

        <div class="separator mb-3 opacity-75"></div>

        <!-- SECTION: RIWAYAT & LOG -->
        <div class="menu-item px-3">
            <div class="menu-content pb-2 px-3 align-items-center text-uppercase fs-9 fw-bold text-muted ls-1">Laporan & Riwayat</div>
        </div>

        <div class="menu-item px-3">
            <a class="menu-link px-5 <?= (uri_string() == 'shift/history') ? 'active' : '' ?>" href="<?= base_url('shift/history') ?>">
                <span class="menu-icon"><i class="ki-duotone ki-timer fs-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i></span>
                <span class="menu-title">Log History Shift</span>
            </a>
        </div>

        <div class="menu-item px-3">
            <a class="menu-link px-5 <?= (url_is('penjualan*')) ? 'active' : '' ?>" href="<?= base_url('penjualan') ?>">
                <span class="menu-icon"><i class="ki-duotone ki-scroll fs-2"><span class="path1"></span><span class="path2"></span></i></span>
                <span class="menu-title">Data Penjualan</span>
            </a>
        </div>

        <!-- ===================================================================
     GROUP: ANALITIK & LAPORAN (Premium Style)
     =================================================================== -->
<div data-kt-menu-trigger="click" data-kt-menu-placement="bottom-start" class="menu-item menu-lg-down-accordion me-0 me-lg-2">
    <!-- Indikator Active jika URL mengandung kata 'laporan' -->
    <span class="menu-link py-3 <?= (url_is('laporan*') ? 'active' : '') ?>">
        <span class="menu-title fw-bold fs-6">Laporan</span>
        <span class="menu-arrow d-lg-none"></span>
    </span>
    
    <div class="menu-sub menu-sub-lg-down-accordion menu-sub-lg-dropdown menu-rounded-0 py-lg-4 w-lg-250px">
        
        <!-- SECTION: OPERASIONAL -->
        <div class="menu-item px-3">
            <div class="menu-content pb-2 px-3 align-items-center text-uppercase fs-9 fw-bold text-muted ls-1">Audit & Kasir</div>
        </div>

        <!-- MENU: Laporan Harian -->
        <div class="menu-item px-3">
            <a class="menu-link px-5 <?= (url_is('laporan/harian')) ? 'active' : '' ?>" href="<?= base_url('laporan/harian') ?>">
                <span class="menu-icon">
                    <i class="ki-duotone ki-calendar-tick fs-2 text-info">
                        <span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span><span class="path6"></span>
                    </i>
                </span>
                <span class="menu-title fw-bold">Rekonsiliasi Harian</span>
            </a>
        </div>

        <div class="separator mb-3 opacity-75"></div>

        <!-- SECTION: KEUANGAN -->
        <div class="menu-item px-3">
            <div class="menu-content pb-2 px-3 align-items-center text-uppercase fs-9 fw-bold text-muted ls-1">Performa Bisnis</div>
        </div>

        <!-- MENU: Laporan Bulanan -->
        <div class="menu-item px-3">
            <a class="menu-link px-5 <?= (url_is('laporan/bulanan')) ? 'active' : '' ?>" href="<?= base_url('laporan/bulanan') ?>">
                <span class="menu-icon">
                    <i class="ki-duotone ki-graph-up fs-2 text-success">
                        <span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span><span class="path6"></span>
                    </i>
                </span>
                <span class="menu-title fw-bold">Laba Rugi Bulanan</span>
                <span class="menu-badge"><span class="badge badge-light-success fw-bold fs-9">PROFIT</span></span>
            </a>
        </div>

        <!-- MENU: Laporan Stok (Optional - Jika Anda sudah buat query nilai aset tadi) -->
        <div class="menu-item px-3">
            <a class="menu-link px-5 <?= (url_is('laporan/stok')) ? 'active' : '' ?>" href="<?= base_url('laporan/stok') ?>">
                <span class="menu-icon">
                    <i class="ki-duotone ki-package fs-2 text-warning">
                        <span class="path1"></span><span class="path2"></span><span class="path3"></span>
                    </i>
                </span>
                <span class="menu-title">Nilai Aset Stok</span>
            </a>
        </div>

    </div>
</div>

    </div>
</div>
<?php endif; ?>

<!-- ===================================================================
     GROUP: CRM & LOYALITAS (Dibuat Lebih Eye-Catching)
     =================================================================== -->
<div data-kt-menu-trigger="click" data-kt-menu-placement="bottom-start" class="menu-item menu-lg-down-accordion me-0 me-lg-2">
    <span class="menu-link py-3 <?= (url_is('pelanggan*') ? 'active' : '') ?>">
        <span class="menu-title fw-bold fs-6">CRM & Member</span>
        <span class="menu-arrow d-lg-none"></span>
    </span>
    <div class="menu-sub menu-sub-lg-down-accordion menu-sub-lg-dropdown menu-rounded-0 py-lg-4 w-lg-250px">
        
        <div class="menu-item px-3">
            <div class="menu-content pb-2 px-3 align-items-center text-uppercase fs-9 fw-bold text-muted ls-1">Loyalty Program</div>
        </div>

        <div class="menu-item px-3">
            <a class="menu-link px-5 <?= (url_is('pelanggan*')) ? 'active' : '' ?>" href="<?= base_url('pelanggan') ?>">
                <span class="menu-icon">
                    <i class="ki-duotone ki-badge fs-2 text-danger"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span></i>
                </span>
                <span class="menu-title fw-bold">Database Member</span>
                <span class="menu-badge">
                    <span class="badge badge-light-danger fw-bold fs-9">POIN</span>
                </span>
            </a>
        </div>

        <div class="menu-item px-3">
    <a class="menu-link px-5 <?= (url_is('promo*')) ? 'active' : '' ?>" href="<?= base_url('promo') ?>">
        <span class="menu-icon"><i class="ki-duotone ki-discount fs-2 text-danger"><span class="path1"></span><span class="path2"></span></i></span>
        <span class="menu-title">Marketing & Promo</span>
    </a>
</div>
        
    </div>
</div>

            <!-- MENU DROPDOWN: LOGISTIK (Tetap) -->
            <?php if (canAny(['VIEW_BISNIS_UNIT','VIEW_KATEGORI','VIEW_MASTER_BARANG','VIEW_BARANG_MASUK','VIEW_STOK_TRANSFER','VIEW_RETUR','VIEW_INVENTORY'])) : ?>
            <div data-kt-menu-trigger="click" data-kt-menu-placement="bottom-start" class="menu-item menu-lg-down-accordion me-0 me-lg-2">
                <span class="menu-link py-3 <?= (url_is('bisnisunit*') || url_is('kategori*') || url_is('master-barang*') || url_is('barangmasuk*') || url_is('stoktransfer*') || url_is('inventory*') || url_is('retur*') ? 'active' : '') ?>">
                    <span class="menu-title">Logistik</span>
                    <span class="menu-arrow d-lg-none"></span>
                </span>
                <div class="menu-sub menu-sub-lg-down-accordion menu-sub-lg-dropdown menu-rounded-0 py-lg-4 w-lg-225px">
                    
                    <?php if (can('VIEW_BISNIS_UNIT')) : ?>
                    <div class="menu-item px-3">
                        <a href="<?= base_url('bisnisunit') ?>" class="menu-link px-5 <?= (url_is('bisnisunit*') ? 'active' : '') ?>">
                            <span class="menu-icon"><i class="ki-duotone ki-abstract-26 fs-2"><span class="path1"></span><span class="path2"></span></i></span>
                            <span class="menu-title">Bisnis Unit</span>
                        </a>
                    </div>
                    <?php endif; ?>

                    <?php if (can('VIEW_KATEGORI')) : ?>
                    <div class="menu-item px-3">
                        <a href="<?= base_url('kategori') ?>" class="menu-link px-5 <?= (url_is('kategori*') ? 'active' : '') ?>">
                            <span class="menu-icon"><i class="ki-duotone ki-category fs-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></i></span>
                            <span class="menu-title">Item Kategori</span>
                        </a>
                    </div>
                    <?php endif; ?>

                    <?php if (can('VIEW_MASTER_BARANG')) : ?>
                    <div class="menu-item px-3">
                        <a href="<?= base_url('master-barang') ?>" class="menu-link px-5 <?= (url_is('master-barang*') ? 'active' : '') ?>">
                            <span class="menu-icon"><i class="ki-duotone ki-briefcase fs-2"><span class="path1"></span><span class="path2"></span></i></span>
                            <span class="menu-title">Master Barang</span>
                        </a>
                    </div>
                    <?php endif; ?>

                    <?php if (can('VIEW_BARANG_MASUK')) : ?>
                    <div class="menu-item px-3">
                        <a href="<?= base_url('barangmasuk') ?>" class="menu-link px-5 <?= (url_is('barangmasuk*') ? 'active' : '') ?>">
                            <span class="menu-icon"><i class="ki-duotone ki-cloud-download fs-2"><span class="path1"></span><span class="path2"></span></i></span>
                            <span class="menu-title">Barang Masuk</span>
                        </a>
                    </div>
                    <?php endif; ?>

                    <?php if (can('VIEW_STOK_TRANSFER')) : ?>
                    <div class="menu-item px-3">
                        <a href="<?= base_url('stoktransfer') ?>" class="menu-link px-5 <?= (url_is('stoktransfer*') ? 'active' : '') ?>">
                            <span class="menu-icon"><i class="ki-duotone ki-arrows-loop fs-2"><span class="path1"></span><span class="path2"></span></i></span>
                            <span class="menu-title">Stok Transfer (SIT)</span>
                        </a>
                    </div>
                    <?php endif; ?>

                    <?php if (can('VIEW_RETUR')) : ?>
                    <div class="menu-item px-3">
                        <a href="<?= base_url('retur') ?>" class="menu-link px-5 <?= (url_is('retur*') ? 'active' : '') ?>">
                            <span class="menu-icon"><i class="ki-duotone ki-arrow-right-left fs-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i></span>
                            <span class="menu-title">Retur Barang</span>
                        </a>
                    </div>
                    <?php endif; ?>

                    <?php if (can('VIEW_INVENTORY')) : ?>
                    <div class="menu-item px-3 border-top mt-2">
                        <a href="<?= base_url('inventory') ?>" class="menu-link px-5 <?= (url_is('inventory*') ? 'active' : '') ?>">
                            <span class="menu-icon"><i class="ki-duotone ki-map fs-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i></span>
                            <span class="menu-title">Stock Map</span>
                        </a>
                    </div>
                    <?php endif; ?>

                </div>
            </div>
            <?php endif; ?>

            <!-- MENU DROPDOWN: PENGATURAN -->
            <?php if (canAny(['VIEW_USERS','VIEW_META','VIEW_CONTACT','VIEW_HAK_AKSES'])) : ?>
            <div data-kt-menu-trigger="click" data-kt-menu-placement="bottom-start" class="menu-item menu-lg-down-accordion me-0 me-lg-2">
                <span class="menu-link py-3 <?= (url_is('users*') || url_is('meta*') || url_is('contact*') || url_is('hak-akses*') ? 'active' : '') ?>">
                    <span class="menu-title">Sistem</span>
                    <span class="menu-arrow d-lg-none"></span>
                </span>
                <div class="menu-sub menu-sub-lg-down-accordion menu-sub-lg-dropdown menu-rounded-0 py-lg-4 w-lg-225px">
                    
                    <?php if (can('VIEW_USERS')) : ?>
                    <div class="menu-item px-3">
                        <a href="<?= base_url('users') ?>" class="menu-link px-5 <?= (url_is('users*') ? 'active' : '') ?>">
                            <span class="menu-icon"><i class="ki-duotone ki-people fs-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span></i></span>
                            <span class="menu-title">Manajemen User</span>
                        </a>
                    </div>
                    <?php endif; ?>

                    <?php if (can('VIEW_META')) : ?>
                    <div class="menu-item px-3">
                        <a href="<?= base_url('meta') ?>" class="menu-link px-5 <?= (url_is('meta*') ? 'active' : '') ?>">
                            <span class="menu-icon"><i class="ki-duotone ki-setting-2 fs-2"><span class="path1"></span><span class="path2"></span></i></span>
                            <span class="menu-title">App Settings</span>
                        </a>
                    </div>
                    <?php endif; ?>

                    <?php if (can('VIEW_CONTACT')) : ?>
                    <div class="menu-item px-3">
                        <a href="<?= base_url('contact') ?>" class="menu-link px-5 <?= (url_is('contact*') ? 'active' : '') ?>">
                            <span class="menu-icon"><i class="ki-duotone ki-phone fs-2"><span class="path1"></span><span class="path2"></span></i></span>
                            <span class="menu-title">Info Kontak</span>
                        </a>
                    </div>
                    <?php endif; ?>

                    <?php if (can('VIEW_HAK_AKSES')) : ?>
                    <div class="menu-item px-3 border-top mt-2">
                        <a href="<?= base_url('hak-akses') ?>" class="menu-link px-5 <?= (url_is('hak-akses*') ? 'active' : '') ?>">
                            <span class="menu-icon"><i class="ki-duotone ki-shield-tick fs-2"><span class="path1"></span><span class="path2"></span></i></span>
                            <span class="menu-title">Matriks Hak Akses</span>
                        </a>
                    </div>
                    <?php endif; ?>

                </div>
            </div>
            <?php endif; ?>

        </div>
    </div>
</div>