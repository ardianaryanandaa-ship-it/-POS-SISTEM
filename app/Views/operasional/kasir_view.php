<?= $this->extend('layout/metronic'); ?>
<?= $this->section('content'); ?>

<style>
    #kt_app_content_container { margin-top: -30px !important; }
    .product-container { height: 75vh; overflow-y: auto; overflow-x: hidden; padding-right: 10px; }
    .cart-container { height: 50vh; overflow-y: auto; }
    .product-card { cursor: pointer; transition: all 0.2s ease; border: 1px solid #f1f1f1; position: relative; }
    .product-card:hover { transform: translateY(-3px); border-color: #009ef7; box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
    .product-card .badge-stok { position: absolute; top: 10px; right: 10px; z-index: 10; }
    
    /* Animasi Scanner Laser Kamera */
    @keyframes scanLaser {
        0% { top: 0%; }
        50% { top: 100%; }
        100% { top: 0%; }
    }
    .scanner-laser {
        animation: scanLaser 2s infinite linear;
    }
    /* Container Video Scanner */
    #reader {
        width: 100% !important;
        border: none !important;
        background: #000;
    }

    /* Memastikan video memenuhi kotak */
    #reader video {
        width: 100% !important;
        height: 260px !important;
        object-fit: cover !important;
    }

    /* Penyesuaian Laser agar responsif di atas video */
    .scanner-laser-container {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: 10;
        pointer-events: none;
    }

    .scanner-laser {
        position: absolute;
        height: 3px;
        width: 100%;
        background: rgba(255, 0, 0, 0.8);
        box-shadow: 0 0 10px 3px rgba(255, 0, 0, 0.5);
        animation: scanLaser 2s infinite linear;
    }

    .variant-option-card {
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .variant-option-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 18px 36px rgba(0, 0, 0, 0.08);
    }

    .variant-option-card.active {
        border-color: #009ef7;
        background: #eef5ff;
    }

    .variant-option-card.disabled {
        opacity: 0.55;
        cursor: not-allowed;
    }

    .product-card.disabled {
        opacity: 0.65;
        cursor: not-allowed;
    }

    .product-card .product-variant-summary {
        min-height: 46px;
    }

    /* Menghilangkan tombol-tombol bawaan library agar tampilan bersih */
    #reader__dashboard, #reader__camera_selection {
        display: none !important;
    }

    .product-container { 
        display: flex;
        flex-wrap: wrap;
        align-content: start; /* Kunci agar item tidak melar */
    }

    /* Membuat kartu menjadi kotak persegi sempurna */
    .product-card {
        aspect-ratio: 1 / 1; /* LEBAR = TINGGI */
        width: 100%;
        height: auto !important;
        display: flex;
        flex-direction: column;
        padding: 8px !important;
        border-radius: 12px;
        background: #ffffff;
        border: 1px solid #f1f1f1;
        position: relative;
        overflow: hidden;
        justify-content: space-between;
    }

    /* Wadah Gambar */
    .product-img-wrapper {
        width: 100%;
        height: 55%; /* Gambar mengambil 55% ruang kotak */
        background: #f8f9fa;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        overflow: hidden;
    }

    .product-img-wrapper img {
        width: 100%;
        height: 100%;
        object-fit: contain; /* Gambar utuh, tidak gepeng */
    }

    /* Area Teks di bawah gambar */
    .product-info-box {
        height: 40%;
        display: flex;
        flex-direction: column;
        justify-content: center;
        text-align: center;
        padding-top: 5px;
    }

    .product-title-box {
        font-size: 0.8rem;
        font-weight: 700;
        line-height: 1.2;
        display: -webkit-box;
        -webkit-line-clamp: 2; /* Maksimal 2 baris nama */
        -webkit-box-orient: vertical;
        overflow: hidden;
        color: #333;
    }

    .price-text {
        font-size: 0.85rem;
        font-weight: 800;
        color: #009ef7;
        margin-top: 2px;
    }

    .badge-container {
        position: absolute;
        top: 5px;
        right: 5px;
        z-index: 10;
        display: flex;
        flex-direction: column;
        gap: 2px;
    }

    .badge-item {
        font-size: 0.6rem !important;
        padding: 2px 5px !important;
    }
</style>

<!-- BAGIAN 1: JIKA UNIT BELUM DIPILIH -->
<?php if (!session()->get('selected_unit')): ?>
    <div class="text-center mb-10">
        <h1 class="text-dark fw-bolder mb-3">Selamat Datang, Kasir</h1>
        <div class="text-gray-400 fw-bold fs-5">Silakan pilih Unit Bisnis tempat Anda bertugas saat ini.</div>
    </div>
    <div class="row g-6 justify-content-center">
        <?php foreach($units as $u): ?>
        <div class="col-md-4">
            <div class="card shadow-sm border-hover-primary h-100">
                <div class="card-body text-center p-10">
                    <div class="symbol symbol-65px symbol-circle mb-5">
                        <span class="symbol-label bg-light-primary">
                            <i class="ki-duotone ki-shop fs-3x text-primary"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span></i>
                        </span>
                    </div>
                    <h3 class="fw-boldest text-gray-800"><?= $u['nama_unit'] ?></h3>
                    <div class="text-muted fs-7 mb-7"><?= $u['wilayah'] ?> (<?= $u['jenis_unit'] ?>)</div>
                    <a href="<?= base_url('kasir/set_unit/'.$u['kode_unit']) ?>" class="btn btn-primary w-100 fw-bold">Mulai Jualan di Sini</a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

<?php else: ?>
    <input type="hidden" id="csrf_token_pos" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>">

    <div class="row g-5">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header border-0 pt-5">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bold fs-3 mb-1">Katalog Produk</span>
                        <span class="text-muted mt-1 fw-semibold fs-7">Unit Aktif: <b class="text-primary"><?= $namaUnit ?></b></span>
                    </h3>
                    <div class="card-toolbar">
                        <a href="<?= base_url('kasir/ganti_unit') ?>" class="btn btn-sm btn-light-warning me-3">Ganti Unit</a>
                        
                        <!-- Input Scan Barcode (F2) -->
                        <div class="d-flex align-items-center position-relative my-1 me-3">
                            <i class="ki-duotone ki-barcode fs-3 position-absolute ms-5">
                                <span class="path1"></span><span class="path2"></span><span class="path3"></span>
                                <span class="path4"></span><span class="path5"></span><span class="path6"></span>
                                <span class="path7"></span><span class="path8"></span>
                            </i>
                            <input type="text" id="scan_barcode" class="form-control form-control-solid border border-primary w-180px ps-13 me-10" placeholder="Scan Barcode..." autocomplete="off" autofocus />
                            <button type="button" class="btn btn-icon btn-light-primary border border-primary position-absolute end-0 me-2" onclick="openCameraScanner()" title="Scan lewat Kamera">
                                <i class="ki-duotone ki-scan-barcode"><span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                    <span class="path4"></span>
                                    <span class="path5"></span>
                                    <span class="path6"></span>
                                    <span class="path7"></span>
                                    <span class="path8"></span></i>
                            </button>
                        </div>

                        <div class="d-flex align-items-center position-relative my-1">
                            <i class="ki-duotone ki-magnifier fs-3 position-absolute ms-5"><span class="path1"></span><span class="path2"></span></i>
                            <input type="text" id="search_produk" class="form-control form-control-solid w-150px ps-13" placeholder="Cari..." />
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Shopee-like Category Tabs -->
                    <div class="d-flex flex-nowrap overflow-auto pb-4 mb-4 gap-2 category-scroll-container" style="scrollbar-width: none; -ms-overflow-style: none;">
                        <button type="button" class="btn btn-sm btn-light-primary active px-4 py-2 text-nowrap rounded-pill category-tab" data-id="all">
                            <i class="ki-duotone ki-category fs-4 me-1"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></i>
                            Semua Produk
                        </button>
                        
                        <?php if (isset($categories)): ?>
                            <?php foreach($categories as $cat): ?>
                                <?php
                                    $catNameLower = strtolower($cat['nama_kategori']);
                                    $icon = 'ki-tag'; // default
                                    if (strpos($catNameLower, 'makanan') !== false || strpos($catNameLower, 'snack') !== false || strpos($catNameLower, 'cemilan') !== false || strpos($catNameLower, 'roti') !== false) {
                                        $icon = 'ki-coffee';
                                    } elseif (strpos($catNameLower, 'minuman') !== false || strpos($catNameLower, 'drink') !== false || strpos($catNameLower, 'kopi') !== false || strpos($catNameLower, 'teh') !== false) {
                                        $icon = 'ki-cup';
                                    } elseif (strpos($catNameLower, 'elektronik') !== false || strpos($catNameLower, 'gadget') !== false || strpos($catNameLower, 'hp') !== false) {
                                        $icon = 'ki-screen';
                                    } elseif (strpos($catNameLower, 'pakaian') !== false || strpos($catNameLower, 'baju') !== false || strpos($catNameLower, 'fashion') !== false) {
                                        $icon = 'ki-lots-shopping';
                                    } elseif (strpos($catNameLower, 'obat') !== false || strpos($catNameLower, 'sehat') !== false || strpos($catNameLower, 'herbal') !== false) {
                                        $icon = 'ki-heart';
                                    } elseif (strpos($catNameLower, 'kosmetik') !== false || strpos($catNameLower, 'cantik') !== false || strpos($catNameLower, 'makeup') !== false) {
                                        $icon = 'ki-face-id';
                                    }
                                ?>
                                <button type="button" class="btn btn-sm btn-outline btn-outline-dashed btn-outline-secondary px-4 py-2 text-nowrap rounded-pill category-tab" data-id="<?= $cat['id_kategori'] ?>">
                                    <i class="ki-duotone <?= $icon ?> fs-4 me-1">
                                        <span class="path1"></span><span class="path2"></span><span class="path3"></span>
                                        <span class="path4"></span><span class="path5"></span><span class="path6"></span>
                                    </i>
                                    <?= $cat['nama_kategori'] ?>
                                </button>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>

                    <div class="row g-5 product-container" id="product_grid">
                        <?php foreach($products as $p): ?>
                            <?php 
                                // Tentukan harga mana yang dipakai (Promo vs Normal)
                                $hargaFinal = (isset($p['harga_promo']) && $p['harga_promo'] < $p['harga_jual']) ? $p['harga_promo'] : $p['harga_jual'];
                                $isPromo = ($p['harga_jual'] > $hargaFinal);
                            ?>
                            <div class="col-md-3 col-6 product-item" data-nama="<?= strtolower($p['nama_barang']) ?>" data-kode="<?= $p['kode_barang'] ?>" data-kategori="<?= $p['id_kategori'] ?>">
                                <div class="card product-card h-100 shadow-none bg-light" 
                                     onclick="addToCart('<?= $p['kode_barang'] ?>', '<?= addslashes($p['nama_barang']) ?>', <?= $hargaFinal ?>, <?= $p['stok'] ?>)">
                                    
                                    <!-- BADGE STOK & PROMO -->
                                    <div class="badge-container">
                                        <?php if($isPromo): ?>
                                            <span class="badge badge-danger fw-bold fs-9 mb-1">PROMO</span><br>
                                        <?php endif; ?>
                                        <span class="badge badge-<?= $p['stok'] > 5 ? 'light-success' : 'light-danger' ?> fs-9">Stok: <?= $p['stok'] ?></span>
                                    </div>

                                    <div class="card-body p-4 text-center mt-5">
                                        <div class="mb-3 text-primary">
                                            <i class="ki-duotone ki-cube fs-3x"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                                        </div>
                                         <div class="fs-7 fw-bold text-gray-800 mb-1"><?= $p['nama_barang'] ?></div>
                                         <div class="text-muted fs-9 mb-2 d-flex align-items-center justify-content-center gap-1">
                                             <span>Kode: <?= $p['kode_barang'] ?></span>
                                             <span class="badge badge-light-success p-1 cursor-pointer" onclick="showProductBarcode(event, '<?= $p['kode_barang'] ?>', '<?= addslashes($p['nama_barang']) ?>')" title="Lihat Barcode">
                                                 <i class="ki-duotone ki-barcode fs-9 text-success p-0"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span><span class="path6"></span><span class="path7"></span><span class="path8"></span></i>
                                             </span>
                                         </div>
                                         
                                         <!-- LOGIKA HARGA CORET -->
                                        <div class="product-price">
                                            <?php if ($isPromo): ?>
                                                <span class="text-muted text-decoration-line-through fs-8">Rp <?= number_format($p['harga_jual'], 0, ',', '.') ?></span><br>
                                                <span class="fs-6 fw-boldest text-danger">Rp <?= number_format($hargaFinal, 0, ',', '.') ?></span>
                                            <?php else: ?>
                                                <div class="fs-6 fw-boldest text-primary">Rp <?= number_format($p['harga_jual'], 0, ',', '.') ?></div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- KOLOM KANAN: KERANJANG -->
        <div class="col-lg-4">
            <div class="card shadow-sm sticky-top" style="top: 100px; z-index: 5;">
                <div class="card-header border-0 pt-5">
                    <h3 class="card-title fw-bold text-gray-800">Keranjang</h3>
                    <div class="card-toolbar">
                        <button class="btn btn-sm btn-light-danger" onclick="clearCart()">Reset</button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="cart-container mb-5" id="cart_list">
                        <div class="text-center text-muted py-10" id="empty_cart_msg">
                            <i class="ki-duotone ki-cart fs-3x mb-3 text-gray-300"></i>
                            <p>Belum ada item dipilih</p>
                        </div>
                    </div>
                    <div class="separator separator-dashed my-4"></div>
                    <div class="d-flex flex-stack mb-5">
                        <span class="fw-bold text-gray-800 fs-3">Total:</span>
                        <span class="text-primary fw-boldest fs-2" id="display_total">Rp 0</span>
                    </div>
                    <button class="btn btn-success w-100 py-4 fs-3 fw-bold" id="btn_bayar_trigger" onclick="openPaymentModal()" disabled>BAYAR SEKARANG</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal_bayar" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered mw-500px">
        <div class="modal-content shadow-lg border-0">
            <!-- Header dengan style Metronic -->
            <div class="modal-header border-0 pb-0 justify-content-end">
                <div class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal">
                    <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                </div>
            </div>

            <div class="modal-body px-10 pb-10">
                <div class="text-center mb-10">
                    <h1 class="text-dark fw-bolder mb-3">Selesaikan Pembayaran</h1>
                    <div class="text-muted fw-semibold fs-6">Pastikan nominal dan pelanggan sudah sesuai</div>
                </div>

                <!-- RINGKASAN TAGIHAN (Lebih Elegan) -->
                <div class="bg-light-primary rounded-3 p-6 mb-8 border border-dashed border-primary">
                    <div class="d-flex flex-stack mb-3">
                        <span class="fw-semibold text-gray-600 fs-7">Subtotal Belanja</span>
                        <span class="fw-bold text-gray-800 fs-6" id="modal_subtotal_display">Rp 0</span>
                    </div>
                    <div class="d-flex flex-stack mb-3">
                        <span class="fw-semibold text-danger fs-7">Promo / Diskon</span>
                        <span class="fw-bold text-danger fs-6" id="display_diskon">- Rp 0</span>
                    </div>
                    <div class="separator border-primary opacity-25 mb-3"></div>
                    <div class="d-flex flex-stack">
                        <span class="fw-bolder text-gray-800 fs-4">Total Akhir</span>
                        <span class="fw-boldest text-primary fs-1" id="modal_total_display">Rp 0</span>
                    </div>
                </div>

                <!-- PILIH PELANGGAN -->
                <div class="fv-row mb-8">
                    <label class="fs-7 fw-bold text-uppercase text-muted mb-2 d-block">Identitas Pelanggan</label>
                    <div class="input-group input-group-solid">
                        <span class="input-group-text"><i class="ki-duotone ki-user fs-2"></i></span>
                        <div class="flex-grow-1">
                            <select id="id_pelanggan" class="form-select form-select-solid border-start-0 fw-bold" data-control="select2" data-placeholder="Umum (Non-Member)" data-dropdown-parent="#modal_bayar">
                                <option value="">-- Umum (Non-Member) --</option>
                                <?php foreach($pelanggan as $plg): ?>
                                    <option value="<?= $plg['id'] ?>"><?= $plg['nama_pelanggan'] ?> (<?= $plg['telepon'] ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <button type="button" class="btn btn-icon btn-primary" onclick="$('#modal_tambah_pelanggan').modal('show')">
                            <i class="ki-duotone ki-plus fs-2"></i>
                        </button>
                    </div>
                </div>

                <!-- METODE & NOMINAL -->
                <div class="row g-5 mb-8">
                    <div class="col-md-6">
                        <label class="fs-7 fw-bold text-uppercase text-muted mb-2">Metode</label>
                        <select id="payment_method" class="form-select form-select-solid fw-bold">
                            <option value="Tunai">Tunai / Cash</option>
                            <option value="QRIS">QRIS</option>
                            <option value="Debit">Debit Card</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="fs-7 fw-bold text-uppercase text-muted mb-2">Uang Diterima</label>
                        <input type="number" id="cash_received" class="form-control form-control-solid form-control-lg fs-2 fw-bold text-success text-end" placeholder="0">
                    </div>
                </div>

                <!-- QUICK CASH BUTTONS (Tombol Cepat) -->
                <div id="quick_cash_section" class="d-flex gap-2 mb-8 flex-wrap">
                    <button type="button" class="btn btn-sm btn-light-info fw-bold" onclick="setQuickCash('pas')">Uang Pas</button>
                    <button type="button" class="btn btn-sm btn-light fw-bold" onclick="setQuickCash(50000)">50.000</button>
                    <button type="button" class="btn btn-sm btn-light fw-bold" onclick="setQuickCash(100000)">100.000</button>
                </div>

                <!-- KEMBALIAN -->
                <div class="d-flex flex-stack bg-light-danger p-5 rounded-3 border border-dashed border-danger">
                    <div class="d-flex flex-column">
                        <span class="fw-bold text-danger fs-7 text-uppercase">Kembalian</span>
                        <span class="fs-1 fw-boldest text-danger" id="display_kembalian">Rp 0</span>
                    </div>
                    <i class="ki-duotone ki-wallet fs-3x text-danger opacity-50"><span class="path1"></span><span class="path2"></span></i>
                </div>
            </div>

            <div class="modal-footer flex-center border-0 pt-0">
                <button type="button" class="btn btn-light me-3 px-9" data-bs-dismiss="modal">Batal</button>
                <button type="button" id="btn_proses_checkout" class="btn btn-primary btn-lg flex-grow-1 fs-3 fw-bold shadow-sm" onclick="processCheckout()">
                    <span class="indicator-label">Selesaikan Transaksi</span>
                </button>
            </div>
        </div>
    </div>
</div>

    <!-- MODAL POP-UP DAFTAR MEMBER BARU -->
    <div class="modal fade" id="modal_tambah_pelanggan" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered mw-400px">
            <div class="modal-content shadow-lg">
                <div class="modal-header">
                    <h3 class="fw-bold">Daftar Member Baru</h3>
                    <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" onclick="$('#modal_tambah_pelanggan').modal('hide')">X</div>
                </div>
                <div class="modal-body">
                    <div class="fv-row mb-5">
                        <label class="form-label">Nama Lengkap</label>
                        <input type="text" id="new_nama" class="form-control form-control-solid" placeholder="Contoh: Budi Santoso">
                    </div>
                    <div class="fv-row mb-5">
                        <label class="form-label">Nomor WhatsApp</label>
                        <input type="number" id="new_telp" class="form-control form-control-solid" placeholder="0812xxxx">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" onclick="$('#modal_tambah_pelanggan').modal('hide')">Batal</button>
                    <button type="button" class="btn btn-primary" onclick="simpanMemberCepat()">Simpan & Gunakan</button>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL CAMERA SCANNER -->
    <!-- MODAL CAMERA SCANNER -->
<div class="modal fade" id="modal_camera_scanner" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered mw-450px">
        <div class="modal-content shadow-lg border-0">
            <div class="modal-header border-0 pb-0 justify-content-end">
                <button type="button" class="btn btn-icon btn-sm btn-active-color-primary" onclick="closeCameraScanner()">
                    <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                </button>
            </div>
            <div class="modal-body text-center px-10 pb-10">
                <h3 class="fw-boldest text-gray-800 mb-1">Scan Barcode</h3>
                <p class="text-muted fs-7 mb-7">Gunakan kamera belakang untuk hasil terbaik</p>

                <!-- Pilihan Kamera -->
                <div class="fv-row mb-5 text-start">
                    <label class="form-label fw-bold text-gray-700 fs-8 text-uppercase mb-2">Pilih Kamera</label>
                    <select id="camera_select" class="form-select form-select-solid fw-bold"></select>
                </div>

                <!-- Kontainer Scanner -->
                <div class="position-relative bg-dark rounded-3 overflow-hidden mb-8" style="height: 260px; border: 3px solid #009ef7;">
                    <!-- Laser Overlay -->
                    <div class="scanner-laser-container">
                        <div class="scanner-laser"></div>
                    </div>
                    
                    <!-- Library Video Target -->
                    <div id="reader"></div>
                </div>

                <button type="button" class="btn btn-light-danger w-100 fw-bold btn-lg" onclick="closeCameraScanner()">Batal / Tutup</button>
            </div>
        </div>
    </div>
</div>

    <!-- Modal Lihat Barcode di Kasir -->
    <div class="modal fade" id="modal_pos_barcode" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered mw-350px">
            <div class="modal-content shadow-lg border-0">
                <div class="modal-header border-0 pb-0 justify-content-end">
                    <button type="button" class="btn btn-icon btn-sm btn-active-color-primary" data-bs-dismiss="modal">
                        <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                    </button>
                </div>
                <div class="modal-body text-center px-10 pb-10">
                    <h3 class="fw-boldest text-gray-800 mb-1" id="posBarcodeTitle">Nama Barang</h3>
                    <p class="text-muted fs-7 mb-5" id="posBarcodeCode">Kode: -</p>
                    
                    <div class="bg-light p-5 rounded-3 d-flex justify-content-center align-items-center" style="min-height: 140px; border: 1px dashed #e4e6ef;">
                        <svg id="posBarcodePreview"></svg>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL PILIH VARIAN (Gaya Shopee) -->
    <div class="modal fade" id="modal_pilih_varian" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered mw-560px">
            <div class="modal-content shadow-lg border-0">
                <div class="modal-header border-0 pb-0 justify-content-end">
                    <button type="button" class="btn btn-icon btn-sm btn-active-color-primary" data-bs-dismiss="modal">
                        <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                    </button>
                </div>
                <div class="modal-body px-10 pb-10">
                    <div class="text-center mb-6">
                        <h2 class="fw-boldest text-gray-800 mb-1" id="variant_parent_name">Nama Barang</h2>
                        <div class="text-muted fw-semibold fs-7">Pilih tipe dan kapasitas agar pesanan sesuai.</div>
                    </div>

                    <!-- List Varian -->
                    <div class="fv-row mb-8">
                        <label class="fs-7 fw-bold text-uppercase text-muted mb-3 d-block">Pilihan Varian / Spesifikasi</label>
                        <div class="d-flex flex-column gap-3 overflow-auto" id="variant_options_container" style="max-height: 250px;">
                            <!-- Diisi oleh JS -->
                        </div>
                    </div>

                    <div class="row g-4 mb-7">
                        <div class="col-md-6">
                            <div class="bg-light rounded-3 p-4 h-100">
                                <div class="text-muted fs-8 text-uppercase mb-2">Harga Terpilih</div>
                                <div class="fw-boldest text-primary fs-2" id="variant_selected_price">Rp -</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="bg-light rounded-3 p-4 h-100">
                                <div class="text-muted fs-8 text-uppercase mb-2">Stok Tersedia</div>
                                <div class="fw-bold text-gray-800 fs-5" id="variant_selected_stock">Stok: -</div>
                            </div>
                        </div>
                    </div>

                    <div class="row g-4 mb-7">
                        <div class="col-6">
                            <div class="text-muted fs-8 text-uppercase mb-2">Harga Terpilih</div>
                            <div class="fw-boldest text-primary fs-4" id="variant_selected_price">Rp -</div>
                        </div>
                        <div class="col-6 text-end">
                            <div class="text-muted fs-8 text-uppercase mb-2">Stok Tersedia</div>
                            <div class="fw-bold text-gray-800 fs-5" id="variant_selected_stock">Stok: -</div>
                        </div>
                    </div>

                    <!-- Tombol Tutup (varian tidak digunakan) -->
                    <button type="button" class="btn btn-light w-100 py-4 fs-4 fw-bold shadow-sm" data-bs-dismiss="modal">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!-- Input file tersembunyi untuk proses upload -->
<?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
<script>

// Cek langsung di bawahnya
   
    // CARA AMAN: Cek dulu apakah variabel sudah ada di browser, jika belum baru buat.
    if (typeof cart === 'undefined') var cart = [];
    if (typeof totalBelanja === 'undefined') var totalBelanja = 0;
    if (typeof selectedCategory === 'undefined') var selectedCategory = 'all'; // Ini perbaikannya
    if (typeof nilaiDiskon === 'undefined') window.nilaiDiskon = 0;
    if (typeof totalAkhir === 'undefined') window.totalAkhir = 0;

    // Untuk productsList, kita timpa dengan data terbaru dari server
    var productsList = <?= isset($products) ? json_encode($products) : '[]' ?>;
    
    var groupedProducts = {};
    var productsList = <?= isset($products) ? json_encode($products) : '[]' ?>;
    
    // Simpan daftar produk dari PHP ke Javascript untuk pencarian barcode cepat

    var groupedProducts = {};

    function escapeHtml(text) {
        return text
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    function initGroupedProducts() {
        groupedProducts = {};
        productsList.forEach(p => {
            // Use explicit nama_varian when available; otherwise fallback to parsing nama_barang
            let baseName = (p.nama_barang || '').toString();
            let variantDetail = (p.nama_varian && p.nama_varian.toString().trim() !== '') ? p.nama_varian.toString() : null;

            if (!variantDetail) {
                // fallback parsing
                let parts = baseName.split(/\s*[-([{\]]\s*/);
                baseName = parts[0].trim();
                variantDetail = baseName === (p.nama_barang || '') ? 'Standar' : (p.nama_barang || '').substring(baseName.length).replace(/^[^a-zA-Z0-9]+|[^a-zA-Z0-9]+$/g, '').trim();
                if (!variantDetail) variantDetail = 'Standar';
            }

            let hargaFinal = (p.harga_promo && Number(p.harga_promo) < Number(p.harga_jual)) ? Number(p.harga_promo) : Number(p.harga_jual || 0);

            if (!groupedProducts[baseName]) {
                groupedProducts[baseName] = {
                    baseName: baseName,
                    minPrice: hargaFinal,
                    maxPrice: hargaFinal,
                    totalStok: Number(p.stok),
                    id_kategori: p.id_kategori,
                    parent_kode: p.parent_kode || null,
                    isPromo: (Number(p.harga_jual || 0) > hargaFinal),
                    variants: []
                };
            } else {
                if (hargaFinal < groupedProducts[baseName].minPrice) groupedProducts[baseName].minPrice = hargaFinal;
                if (hargaFinal > groupedProducts[baseName].maxPrice) groupedProducts[baseName].maxPrice = hargaFinal;
                groupedProducts[baseName].totalStok += Number(p.stok);
                if (Number(p.harga_jual || 0) > hargaFinal) groupedProducts[baseName].isPromo = true;
            }

            groupedProducts[baseName].variants.push({
                ...p,
                variantDetail: variantDetail,
                hargaFinal: hargaFinal
            });
        });
    }

    function renderProductGrid() {
    let searchVal = $('#search_produk').val().toLowerCase();
    let html = '';
    
    Object.keys(groupedProducts).forEach(baseName => {
        let group = groupedProducts[baseName];
        let matchesSearch = baseName.toLowerCase().includes(searchVal) || 
                            group.variants.some(v => v.kode_barang.toLowerCase().includes(searchVal));
        let matchesCategory = (selectedCategory === 'all' || group.id_kategori.toString() === selectedCategory);
        
        if (matchesSearch && matchesCategory) {
            let hasVariants = group.variants.length > 1;
            let singleVar = group.variants[0];
            
            // --- LOGIKA GAMBAR ---
            let imageHtml = '';
            if (singleVar.foto) {
                // Gunakan base_url yang bersih
                let imgUrl = "<?= base_url('uploads/produk/') ?>/" + singleVar.foto + "?t=" + new Date().getTime();
                
                // onerror: jika file tidak ada di folder, balikkan ke tampilan icon kubus
                imageHtml = `<img src="${imgUrl}" 
                                  style="width: 100%; height: 100%; object-fit: cover;" 
                                  onerror="this.parentElement.innerHTML='<i class=\"ki-duotone ki-picture fs-3x text-gray-300\"></i>`;
            } else {
                imageHtml = `<i class="ki-duotone ki-picture fs-3x text-gray-300"></i>`;
            }

            let priceDisplay = hasVariants ? `Rp ${group.minPrice.toLocaleString('id-ID')}` : `Rp ${singleVar.hargaFinal.toLocaleString('id-ID')}`;

            html += `
                <div class="col-md-3 col-6 mb-4">
                    <div class="card product-card shadow-sm ${group.totalStok <= 0 ? 'disabled' : ''}" 
                        data-base="${escapeHtml(group.baseName)}" 
                        data-variants="${group.variants.length}" 
                        data-singlecode="${singleVar.kode_barang}" 
                        data-single-name="${escapeHtml(singleVar.nama_barang)}" 
                        data-parent="${group.parent_kode || ''}" 
                        data-harga="${singleVar.hargaFinal}" 
                        data-stok="${group.totalStok}">
                        
                        <div class="badge-container">
                            ${group.isPromo ? '<span class="badge badge-danger badge-item">PROMO</span>' : ''}
                            ${hasVariants ? `<span class="badge badge-light-primary badge-item">${group.variants.length} Varian</span>` : ''}
                            <span class="badge badge-${group.totalStok > 5 ? 'light-success' : 'light-danger'} badge-item">Stok: ${group.totalStok}</span>
                        </div>

                        <div class="product-img-wrapper position-relative">
                            ${imageHtml}
                            <button type="button" class="btn btn-icon btn-sm btn-light-primary position-absolute bottom-0 end-0 m-1 rounded-circle shadow-sm" 
                                    style="width:25px; height:25px; z-index: 20;" 
                                    onclick="triggerUpload(event, '${singleVar.kode_barang}')">
                                <i class="ki-duotone ki-picture"><span class="path1"></span><span class="path2"></span></i>
                            </button>
                        </div>

                        <div class="product-info-box">
                            <div class="product-title-box">${baseName}</div>
                            <div class="text-muted" style="font-size: 0.65rem;">${hasVariants ? 'Pilih Varian' : singleVar.kode_barang}</div>
                            <div class="price-text">${priceDisplay}${hasVariants ? '+' : ''}</div>
                        </div>
                    </div>
                </div>
            `;
        }
    });
    
    $('#product_grid').html(html);

        // Re-bind Click Event (Tetap sama)
        $('#product_grid').off('click', '.product-card').on('click', '.product-card', function() {
            let variantsCount = Number($(this).data('variants')) || 0;
            let base = $(this).data('base');
            let parentKode = $(this).data('parent') || null;
            let stok = Number($(this).data('stok')) || 0;

            if (variantsCount > 1) {
                if (stok <= 0) {
                    Swal.fire('Stok Habis', 'Varian produk ini sudah habis.', 'warning');
                    return;
                }
                openVariantModal(base, parentKode);
                return;
            }

            if (stok <= 0) {
                Swal.fire('Stok Habis', 'Stok tidak mencukupi', 'warning');
                return;
            }

            let kode = $(this).data('singlecode');
            let nama = $(this).data('singleName') || base;
            let harga = Number($(this).data('harga')) || 0;
            addToCart(kode, nama, harga, stok);
        });
}

function openUploadModal(event, kode) {
    event.stopPropagation(); // Agar tidak masuk ke keranjang saat klik tombol kamera
    $('#upload_kode_barang').val(kode);
    $('#modal_upload_gambar').modal('show');
        }

        // Preview Gambar Sebelum Upload
        $('#file_foto').change(function() {
            const file = this.files[0];
            if (file) {
                let reader = new FileReader();
                reader.onload = function(e) { $('#img_preview').attr('src', e.target.result); }
                reader.readAsDataURL(file);
            }   
        });

        // Proses Kirim ke Server
        $('#form_upload_foto').submit(function(e) {
            e.preventDefault();
            let formData = new FormData(this);
            formData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');

            $('#btn_save_foto').prop('disabled', true).text('Uploading...');

            $.ajax({
                url: "<?= base_url('kasir/upload_foto') ?>",
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                dataType: "JSON",
                success: function(res) {
                    if(res.status === 'success') {
                        Swal.fire('Berhasil', 'Foto diperbarui', 'success').then(() => location.reload());
                    } else {
                        Swal.fire('Gagal', res.message, 'error');
                        $('#btn_save_foto').prop('disabled', false).text('Simpan Foto');
                    }
                }
            });
        });

        // --- PASTIKAN INI ADA DI LUAR $(document).ready ---

function triggerUpload(event, kodeBarang) {
    event.stopPropagation(); // Stop agar tidak masuk keranjang
    
    // Gunakan input file tersembunyi (pastikan elemen ini sudah Anda tambah di HTML)
    let fileInput = $('#global_file_input');
    if (fileInput.length === 0) {
        // Jika belum ada, buat otomatis
        $('body').append('<input type="file" id="global_file_input" style="display:none" accept="image/*">');
        fileInput = $('#global_file_input');
    }

    fileInput.val(''); // Reset file
    
    fileInput.off('change').on('change', function() {
        let file = this.files[0];
        if (!file) return;

        let formData = new FormData();
        formData.append('foto', file);
        formData.append('kode_barang', kodeBarang);
        
        // Ambil CSRF dari input hidden yang sudah ada di aplikasi Anda
        formData.append($('#csrf_token_pos').attr('name'), $('#csrf_token_pos').val());

        Swal.fire({
            title: 'Mengunggah...',
            allowOutsideClick: false,
            didOpen: () => { Swal.showLoading(); }
        });

        $.ajax({
            url: "<?= base_url('kasir/upload_gambar_barang') ?>",
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            dataType: "JSON",
            success: function(res) {
                Swal.close();
                if (res.status === 'success') {
                    // Update CSRF token jika server mengirim yang baru
                    if(res.csrf_hash) $('#csrf_token_pos').val(res.csrf_hash);
                    
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: 'Gambar diperbarui!',
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        location.reload(); // Reload agar data productsList terupdate
                    });
                } else {
                    Swal.fire('Gagal', res.message, 'error');
                }
            },
            error: function() {
                Swal.close();
                Swal.fire('Error', 'Gagal terhubung ke server', 'error');
            }
        });
    });

    fileInput.click();
}

// --- AKHIR FUNGSI ---

    function filterProducts() {
        renderProductGrid();
    }

    $(document).ready(function() {
        // Inisialisasi grouping varian & render catalog grid
        initGroupedProducts();
        renderProductGrid();

        // Fokuskan kolom barcode saat pertama kali dimuat
        $('#scan_barcode').focus();

        // Cari manual
        $('#search_produk').on('keyup', filterProducts);

        // Click handler untuk category tabs (gaya Shopee)
        $('.category-tab').on('click', function() {
            $('.category-tab').removeClass('active btn-light-primary').addClass('btn-outline btn-outline-dashed btn-outline-secondary');
            $(this).removeClass('btn-outline btn-outline-dashed btn-outline-secondary').addClass('active btn-light-primary');
            selectedCategory = $(this).data('id').toString();
            filterProducts();
        });

        // Scan barcode filter
        $('#scan_barcode').on('keydown keypress', function(e) {
            if (e.key === 'Enter' || e.keyCode === 13) {
                e.preventDefault();
                let code = $(this).val().trim();
                if (code !== '') {
                    scanProductByBarcode(code);
                }
            }
        });

        // Shortcut keyboard F2 untuk memfokuskan kembali kolom scan barcode
        $(document).on('keydown', function(e) {
            if (e.key === 'F2') {
                e.preventDefault();
                $('#scan_barcode').focus();
            }
        });

        $('#cash_received').on('input', calculateKembalian);
        
        // Listener Perubahan Pelanggan
        $('#id_pelanggan').on('change', function() {
            hitungDiskonOtomatis();
        });
    });

    // 1. Fungsi Pencarian Barang (Pastikan nama variabelnya productsList)
function scanProductByBarcode(code) {
    if (!code) return;
    let cleanCode = code.toString().replace(/\s+/g, '').trim().toLowerCase();

    // Cari di productsList yang sudah dideklarasikan di atas
    let product = productsList.find(p => p.kode_barang.toString().toLowerCase() === cleanCode);

    if (product) {
        if (selectedCategory !== 'all' && product.id_kategori.toString() !== selectedCategory.toString()) {
            Swal.fire({
                icon: 'warning',
                title: 'Kategori Tidak Sesuai',
                text: 'Produk ini tidak termasuk dalam kategori yang dipilih.',
                timer: 2000
            });
            return;
        }

            // langsung tambahkan produk yang discan ke keranjang

        let hargaFinal = (product.harga_promo && Number(product.harga_promo) < Number(product.harga_jual)) 
                         ? Number(product.harga_promo) 
                         : Number(product.harga_jual);
        
        addToCart(product.kode_barang, product.nama_barang, hargaFinal, Number(product.stok));
        $('#scan_barcode').val('');
        $('#scan_barcode').focus();
        playBeep();
        
        Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 1000
        }).fire({
            icon: 'success',
            title: product.nama_barang + " ditambahkan"
        });
    } else {
        Swal.fire({
            icon: 'error',
            title: 'Tidak Ditemukan',
            text: 'Kode: ' + cleanCode,
            timer: 2000
        });
    }

}

function decodeHtmlEntities(str) {
    if (!str) return str;
    return str.replace(/&amp;/g, '&').replace(/&lt;/g, '<').replace(/&gt;/g, '>').replace(/&quot;/g, '"').replace(/&#039;/g, "'");
}

function normalizeKey(s) {
    return (s || '').toString().toLowerCase().replace(/[^a-z0-9]/g, '');
}

function openVariantModal(baseName, parentKode) {
        if (!baseName && !parentKode) return;
        // Try direct lookup
        let group = groupedProducts[baseName] || null;
        if (!group) {
            // Try decoding HTML entities
            let dec = decodeHtmlEntities(baseName || '');
            group = groupedProducts[dec] || null;
        }
        if (!group && baseName) {
            // Fallback: case-insensitive/normalized key match
            let target = normalizeKey(decodeHtmlEntities(baseName || ''));
            for (let k in groupedProducts) {
                if (normalizeKey(k) === target) { group = groupedProducts[k]; break; }
            }
        }

        console.log('openVariantModal input:', baseName, 'parent:', parentKode, 'groupFound:', !!group);

        if (!group && parentKode) {
            // Fallback to server-side lookup by parent_kode
            $.ajax({
                url: "<?= base_url('master-barang/get_variants/') ?>" + parentKode,
                type: 'GET',
                dataType: 'JSON',
                success: function(res) {
                    let variantsData = [];
                    if (Array.isArray(res)) variantsData = res;
                    else if (res.variants) variantsData = res.variants;
                    else if (res.data) variantsData = res.data;
                    else if (res.status === 'success' && res.data) variantsData = res.data;

                    if (!variantsData || variantsData.length === 0) {
                        $('#variant_options_container').html('<div class="text-muted">Tidak ada varian untuk produk ini</div>');
                        $('#variant_parent_name').text(baseName || '');
                        $('#modal_pilih_varian').modal('show');
                        return;
                    }

                    // Build a temporary group and render
                    let tempGroup = {
                        baseName: baseName || (variantsData[0] && variantsData[0].nama_barang) || '',
                        variants: variantsData.map(v => ({
                            ...v,
                            variantDetail: v.nama_varian || v.nama_barang,
                            hargaFinal: (v.harga_promo && Number(v.harga_promo) < Number(v.harga_jual)) ? Number(v.harga_promo) : Number(v.harga_jual || 0)
                        }))
                    };

                    renderVariantGroup(tempGroup);
                },
                error: function() {
                    $('#variant_options_container').html('<div class="text-muted">Tidak ada varian untuk produk ini</div>');
                    $('#variant_parent_name').text(baseName || '');
                    $('#modal_pilih_varian').modal('show');
                }
            });
            return;
        }

        if (!group) {
            console.warn('Variant group not found for', baseName);
            $('#variant_options_container').html('<div class="text-muted">Tidak ada varian untuk produk ini</div>');
            $('#variant_parent_name').text(baseName || '');
            $('#modal_pilih_varian').modal('show');
            return;
        }

        renderVariantGroup(group);
    }

    function renderVariantGroup(group) {
        $('#variant_parent_name').text(group.baseName);
        $('#variant_options_container').empty();

        group.variants.forEach(function(v) {
            let disabled = Number(v.stok || 0) <= 0;
            let card = $(
                `<div class="card variant-option-card p-3 d-flex justify-content-between align-items-center ${disabled ? 'disabled' : ''}" data-kode="${v.kode_barang}" data-harga="${v.hargaFinal}" data-stok="${v.stok}">
                    <div>
                        <div class="fw-bold">${escapeHtml(v.variantDetail || v.nama_varian || v.nama_barang)}</div>
                        ${disabled ? '<div class="text-danger fs-8">Stok habis</div>' : ''}
                    </div>
                    <div class="text-end">
                        <div class="text-muted fs-8">Rp ${Number(v.hargaFinal).toLocaleString('id-ID')}</div>
                        <div class="fw-bold text-gray-800">Stok: ${v.stok}</div>
                    </div>
                </div>`
            );

            if (disabled) {
                card.on('click', function() {
                    Swal.fire('Stok Habis', 'Varian ini tidak tersedia.', 'warning');
                });
            } else {
                card.on('click', function() {
                    addToCart(v.kode_barang, v.nama_varian || v.nama_barang || v.variantDetail || '', v.hargaFinal, Number(v.stok || 0));
                    $('#modal_pilih_varian').modal('hide');

                    Swal.mixin({toast:true, position:'top-end', showConfirmButton:false, timer:1000}).fire({icon:'success', title: (v.nama_varian || v.nama_barang || v.variantDetail || '') + ' ditambahkan'});
                });
            }

            $('#variant_options_container').append(card);
        });

        $('#modal_pilih_varian').modal('show');
    }

    // Pilih varian berdasarkan kode, tambahkan ke keranjang
    function selectVariant(kode) {
        // Cari varian di productsList
        let prod = productsList.find(p => p.kode_barang === kode);
        if (!prod) {
            // Jika tidak ada di productsList, coba cari di groupedProducts
            for (let k in groupedProducts) {
                let found = groupedProducts[k].variants.find(v => v.kode_barang === kode);
                if (found) { prod = found; break; }
            }
        }
        if (!prod) return;

        let hargaFinal = (prod.harga_promo && Number(prod.harga_promo) < Number(prod.harga_jual)) ? Number(prod.harga_promo) : Number(prod.harga_jual || prod.hargaFinal || 0);
        addToCart(prod.kode_barang, prod.nama_barang, hargaFinal, Number(prod.stok || 0));
        $('#modal_pilih_varian').modal('hide');

        Swal.mixin({toast:true, position:'top-end', showConfirmButton:false, timer:1000}).fire({icon:'success', title: prod.nama_barang + ' ditambahkan'});
    }

function startCameraScanning(cameraId) {
    if (html5QrCode === null) {
        html5QrCode = new Html5Qrcode("reader");
    }

    const config = { 
        fps: 25,
        qrbox: { width: 300, height: 150 },
        aspectRatio: 1.0,
        experimentalFeatures: {
            useBarCodeDetectorIfSupported: true
        },
        formatsToSupport: [
            Html5QrcodeSupportedFormats.CODE_128,
            Html5QrcodeSupportedFormats.CODE_39,
            Html5QrcodeSupportedFormats.CODE_93,
            Html5QrcodeSupportedFormats.EAN_8,
            Html5QrcodeSupportedFormats.EAN_13,
            Html5QrcodeSupportedFormats.ITF,
            Html5QrcodeSupportedFormats.UPC_A,
            Html5QrcodeSupportedFormats.UPC_E
        ]
    };

    const startScanner = () => {
        html5QrCode.start(
            cameraId,
            config,
            (decodedText) => {
                console.log("Kamera Mendeteksi:", decodedText);
                playBeep();
                scanProductByBarcode(decodedText);
                closeCameraScanner();
                if (navigator.vibrate) navigator.vibrate(100);
            },
            (errorMessage) => {
                // ignore frame parse errors
            }
        ).catch(err => {
            console.error("Gagal start kamera:", err);
            Swal.fire('Error', 'Tidak dapat memulai scanner kamera.', 'error');
        });
    };

    if (html5QrCode.isScanning) {
        html5QrCode.stop().then(() => startScanner()).catch(() => startScanner());
    } else {
        startScanner();
    }
}

    function showProductBarcode(event, kode, nama) {
        event.stopPropagation(); // Mencegah terpicunya addToCart pada card click
        $('#posBarcodeTitle').text(nama);
        $('#posBarcodeCode').text('Kode: ' + kode);
        $('#modal_pos_barcode').modal('show');
        
        setTimeout(function() {
            JsBarcode("#posBarcodePreview", kode, {
                format: "CODE128",
                width: 2,
                height: 70,
                displayValue: true,
                fontSize: 13
            });
        }, 200);
    }

    function addToCart(kode, nama, harga, stok) {
        if (Number(stok) <= 0) {
            Swal.fire('Stok Habis', 'Stok tidak mencukupi', 'warning');
            return;
        }
        let find = cart.find(item => item.kode_barang === kode);
        if (find) {
            if (find.qty >= stok) { Swal.fire('Stok Terbatas', 'Stok tidak mencukupi', 'warning'); return; }
            find.qty += 1;
        } else {
            cart.push({ kode_barang: kode, nama: nama, harga: Number(harga), qty: 1, max_stok: stok });
        }
        renderCart();
    }

    function renderCart() {
        let html = ''; totalBelanja = 0;
        if (cart.length === 0) {
            $('#empty_cart_msg').show(); $('#btn_bayar_trigger').attr('disabled', true);
        } else {
            $('#empty_cart_msg').hide(); $('#btn_bayar_trigger').attr('disabled', false);
            cart.forEach((item, index) => {
                let subtotal = item.harga * item.qty; totalBelanja += subtotal;
                html += `<div class="d-flex align-items-center mb-4 bg-light-primary p-3 rounded">
                            <div class="flex-grow-1"><div class="fs-7 fw-bold text-gray-800">${item.nama}</div><div class="fs-8 text-muted">@ Rp ${item.harga.toLocaleString('id-ID')}</div></div>
                            <div class="d-flex align-items-center me-3">
                                <button class="btn btn-icon btn-xs btn-outline btn-outline-primary" onclick="updateQty(${index}, -1)">-</button>
                                <span class="fw-bold px-3">${item.qty}</span>
                                <button class="btn btn-icon btn-xs btn-outline btn-outline-primary" onclick="updateQty(${index}, 1)">+</button>
                            </div>
                            <div class="text-end min-w-80px"><div class="fs-7 fw-boldest">Rp ${subtotal.toLocaleString('id-ID')}</div></div>
                        </div>`;
            });
        }
        $('#cart_list').html(html);
        $('#display_total').text('Rp ' + totalBelanja.toLocaleString('id-ID'));
        // Reset diskon jika isi keranjang berubah
        window.totalAkhir = totalBelanja;
    }

    function updateQty(index, val) {
        cart[index].qty += val;
        if (cart[index].qty > cart[index].max_stok) cart[index].qty = cart[index].max_stok;
        if (cart[index].qty <= 0) cart.splice(index, 1);
        renderCart();
    }

    function clearCart() { cart = []; renderCart(); $('#scan_barcode').focus(); }

    function openPaymentModal() {
        $('#modal_bayar').modal('show'); 
        $('#cash_received').val('');
        $('#modal_subtotal_display').text('Rp ' + totalBelanja.toLocaleString('id-ID'));
        hitungDiskonOtomatis(); // Hitung diskon saat modal buka
    }

    function calculateKembalian() {
        let bayar = Number($('#cash_received').val()) || 0;
        let totalHarusBayar = window.totalAkhir > 0 ? window.totalAkhir : totalBelanja;
        let kembali = bayar - totalHarusBayar;
        $('#display_kembalian').text('Rp ' + (kembali < 0 ? 0 : kembali).toLocaleString('id-ID'));
    }

    function hitungDiskonOtomatis() {
        let id_pelanggan = $('#id_pelanggan').val();
        if (id_pelanggan == "") {
            $('#display_diskon').text('- Rp 0');
            $('#modal_total_display').text('Rp ' + totalBelanja.toLocaleString('id-ID'));
            window.nilaiDiskon = 0;
            window.totalAkhir = totalBelanja;
            calculateKembalian();
            return;
        }

        $.ajax({
            url: "<?= base_url('kasir/cek_diskon') ?>",
            type: "GET",
            data: { id_pelanggan: id_pelanggan, total_belanja: totalBelanja },
            dataType: "JSON",
            success: function(res) {
                $('#display_diskon').text('- Rp ' + res.diskon.toLocaleString('id-ID'));
                $('#modal_total_display').text('Rp ' + res.total_akhir.toLocaleString('id-ID'));
                window.nilaiDiskon = res.diskon;
                window.totalAkhir = res.total_akhir;
                calculateKembalian(); 
            }
        });
    }

    function processCheckout() {
        let finalTotal = window.totalAkhir > 0 ? window.totalAkhir : totalBelanja;
        let bayar = Number($('#cash_received').val());
        
        if (bayar < finalTotal) { 
            Swal.fire('Gagal', 'Uang diterima kurang dari total tagihan!', 'error'); 
            return; 
        }

        let btn = $('#btn_proses_checkout');
        btn.prop('disabled', true).text('Memproses...');

        $.ajax({
            url: "<?= base_url('kasir/checkout') ?>",
            type: "POST",
            data: {
                [$('#csrf_token_pos').attr('name')]: $('#csrf_token_pos').val(),
                cart: cart, 
                total_price: totalBelanja, // Kirim total asli
                discount: window.nilaiDiskon, // Kirim nilai diskon
                cash_received: bayar, 
                payment_method: $('#payment_method').val(),
                id_pelanggan: $('#id_pelanggan').val()
            },
            dataType: "JSON",
            success: function(res) {
                if (res.status === 'success') {
                    $('#modal_bayar').modal('hide');
                    Swal.fire({
                        title: 'Berhasil!',
                        text: "Invoice: " + res.invoice + ". Cetak nota?",
                        icon: 'success',
                        showCancelButton: true,
                        confirmButtonText: 'Cetak Nota',
                        cancelButtonText: 'Nanti'
                    }).then((result) => {
                        if (result.isConfirmed) window.open("<?= base_url('kasir/cetak') ?>/" + res.sale_id, "_blank");
                        location.reload();
                    });
                } else { 
                    btn.prop('disabled', false).text('Selesaikan Transaksi'); 
                    Swal.fire('Gagal', res.message, 'error'); 
                }
            },
            error: function() {
                btn.prop('disabled', false).text('Selesaikan Transaksi');
                Swal.fire('Error', 'Kesalahan Sistem!', 'error');
            }
        });
    }

    function simpanMemberCepat() {
    let nama = $('#new_nama').val();
    let telp = $('#new_telp').val();

    $.ajax({
        url: "<?= base_url('kasir/save_pelanggan_ajax') ?>",
        type: "POST",
        data: {
            nama_pelanggan: nama,
            telepon: telp,
            // AMBIL TOKEN DARI INPUT HIDDEN
            [$('#csrf_token_pos').attr('name')]: $('#csrf_token_pos').val() 
        },
        dataType: "JSON",
        success: function(res) {
            if(res.status === 'success') {
                // UPDATE TOKEN CSRF BARU (Agar transaksi selanjutnya tidak 403)
                $('#csrf_token_pos').val(res.csrf_hash);

                let newOption = new Option(res.nama + " (" + res.telp + ")", res.id, true, true);
                $('#id_pelanggan').append(newOption).trigger('change');
                $('#modal_tambah_pelanggan').modal('hide');
                Swal.fire('Berhasil', 'Member terdaftar.', 'success');
            }
        },
        error: function(xhr) {
            // Jika error 500, kita bisa lihat pesannya di sini
            console.log(xhr.responseText);
            Swal.fire('Error', 'Terjadi kesalahan sistem (500)', 'error');
        }
    });
}

function setQuickCash(amount) {
    let totalHarusBayar = window.totalAkhir > 0 ? window.totalAkhir : totalBelanja;
    
    if (amount === 'pas') {
        $('#cash_received').val(totalHarusBayar);
    } else {
        $('#cash_received').val(amount);
    }
    
    // Trigger perhitungan kembalian
    calculateKembalian();
}

// Logic tambahan: Jika metode QRIS/Debit, otomatis set uang pas dan kunci inputnya
$('#payment_method').on('change', function() {
    let method = $(this).val();
    let totalHarusBayar = window.totalAkhir > 0 ? window.totalAkhir : totalBelanja;
    
    if (method !== 'Tunai') {
        $('#cash_received').val(totalHarusBayar).prop('readonly', true);
        $('#quick_cash_section').fadeOut();
    } else {
        $('#cash_received').val('').prop('readonly', false);
        $('#quick_cash_section').fadeIn();
    }
    calculateKembalian();
});

// --- LOGIKA SCAN BARCODE KAMERA ---
let html5QrCode = null;

function openCameraScanner() {
    $('#modal_camera_scanner').modal('show');

    setTimeout(() => {
        if (html5QrCode && html5QrCode.isScanning) {
            stopCameraScanning();
        }

        Html5Qrcode.getCameras().then(devices => {
            if (devices && devices.length > 0) {
                let optionsHtml = '';
                let backCameraId = devices[0].id;

                devices.forEach((device) => {
                    let label = device.label || 'Kamera ' + device.id;
                    if (/back|rear|environment/i.test(label)) {
                        backCameraId = device.id;
                    }
                    optionsHtml += `<option value="${device.id}">${label}</option>`;
                });

                $('#camera_select').html(optionsHtml).val(backCameraId);
                startCameraScanning(backCameraId);

                $('#camera_select').off('change').on('change', function() {
                    let selectedCamera = $(this).val();
                    if (!selectedCamera) return;
                    stopCameraScanning().then(() => {
                        startCameraScanning(selectedCamera);
                    });
                });
            } else {
                Swal.fire('Error', 'Kamera tidak ditemukan pada perangkat ini.', 'error');
                $('#modal_camera_scanner').modal('hide');
            }
        }).catch(err => {
            console.error(err);
            Swal.fire('Error', 'Izin kamera ditolak atau tidak dapat mengakses kamera.', 'error');
            $('#modal_camera_scanner').modal('hide');
        });
    }, 500);
}

function startCameraScanning(cameraId) {
    if (html5QrCode === null) {
        html5QrCode = new Html5Qrcode("reader");
    }

    const config = {
        fps: 25,
        qrbox: { width: 300, height: 150 },
        aspectRatio: 1.0,
        experimentalFeatures: {
            useBarCodeDetectorIfSupported: true
        }
    };

    if (typeof Html5QrcodeSupportedFormats !== 'undefined') {
        config.formatsToSupport = [
            Html5QrcodeSupportedFormats.CODE_128,
            Html5QrcodeSupportedFormats.CODE_39,
            Html5QrcodeSupportedFormats.CODE_93,
            Html5QrcodeSupportedFormats.EAN_8,
            Html5QrcodeSupportedFormats.EAN_13,
            Html5QrcodeSupportedFormats.ITF,
            Html5QrcodeSupportedFormats.UPC_A,
            Html5QrcodeSupportedFormats.UPC_E
        ];
    }

    const startScanner = () => {
        html5QrCode.start(
            cameraId,
            config,
            (decodedText) => {
                console.log("Kamera Mendeteksi:", decodedText);
                playBeep();
                scanProductByBarcode(decodedText);
                closeCameraScanner();
                if (navigator.vibrate) navigator.vibrate(100);
            },
            (errorMessage) => {
                // Abaikan error saat proses parse frame
            }
        ).catch(err => {
            console.error("Gagal start kamera:", err);
            Swal.fire('Error', 'Tidak dapat memulai scanner kamera.', 'error');
        });
    };

    if (html5QrCode.isScanning) {
        html5QrCode.stop().then(() => startScanner()).catch(() => startScanner());
    } else {
        startScanner();
    }
}

function stopCameraScanning() {
    return new Promise((resolve) => {
        if (html5QrCode && html5QrCode.isScanning) {
            html5QrCode.stop().then(() => {
                resolve();
            }).catch(err => {
                console.error("Gagal stop kamera:", err);
                resolve();
            });
        } else {
            resolve();
        }
    });
}

function closeCameraScanner() {
    stopCameraScanning().then(() => {
        $('#modal_camera_scanner').modal('hide');
        setTimeout(() => $('#scan_barcode').focus(), 500);
    });
}

// Fungsi BEEP yang lebih mantap
function playBeep() {
    try {
        let audioCtx = new (window.AudioContext || window.webkitAudioContext)();
        let oscillator = audioCtx.createOscillator();
        let gainNode = audioCtx.createGain();
        oscillator.connect(gainNode);
        gainNode.connect(audioCtx.destination);
        oscillator.type = 'sine';
        oscillator.frequency.value = 1000; 
        gainNode.gain.setValueAtTime(0.1, audioCtx.currentTime);
        oscillator.start();
        oscillator.stop(audioCtx.currentTime + 0.1);
    } catch (e) {
        console.warn("Audio Context tidak diizinkan browser");
    }
}

    
</script>
<?= $this->endSection(); ?>