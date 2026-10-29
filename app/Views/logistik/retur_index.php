<?= $this->extend('layout/metronic'); ?>
<?= $this->section('content'); ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
    /* Agar dropdown select2 tidak tertutup modal */
    .select2-container--bootstrap5 .select2-dropdown { z-index: 9999; }
    /* Memastikan invalid-feedback terlihat saat is-invalid aktif */
    .is-invalid ~ .invalid-feedback { display: block; }
    /* Styling khusus untuk sidebar history */
    .log-item-retur { border-left: 3px solid #3e97ff; padding: 10px; margin-bottom: 10px; background: white; border-radius: 5px; box-shadow: 0px 2px 5px rgba(0,0,0,0.05); }
</style>

<!-- ALERT ERROR DARI CI4 VALIDATION -->
<?php if (session()->getFlashdata('errors')) : ?>
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Validasi Gagal',
            html: '<ul class="text-start"><?php foreach (session()->getFlashdata("errors") as $error) : ?><li><?= esc($error) ?></li><?php endforeach ?></ul>',
            confirmButtonColor: '#f1416c',
        }).then(() => { $('#addRetur').modal('show'); });
    </script>
<?php endif; ?>

<div class="card shadow-sm">
    <div class="card-header border-0 pt-5">
        <h3 class="card-title fw-bold">
            <i class="ki-duotone ki-arrows-loop fs-2x text-danger me-3"><span class="path1"></span><span class="path2"></span></i> 
            Retur Barang ke Vendor
        </h3>
        <div class="card-toolbar">
            <button type="button" class="btn btn-sm btn-danger" onclick="bukaModalBaru()">
                <i class="ki-duotone ki-plus fs-2"></i> Buat Retur Baru
            </button>
        </div>
    </div>
    <div class="card-body">
        <table class="table table-row-dashed align-middle gs-0 gy-4">
            <thead>
                <tr class="fw-bold text-muted bg-light ps-4">
                    <th class="ps-4 rounded-start">No. Retur</th>
                    <th>Vendor</th>
                    <th>Unit Asal</th>
                    <th>User</th> <!-- TAMBAHAN HEADER USER -->
                    <th class="text-center">Status</th>
                    <th class="text-end pe-4 rounded-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($retur as $r) : ?>
                <tr>
                    <td class="ps-4">
                        <div class="d-flex align-items-center">
                            <i class="ki-duotone ki-file-text fs-2 text-primary me-2"><span class="path1"></span><span class="path2"></span></i>
                            <b>#<?= $r['no_retur'] ?></b>
                        </div>
                    </td>
                    <td><?= $r['nama_vendor'] ?></td>
                    <td><?= $r['nama_unit'] ?></td>
                    
                    <!-- TAMBAHAN KOLOM USERNAME -->
                    <td>
                        <div class="d-flex align-items-center">
                            <i class="ki-duotone ki-user fs-3 text-success me-2"><span class="path1"></span><span class="path2"></span></i>
                            <span class="text-gray-800 fw-bold"><?= $r['user'] ?? '' ?></span>
                        </div>
                    </td>

                    <td class="text-center">
                        <span class="badge badge-light-<?= $r['status']==0?'warning':'success' ?> fw-bold px-4 py-3"><?= $r['status']==0?'PENDING':'COMPLETED' ?></span>
                    </td>
                    <td class="text-end pe-4">
                        <button class="btn btn-sm btn-icon btn-bg-light btn-active-color-primary" onclick="detailRetur(<?= $r['id_retur'] ?>)" title="Lihat Detail">
                            <i class="ki-duotone ki-eye fs-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                        </button>
                        <?php if($r['status'] == 0): ?>
                        <button class="btn btn-sm btn-icon btn-bg-light btn-active-color-warning ms-1" onclick="editRetur(<?= $r['id_retur'] ?>)" title="Edit Data">
                            <i class="ki-duotone ki-pencil fs-2"><span class="path1"></span><span class="path2"></span></i>
                        </button>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- MODAL INPUT/EDIT RETUR -->
<div class="modal fade" id="addRetur" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-1000px">
        <form action="<?= base_url('retur/save') ?>" method="post" id="formRetur" class="modal-content">
            <?= csrf_field() ?>
            <input type="hidden" name="id_retur" id="id_retur_input">
            <input type="hidden" name="no_retur" id="no_retur_hidden">

            <div class="modal-header">
                <h2 class="fw-bold"><i class="ki-duotone ki-notepad-edit fs-2qx me-2"><span class="path1"></span><span class="path2"></span></i> <span id="modalTitle">Form Retur Barang</span></h2>
                <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal"><i class="ki-duotone ki-cross fs-1"></i></div>
            </div>
            
            <div class="modal-body py-10 px-lg-17">
                <div class="row g-9 mb-7">
                    <div class="col-md-4">
                        <label class="required fw-bold mb-2">Pilih Nota Masuk</label>
                        <select name="id_masuk_asal" id="id_masuk_asal" class="form-select form-select-solid" data-control="select2" data-dropdown-parent="#addRetur" required>
                            <option value="">-- Pilih Nota --</option>
                            <?php foreach($list_nota as $ln): ?>
                                <option value="<?= $ln['id_masuk'] ?>"><?= $ln['no_referensi'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="fw-bold mb-2">Vendor</label>
                        <input name="nama_vendor" id="input_vendor" class="form-control form-control-solid" readonly />
                    </div>
                    <div class="col-md-4">
                        <label class="fw-bold mb-2">Unit</label>
                        <input type="hidden" name="kode_unit" id="input_kode_unit">
                        <input id="input_nama_unit" class="form-control form-control-solid" readonly />
                    </div>
                </div>

                <table class="table table-bordered align-middle" id="tabelItemRetur">
                    <thead class="bg-light">
                        <tr class="fw-bold text-center">
                            <th>Barang dari Nota</th>
                            <th class="w-150px">Jumlah</th>
                            <th>Alasan Retur</th>
                            <th class="w-50px text-center">Hapus</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
                <button type="button" class="btn btn-sm btn-light-primary mt-2" id="btnTambahBarisRetur">
                    <i class="ki-duotone ki-plus-circle fs-3"><span class="path1"></span><span class="path2"></span></i> Tambah Item
                </button>
            </div>
            
            <div class="modal-footer">
                <button type="submit" id="btnSimpanRetur" class="btn btn-danger w-100 fw-bold">
                    <i class="ki-duotone ki-save-2 fs-2 me-2"><span class="path1"></span><span class="path2"></span></i> SIMPAN DATA RETUR
                </button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL DETAIL -->
<div class="modal fade" id="modalDetailRetur" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-1000px">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="fw-bold"><i class="ki-duotone ki-magnifier fs-2 text-danger me-2"><span class="path1"></span><span class="path2"></span></i> Rincian Barang Retur: <span id="txtNoRetur" class="text-danger"></span></h3>
                <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal"><i class="ki-duotone ki-cross fs-1"></i></div>
            </div>
            <div class="modal-body p-0">
                <div class="row g-0">
                    <div class="col-lg-8 p-8">
                        <table class="table table-bordered align-middle">
                            <thead class="bg-light">
                                <tr class="fw-bold"><th>Nama Barang</th><th class="text-center w-100px">Jumlah</th><th>Alasan</th></tr>
                            </thead>
                            <tbody id="bodyDetailRetur"></tbody>
                        </table>
                    </div>
                    <div class="col-lg-4 bg-light p-8 border-start" style="max-height: 400px; overflow-y: auto;">
                        <h5 class="fw-bold mb-5">History Aktivitas</h5>
                        <div id="logTrackingRetur"></div>
                    </div>
                </div>
            </div>
            <div id="footerRetur" class="modal-footer bg-light justify-content-between">
                <button class="btn btn-light-dark btn-sm"><i class="ki-duotone ki-printer fs-3 me-1"></i> Cetak Bukti</button>
                <div id="btnVerifyArea"></div>
            </div>
        </div>
    </div>
</div>

<script>
let itemsDariNota = [];
let isEditing = false; 

/** 
 * FUNGSI TAMBAHAN: Update konten tabel tanpa reload browser
 * Ini akan mengambil HTML terbaru dari server dan mengganti isi target
 */
function refreshTableOnly() {
    let currentUrl = window.location.href;
    $.get(currentUrl, function(response) {
        // Cari konten di dalam #ajax-content-target pada response
        let tempDom = $('<div>').append($.parseHTML(response));
        let newContent = tempDom.find('#ajax-content-target').html();
        
        if (newContent) {
            $('#ajax-content-target').html(newContent);
        } else {
            // Jika struktur layout berbeda, ambil div card utama
            $('#ajax-content-target').html(tempDom.find('.card.shadow-sm').parent().html());
        }
    });
}

function bukaModalBaru() {
    isEditing = false;
    $('#id_retur_input').val(''); 
    $('#formRetur')[0].reset();
    $('#modalTitle').text('Form Retur Barang Baru');
    $('#id_masuk_asal').val(null).trigger('change.select2');
    $('#tabelItemRetur tbody').empty();
    $('#btnSimpanRetur').prop('disabled', false);
    $('#addRetur').modal('show');
}

$(document).ready(function() {
    // TAMBAHAN: Handle Submit Form via AJAX agar tidak reload
    $('#formRetur').on('submit', function(e) {
        e.preventDefault(); // Mencegah reload browser
        let form = $(this);
        let url = form.attr('action');
        let data = form.serialize();

        $.ajax({
            url: url,
            type: 'POST',
            data: data,
            dataType: 'JSON',
            success: function(res) {
                $('#addRetur').modal('hide');
                Swal.fire('Berhasil!', 'Data retur telah disimpan.', 'success');
                refreshTableOnly(); // Update tabel utama
            },
            error: function() {
                // Fallback jika response bukan JSON (misal redirect dari Controller)
                $('#addRetur').modal('hide');
                refreshTableOnly();
            }
        });
    });

    $('#id_masuk_asal').on('change', function() {
        let id = $(this).val();
        if(!id || isEditing) return; 

        $.get("<?= base_url('barangmasuk/get_detail') ?>/" + id, function(res) {
            itemsDariNota = res.items;
            $('#input_vendor').val(res.header.nama_vendor);
            $('#input_kode_unit').val(res.header.kode_unit);
            $('#input_nama_unit').val(res.header.nama_unit);
            
            $('#tabelItemRetur tbody').empty();
            tambahBaris(); 
        });
    });

    $('#btnTambahBarisRetur').on('click', function() { tambahBaris(); });
});

function tambahBaris(dataItem = null) {
    let options = '<option value="">-- Pilih Barang --</option>';
    itemsDariNota.forEach(it => {
        let isSelected = (dataItem && dataItem.kode_barang == it.kode_barang) ? 'selected' : '';
        options += `<option value="${it.kode_barang}" data-max="${it.jumlah}" ${isSelected}>${it.nama_barang} (Stok: ${it.jumlah})</option>`;
    });

    let row = `<tr class="row-item">
        <td><select name="kode_barang[]" class="form-select select-barang" required>${options}</select></td>
        <td>
            <input type="number" name="jumlah[]" class="form-control input-jumlah" value="${dataItem ? dataItem.jumlah : ''}" min="1" required>
            <div class="invalid-feedback fw-bold"></div>
        </td>
        <td><input type="text" name="alasan[]" class="form-control" value="${dataItem ? dataItem.alasan_retur : ''}" placeholder="..." required></td>
        <td class="text-center">
            <button type="button" class="btn btn-sm btn-icon btn-light-danger btn-remove" title="Hapus"><i class="ki-duotone ki-trash fs-3"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span></i></button>
        </td>
    </tr>`;
    
    let $row = $(row);
    $('#tabelItemRetur tbody').append($row);
    $row.find('.select-barang').select2({ dropdownParent: $('#addRetur'), width: '100%' });
    
    $row.find('.select-barang').on('change', function() {
        let maxStok = $(this).find('option:selected').data('max');
        if (!isEditing && maxStok) $row.find('.input-jumlah').val(maxStok);
        validateRow($row);
    });

    $row.find('.input-jumlah').on('input', function() { validateRow($row); });
}

function validateRow($row) {
    let input = $row.find('.input-jumlah');
    let max = parseInt($row.find('.select-barang option:selected').data('max')) || 0;
    let val = parseInt(input.val()) || 0;
    let feedback = $row.find('.invalid-feedback');

    if (val > max) {
        input.addClass('is-invalid');
        feedback.text('Maksimal stok: ' + max);
        $('#btnSimpanRetur').prop('disabled', true);
    } else {
        input.removeClass('is-invalid');
        if ($('.is-invalid').length === 0) $('#btnSimpanRetur').prop('disabled', false);
    }
}

function detailRetur(id) {
    Swal.fire({ title: 'Memuat...', allowOutsideClick: false, didOpen: () => { Swal.showLoading(); }});
    $.get("<?= base_url('retur/get_detail') ?>/" + id, function(res) {
        if (res.status === 'success') {
            $('#txtNoRetur').text('#' + res.header.no_retur);
            let html = '';
            res.items.forEach(item => {
                html += `<tr><td><b>${item.nama_barang}</b><br><small class="text-muted">${item.kode_barang}</small></td><td class="text-center fw-bold fs-5">${item.jumlah}</td><td>${item.alasan_retur}</td></tr>`;
            });
            $('#bodyDetailRetur').html(html);

            let logsHtml = '';
            if (res.logs && res.logs.length > 0) {
                res.logs.forEach(log => {
                    logsHtml += `
                        <div class="log-item-retur border-start border-primary border-3 ps-4">
                            <div class="text-muted fs-8">${log.created_at}</div>
                            <div class="fw-bold text-gray-800">${log.user}</div>
                            <div class="badge badge-light-primary fs-9 mb-1">${log.aksi}</div>
                            <p class="text-muted fs-8 m-0">${log.keterangan || ''}</p>
                        </div>`;
                });
            } else { logsHtml = '<p class="text-muted fs-8 p-5 text-center">Belum ada riwayat.</p>'; }
            $('#logTrackingRetur').html(logsHtml);

            let footer = (res.header.status == 0) ? 
                `<button class="btn btn-danger fw-bold" onclick="prosesVerifyRetur(${id})"><i class="ki-duotone ki-check-circle fs-3 me-1"><span class="path1"></span><span class="path2"></span></i> VERIFIKASI SEKARANG</button>` : 
                `<span class="badge badge-light-success fs-7 fw-bold">RETUR SELESAI</span>`;
            $('#btnVerifyArea').html(footer);
            
            Swal.close();
            $('#modalDetailRetur').modal('show');
        }
    }).fail(() => { Swal.fire('Error', 'Gagal memuat detail.', 'error'); });
}

function editRetur(id) {
    isEditing = true; 
    Swal.fire({ title: 'Memuat Data...', allowOutsideClick: false, didOpen: () => { Swal.showLoading(); }});
    $.get("<?= base_url('retur/get_detail') ?>/" + id, function(res) {
        if (res.status === 'success') {
            $('#id_retur_input').val(res.header.id_retur);
            $('#no_retur_hidden').val(res.header.no_retur);
            $('#modalTitle').text('Edit Retur: #' + res.header.no_retur);
            $.get("<?= base_url('barangmasuk/get_detail') ?>/" + res.header.id_masuk, function(notaRes) {
                itemsDariNota = notaRes.items;
                $('#id_masuk_asal').val(res.header.id_masuk).trigger('change.select2');
                $('#input_vendor').val(notaRes.header.nama_vendor);
                $('#input_kode_unit').val(notaRes.header.kode_unit);
                $('#input_nama_unit').val(notaRes.header.nama_unit);
                $('#tabelItemRetur tbody').empty();
                res.items.forEach(item => { tambahBaris(item); });
                Swal.close();
                $('#addRetur').modal('show');
                setTimeout(() => { isEditing = false; }, 500);
            }).fail(() => { Swal.fire('Error', 'Gagal memuat nota asal.', 'error'); isEditing = false; });
        }
    }).fail(() => { Swal.fire('Error', 'Gagal menghubungi server.', 'error'); isEditing = false; });
}

$(document).on('click', '.btn-remove', function() { if($('#tabelItemRetur tbody tr').length > 1) $(this).closest('tr').remove(); });

/**
 * PERBAIKAN: Verifikasi tanpa reload
 */
function prosesVerifyRetur(id) {
    Swal.fire({
        title: 'Verifikasi Retur?',
        text: "Stok akan langsung dipotong!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#f1416c',
        confirmButtonText: 'Ya, Verifikasi!'
    }).then((result) => {
        if (result.isConfirmed) {
            // Ganti window.location.href menjadi AJAX $.get
            $.get("<?= base_url('retur/verify/') ?>" + id, function() {
                $('#modalDetailRetur').modal('hide');
                Swal.fire('Berhasil!', 'Retur telah diverifikasi.', 'success');
                refreshTableOnly(); // Update tabel utama tanpa reload
            }).fail(() => {
                Swal.fire('Error', 'Terjadi kesalahan saat verifikasi.', 'error');
            });
        }
    });
}

$('#formRetur').on('submit', function(e) {
    e.preventDefault();
    let form = $(this);
    
    $.ajax({
        url: form.attr('action'),
        type: 'POST',
        data: form.serialize(),
        dataType: 'JSON',
        success: function(res) {
            if (res.status === 'success') {
                $('#addRetur').modal('hide');
                Swal.fire('Berhasil!', res.message, 'success').then(() => {
                    location.reload(); // Harus reload agar data muncul di tabel
                });
            } else {
                // Tampilkan error jika validasi gagal atau stok kurang
                let pesan = res.message;
                if(res.errors) pesan = Object.values(res.errors).join("<br>");
                Swal.fire('Gagal!', pesan, 'error');
            }
        },
        error: function(xhr) {
            Swal.fire('Error', 'Terjadi kesalahan sistem atau URL tidak ditemukan.', 'error');
        }
    });
});
</script>
<?= $this->endSection(); ?>