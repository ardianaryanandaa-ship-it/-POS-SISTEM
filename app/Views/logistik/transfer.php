<?= $this->extend('layout/metronic'); ?>
<?= $this->section('content'); ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="card shadow-sm">
    <div class="card-header border-0 pt-5">
        <h3 class="card-title fw-bold">🚚 Stock Internal Transfer (SIT)</h3>
        <div class="card-toolbar">
            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addTrfModal">
                <i class="ki-duotone ki-plus fs-2"></i> + Buat Transfer Baru
            </button>
        </div>
    </div>
    <div class="card-body">
        <table class="table table-row-dashed align-middle" id="tableMainSIT">
            <thead>
                <tr class="fw-bold text-muted bg-light ps-4">
                    <th class="ps-4">No. Transfer</th>
                    <th>Asal</th>
                    <th>Tujuan</th>
                    <th>Tanggal</th>
                    <th class="text-center">Status</th>
                    <th class="text-end pe-4">Aksi</th>
                </tr>
            </thead>
            <!-- Penambahan ID pada tbody untuk update data otomatis -->
            <tbody id="tableTransferBody">
                <?php foreach($transfers as $trf) : ?>
                <tr>
                    <td class="ps-4"><span class="fw-bold text-primary">#<?= $trf['no_trf']; ?></span></td>
                    <td><?= $trf['nama_asal']; ?></td>
                    <td><span class="badge badge-light-primary fw-bold"><?= $trf['nama_tujuan']; ?></span></td>
                    <td><?= date('d/m/Y', strtotime($trf['tanggal'])); ?></td>
                    <td class="text-center">
                        <span class="badge badge-light-<?= $trf['status']==0?'info':'success' ?> fw-bold px-3 py-2">
                            <?= $trf['status']==0?'IN TRANSIT':'RECEIVED' ?>
                        </span>
                    </td>
                    <td class="text-end pe-4">
                        <button class="btn btn-sm btn-icon btn-bg-light btn-active-color-primary" onclick="detailTrf(<?= $trf['id_trf']; ?>)" title="Lihat Detail">
                            <i class="ki-duotone ki-eye fs-2"></i>
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- MODAL ADD TRANSFER -->
<div class="modal fade" id="addTrfModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-900px">
        <form id="formSIT" action="<?= base_url('stoktransfer/save') ?>" method="post" class="modal-content">
            <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>" />
            <div class="modal-header">
                <h3 class="fw-bold">Buat Surat Jalan SIT</h3>
                <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal"><i class="ki-duotone ki-cross fs-1"></i></div>
            </div>
            <div class="modal-body">
                <div class="row g-9 mb-7">
                    <div class="col-md-4">
                        <label class="required fw-bold mb-2">Unit Asal (Pengirim)</label>
                        <select name="unit_asal" id="select_unit_asal" class="form-select form-select-solid" required>
                            <option value="">Pilih Asal...</option>
                            <?php foreach($units_asal as $jenis => $list): ?>
                                <optgroup label="JENIS: <?= $jenis ?>">
                                    <?php foreach($list as $ua): ?>
                                        <option value="<?= $ua['kode_unit'] ?>"><?= $ua['nama_unit'] ?></option>
                                    <?php endforeach; ?>
                                </optgroup>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="required fw-bold mb-2">Unit Tujuan (Penerima)</label>
                        <select name="unit_tujuan" class="form-select form-select-solid" required>
                            <option value="">Pilih Tujuan...</option>
                            <?php foreach($units_tujuan as $jenis => $list): ?>
                                <optgroup label="JENIS: <?= $jenis ?>">
                                    <?php foreach($list as $ut): ?>
                                        <option value="<?= $ut['kode_unit'] ?>"><?= $ut['nama_unit'] ?></option>
                                    <?php endforeach; ?>
                                </optgroup>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="required fw-bold mb-2">Nama Kurir</label>
                        <input type="text" name="penerima" class="form-control form-control-solid" placeholder="Nama pengantar..." required />
                    </div>
                </div>

                <table class="table table-bordered align-middle" id="tabelItemTrf">
                    <thead class="bg-light">
                        <tr>
                            <th>Barang</th>
                            <th class="w-150px text-center">Jumlah</th>
                            <th class="w-50px text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Baris pertama (Template) -->
                        <tr class="item-row">
                            <td>
                                <select name="kode_barang[]" class="form-select form-select-solid select-barang" required disabled>
                                    <option value="">Pilih Unit Asal Dulu...</option>
                                    <?php foreach($list_barang as $lb): ?>
                                        <option value="<?= $lb['kode_barang'] ?>" data-nama="<?= $lb['nama_barang'] ?>">
                                            <?= $lb['nama_barang'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                            <td><input type="number" name="jumlah[]" class="form-control form-control-solid text-center" value="1" min="1" required></td>
                            <td class="text-center">
                                <button type="button" class="btn btn-sm btn-icon btn-light-danger btn-remove-row"><i class="ki-duotone ki-trash fs-3"></i></button>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <button type="button" class="btn btn-sm btn-light-primary mt-2" id="btnTambahBarisTrf">+ Tambah Barang Lain</button>
            </div>
            <div class="modal-footer bg-light">
                <button type="submit" class="btn btn-primary w-100 fw-bold" id="btnSubmitSIT">PROSES PENGIRIMAN</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL DETAIL SIT -->
<div class="modal fade" id="modalTrfDetail" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-1000px">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="fw-bold">Detail SIT: <span id="txtNoTrf" class="text-success"></span></h3>
                <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal"><i class="ki-duotone ki-cross fs-1"></i></div>
            </div>
            <div class="modal-body">
                <div class="row mb-8">
                    <div class="col-md-6"><div class="border border-dashed border-gray-300 rounded px-7 py-3"><span class="fs-8 text-gray-400 uppercase">Pengirim</span><div id="vUnitAsal" class="fs-6 fw-bold">-</div></div></div>
                    <div class="col-md-6"><div class="border border-dashed border-primary rounded px-7 py-3"><span class="fs-8 text-primary uppercase">Tujuan</span><div id="vUnitTujuan" class="fs-6 fw-bold">-</div></div></div>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered align-middle gs-5 gy-3">
                        <thead class="bg-light fw-bold"><tr><th>Nama Barang</th><th class="w-150px text-center">Jumlah Transfer</th></tr></thead>
                        <tbody id="bodyTrfDetail"></tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer justify-content-between">
                <a href="#" id="btnCetakSIT" target="_blank" class="btn btn-light-danger btn-sm fw-bold">Cetak Surat Jalan</a>
                <div id="footerTrfAction"></div>
            </div>
        </div>
    </div>
</div>

<script>
let currentStockMap = {}; 

// Fungsi tambahan untuk refresh tabel tanpa reload halaman
function refreshTable() {
    $.get(window.location.href, function(data) {
        const newTableBody = $(data).find('#tableTransferBody').html();
        $('#tableTransferBody').html(newTableBody);
    });
}

$(document).ready(function() {
    // 1. Logic Pilih Unit Asal (Ambil Stok)
    $('#select_unit_asal').on('change', function() {
        const kodeUnit = $(this).val();
        const $barangSelect = $('.select-barang');
        const $btnTambah = $('#btnTambahBarisTrf');

        if (!kodeUnit) {
            $barangSelect.prop('disabled', true).val('');
            updateBarangDropdowns();
            return;
        }

        // Loading State
        $barangSelect.prop('disabled', true).find('option[value=""]').text('Mengambil data stok...');
        $btnTambah.prop('disabled', true);

        $.ajax({
            url: "<?= base_url('stoktransfer/get_stok_per_unit') ?>/" + kodeUnit,
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                currentStockMap = {};
                if(data && Array.isArray(data)) {
                    data.forEach(item => { currentStockMap[item.kode_barang] = item.jumlah; });
                }
                updateBarangDropdowns();
            },
            error: function() {
                Swal.fire('Error', 'Gagal memuat data stok unit.', 'error');
                $('.select-barang').find('option[value=""]').text('Gagal memuat stok');
            },
            complete: function() {
                $btnTambah.prop('disabled', false);
            }
        });
    });

    // 2. Fungsi Update Teks dan Status Dropdown (Anti-Stuck)
    function updateBarangDropdowns() {
        const unitTerpilih = $('#select_unit_asal').val();
        
        $('.select-barang').each(function() {
            const $select = $(this);
            const $placeholder = $select.find('option[value=""]');
            
            if (unitTerpilih) {
                $placeholder.text('-- Pilih Barang --');
                $select.prop('disabled', false);
            } else {
                $placeholder.text('Pilih Unit Asal Dulu...');
                $select.prop('disabled', true);
            }

            // Update info stok
            $select.find('option').each(function() {
                const kode = $(this).val();
                if (kode) {
                    const nama = $(this).data('nama');
                    const stok = currentStockMap[kode] || 0;
                    $(this).text(`${nama} (Stok: ${stok})`);
                }
            });
        });
    }

    // 3. Tombol Tambah Baris (Tanpa Reload)
    $('#btnTambahBarisTrf').on('click', function() {
        // Clone baris pertama secara bersih
        let $newRow = $('#tabelItemTrf tbody tr:first').clone();
        
        // Reset nilai-nilai di baris baru
        $newRow.find('input').val('1');
        $newRow.find('select').val('');
        
        // Tambahkan ke tabel
        $('#tabelItemTrf tbody').append($newRow);
        
        // Sinkronkan stok di baris yang baru ditambah
        updateBarangDropdowns();
    });

    // 4. Hapus Baris
    $(document).on('click', '.btn-remove-row', function() {
        if($('#tabelItemTrf tbody tr').length > 1) {
            $(this).closest('tr').remove();
        } else {
            Swal.fire('Peringatan', 'Minimal harus ada satu barang dalam transfer.', 'warning');
        }
    });

    // 5. Submit Form dengan AJAX (TANPA RELOAD)
    $('#formSIT').on('submit', function(e) {
        e.preventDefault(); // Mencegah reload halaman

        // Cek apakah ada barang yang belum dipilih
        let valid = true;
        $('.select-barang').each(function() {
            if($(this).val() == "") valid = false;
        });

        if(!valid) {
            Swal.fire('Peringatan', 'Silakan pilih barang terlebih dahulu!', 'warning');
            return;
        }

        const formData = $(this).serialize();
        const actionUrl = $(this).attr('action');

        // Di dalam fungsi submit $('#formSIT').on('submit', ...)
$.ajax({
    url: actionUrl,
    type: 'POST',
    // Gunakan FormData agar token ikut terkirim dan terbaca dengan benar
    data: new FormData(this), 
    processData: false,
    contentType: false,
    success: function(response) {
        Swal.fire('Berhasil', 'Transfer berhasil diproses!', 'success');
        // ... kode lainnya
    },
    error: function(xhr) {
        // Log error untuk debug
        console.log(xhr.responseText);
        Swal.fire('Error', 'Gagal memproses data. Cek CSRF Token.', 'error');
    }
});
    });
});

// Fungsi Detail SIT (Tanpa Reload)
function detailTrf(id) {
    $.get("<?= base_url('stoktransfer/get_detail') ?>/" + id, function(res) {
        $('#txtNoTrf').text(res.header.no_trf);
        $('#vUnitAsal').text(res.header.nama_asal);
        $('#vUnitTujuan').text(res.header.nama_tujuan);
        let html = '';
        res.items.forEach(item => {
            html += `<tr><td><b>${item.nama_barang}</b></td><td class="text-center fw-bold text-primary fs-5">${item.jumlah}</td></tr>`;
        });
        $('#bodyTrfDetail').html(html);
        $('#btnCetakSIT').attr('href', "<?= base_url('stoktransfer/cetak_sit') ?>/" + id);
        
        let footer = (res.header.status == 0) ? 
            `<button class="btn btn-success fw-bold" onclick="receiveTrf(${id})">KONFIRMASI PENERIMAAN</button>` : 
            `<span class="badge badge-light-success fs-7 fw-bold">✅ BARANG SUDAH DITERIMA</span>`;
        
        $('#footerTrfAction').html(footer);
        $('#modalTrfDetail').modal('show');
    });
}

// Konfirmasi Penerimaan dengan AJAX (TANPA RELOAD)
function receiveTrf(id) {
    Swal.fire({ 
        title: 'Konfirmasi?', 
        text: "Pastikan fisik barang sudah sesuai di tujuan.", 
        icon: 'question', 
        showCancelButton: true, 
        confirmButtonColor: '#3085d6', 
        confirmButtonText: 'Ya, Sudah Sesuai!' 
    })
    .then((result) => { 
        if (result.isConfirmed) { 
            $.ajax({
                url: "<?= base_url('stoktransfer/receive') ?>/" + id,
                type: 'GET',
                success: function(response) {
                    Swal.fire('Berhasil', 'Konfirmasi Penerimaan Berhasil!', 'success');
                    $('#modalTrfDetail').modal('hide');
                    refreshTable(); // Update tabel utama tanpa reload
                },
                error: function() {
                    Swal.fire('Error', 'Gagal memproses konfirmasi.', 'error');
                }
            });
        } 
    });
}
</script>
<?= $this->endSection(); ?>