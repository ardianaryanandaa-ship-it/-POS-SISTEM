<?= $this->extend('layout/metronic'); ?>
<?= $this->section('content'); ?>

<div class="card shadow-sm">
    <div class="card-header border-0 pt-5">
        <h3 class="card-title align-items-start flex-column">
            <span class="card-label fw-bold fs-3 mb-1">Daftar Transaksi Penjualan</span>
            <span class="text-muted mt-1 fw-semibold fs-7">Pantau semua nota lunas, diskon, dan member</span>
        </h3>
        <div class="card-toolbar">
            <div class="d-flex align-items-center position-relative my-1">
                <i class="ki-duotone ki-magnifier fs-3 position-absolute ms-5"><span class="path1"></span><span class="path2"></span></i>
                <input type="text" id="search_inv" class="form-control form-control-solid w-250px ps-13" placeholder="Cari No. Invoice..." />
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="kt_table_riwayat" class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
                <thead>
                    <tr class="fw-bold text-muted bg-light">
                        <th class="ps-4 rounded-start">Invoice</th>
                        <th class="text-center">Waktu</th>
                        <th class="text-center">Unit</th>
                        <th class="text-center">Member</th>
                        <th class="text-center">Kasir</th>
                        <th class="text-end">Diskon</th>
                        <th class="text-end">Total Bayar</th>
                        <th class="text-center">Status</th>
                        <th class="text-end pe-4 rounded-end">Aksi</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Detail -->
<div class="modal fade" id="modal_detail" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-650px">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h3 class="fw-bold">Detail Transaksi: <span id="span_inv" class="text-primary"></span></h3>
                <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal">X</div>
            </div>
            <div class="modal-body">
                <table class="table table-bordered align-middle">
                    <thead class="bg-light">
                        <tr class="fw-bold text-gray-800">
                            <th class="ps-3">Produk</th>
                            <th class="text-center">Qty</th>
                            <th class="text-end pe-3">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody id="body_detail"></tbody>
                    <!-- BAGIAN RINGKASAN BARU -->
                    <tfoot id="footer_detail" class="bg-light-secondary">
                        <!-- Akan diisi via JavaScript -->
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>  
<!-- Library Moment.js untuk format tanggal jika belum ada di layout -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    var tableRiwayat;

    $(document).ready(function() {
        tableRiwayat = $('#kt_table_riwayat').DataTable({
            dom: 'rtip',
            processing: true,
            serverSide: false, // Set false jika data tidak jutaan baris
            ajax: { 
                url: "<?= base_url('penjualan/list') ?>", 
                dataSrc: "" 
            },
            columns: [
                { data: "invoice_no", className: "ps-4 fw-bold text-gray-800" }, // 1
                { 
                    data: "created_at", 
                    className: "text-center",
                    render: (data) => data ? moment(data).format('DD/MM/YYYY HH:mm') : '-' 
                }, // 2
                { data: "nama_unit", className: "text-center" }, // 3
                { 
                    data: "nama_pelanggan", 
                    className: "text-center",
                    render: (data) => data ? `<span class="badge badge-light-primary fw-bold">${data}</span>` : '<span class="text-muted small">Umum</span>' 
                }, // 4
                { data: "nama_kasir", className: "text-center" }, // 5
                { 
                    data: "discount", 
                    className: "text-end",
                    render: function(data, type, row) {
                        if (data && data > 0) {
                            return `
                                <div class="d-flex flex-column">
                                    <span class="text-danger fw-bold">- Rp ${Number(data).toLocaleString('id-ID')}</span>
                                    <span class="badge badge-light-danger fs-9 px-2 py-0 fw-bold" style="width:fit-content; align-self:end;">
                                        ${row.nama_promo ? row.nama_promo : 'Promo'}
                                    </span>
                                </div>
                            `;
                        }
                        return '<span class="text-muted small">Rp 0</span>';
                    }
                }, // 6
                { 
                    data: "total_price", 
                    className: "text-end fw-boldest text-success fs-6",
                    render: function(data, type, row) {
                        let netto = Number(row.total_price) - Number(row.discount);
                        return "Rp " + netto.toLocaleString('id-ID');
                    }
                }, // 7
                { 
                    // TAMBAHKAN KOLOM STATUS (KE-8)
                    data: "status", 
                    className: "text-center",
                    render: (data) => {
                        let color = (data == 'Lunas') ? 'success' : 'danger';
                        return `<span class="badge badge-light-${color} fw-bold">${data}</span>`;
                    }
                }, // 8
                { 
                    // TAMBAHKAN KOLOM AKSI (KE-9)
                    data: "id",
                    className: "text-end pe-4",
                    orderable: false,
                    render: function(data, type, row) {
                        return `
                            <button class="btn btn-icon btn-bg-light btn-active-color-info btn-sm me-1" onclick="lihatDetail('${data}', '${row.invoice_no}')" title="Detail"><i class="ki-duotone ki-eye fs-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i></button>
                            <button class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1" onclick="cetakUlang('${data}')" title="Cetak"><i class="ki-duotone ki-printer fs-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span></i></button>
                            ${row.status == 'Lunas' ? `<button class="btn btn-icon btn-bg-light btn-active-color-danger btn-sm" onclick="voidNota('${data}')" title="Void"><i class="ki-duotone ki-trash-square fs-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></i></button>` : ''}
                        `;
                    }
                } // 9
            ], // <-- Perhatikan koma di sini
            order: [[1, 'desc']] 
        });

        $('#search_inv').on('keyup', function() { tableRiwayat.search(this.value).draw(); });
    });

    function lihatDetail(id, inv) {
    $('#span_inv').text(inv);
    $.get("<?= base_url('penjualan/get_detail_with_sales') ?>/" + id, function(res) {
        // Asumsi: res sekarang berisi { items: [], sales: {} }
        let html = '';
        res.items.forEach(it => {
            html += `<tr>
                        <td class="ps-3">
                            <div class="fw-bold text-gray-800">${it.nama_barang}</div>
                            <div class="text-muted small">@ Rp ${Number(it.price_at_sale).toLocaleString('id-ID')}</div>
                        </td>
                        <td class="text-center">${it.qty}</td>
                        <td class="text-end pe-3 fw-boldest text-dark">Rp ${Number(it.subtotal).toLocaleString('id-ID')}</td>
                     </tr>`;
        });
        $('#body_detail').html(html);

        // Render Footer Detail (Diskon & Promo)
        let sales = res.sales;
        let netto = Number(sales.total_price) - Number(sales.discount);
        let footerHtml = `
            <tr>
                <td colspan="2" class="text-end fw-bold">Subtotal:</td>
                <td class="text-end pe-3">Rp ${Number(sales.total_price).toLocaleString('id-ID')}</td>
            </tr>
            <tr class="text-danger">
                <td colspan="2" class="text-end fw-bold">Potongan (${sales.nama_promo ?? 'Promo'}):</td>
                <td class="text-end pe-3">- Rp ${Number(sales.discount).toLocaleString('id-ID')}</td>
            </tr>
            <tr class="fs-4">
                <td colspan="2" class="text-end fw-boldest text-gray-800">Total Akhir:</td>
                <td class="text-end pe-3 fw-boldest text-primary">Rp ${netto.toLocaleString('id-ID')}</td>
            </tr>
        `;
        $('#footer_detail').html(footerHtml);
        $('#modal_detail').modal('show');
    });
}

    function cetakUlang(id) {
        window.open("<?= base_url('kasir/cetak') ?>/" + id, "_blank");
    }

    function voidNota(id) {
        Swal.fire({
            title: 'Batalkan Transaksi?',
            text: "Stok akan dikembalikan dan poin member akan dikurangi otomatis.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Void Nota!',
            cancelButtonText: 'Batal',
            customClass: { confirmButton: 'btn btn-danger', cancelButton: 'btn btn-light' }
        }).then((result) => {
            if (result.isConfirmed) {
                $.post("<?= base_url('penjualan/void') ?>/" + id, { 
                    "<?= csrf_token() ?>": "<?= csrf_hash() ?>" 
                }, function(res) {
                    if(res.status) {
                        Swal.fire('Berhasil!', res.message, 'success');
                        tableRiwayat.ajax.reload();
                    } else {
                        Swal.fire('Gagal!', res.message, 'error');
                    }
                }).fail(function() {
                    Swal.fire('Error', 'Terjadi kesalahan sistem atau masalah hak akses.', 'error');
                });
            }
        });
    }
</script>

<?= $this->endSection(); ?>