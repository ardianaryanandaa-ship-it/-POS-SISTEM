<?= $this->extend('layout/metronic'); ?>
<?= $this->section('content'); ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
    .modal-landscape { max-width: 1400px !important; width: 95%; }
    .tracking-sidebar { background: #f8f9fa; border-left: 1px solid #eff2f5; height: 500px; overflow-y: auto; padding: 20px; }
    .log-item { border-left: 3px solid #3e97ff; padding: 10px; margin-bottom: 10px; background: white; border-radius: 5px; }
    .total-row { background-color: #f1f1f1 !important; font-weight: bold; }
    /* Style Tambahan untuk Baris Neto */
    .neto-row { background-color: #e8fff3 !important; font-weight: bold; color: #17c653; }
    .terima-neto { font-size: 0.8rem; color: #7239ea; display: block; margin-top: 4px; }
</style>

<div class="card shadow-sm">
    <div class="card-header border-0 pt-5">
        <h3 class="card-title fw-bold text-gray-800">Transaksi Barang Masuk</h3>
        <div class="card-toolbar">
            <div class="me-3">
                <select id="filter_selisih_utama" class="form-select form-select-solid form-select-sm w-150px">
                    <option value="all">Semua Data</option>
                    <option value="ada">Ada Selisih</option>
                    <option value="tidak">Tidak Selisih</option>
                </select>
            </div>
            <div class="d-flex align-items-center position-relative my-1 me-5">
                <i class="ki-duotone ki-magnifier fs-3 position-absolute ms-5"><span class="path1"></span><span class="path2"></span></i>
                <input type="text" id="kt_filter_search" class="form-control form-control-solid w-250px ps-13" placeholder="Cari No. Ref / Unit..." />
            </div>
            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addMasuk">
                <i class="ki-duotone ki-plus fs-2"><span class="path1"></span><span class="path2"></span></i> + Input Nota Baru
            </button>
        </div>
    </div>
    <div class="card-body">
        <table id="kt_table_barang_masuk" class="table table-row-dashed align-middle gs-0 gy-4">
            <thead>
                <tr class="fw-bold text-muted bg-light ps-4">
                    <th class="ps-4 rounded-start">Tgl Masuk</th>
                    <th>No Referensi</th>
                    <th>Vendor</th>
                    <th>Unit Bisnis</th>
                    <th class="text-center">Total Selisih</th> 
                    <th class="text-center">Status</th>
                    <th class="text-end pe-4 rounded-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($barang_masuk as $bm) : 
                    $total_f = (int)$bm['total_faktur'];
                    $total_t = (int)$bm['total_terima'];
                    $selisih_global = $total_f - $total_t;
                ?>
                <tr>
                    <td class="ps-4">
                        <div class="d-none"><?= $bm['tanggal_masuk']; ?></div>
                        <span class="text-gray-800 fw-bold d-block fs-6"><?= date('d M Y', strtotime($bm['tanggal_masuk'])); ?></span>
                        <span class="text-muted fs-7"><?= date('H:i', strtotime($bm['tanggal_masuk'])); ?> WIB</span>
                    </td>
                    <td><span class="fw-bold text-primary fs-6">#<?= $bm['no_referensi']; ?></span></td>
                    <td><span class="text-gray-800 fw-bold"><?= $bm['nama_vendor'] ?? '-'; ?></span></td>
                    <td><span class="text-gray-700"><?= $bm['nama_unit']; ?></span></td>
                    <td class="text-center">
                         <span class="fw-bold selisih-main-val <?= $selisih_global != 0 ? 'text-danger' : 'text-gray-600' ?>">
                            <?= number_format($selisih_global) ?>
                         </span>
                    </td>
                    <td class="text-center">
                        <span class="badge badge-light-<?= $bm['status']==0?'warning':'primary' ?> fw-bold px-4 py-3">
                            <?= $bm['status']==0?'PENDING':'VERIFIED' ?>
                        </span>
                    </td>
                    <td class="text-end pe-4">
                        <button class="btn btn-sm btn-icon btn-bg-light btn-active-color-primary" onclick="bukaModalDetail(<?= $bm['id_masuk']; ?>)" title="Lihat Detail">
                            <i class="ki-duotone ki-eye fs-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="modalDetail" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-landscape">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="fw-bold">Nota #<span id="txtNoRef" class="text-primary"></span> - <small id="txtVendorDetail"></small></h3>
                <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal"><i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i></div>
            </div>
            <div class="modal-body p-0">
                <div class="row g-0">
                    <div class="col-lg-9 p-8">
                        <div class="d-flex align-items-center mb-5">
                            <div class="form-check form-check-custom form-check-solid">
                                <input class="form-check-input h-20px w-20px" type="checkbox" id="checkOnlySelisih" onchange="filterSelisihTable()"/>
                                <label class="form-check-label fw-bold text-gray-700 ms-3" for="checkOnlySelisih">Hanya Tampilkan Barang dengan Selisih Stock</label>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered align-middle" id="tabelKroscek">
                                <thead class="bg-light">
                                    <tr class="fw-bold text-center">
                                        <th>Nama Barang</th>
                                        <th class="w-80px">Faktur</th>
                                        <th class="w-120px">Terima</th>
                                        <th class="w-80px text-primary">Retur</th>
                                        <th class="w-120px text-danger">Selisih Stock</th>
                                        <th>Alasan Selisih</th>
                                        <th class="w-50px">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="bodyModal"></tbody>
                                <tfoot class="total-row">
                                    <tr class="fw-bold text-gray-800">
                                        <td class="text-end">TOTAL KESELURUHAN:</td>
                                        <td id="grandTotalFaktur" class="text-center">0</td>
                                        <td id="grandTotalTerima" class="text-center">0</td>
                                        <td id="grandTotalRetur" class="text-center text-primary">0</td>
                                        <td id="grandTotalSelisih" class="text-center text-danger">0</td>
                                        <td colspan="2"></td>
                                    </tr>
                                    <!-- ROW TAMBAHAN UNTUK TOTAL NETO -->
                                    <tr class="neto-row">
                                        <td class="text-end">TOTAL TERIMA BERSIH (NETO):</td>
                                        <td colspan="2" id="grandTotalNeto" class="text-center fs-5">0</td>
                                        <td colspan="4" class="fs-7 text-muted ps-4">* Total Terima dikurangi Total Retur (Stok Fisik Gudang)</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    <div class="col-lg-3 tracking-sidebar"><h5 class="fw-bold mb-4">History Aktivitas</h5><div id="logTracking"></div></div>
                </div>
            </div>
            <div class="modal-footer justify-content-between">
                <a href="" id="btnCetak" target="_blank" class="btn btn-light-danger fw-bold"><i class="ki-duotone ki-printer fs-3 me-1"></i> Cetak Nota</a>
                <div id="footerAction"></div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="addMasuk" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-900px">
        <form action="<?= base_url('barangmasuk/save') ?>" method="post" class="modal-content">
            <?= csrf_field() ?>
            <div class="modal-header"><h2 class="fw-bold">Input Nota</h2><div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal"><i class="ki-duotone ki-cross fs-1"></i></div></div>
            <div class="modal-body py-10 px-lg-17">
                <div class="row g-9 mb-7">
                    <div class="col-md-3"><label class="required fw-bold mb-2">No Ref</label><input name="no_referensi" class="form-control form-control-solid" required /></div>
                    <div class="col-md-3"><label class="required fw-bold mb-2">Vendor</label><input name="nama_vendor" class="form-control form-control-solid" placeholder="PT/CV..." required /></div>
                    <div class="col-md-3"><label class="required fw-bold mb-2">Unit</label><select name="kode_unit" class="form-select form-select-solid" data-control="select2" data-tags="true" data-dropdown-parent="#addMasuk" required><option></option><?php foreach($list_unit as $lu):?><option value="<?= $lu['kode_unit']?>"><?= $lu['nama_unit']?></option><?php endforeach;?></select></div>
                    <div class="col-md-3"><label class="required fw-bold mb-2">Penerima</label><input name="penerima" class="form-control form-control-solid" required /></div>
                </div>
                <table class="table table-bordered" id="tabelInput">
                    <thead><tr class="bg-light text-center"><th class="min-w-200px">Barang</th><th class="w-120px">Jumlah</th><th class="w-50px">Hapus</th></tr></thead>
                    <tbody><tr><td><select name="kode_barang[]" class="form-select select-barang-row" data-control="select2" data-tags="true" data-placeholder="Pilih/Ketik..." data-dropdown-parent="#addMasuk" required><option></option><?php foreach($list_barang as $lb):?><option value="<?=$lb['kode_barang']?>"><?=$lb['nama_barang']?></option><?php endforeach;?></select></td><td><input type="number" name="jumlah[]" class="form-control text-center form-control-solid" value="1" min="1"></td><td class="text-center"><button type="button" class="btn btn-sm btn-icon btn-light-danger btn-remove-row"><i class="ki-duotone ki-trash fs-3"></i></button></td></tr></tbody>
                </table>
                <button type="button" class="btn btn-sm btn-light-success mt-2" id="btnTambahBaris"><i class="ki-duotone ki-plus fs-3"></i> + Item</button>
            </div>
            <div class="modal-footer"><button type="submit" class="btn btn-primary w-100 fw-bold">SIMPAN NOTA PENDING</button></div>
        </form>
    </div>
</div>

<script>
let active_id_masuk = null;

$(document).ready(function() {
    // 1. Inisialisasi DataTable
    var datatable = $('#kt_table_barang_masuk').DataTable({
        "info": true, 
        'order': [[0, 'desc']], 
        'pageLength': 10, 
        "columnDefs": [{ "orderable": false, "targets": 6 }]
    });

    // 2. Filter Search
    $('#kt_filter_search').on('keyup', function () { 
        datatable.search(this.value).draw(); 
    });

    // 3. Custom Filter (Selisih)
    $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
        if (settings.nTable.id !== 'kt_table_barang_masuk') return true;
        let filterVal = $('#filter_selisih_utama').val();
        let selisih = parseInt($(datatable.cell(dataIndex, 4).node()).find('.selisih-main-val').text().replace(/,/g, '')) || 0;
        if (filterVal === 'all') return true;
        if (filterVal === 'ada' && selisih !== 0) return true;
        if (filterVal === 'tidak' && selisih === 0) return true;
        return false;
    });

    $('#filter_selisih_utama').on('change', function() { 
        datatable.draw(); 
    });

    // 4. Tambah Baris Input Nota (Modal)
    $('#btnTambahBaris').on('click', function() {
        let tbody = $('#tabelInput tbody'); 
        let row = tbody.find('tr:first').clone();
        row.find('input').val('1'); 
        row.find('.select2-container').remove();
        row.find('select').removeClass('select2-hidden-accessible').removeAttr('data-select2-id').val(null);
        tbody.append(row); 
        row.find('[data-control="select2"]').select2({ tags: true, dropdownParent: $('#addMasuk') });
    });

    $(document).on('click', '.btn-remove-row', function() { 
        if($('#tabelInput tbody tr').length > 1) $(this).closest('tr').remove(); 
    });

    // 5. Handle Submit Form "Input Nota Baru" via AJAX (Agar Tidak Reload)
    $('#addMasuk form').on('submit', function(e) {
        e.preventDefault();
        let form = $(this);
        let url = form.attr('action');
        let data = form.serialize();

        $.post(url, data, function(res) {
            $('#addMasuk').modal('hide');
            // Reset form
            form[0].reset();
            // Refresh halaman via AJAX (Memanggil fungsi global di layout)
            refreshCurrentPage();
            Swal.fire('Berhasil!', 'Nota pending berhasil disimpan.', 'success');
        });
    });
});

// Fungsi untuk refresh konten utama tanpa reload browser
function refreshCurrentPage() {
    let currentUrl = window.location.href;
    // Trigger klik pada menu yang sedang aktif atau panggil ulang URL via AJAX
    // Jika Anda menggunakan script layout yang saya berikan sebelumnya:
    $('.menu-link.active').click(); 
    // Jika link tidak diklik, paksa load:
    $.get(currentUrl, function(response) {
        let tempDom = $('<div>').append($.parseHTML(response));
        let htmlContent = tempDom.find('#ajax-content-target').html();
        if (htmlContent) {
            $('#ajax-content-target').html(htmlContent);
            // Inisialisasi ulang datatable setelah konten diganti
            $('#kt_table_barang_masuk').DataTable();
        }
    });
}

function bukaModalDetail(id) {
    active_id_masuk = id;
    $('#checkOnlySelisih').prop('checked', false); 
    $.get("<?= base_url('barangmasuk/get_detail/') ?>" + id, function(res) {
        $('#txtNoRef').text(res.header.no_referensi);
        $('#txtVendorDetail').text(res.header.nama_vendor || '');
        let html = '';
        res.items.forEach(item => {
            let f = parseInt(item.jumlah_faktur) || 0;
            let t_awal = parseInt(item.jumlah) || 0;
            let r = parseInt(item.jumlah_retur) || 0;
            let s = f - t_awal; 
            let t_neto = t_awal - r; 

            if(res.header.status == 0) {
                html += `<tr data-id="${item.id_detail}" class="item-row">
                    <td>${item.nama_barang}</td>
                    <td><input type="number" class="faktur-in form-control form-control-sm text-center" value="${f}" onchange="calcUpdate(${item.id_detail})"></td>
                    <td>
                        <input type="number" class="terima-in form-control form-control-sm text-center" value="${t_awal}" onchange="calcUpdate(${item.id_detail})">
                        <small class="terima-neto text-center">${t_neto} (Neto)</small>
                    </td>
                    <td class="text-center text-primary fw-bold retur-val">${r}</td>
                    <td class="text-center text-danger fw-bold selisih-val">${s}</td>
                    <td><input type="text" class="alasan-in form-control form-control-sm" placeholder="Alasan..." value="${item.alasan_hilang||''}" onchange="calcUpdate(${item.id_detail})"></td>
                    <td class="text-center"><button class="btn btn-sm btn-icon btn-light-danger" onclick="hapusItemRow(${item.id_detail})"><i class="ki-duotone ki-trash fs-3"></i></button></td>
                </tr>`;
                $('#footerAction').html(`<button class="btn btn-primary fw-bold" onclick="prosesVerify(${id})">Verifikasi & Stok</button>`);
            } else {
                html += `<tr class="item-row">
                    <td>${item.nama_barang}</td>
                    <td class="text-center">${f}</td>
                    <td class="text-center fw-bold">${t_awal} <br><small class='text-muted'>(Neto: ${t_neto})</small></td>
                    <td class="text-center text-primary fw-bold retur-val">${r}</td>
                    <td class="text-center text-danger fw-bold selisih-val">${s}</td>
                    <td>${item.alasan_hilang || '-'}</td>
                    <td class="text-center">-</td>
                </tr>`;
                $('#footerAction').html(`<button class="btn btn-warning fw-bold" onclick="prosesRollback(${id})">Rollback</button>`);
            }
        });
        $('#bodyModal').html(html);
        updateModalTotals();
        let logs = ''; res.logs.forEach(l => { logs += `<div class="log-item"><small class="text-muted">${l.created_at}</small><br><b>${l.user}</b>: <span class="text-success">${l.aksi}</span></div>`; });
        $('#logTracking').html(logs);
        $('#btnCetak').attr('href', "<?= base_url('barangmasuk/cetak_nota/') ?>" + id);
        $('#modalDetail').modal('show');
    });
}

function calcUpdate(id_det) {
    let row = $(`tr[data-id="${id_det}"]`);
    let f = parseInt(row.find('.faktur-in').val()) || 0;
    let t = parseInt(row.find('.terima-in').val()) || 0;
    let r = parseInt(row.find('.retur-val').text()) || 0;
    let neto = t - r;
    row.find('.terima-neto').text(neto + ' (Neto)');
    let s = f - t;
    row.find('.selisih-val').text(s);
    updateModalTotals();
    $.post("<?= base_url('barangmasuk/update_item') ?>", { 
        id_detail: id_det, 
        id_masuk: active_id_masuk, 
        faktur: f, 
        terima: t, 
        alasan: row.find('.alasan-in').val() 
    });
}

function updateModalTotals() {
    let tF = 0, tT = 0, tR = 0, tS = 0;
    $('.item-row').each(function() {
        let f = parseInt($(this).find('.faktur-in').val()) || parseInt($(this).find('td:eq(1)').text()) || 0;
        let t = parseInt($(this).find('.terima-in').val()) || parseInt($(this).find('td:eq(2)').text()) || 0;
        let r = parseInt($(this).find('.retur-val').text()) || 0;
        tF += f; tT += t; tR += r; tS += (f - t);
    });
    $('#grandTotalFaktur').text(tF); 
    $('#grandTotalTerima').text(tT); 
    $('#grandTotalRetur').text(tR); 
    $('#grandTotalSelisih').text(tS);
    $('#grandTotalNeto').text(tT - tR);
}

function filterSelisihTable() {
    let checked = $('#checkOnlySelisih').is(':checked');
    $('.item-row').each(function() {
        let sel = parseInt($(this).find('.selisih-val').text()) || 0;
        if(checked && sel === 0) { $(this).hide(); } else { $(this).show(); }
    });
}

function hapusItemRow(id_det) { 
    if(!confirm("Hapus item?")) return; 
    $.post("<?= base_url('barangmasuk/delete_item/') ?>" + id_det + "/" + active_id_masuk, function() { 
        bukaModalDetail(active_id_masuk); 
    }); 
}

// PERBAIKAN: Fungsi Verifikasi tanpa location.reload
function prosesVerify(id) { 
    $.post("<?= base_url('barangmasuk/verify/') ?>" + id, function() { 
        $('#modalDetail').modal('hide');
        refreshCurrentPage();
        Swal.fire('Berhasil!', 'Data telah diverifikasi dan stok bertambah.', 'success');
    }); 
}

// PERBAIKAN: Fungsi Rollback tanpa location.reload
function prosesRollback(id) { 
    $.post("<?= base_url('barangmasuk/rollback/') ?>" + id, function() { 
        $('#modalDetail').modal('hide');
        refreshCurrentPage();
        Swal.fire('Rollback!', 'Status nota kembali ke PENDING.', 'info');
    }); 
}
</script>
<?= $this->endSection(); ?>