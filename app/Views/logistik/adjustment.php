<?= $this->extend('layout/metronic'); ?>
<?= $this->section('content'); ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="card shadow-sm">
    <div class="card-header border-0 pt-5">
        <h3 class="card-title fw-bold">🛠️ Inventory Adjustment (Stok Opname)</h3>
        <div class="card-toolbar">
            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addAdjModal">+ Buat Opname Baru</button>
        </div>
    </div>
    <div class="card-body">
        <table class="table table-row-dashed align-middle">
            <thead>
                <tr class="fw-bold text-muted bg-light ps-4">
                    <th class="ps-4">No. Adjustment</th>
                    <th>Tanggal</th>
                    <th>Petugas</th>
                    <th class="text-center">Status</th>
                    <th class="text-end pe-4">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($adjustments as $adj) : ?>
                <tr>
                    <td class="ps-4"><span class="fw-bold text-primary">#<?= $adj['no_adj']; ?></span></td>
                    <td><?= date('d M Y H:i', strtotime($adj['tanggal'])); ?></td>
                    <td><?= $adj['user']; ?></td>
                    <td class="text-center">
                        <span class="badge badge-light-<?= $adj['status']==0?'warning':'success' ?> fw-bold">
                            <?= $adj['status']==0?'DRAFT':'APPLIED' ?>
                        </span>
                    </td>
                    <td class="text-end pe-4">
                        <button class="btn btn-sm btn-icon btn-bg-light btn-active-color-primary" onclick="detailAdj(<?= $adj['id_adj']; ?>)">
                            <i class="ki-duotone ki-eye fs-2"></i>
                        </button>
                        <?php if($adj['status'] == 0) : ?>
                            <a href="<?= base_url('stokadjustment/delete/'.$adj['id_adj']) ?>" class="btn btn-sm btn-icon btn-bg-light btn-active-color-danger" onclick="return confirm('Hapus draft?')">
                                <i class="ki-duotone ki-trash fs-2"></i>
                            </a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- MODAL INPUT OPNAME BARU -->
<div class="modal fade" id="addAdjModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-900px">
        <form action="<?= base_url('stokadjustment/save') ?>" method="post" class="modal-content">
            <div class="modal-header"><h3 class="fw-bold">Input Hasil Stok Opname</h3></div>
            <div class="modal-body">
                <table class="table table-bordered" id="tabelInputAdj">
                    <thead class="bg-light">
                        <tr class="fw-bold">
                            <th>Nama Barang</th>
                            <th class="w-150px">Stok Fisik (Nyata)</th>
                            <th>Alasan / Keterangan</th>
                            <th class="w-50px">Hapus</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <select name="kode_barang[]" class="form-select form-select-solid" required>
                                    <option value="">Pilih Barang...</option>
                                    <?php foreach($list_barang as $lb): ?>
                                        <option value="<?= $lb['kode_barang'] ?>"><?= $lb['nama_barang'] ?> (Sisa: <?= $lb['stok_akhir'] ?>)</option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                            <td><input type="number" name="stok_fisik[]" class="form-control" placeholder="0" required></td>
                            <td><input type="text" name="alasan[]" class="form-control" placeholder="Misal: Barang Rusak"></td>
                            <td class="text-center"><button type="button" class="btn btn-sm btn-icon btn-light-danger btn-remove"><i class="ki-duotone ki-trash fs-3"></i></button></td>
                        </tr>
                    </tbody>
                </table>
                <button type="button" class="btn btn-sm btn-light-primary mt-2" id="btnTambahBarisAdj">+ Tambah Barang</button>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary w-100 fw-bold">SIMPAN SEBAGAI DRAFT</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL DETAIL & APPLY (LANDSCAPE mw-1200px) -->
<div class="modal fade" id="modalAdjDetail" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-1200px">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="fw-bold">Rincian Selisih Stok: <span id="txtNoAdj" class="text-primary"></span></h3>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-bordered align-middle gs-5 gy-3">
                        <thead class="bg-light fw-bold text-gray-800">
                            <tr>
                                <th>Nama Barang</th>
                                <th class="text-center">Stok Sistem</th>
                                <th class="text-center">Stok Fisik</th>
                                <th class="text-center">Selisih</th>
                                <th>Alasan</th>
                            </tr>
                        </thead>
                        <tbody id="bodyAdjDetail"></tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer justify-content-between bg-light">
                <span class="text-muted fs-8 italic">* Klik Apply untuk merubah stok master menjadi sesuai Stok Fisik.</span>
                <div id="footerAdjAction"></div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#btnTambahBarisAdj').click(function() {
        let row = $('#tabelInputAdj tbody tr:first').clone();
        row.find('input').val('');
        $('#tabelInputAdj tbody').append(row);
    });
    $(document).on('click', '.btn-remove', function() {
        if($('#tabelInputAdj tbody tr').length > 1) $(this).closest('tr').remove();
    });
});

function detailAdj(id) {
    $.get("<?= base_url('stokadjustment/get_detail/') ?>" + id, function(res) {
        $('#txtNoAdj').text(res.header.no_adj);
        let html = '';
        res.items.forEach(item => {
            let color = item.selisih < 0 ? 'text-danger' : (item.selisih > 0 ? 'text-success' : 'text-gray-400');
            html += `<tr>
                <td><div class="fw-bold text-gray-800">${item.nama_barang}</div><small>${item.kode_barang}</small></td>
                <td class="text-center bg-light">${item.stok_sistem}</td>
                <td class="text-center fw-bold text-primary fs-5">${item.stok_fisik}</td>
                <td class="text-center fw-bold ${color} fs-5">${item.selisih > 0 ? '+'+item.selisih : item.selisih}</td>
                <td>${item.alasan || '-'}</td>
            </tr>`;
        });
        $('#bodyAdjDetail').html(html);

        let footer = (res.header.status == 0) ? 
            `<button class="btn btn-success fw-bold" onclick="applyAdj(${id})">✅ APPLY & UPDATE MASTER STOK</button>` : 
            `<span class="badge badge-light-success fs-6 fw-bold">✅ STOK SUDAH DISINKRONKAN</span>`;
        
        $('#footerAdjAction').html(footer);
        $('#modalAdjDetail').modal('show');
    });
}

function applyAdj(id) {
    Swal.fire({
        title: 'Apply Penyesuaian Stok?',
        text: "Stok di tabel Master Barang akan DIUBAH TOTAL mengikuti kolom Stok Fisik!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, Sinkronkan!'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = "<?= base_url('stokadjustment/apply/') ?>" + id;
        }
    });
}
</script>

<?= $this->endSection(); ?>