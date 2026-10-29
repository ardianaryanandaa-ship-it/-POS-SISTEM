<?= $this->extend('layout/metronic'); ?>
<?= $this->section('content'); ?>

<!-- TOOLBAR HALAMAN -->
<div class="d-flex flex-stack mb-6">
    <div class="d-flex align-items-center position-relative my-1">
        <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
            Riwayat Stock Opname
            <span class="text-muted fs-7 fw-semibold pt-1">Kelola sinkronisasi stok fisik antar unit bisnis</span>
        </h1>
    </div>
    <div class="d-flex align-items-center gap-2 gap-lg-3">
        <button type="button" class="btn btn-sm btn-primary fw-bold" data-bs-toggle="modal" data-bs-target="#modalTambahOpname">
            <i class="ki-duotone ki-plus fs-2"></i> Tambah Stock Opname
        </button>
    </div>
</div>

<!-- ALERT NOTIFIKASI -->
<?php if (session()->getFlashdata('success')) : ?>
    <div class="alert alert-dismissible bg-light-success d-flex flex-column flex-sm-row p-5 mb-10">
        <i class="ki-duotone ki-notification-on fs-2hx text-success me-4 mb-5 mb-sm-0"></i>
        <div class="d-flex flex-column pe-0 pe-sm-10">
            <h4 class="fw-semibold">Berhasil!</h4>
            <span><?= session()->getFlashdata('success') ?></span>
        </div>
        <button type="button" class="position-absolute position-sm-relative m-2 m-sm-0 top-0 end-0 btn btn-icon ms-sm-auto" data-bs-dismiss="alert">
            <i class="ki-duotone ki-cross fs-1 text-success"><span class="path1"></span><span class="path2"></span></i>
        </button>
    </div>
<?php endif; ?>

<!-- TABEL DATA UTAMA -->
<div class="card card-flush shadow-sm">
    <div class="card-body py-5">
        <div class="table-responsive">
            <table class="table align-middle table-row-dashed fs-6 gy-5">
    <thead>
        <tr class="text-start text-gray-400 fw-bold fs-7 text-uppercase gs-0">
            <th>Tanggal</th>
            <th>Barang & Unit</th>
            <th class="text-end">Sistem</th>
            <th class="text-end">Fisik</th>
            <th class="text-center">Status</th>
            <th class="text-center">Aksi</th>
        </tr>
    </thead>
    <tbody class="fw-semibold text-gray-600">
        <?php foreach ($history as $h) : ?>
            <tr>
                <td><?= date('d/m/Y H:i', strtotime($h['tanggal_opname'])) ?></td>
                <td>
                    <div class="text-dark fw-bold"><?= $h['nama_barang'] ?></div>
                    <div class="text-muted fs-7"><?= $h['nama_unit'] ?></div>
                </td>
                <td class="text-end"><?= $h['stok_sistem'] ?></td>
                <td class="text-end fw-bold text-primary"><?= $h['stok_fisik'] ?></td>
                <td class="text-center">
                    <!-- BADGE STATUS METRONIC -->
                    <?php if ($h['status'] == 'Pending') : ?>
                        <span class="badge badge-light-warning fw-bold">Pending</span>
                    <?php elseif ($h['status'] == 'Approved') : ?>
                        <span class="badge badge-light-success fw-bold">Approved</span>
                    <?php else : ?>
                        <span class="badge badge-light-danger fw-bold">Rejected</span>
                    <?php endif; ?>
                </td>
                <td class="text-center">
                    <?php if ($h['status'] == 'Pending') : ?>
                        <!-- Tombol Approve (Hanya muncul jika status Pending) -->
                        <a href="<?= base_url('stockopname/approve/' . $h['id_opname']) ?>" 
                           class="btn btn-sm btn-icon btn-light-success" title="Setujui Adjustment"
                           onclick="return confirm('Setujui penyesuaian stok ini?')">
                            <i class="ki-duotone ki-check fs-2"></i>
                        </a>
                        <a href="<?= base_url('stockopname/reject/' . $h['id_opname']) ?>" 
                           class="btn btn-sm btn-icon btn-light-danger" title="Tolak"
                           onclick="return confirm('Tolak data ini?')">
                            <i class="ki-duotone ki-cross fs-2"></i>
                        </a>
                    <?php else: ?>
                        <!-- Jika sudah di-approve, hanya bisa lihat detail -->
                        <button class="btn btn-sm btn-icon btn-light-primary btn-detail" data-id="<?= $h['id_opname'] ?>">
                            <i class="ki-duotone ki-eye fs-2"></i>
                        </button>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
        </div>
    </div>
</div>

<!-- ======================================================
     MODAL TAMBAH (METRONIC STYLE)
     ====================================================== -->
<div class="modal fade" id="modalTambahOpname" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-650px">
        <div class="modal-content rounded">
            <div class="modal-header pb-0 border-0 justify-content-end">
                <div class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal">
                    <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                </div>
            </div>
            <div class="modal-body scroll-y px-10 px-lg-15 pt-0 pb-15">
                <form action="<?= base_url('stockopname/save') ?>" method="post">
                    <?= csrf_field(); ?>
                    <div class="mb-13 text-center">
                        <h1 class="mb-3">Penyesuaian Stok</h1>
                        <div class="text-muted fw-semibold fs-5">Lakukan sinkronisasi data fisik unit bisnis</div>
                    </div>
                    <div class="row g-9 mb-8">
                        <div class="col-md-6 fv-row">
                            <label class="required fs-6 fw-semibold mb-2">Unit Bisnis</label>
                            <select name="kode_unit" class="form-select form-select-solid" data-control="select2" data-placeholder="Pilih Unit" data-dropdown-parent="#modalTambahOpname" required>
                                <option value=""></option>
                                <?php foreach ($unit as $u) : ?>
                                    <option value="<?= $u['kode_unit'] ?>"><?= $u['nama_unit'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 fv-row">
                            <label class="required fs-6 fw-semibold mb-2">Pilih Barang</label>
                            <select name="kode_barang" id="kode_barang" class="form-select form-select-solid" data-control="select2" data-placeholder="Cari Barang" data-dropdown-parent="#modalTambahOpname" required>
                                <option value=""></option>
                                <?php foreach ($barang as $b) : ?>
                                    <option value="<?= $b['kode_barang'] ?>"><?= $b['nama_barang'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="row g-9 mb-8 bg-light-primary p-5 rounded border border-dashed border-primary">
                        <div class="col-md-4 text-center">
                            <label class="fs-7 fw-bold text-gray-600 text-uppercase">Sistem</label>
                            <input type="number" name="stok_sistem" id="stok_sistem" class="form-control form-control-solid text-center fs-3 fw-bold" readonly>
                        </div>
                        <div class="col-md-4 text-center">
                            <label class="fs-7 fw-bold text-primary text-uppercase">Fisik</label>
                            <input type="number" name="stok_fisik" id="stok_fisik" class="form-control form-control-solid border-primary text-center fs-1 fw-bolder" placeholder="0" required>
                        </div>
                        <div class="col-md-4 text-center">
                            <label class="fs-7 fw-bold text-gray-600 text-uppercase">Selisih</label>
                            <input type="number" name="selisih" id="selisih" class="form-control form-control-solid text-center fs-3 fw-bold" readonly>
                        </div>
                    </div>
                    <div class="mb-8">
                        <label class="fs-6 fw-semibold mb-2">Keterangan</label>
                        <textarea class="form-control form-control-solid" name="keterangan" rows="2" placeholder="Alasan penyesuaian..."></textarea>
                    </div>
                    <div class="text-center">
                        <button type="button" class="btn btn-light me-3" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary fw-bold">Simpan & Sinkronkan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- ======================================================
     MODAL EDIT (WARNING THEME)
     ====================================================== -->
<div class="modal fade" id="modalEditOpname" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-600px">
        <div class="modal-content rounded">
            <div class="modal-header justify-content-between bg-warning py-4">
                <h3 class="modal-title text-white fw-bold">Koreksi Data Opname</h3>
                <div class="btn btn-sm btn-icon btn-active-color-white" data-bs-dismiss="modal">
                    <i class="ki-duotone ki-cross fs-1 text-white"><span class="path1"></span><span class="path2"></span></i>
                </div>
            </div>
            <form action="<?= base_url('stockopname/update') ?>" method="post">
                <?= csrf_field(); ?>
                <input type="hidden" name="id_opname" id="edit_id">
                <input type="hidden" name="kode_unit" id="edit_kode_unit">
                <input type="hidden" name="kode_barang" id="edit_kode_barang">
                <div class="modal-body px-10">
                    <div class="mb-6">
                        <label class="fw-bold fs-7 text-uppercase text-muted">Barang & Unit</label>
                        <input type="text" id="edit_display" class="form-control form-control-solid fw-bold fs-5" readonly>
                    </div>
                    <div class="row g-9 mb-6 bg-light p-4 rounded">
                        <div class="col-md-6">
                            <label class="fw-bold fs-7 text-muted">Sistem</label>
                            <input type="number" name="stok_sistem" id="edit_stok_sistem" class="form-control form-control-solid text-center fs-4 fw-bold" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="fw-bold fs-7 text-warning">Fisik Baru</label>
                            <input type="number" name="stok_fisik" id="edit_stok_fisik" class="form-control border-warning text-center fs-3 fw-bold" required>
                        </div>
                    </div>
                    <div class="mb-5">
                        <label class="fw-bold fs-7 text-muted">Alasan Koreksi</label>
                        <textarea name="keterangan" id="edit_keterangan" class="form-control form-control-solid" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-warning fw-bold px-10">Update Data</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ======================================================
     MODAL DETAIL (INFO ONLY)
     ====================================================== -->
<div class="modal fade" id="modalDetailOpname" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-500px">
        <div class="modal-content rounded">
            <div class="modal-header bg-primary py-4">
                <h3 class="modal-title text-white fw-bold">Detail Laporan Opname</h3>
                <div class="btn btn-sm btn-icon btn-active-color-white" data-bs-dismiss="modal">
                    <i class="ki-duotone ki-cross fs-1 text-white"><span class="path1"></span><span class="path2"></span></i>
                </div>
            </div>
            <div class="modal-body p-10">
                <div class="d-flex flex-stack mb-5">
                    <span class="text-muted fw-bold">Unit Bisnis</span>
                    <span id="det_unit" class="text-gray-800 fw-bolder fs-5"></span>
                </div>
                <div class="d-flex flex-stack mb-5 border-bottom pb-3">
                    <span class="text-muted fw-bold">Barang</span>
                    <span id="det_barang" class="text-gray-800 fw-bolder fs-5"></span>
                </div>
                <div class="row mb-5 py-3 bg-light rounded text-center">
                    <div class="col-4">
                        <div class="text-muted small fw-bold">Sistem</div>
                        <div id="det_sistem" class="fs-4 fw-bolder"></div>
                    </div>
                    <div class="col-4 border-start border-end">
                        <div class="text-primary small fw-bold">Fisik</div>
                        <div id="det_fisik" class="fs-4 fw-bolder"></div>
                    </div>
                    <div class="col-4">
                        <div class="text-muted small fw-bold">Selisih</div>
                        <div id="det_selisih" class="fs-4 fw-bolder"></div>
                    </div>
                </div>
                <div class="mb-0">
                    <label class="text-muted fw-bold d-block mb-1">Catatan Manager:</label>
                    <div id="det_ket" class="fst-italic text-gray-700 bg-light p-3 rounded border"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        // --- AJAX TAMBAH ---
        function loadStokSistem() {
            let unit = $('select[name="kode_unit"]').val();
            let barang = $('#kode_barang').val();
            if (unit && barang) {
                $.ajax({
                    url: "<?= base_url('stockopname/get_stok_sistem') ?>/" + unit + "/" + barang,
                    type: "GET",
                    dataType: "JSON",
                    success: function(d) {
                        $('#stok_sistem').val(d.stok);
                        calcDiff('#stok_sistem', '#stok_fisik', '#selisih');
                    }
                });
            }
        }
        $('select[name="kode_unit"], #kode_barang').change(loadStokSistem);
        $('#stok_fisik').on('input', function() { calcDiff('#stok_sistem', '#stok_fisik', '#selisih'); });

        // --- AJAX EDIT ---
        $(document).on('click', '.btn-edit', function() {
            let id = $(this).data('id');
            $.get("<?= base_url('stockopname/get_detail') ?>/" + id, function(data) {
                $('#edit_id').val(data.id_opname);
                $('#edit_kode_unit').val(data.kode_unit);
                $('#edit_kode_barang').val(data.kode_barang);
                $('#edit_display').val(data.nama_unit + " - " + data.nama_barang);
                $('#edit_stok_sistem').val(data.stok_sistem);
                $('#edit_stok_fisik').val(data.stok_fisik);
                $('#edit_keterangan').val(data.keterangan);
                $('#modalEditOpname').modal('show');
            });
        });

        // --- AJAX DETAIL ---
        $(document).on('click', '.btn-detail', function() {
            let id = $(this).data('id');
            $.get("<?= base_url('stockopname/get_detail') ?>/" + id, function(data) {
                $('#det_unit').text(data.nama_unit);
                $('#det_barang').text(data.nama_barang);
                $('#det_tgl').text(data.tanggal_opname);
                $('#det_sistem').text(data.stok_sistem);
                $('#det_fisik').text(data.stok_fisik);
                $('#det_selisih').html('<span class="badge badge-light-info fs-4 px-3">'+data.selisih+'</span>');
                $('#det_ket').text(data.keterangan || 'Tidak ada catatan.');
                $('#modalDetailOpname').modal('show');
            });
        });

        function calcDiff(sysId, physId, diffId) {
            let s = parseInt($(sysId).val()) || 0;
            let f = parseInt($(physId).val()) || 0;
            let res = f - s;
            if(diffId) $(diffId).val(res);
        }
    });
</script>

<?= $this->endSection(); ?>