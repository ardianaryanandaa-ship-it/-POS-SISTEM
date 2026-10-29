<?= $this->extend('layout/metronic') ?>

<?= $this->section('content') ?>
<!-- Tambahan CSS untuk merapikan kontrol DataTable -->
<style>
    /* Merapikan posisi Search dan Length Menu agar sejajar dan cantik */
    .dataTables_wrapper .row:first-child {
        padding: 0 1.25rem 1rem 1.25rem;
        align-items: center;
    }
    .dataTables_filter input {
        margin-left: 0.5rem !important;
    }
</style>

<div class="card card-flush shadow-sm">
    <div class="card-header align-items-center py-5 gap-2 gap-md-5">
        <div class="card-title">
            <div class="d-flex flex-column">
                <h3 class="fw-bolder fs-3 mb-1 text-gray-900">Riwayat Stok Keluar</h3>
                <span class="text-muted fw-bold fs-7">Monitoring data pengeluaran stok barang secara real-time</span>
            </div>
        </div>
        <div class="card-toolbar">
            <!-- Tempat tombol tambahan jika ada di masa depan -->
        </div>
    </div>

    <div class="card-body pt-0">
        <div class="table-responsive">
            <!-- Menggunakan table-row-dashed agar lebih bersih dibanding table-striped -->
            <table id="kt_table_stok" class="table align-middle table-row-dashed fs-6 gy-5 gs-7">
                <thead>
                    <tr class="text-start text-gray-400 fw-bold fs-7 text-uppercase gs-0">
                        <th class="min-w-70px">ID Stok</th>
                        <th class="text-center min-w-100px">Jumlah Keluar</th>
                        <th class="min-w-100px">ID Penjualan</th>
                        <th class="text-end min-w-70px">Aksi</th>
                    </tr>
                </thead>
                <tbody class="fw-semibold text-gray-600">
                    <?php foreach ($stok_keluar as $s) : ?>
                    <tr>
                        <td class="ps-0">
                            <span class="text-gray-800 fw-bold">#<?= $s['id_stok']; ?></span>
                        </td>
                        <td class="text-center">
                            <div class="badge badge-light-danger fw-bolder px-4 py-3">
                                <?= number_format($s['jumlah_stok_keluar'], 0, ',', '.'); ?>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <i class="ki-duotone ki-abstract-26 fs-4 text-primary me-2"><span class="path1"></span><span class="path2"></span></i>
                                <span class="text-gray-700 fw-bold">Penjualan #<?= $s['id_penjualan']; ?></span>
                            </div>
                        </td>
                        <td class="text-end">
                            <button class="btn btn-sm btn-icon btn-bg-light btn-active-color-primary btn-detail-stok" 
                                    data-id="<?= $s['id_penjualan']; ?>" 
                                    title="Lihat Detail">
                                <i class="ki-duotone ki-eye fs-2">
                                    <span class="path1"></span><span class="path2"></span><span class="path3"></span>
                                </i>
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Detail (Tetap Sama Seperti Sebelumnya) -->
<div class="modal fade" id="modal_detail_stok" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-600px">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="fw-bold">Rincian Penjualan</h2>
                <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                    <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                </div>
            </div>
            <div class="modal-body py-10 px-lg-17" id="printableArea">
                <div class="table-responsive">
                    <table class="table table-row-bordered table-row-gray-300 align-middle gs-0 gy-4">
                        <tbody>
                            <tr>
                                <td class="fw-bold text-gray-600 w-200px">ID Penjualan</td>
                                <td class="fw-bolder text-gray-800" id="det_id"></td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-gray-600">Produk</td>
                                <td class="fw-bolder text-primary fs-5" id="det_barang"></td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-gray-600">Jumlah Keluar</td>
                                <td class="fw-bolder text-gray-800" id="det_jumlah"></td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-gray-600">Harga Modal</td>
                                <td class="fw-bolder text-gray-800" id="det_modal"></td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-gray-600">Harga Jual</td>
                                <td class="fw-bolder text-gray-800" id="det_jual"></td>
                            </tr>
                            <tr class="bg-light-success">
                                <td class="fw-bold text-success ps-3">Total Keuntungan</td>
                                <td class="fw-bolder text-success fs-4" id="det_untung"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer flex-center">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary" id="btn_cetak_rincian">
                    <i class="ki-duotone ki-printer fs-3"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span></i> Cetak Struk
                </button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Inisialisasi DataTables dengan styling yang lebih rapi
    $('#kt_table_stok').DataTable({
        "language": {
            "lengthMenu": "Tampilkan _MENU_",
            "search": "Cari:",
            "zeroRecords": "Data tidak ditemukan",
            "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
            "paginate": {
                "next": '<i class="ki-duotone ki-right fs-2"></i>',
                "previous": '<i class="ki-duotone ki-left fs-2"></i>'
            }
        },
        "pageLength": 10,
        "order": [[ 0, "desc" ]], // Urutkan berdasarkan ID Stok terbaru
        "dom": "<'row'<'col-sm-12 col-md-6 d-flex align-items-center justify-content-start'l><'col-sm-12 col-md-6 d-flex align-items-center justify-content-end'f>>" +
               "<'table-responsive'tr>" +
               "<'row'<'col-sm-12 col-md-5 d-flex align-items-center justify-content-center justify-content-md-start'i><'col-sm-12 col-md-7 d-flex align-items-center justify-content-center justify-content-md-end'p>>",
    });

    // Script AJAX Detail (Sama Seperti Sebelumnya)
    $(document).off('click', '.btn-detail-stok').on('click', '.btn-detail-stok', function() {
        const id = $(this).data('id');
        const $btn = $(this);
        $btn.attr('disabled', true).html('<span class="spinner-border spinner-border-sm"></span>');

        $.ajax({
            url: '<?= base_url('stok-keluar/get_detail') ?>/' + id,
            type: 'GET',
            dataType: 'json',
            success: function(res) {
                if(res) {
                    $('#det_id').text('#' + res.id_penjualan);
                    $('#det_barang').text(res.nama_barang);
                    $('#det_jumlah').text(res.jumlah_stok_keluar + ' Item');
                    const formatRupiah = (num) => 'Rp ' + new Intl.NumberFormat('id-ID').format(num);
                    let profit = (res.harga_jual - res.harga_modal) * res.jumlah_stok_keluar;
                    $('#det_modal').text(formatRupiah(res.harga_modal));
                    $('#det_jual').text(formatRupiah(res.harga_jual));
                    $('#det_untung').text(formatRupiah(profit));
                    $('#modal_detail_stok').modal('show');
                }
            },
            complete: function() {
                $btn.attr('disabled', false).html('<i class="ki-duotone ki-eye fs-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>');
            }
        });
    });

    // Fungsi Cetak (Sama Seperti Sebelumnya)
    $('#btn_cetak_rincian').on('click', function() {
        var printContents = document.getElementById('printableArea').innerHTML;
        var originalContents = document.body.innerHTML;
        document.body.innerHTML = "<html><head><title>Struk Penjualan</title></head><body><h2 style='text-align:center'>RINGKASAN PENJUALAN</h2>" + printContents + "</body></html>";
        window.print();
        document.body.innerHTML = originalContents;
        location.reload(); 
    });
});
</script>
<?= $this->endSection() ?>