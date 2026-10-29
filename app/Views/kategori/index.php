<?= $this->extend('layout/metronic'); ?>
<?= $this->section('content'); ?>

<div class="card shadow-sm">
    <div class="card-header border-0 pt-5">
        <h3 class="card-title align-items-start flex-column">
            <span class="card-label fw-bold fs-3 mb-1">Manajemen Kategori</span>
        </h3>
        <div class="card-toolbar">
            <!-- Filter Pencarian -->
            <div class="d-flex align-items-center position-relative my-1 me-5">
                <i class="ki-duotone ki-magnifier fs-3 position-absolute ms-5">
                    <span class="path1"></span><span class="path2"></span>
                </i>
                <input type="text" id="kt_filter_search" class="form-control form-control-solid w-250px ps-13" placeholder="Cari kategori..." />
            </div>

            <button type="button" class="btn btn-sm btn-primary" onclick="addKategori()">
                <i class="ki-duotone ki-plus fs-2">
                    <span class="path1"></span><span class="path2"></span>
                </i> Tambah Kategori
            </button>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="kt_datatable_kategori" class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
                <thead>
                    <tr class="fw-bold text-muted bg-light">
                        <th class="ps-4 rounded-start">ID</th>
                        <th>Nama Kategori</th>
                        <th>Deskripsi</th>
                        <th class="text-end pe-4 rounded-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Data diisi oleh DataTables AJAX -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Form (Tambah & Edit) -->
<div class="modal fade" id="modalKategori" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form id="formKategori" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Tambah Kategori</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="is_edit" id="is_edit" value="0">
                <input type="hidden" name="id_kategori" id="id_kategori">
                
                <div class="mb-3">
                    <label class="required form-label">Nama Kategori</label>
                    <input type="text" name="nama_kategori" id="nama_kategori" class="form-control form-control-solid" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Deskripsi</label>
                    <textarea name="deskripsi" id="deskripsi" class="form-control form-control-solid" rows="3"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    var myModal;
    var dataTable;

    $(document).ready(function() {
        // Inisialisasi Modal
        myModal = new bootstrap.Modal(document.getElementById('modalKategori'));

        // Load DataTables
        loadData();

        // Fitur Filter Pencarian
        $('#kt_filter_search').on('keyup', function() {
            dataTable.search(this.value).draw();
        });

        // Handle Simpan (AJAX)
        $('#formKategori').submit(function(e) {
            e.preventDefault();
            let formData = $(this).serialize();
            let isEdit = $('#is_edit').val();
            // Sesuaikan URL dengan route Anda
            let url = isEdit == "1" ? "<?= base_url('kategori/update') ?>/" + $('#id_kategori').val() : "<?= base_url('kategori/save') ?>";

            $.ajax({
                url: url,
                type: "POST",
                data: formData,
                dataType: "JSON",
                success: function(response) {
                    if (response.status) {
                        myModal.hide();
                        Swal.fire('Berhasil', response.message, 'success');
                        dataTable.ajax.reload(null, false);
                    }
                }
            });
        });
    });

    function loadData() {
        if ($.fn.DataTable.isDataTable('#kt_datatable_kategori')) {
            $('#kt_datatable_kategori').DataTable().destroy();
        }

        dataTable = $('#kt_datatable_kategori').DataTable({
            dom: 'rtip',
            ajax: {
                url: "<?= base_url('kategori/list') ?>", // Pastikan method list ada di Controller
                dataSrc: ""
            },
            columns: [
                { data: "id_kategori", className: "ps-4" },
                { data: "nama_kategori", className: "fw-bold" },
                { data: "deskripsi", render: (data) => data || '-' },
                { 
                    data: "id_kategori",
                    className: "text-end pe-4",
                    orderable: false,
                    render: function(data) {
                        return `
                            <button type="button" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1" onclick="editKategori('${data}')">
                                <i class="ki-duotone ki-pencil fs-2">
                                    <span class="path1"></span><span class="path2"></span>
                                </i>
                            </button>
                            <button type="button" class="btn btn-icon btn-bg-light btn-active-color-danger btn-sm" onclick="deleteKategori('${data}')">
                                <i class="ki-duotone ki-trash fs-2">
                                    <span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span>
                                </i>
                            </button>
                        `;
                    }
                }
            ],
            language: {
                zeroRecords: "Data tidak ditemukan",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                paginate: { next: "Sesudah", previous: "Sebelum" }
            }
        });
    }

    function addKategori() {
        $('#formKategori')[0].reset();
        $('#is_edit').val("0");
        $('#modalTitle').text('Tambah Kategori');
        myModal.show();
    }

    function editKategori(id) {
        $.ajax({
            url: "<?= base_url('kategori/get/') ?>/" + id, // Pastikan method get ada di Controller
            type: "GET",
            dataType: "JSON",
            success: function(data) {
                if(data) {
                    $('#is_edit').val("1");
                    $('#id_kategori').val(data.id_kategori);
                    $('#nama_kategori').val(data.nama_kategori);
                    $('#deskripsi').val(data.deskripsi);
                    $('#modalTitle').text('Edit Kategori');
                    myModal.show();
                }
            }
        });
    }

    function deleteKategori(id) {
        Swal.fire({
            title: 'Apakah anda yakin?',
            text: "Data akan dihapus secara permanen!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Hapus!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "<?= base_url('kategori/delete/') ?>/" + id,
                    type: "GET",
                    dataType: "JSON",
                    success: function(response) {
                        Swal.fire('Terhapus!', response.message, 'success');
                        dataTable.ajax.reload(null, false);
                    }
                });
            }
        });
    }
</script>

<?= $this->endSection(); ?>