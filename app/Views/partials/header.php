<?php 
helper('auth'); 

// --- LOGIKA SOLUSI AVATAR (Taruh di sini) ---
$avatarName = user('avatar'); 
$uploadPath = 'uploads/avatars/'; // Folder tempat foto berada

// Cek apakah file ada di folder public/uploads/avatars/
if (!empty($avatarName) && is_file(FCPATH . $uploadPath . $avatarName)) {
    $finalAvatarUrl = base_url($uploadPath . $avatarName);
} else {
    // Jika file tidak ada/kosong, pakai gambar default dari template
    $finalAvatarUrl = base_url('template/assets/media/avatars/300-1.jpg');
}
// --------------------------------------------
?>

<!--begin::Header-->
<div id="kt_header" class="header" data-kt-sticky="true" data-kt-sticky-name="header" data-kt-sticky-offset="{default: '200px', lg: '300px'}">
    <!--begin::Container-->
    <div class="container-xxl d-flex flex-grow-1 flex-stack">
        
        <!--begin::Header Logo & Mobile Toggle-->
        <div class="d-flex align-items-center me-5">
            <div class="d-lg-none btn btn-icon btn-active-color-primary w-30px h-30px ms-n2 me-3" id="kt_header_menu_toggle">
                <i class="ki-duotone ki-abstract-14 fs-2">
                    <span class="path1"></span>
                    <span class="path2"></span>
                </i>
            </div>
            
            <a href="<?= base_url('dashboard') ?>" class="d-flex align-items-center">
                <img alt="Logo" src="<?= base_url('Gambar 2.png') ?>" class="h-100px h-lg-120px me-3" />
                <h1 class="text-dark fw-bolder fs-2qx my-1 d-none d-sm-block">Pos System</h1>
            </a>
        </div>

        <!--begin::Topbar-->
        <div class="d-flex align-items-center flex-shrink-0">

            <!-- Search, Theme Mode, dll (Dibiarkan tetap) -->
            <div id="app_header_search" class="d-flex align-items-center w-lg-250px me-2">
                <form class="d-none d-lg-block w-100 position-relative mb-2 mb-lg-0" autocomplete="off">
                    <i class="ki-duotone ki-magnifier fs-2 text-gray-700 position-absolute top-50 translate-middle-y ms-4">
                        <span class="path1"></span><span class="path2"></span>
                    </i>
                    <input type="text" class="form-control bg-transparent ps-13 fs-7 h-40px" name="search" value="" placeholder="Quick Search" />
                </form>
            </div>

            <div class="d-flex align-items-center ms-1">
                <a href="#" id="kt_theme_mode_toggle" class="btn btn-icon btn-color-gray-700 btn-active-color-primary btn-outline btn-active-bg-light w-30px h-30px w-lg-40px h-lg-40px">
                    <i class="ki-duotone ki-night-day theme-light-show fs-1">
                        <span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span><span class="path6"></span><span class="path7"></span><span class="path8"></span><span class="path9"></span><span class="path10"></span>
                    </i>
                    <i class="ki-duotone ki-moon theme-dark-show fs-1">
                        <span class="path1"></span><span class="path2"></span>
                    </i>
                </a>
            </div>
            
            <!--begin::User Info & Dropdown -->
            <div class="d-flex align-items-center ms-3 ms-lg-4" id="kt_header_user_menu_toggle">
                <div class="me-3 text-end d-none d-md-block">
                    <span class="text-muted fw-semibold d-block fs-8">Selamat Datang,</span>
                    <span class="text-gray-900 fw-bold d-block fs-7 header-user-name">
                        <?= user('full_name') ?? 'User' ?>
                    </span>
                </div>

                <div class="btn btn-icon btn-color-gray-700 btn-active-color-primary btn-outline btn-active-bg-light w-35px h-35px w-lg-40px h-lg-40px" 
                     data-kt-menu-trigger="click" 
                     data-kt-menu-attach="parent" 
                     data-kt-menu-placement="bottom-end">
                    <div class="symbol symbol-30px symbol-lg-40px">
                        <!-- PERBAIKAN: Menggunakan variabel $finalAvatarUrl yang sudah dicek -->
                        <img src="<?= $finalAvatarUrl ?>" alt="user" />
                    </div>
                </div>

                <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg menu-state-color fw-semibold py-4 fs-6 w-275px" data-kt-menu="true">
                    <div class="menu-item px-3">
                        <div class="menu-content d-flex align-items-center px-3">
                            <div class="symbol symbol-50px me-5">
                                <!-- PERBAIKAN: Menggunakan variabel yang sama agar konsisten -->
                                <img src="<?= $finalAvatarUrl ?>" class="header-user-avatar" alt="user" />
                            </div>
                            <div class="d-flex flex-column">
                                <div class="fw-bold d-flex align-items-center fs-5">
                                    <span class="header-user-name"><?= user('full_name') ?? 'User' ?></span>
                                    <span class="badge badge-light-success fw-bold fs-8 px-2 py-1 ms-2"><?= user('role_name') ?? 'User' ?></span>
                                </div>
                                <span class="fw-semibold text-muted fs-7 header-user-email">
                                    <?= user('email') ?? '' ?>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="separator my-2"></div>
                    <div class="menu-item px-5"><a href="<?= base_url('profile') ?>" class="menu-link px-5">Profil Saya</a></div>
                    <div class="menu-item px-5"><a href="<?= base_url('dashboard') ?>" class="menu-link px-5">My Dashboard</a></div>
                    <div class="separator my-2"></div>
                    <div class="menu-item px-5"><a href="<?= base_url('logout') ?>" class="menu-link px-5 text-danger">Keluar / Logout</a></div>
                </div>
            </div>

            <!-- Chat -->
            <div class="d-flex align-items-center ms-3 ms-lg-4">
                <div class="btn btn-icon btn-color-gray-700 btn-active-color-primary btn-outline btn-active-bg-light w-30px h-30px w-lg-40px h-lg-40px fw-bold position-relative">
                    3 
                    <span class="bullet bullet-dot bg-danger h-6px w-6px position-absolute translate-middle top-0 start-50 animation-blink"></span>
                </div>
            </div>

        </div>
    </div>

    <div class="separator"></div>

    <div class="header-menu-container container-xxl d-flex flex-stack h-lg-75px w-100" id="kt_header_nav">
        <div class="d-flex align-items-center">
            <span class="badge badge-light fw-bold fs-8 px-3 py-2"><?= $meta['version'] ?? 'v8.2.9' ?></span>
        </div>
    </div>
</div>
<!--end::Header-->