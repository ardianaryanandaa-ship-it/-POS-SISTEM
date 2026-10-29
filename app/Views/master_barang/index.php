<?= $this->extend('layout/metronic'); ?>
<?= $this->section('content'); ?>

<style>
    /* Indikasi visual untuk varian di tabel */
    .variant-row { background-color: #fcfcfc !important; }
    .badge-variant { background-color: #e8fff3; color: #50cd89; border: 1px dashed #50cd89; font-size: 0.7rem; }
</style>

<div class="card shadow-sm">
    <div class="card-header border-0 pt-5">
        <h3 class="card-title align-items-start flex-column">
            <span class="card-label fw-bold fs-3 mb-1">Master Barang</span>
            <span class="text-muted mt-1 fw-semibold fs-7">Kelola daftar katalog barang dan tipe variannya</span>
        </h3>
        <div class="card-toolbar">
            <div class="d-flex align-items-center position-relative my-1 me-5">
                <i class="ki-duotone ki-magnifier fs-3 position-absolute ms-5"><span class="path1"></span><span class="path2"></span></i>
                <input type="text" id="kt_filter_search" class="form-control form-control-solid w-250px ps-13" placeholder="Cari barang..." />
            </div>

            <button type="button" class="btn btn-sm btn-primary" onclick="addBarang()">
                <i class="ki-duotone ki-plus fs-2"></i> Tambah Barang / Varian
            </button>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="kt_datatable_master_barang" class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
                <thead>
                    <tr class="fw-bold text-muted bg-light">
                        <th class="ps-4 rounded-start">Kode</th>
                        <th>Nama Barang & Tipe</th>
                        <th>Kategori</th>
                        <th>Harga Beli</th>
                        <th class="text-primary">Harga Jual</th>
                        <th>Satuan</th>
                        <th class="text-end pe-4 rounded-end">Aksi</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Form -->
<div class="modal fade" id="modalBarang" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-650px">
        <form id="formBarang" class="modal-content">
            <div class="modal-header">
                <h2 class="fw-bold" id="modalTitle">Tambah Master Barang</h2>
                <button type="button" class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">X</button>
            </div>
            <div class="modal-body py-10 px-lg-17">
                <input type="hidden" name="is_edit" id="is_edit" value="0">
                
                <div class="row mb-7">
                    <div class="col-md-6 fv-row">
                        <label class="required fs-6 fw-semibold mb-2">Kode Barang (Barcode)</label>
                        <div class="input-group input-group-solid">
                            <input type="text" name="kode_barang" id="kode_barang" class="form-control form-control-solid" required>
                            <button type="button" class="btn btn-light-primary" onclick="generateRandomBarcode()">Auto</button>
                        </div>
                    </div>
                    <div class="col-md-6 fv-row">
                        <label class="required fs-6 fw-semibold mb-2">Kategori</label>
                        <select name="id_kategori" id="id_kategori" class="form-select form-select-solid" required>
                            <option value="">Pilih Kategori</option>
                            <?php foreach($kategori as $k) : ?>
                            <option value="<?= $k['id_kategori']; ?>"><?= $k['nama_kategori']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="row mb-7">
                    <div class="col-md-6 fv-row">
                        <label class="fs-6 fw-semibold mb-2 text-primary">Produk Utama (Induk)</label>
                        <select name="parent_kode" id="parent_kode" class="form-select form-select-solid border-primary" data-control="select2" data-placeholder="Kosongkan jika Produk Utama">
                            <option value="">-- Produk Utama (Bukan Varian) --</option>
                            <?php foreach($induk_barang as $induk) : ?>
                                <option value="<?= $induk['kode_barang']; ?>"><?= $induk['nama_barang']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6 fv-row" id="container_nama_varian" style="display:none;">
                        <label class="required fs-6 fw-semibold mb-2 text-primary">Nama Tipe / Varian</label>
                        <input type="text" name="nama_varian" id="nama_varian" class="form-control form-control-solid border-primary" placeholder="Misal: i5/8GB/256GB">
                    </div>
                </div>

                <div class="mb-7 fv-row">
                    <label class="required fs-6 fw-semibold mb-2">Nama Barang</label>
                    <input type="text" name="nama_barang" id="nama_barang" class="form-control form-control-solid" required placeholder="Nama Produk Utama">
                </div>

                <!-- Cari bagian di bawah Nama Barang, tambahkan kode ini sebelum Harga Beli -->
<div class="separator separator-dashed my-8"></div>

<div class="mb-7">
    <div class="d-flex flex-stack mb-3">
        <label class="fs-6 fw-bold text-gray-800">Daftar Tipe / Varian (Opsional)</label>
        <button type="button" class="btn btn-sm btn-light-success fw-bold" onclick="addRowVarian()">
            <i class="ki-duotone ki-plus fs-3"></i> Tambah Tipe
        </button>
    </div>
    
    <div class="table-responsive border rounded">
        <table class="table table-striped align-middle gs-4 gy-3 mb-0" id="table_input_varian">
            <thead class="bg-light">
                <tr class="fw-bold fs-8 text-gray-800 text-uppercase">
                    <th class="min-w-150px">Nama Tipe (Misal: RAM 8GB)</th>
                    <th class="min-w-150px">Kode Barcode (Unique)</th>
                    <th class="min-w-100px">Harga Jual</th>
                    <th class="w-50px"></th>
                </tr>
            </thead>
            <tbody id="container_list_varian">
                <!-- Baris varian muncul di sini -->
            </tbody>
        </table>
    </div>
    <div class="text-muted fs-8 mt-2">Kosongkan tabel jika barang ini tidak memiliki tipe/varian.</div>
</div>

                <div class="row mb-7">
                    <div class="col-md-6 fv-row">
                        <label class="required fs-6 fw-semibold mb-2">Harga Beli (Modal)</label>
                        <input type="number" name="harga_beli" id="harga_beli" class="form-control form-control-solid" required>
                    </div>
                    <div class="col-md-6 fv-row">
                        <label class="required fs-6 fw-semibold mb-2 text-success">Harga Jual (Kasir)</label>
                        <input type="number" name="harga_jual" id="harga_jual" class="form-control form-control-solid border-success" required>
                    </div>
                </div>

                <div class="fv-row">
                    <label class="fs-6 fw-semibold mb-2">Satuan Standar</label>
                    <input type="text" name="satuan_standar" id="satuan_standar" class="form-control form-control-solid" placeholder="Pcs / Box">
                </div>
            </div>
            <div class="modal-footer flex-center">
                <button type="button" class="btn btn-light me-3" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Data</button>
                <?= csrf_field() ?> 
            </div>
        </form>
    </div>
</div>

<!-- Modal Barcode -->
<div class="modal fade" id="modalBarcode" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered mw-450px">
        <div class="modal-content shadow-lg border-0">
            <div class="modal-header border-0 pb-0 justify-content-end">
                <button type="button" class="btn btn-icon btn-sm btn-active-color-primary" data-bs-dismiss="modal">
                    <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                </button>
            </div>
            <div class="modal-body text-center px-10 pb-10">
                <div class="symbol symbol-50px symbol-circle mb-5">
                    <span class="symbol-label bg-light-primary">
                        <i class="ki-duotone ki-barcode fs-2x text-primary"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span><span class="path6"></span><span class="path7"></span><span class="path8"></span></i>
                    </span>
                </div>
                <h3 class="fw-boldest text-gray-800 mb-1" id="barcodeProductTitle">Nama Barang</h3>
                <p class="text-muted fs-7 mb-7" id="barcodeProductCode">Kode: -</p>
                
                <div class="bg-light p-6 rounded-3 mb-6 d-flex justify-content-center align-items-center" style="min-height: 160px; border: 1px dashed #e4e6ef;">
                    <svg id="barcodePreview"></svg>
                </div>
                
                <div class="fv-row mb-8 text-start">
                    <label class="form-label fw-bold text-gray-700 fs-7 text-uppercase mb-2">Jumlah Cetak Label</label>
                    <input type="number" id="barcodePrintQty" class="form-control form-control-solid fw-bold" value="12" min="1" max="100">
                    <div class="text-muted fs-8 mt-1">Masukkan jumlah copy barcode yang ingin dicetak.</div>
                </div>

                <div class="d-flex gap-3">
                    <button type="button" class="btn btn-light w-50 fw-bold" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary w-50 fw-bold shadow-sm" onclick="printBarcodeAction()">
                        <i class="ki-duotone ki-printer fs-2 me-1"><span class="path1"></span><span class="path2"></span></i> Cetak Barcode
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>

<script>
    var tableBarang;

    function formatRupiah(angka) {
        return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(angka);
    }

    function initDataTableBarang() {
        if ($.fn.DataTable.isDataTable('#kt_datatable_master_barang')) {
            $('#kt_datatable_master_barang').DataTable().destroy();
        }

        tableBarang = $('#kt_datatable_master_barang').DataTable({
            dom: 'rtip',
            processing: true,
            ajax: { url: "<?= base_url('master-barang/list') ?>", dataSrc: "" },
            columns: [
                { data: "kode_barang", className: "ps-4 fw-bold text-gray-800" },
                { 
                    data: "nama_barang", 
                    render: function(data, type, row) {
                        let res = `<div class="fw-bold text-gray-800">${data}</div>`;
                        if (row.nama_varian) {
                            res += `<span class="badge badge-variant">Tipe: ${row.nama_varian}</span>`;
                        }
                        return res;
                    }
                },
                { data: "nama_kategori", render: (data) => `<span class="badge badge-light-primary fw-bold">${data}</span>` },
                { data: "harga_beli", render: (data) => `<span class="text-gray-600">${formatRupiah(data)}</span>` },
                { data: "harga_jual", render: (data) => `<span class="fw-boldest text-primary">${formatRupiah(data)}</span>` },
                { data: "satuan_standar", render: (data) => data || '-' },
                { 
                    data: "kode_barang",
                    className: "text-end pe-4",
                    render: function(data) {
                        return `
                            <button type="button" class="btn btn-icon btn-bg-light btn-active-color-success btn-sm me-1" onclick="showBarcodeModal('${data}')"><i class="ki-duotone ki-barcode fs-2"></i></button>
                            <button type="button" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1" onclick="editBarang('${data}')"><i class="ki-duotone ki-pencil fs-2"></i></button>
                            <button type="button" class="btn btn-icon btn-bg-light btn-active-color-danger btn-sm" onclick="deleteBarang('${data}')"><i class="ki-duotone ki-trash fs-2"></i></button>
                        `;
                    }
                }
            ],
            createdRow: function(row, data, dataIndex) {
                if (data.parent_kode) $(row).addClass('variant-row');
            }
        });
    }

    $(document).ready(function() {
        initDataTableBarang();

        // LOGIKA TOGGLE INPUT VARIAN
        $('#parent_kode').on('change', function() {
            let val = $(this).val();
            if (val !== "") {
                $('#container_nama_varian').slideDown();
                $('#nama_varian').attr('required', true);
                
                // Set otomatis nama barang agar sama dengan induknya
                let namaInduk = $(this).find('option:selected').text();
                $('#nama_barang').val(namaInduk);
            } else {
                $('#container_nama_varian').slideUp();
                $('#nama_varian').attr('required', false).val('');
            }
        });

        $('#formBarang').on('submit', function(e) {
            e.preventDefault();
            let isEdit = $('#is_edit').val();
            let kode = $('#kode_barang').val();
            let url = (isEdit == "1") ? "<?= base_url('master-barang/update') ?>/" + kode : "<?= base_url('master-barang/save') ?>";

            $.ajax({
                url: url,
                type: "POST",
                data: $(this).serialize(),
                success: function(res) {
                    if (res.status) {
                        $('#modalBarang').modal('hide');
                        Swal.fire('Berhasil', res.message, 'success');
                        location.reload(); // Reload agar dropdown Induk terupdate
                    } else {
                        Swal.fire('Gagal', res.message, 'error');
                    }
                }
            });
        });

        $('#kt_filter_search').on('keyup', function() { tableBarang.search(this.value).draw(); });
    });

    function addBarang() {
        $('#formBarang')[0].reset();
        $('#parent_kode').val('').trigger('change');
        $('#is_edit').val("0");
        $('#kode_barang').prop('readonly', false);
        $('#modalTitle').text('Tambah Master Barang');
        $('#modalBarang').modal('show');
    }

    function editBarang(id) {
        $.get("<?= base_url('master-barang/get/') ?>/" + id, function(data) {
            $('#is_edit').val("1");
            $('#kode_barang').val(data.kode_barang).prop('readonly', true);
            $('#parent_kode').val(data.parent_kode).trigger('change');
            $('#nama_barang').val(data.nama_barang);
            $('#nama_varian').val(data.nama_varian);
            $('#id_kategori').val(data.id_kategori);
            $('#satuan_standar').val(data.satuan_standar);
            $('#harga_beli').val(data.harga_beli);
            $('#harga_jual').val(data.harga_jual);
            $('#modalTitle').text('Edit Master Barang');
            $('#modalBarang').modal('show');
            // Jika yang diedit adalah produk induk, ambil varian anaknya dan tampilkan di tabel varian
            if (!data.parent_kode) {
                $('#container_list_varian').empty();
                $.get("<?= base_url('master-barang/get_variants/') ?>/" + data.kode_barang, function(vars) {
                    if (Array.isArray(vars)) {
                        vars.forEach(function(v) {
                            appendRowVarian(v.nama_varian, v.kode_barang, v.harga_jual);
                        });
                    }
                }, 'json');
            } else {
                // Jika mengedit varian tunggal, kosongkan daftar varian pada modal
                $('#container_list_varian').empty();
            }
        }, 'json');
    }

    function deleteBarang(id) {
        Swal.fire({
            title: 'Hapus barang?',
            icon: 'warning',
            showCancelButton: true
        }).then((result) => {
            if (result.isConfirmed) {
                $.get("<?= base_url('master-barang/delete/') ?>/" + id, function(res) {
                    Swal.fire('Terhapus!', res.message, 'success');
                    tableBarang.ajax.reload();
                }, 'json');
            }
        });
    }

    // Tambahkan fungsi ini di dalam tag <script> Anda

function addRowVarian() {
    let id = Date.now();
    let html = `
        <tr id="v_row_${id}">
            <td>
                <input type="text" name="v_nama[]" class="form-control form-control-sm form-control-solid" placeholder="Tipe..." required>
            </td>
            <td>
                <div class="input-group input-group-sm input-group-solid">
                    <input type="text" name="v_kode[]" id="v_input_kode_${id}" class="form-control" required>
                    <button type="button" class="btn btn-icon btn-light-primary" onclick="generateVarianBarcode(${id})">
                        <i class="ki-duotone ki-barcode fs-3"><span class="path1"></span><span class="path2"></span></i>
                    </button>
                </div>
            </td>
            <td>
                <input type="number" name="v_harga[]" class="form-control form-control-sm form-control-solid" placeholder="0" required>
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-icon btn-sm btn-light-danger" onclick="$('#v_row_${id}').remove()">
                    <i class="ki-duotone ki-trash fs-3"></i>
                </button>
            </td>
        </tr>
    `;
    $('#container_list_varian').append(html);
}

// Append a prefilled variant row (used when loading existing variants)
function appendRowVarian(nama, kode, harga) {
    let id = Date.now() + Math.floor(Math.random() * 1000);
    let html = `
        <tr id="v_row_${id}">
            <td>
                <input type="text" name="v_nama[]" class="form-control form-control-sm form-control-solid" value="${nama}" placeholder="Tipe..." required>
            </td>
            <td>
                <div class="input-group input-group-sm input-group-solid">
                    <input type="text" name="v_kode[]" id="v_input_kode_${id}" class="form-control" value="${kode}" required>
                    <button type="button" class="btn btn-icon btn-light-primary" onclick="generateVarianBarcode(${id})">
                        <i class="ki-duotone ki-barcode fs-3"><span class="path1"></span><span class="path2"></span></i>
                    </button>
                </div>
            </td>
            <td>
                <input type="number" name="v_harga[]" class="form-control form-control-sm form-control-solid" value="${harga}" placeholder="0" required>
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-icon btn-sm btn-light-danger" onclick="$('#v_row_${id}').remove()">
                    <i class="ki-duotone ki-trash fs-3"></i>
                </button>
            </td>
        </tr>
    `;
    $('#container_list_varian').append(html);
}

function generateVarianBarcode(id) {
    let rand = '899' + Math.floor(100000000 + Math.random() * 900000000);
    $(`#v_input_kode_${id}`).val(rand);
}

// Tambahkan trigger reset tabel saat klik "Tambah Barang"
function addBarang() {
    $('#formBarang')[0].reset();
    $('#container_list_varian').empty(); // Reset tabel varian
    $('#is_edit').val("0");
    $('#kode_barang').prop('readonly', false);
    $('#modalTitle').text('Tambah Master Barang');
    $('#modalBarang').modal('show');
}

    function generateRandomBarcode() {
        let rand = '899' + Math.floor(100000000 + Math.random() * 900000000);
        $('#kode_barang').val(rand);
    }

    currentBarcodeProduct = null;

    function showBarcodeModal(id) {
        $.get("<?= base_url('master-barang/get/') ?>/" + id, function(data) {
            currentBarcodeProduct = data;
            $('#barcodeProductTitle').text(data.nama_barang);
            $('#barcodeProductCode').text('Kode: ' + data.kode_barang);
            $('#barcodePrintQty').val(12); // default print 12 labels
            $('#modalBarcode').modal('show');
            
            // Render the barcode on preview SVG
            setTimeout(function() {
                JsBarcode("#barcodePreview", data.kode_barang, {
                    format: "CODE128",
                    width: 2,
                    height: 80,
                    displayValue: true,
                    fontSize: 14
                });
            }, 200);
        }, 'json');
    }

    function printBarcodeAction() {
        if (!currentBarcodeProduct) return;
        let qty = $('#barcodePrintQty').val() || 12;
        printBarcode(currentBarcodeProduct.kode_barang, currentBarcodeProduct.nama_barang, currentBarcodeProduct.harga_jual, qty);
    }

    function printBarcode(kode, nama, harga, qty) {
        let printWindow = window.open('', '_blank', 'width=800,height=600');
        printWindow.document.write(`
            <html>
            <head>
                <title>Cetak Barcode - ${nama}</title>
                <style>
                    body { font-family: 'Arial', sans-serif; margin: 0; padding: 20px; background: #fff; }
                    .grid-container {
                        display: grid;
                        grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
                        gap: 15px;
                    }
                    .barcode-card {
                        border: 1px dashed #ccc;
                        padding: 10px;
                        text-align: center;
                        background: #fff;
                        page-break-inside: avoid;
                        display: flex;
                        flex-direction: column;
                        align-items: center;
                        justify-content: center;
                    }
                    .product-title {
                        font-size: 10px;
                        font-weight: bold;
                        margin-bottom: 2px;
                        max-width: 160px;
                        white-space: nowrap;
                        overflow: hidden;
                        text-overflow: ellipsis;
                    }
                    .product-price {
                        font-size: 9px;
                        font-weight: bold;
                        color: #000;
                        margin-bottom: 5px;
                    }
                    .barcode-svg {
                        max-width: 100%;
                        height: auto;
                    }
                    @media print {
                        body { padding: 0; }
                        .barcode-card { border: 1px solid #ddd; }
                    }
                </style>
                <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"><\/script>
            </head>
            <body>
                <div class="grid-container">
        `);
        
        for (let i = 0; i < qty; i++) {
            printWindow.document.write(`
                <div class="barcode-card">
                    <div class="product-title">${nama}</div>
                    <div class="product-price">Rp ${Number(harga).toLocaleString('id-ID')}</div>
                    <svg class="barcode-svg" id="barcode_${i}"></svg>
                </div>
            `);
        }

        printWindow.document.write(`
                </div>
                <script>
                    window.onload = function() {
                        for (let i = 0; i < ${qty}; i++) {
                            JsBarcode("#barcode_" + i, "${kode}", {
                                format: "CODE128",
                                width: 1.5,
                                height: 40,
                                displayValue: true,
                                fontSize: 10,
                                margin: 5
                            });
                        }
                        setTimeout(function() {
                            window.print();
                            window.close();
                        }, 500);
                    };
                <\/script>
            </body>
            </html>
        `);
        printWindow.document.close();
    }
</script>

<?= $this->endSection(); ?>