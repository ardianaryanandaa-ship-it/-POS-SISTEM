<?= $this->extend('layout/metronic'); ?>
<?= $this->section('content'); ?>

<div class="card shadow-sm">
    <div class="card-header border-0 pt-5">
        <h3 class="card-title align-items-start flex-column">
            <span class="card-label fw-bold fs-3 mb-1">Daftar Bisnis Unit</span>
        </h3>
        <div class="card-toolbar">
            <div class="d-flex align-items-center position-relative my-1 me-5">
                <i class="ki-duotone ki-magnifier fs-3 position-absolute ms-5">
                    <span class="path1"></span><span class="path2"></span>
                </i>
                <input type="text" id="kt_filter_search" class="form-control form-control-solid w-250px ps-13" placeholder="Cari data..." />
            </div>
            
            <button type="button" class="btn btn-sm btn-primary" onclick="addUnit()">
                <i class="ki-duotone ki-plus fs-2">
                    <span class="path1"></span><span class="path2"></span>
                </i> Tambah Unit
            </button>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="kt_datatable_bisnisunit" class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
                <thead>
                    <tr class="fw-bold text-muted bg-light">
                        <th class="ps-4 rounded-start">Kode</th>
                        <th>Nama Unit</th>
                        <th>Fungsi Unit</th> <!-- Kolom Baru -->
                        <th>Wilayah</th> <!-- Kolom Baru -->
                        <th>Jenis Bisnis</th>
                        <th>Alamat</th>
                        <th class="text-end pe-4 rounded-end">Aksi</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Form -->
<div class="modal fade" id="unitModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-650px"> <!-- Lebar modal disesuaikan -->
        <form id="unitForm" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Tambah Unit</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="is_edit" id="is_edit" value="0">
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="required form-label">Kode Unit</label>
                        <input type="text" name="kode_unit" id="kode_unit" class="form-control form-control-solid" required>
                    </div>
                    <div class="col-md-6">
                        <label class="required form-label">Nama Unit</label>
                        <input type="text" name="nama_unit" id="nama_unit" class="form-control form-control-solid" required>
                    </div>
                </div>

                <!-- INPUT BARU: FUNGSI UNIT & WILAYAH -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="required form-label">Fungsi Unit</label>
                        <select name="jenis_unit" id="jenis_unit" class="form-select form-select-solid" required>
                            <option value="GUDANG">GUDANG (Pusat)</option>
                            <option value="RETAIL">RETAIL (Cabang)</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="required form-label">Wilayah</label>
                        <input type="text" name="wilayah" id="wilayah" class="form-control form-control-solid" placeholder="Contoh: Jawa Tengah" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Keterangan Bisnis</label>
                    <input type="text" name="jenis" id="jenis" class="form-control form-control-solid">
                </div>
                <div class="mb-3">
                    <label class="form-label">Alamat</label>
                    <textarea name="alamat" id="alamat" class="form-control form-control-solid" rows="2"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary" id="btnSave">Simpan</button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    var myModal;
    var dataTable;
    
    $(document).ready(function() {
        if (document.getElementById('unitModal')) {
            myModal = new bootstrap.Modal(document.getElementById('unitModal'));
        }
        loadData();

        $('#kt_filter_search').on('keyup', function() {
            dataTable.search(this.value).draw();
        });

        $('#unitForm').submit(function(e) {
            e.preventDefault();
            let formData = $(this).serialize();
            let isEdit = $('#is_edit').val();
            let url = isEdit == "1" ? "<?= base_url('bisnisunit/update') ?>" : "<?= base_url('bisnisunit/save') ?>";

            $.ajax({
                url: url, type: "POST", data: formData, dataType: "JSON",
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
        if ($.fn.DataTable.isDataTable('#kt_datatable_bisnisunit')) {
            $('#kt_datatable_bisnisunit').DataTable().destroy();
        }

        dataTable = $('#kt_datatable_bisnisunit').DataTable({
            dom: 'rtip',
            ajax: { url: "<?= base_url('bisnisunit/list') ?>", dataSrc: "" },
            columns: [
                { data: "kode_unit", className: "ps-4 fw-bold" },
                { data: "nama_unit" },
                { 
                    data: "jenis_unit", 
                    render: function(data) {
                        let color = data === 'GUDANG' ? 'primary' : 'success';
                        return `<span class="badge badge-light-${color} fw-bold">${data}</span>`;
                    }
                },
                { data: "wilayah" },
                { data: "jenis", render: (data) => data || '-' },
                { data: "alamat", render: (data) => data || '-' },
                { 
                    data: "kode_unit", className: "text-end pe-4", orderable: false,
                    render: function(data) {
                        return `
                            <button type="button" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1" onclick="editUnit('${data}')" title="Edit Unit">
                                <i class="ki-duotone ki-pencil fs-2">
                                    <span class="path1"></span><span class="path2"></span>
                                </i>
                            </button>
                            <button type="button" class="btn btn-icon btn-bg-light btn-active-color-danger btn-sm" onclick="deleteUnit('${data}')" title="Hapus Unit">
                                <i class="ki-duotone ki-trash fs-2">
                                    <span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span>
                                </i>
                            </button>
                        `;
                    }
                }
            ]
        });
    }

    function addUnit() {
        $('#unitForm')[0].reset();
        $('#is_edit').val("0");
        $('#kode_unit').attr('readonly', false);
        $('#modalTitle').text('Tambah Unit');
        myModal.show();
    }

    function editUnit(id) {
        $.ajax({
            url: "<?= base_url('bisnisunit/get/') ?>/" + id,
            type: "GET", dataType: "JSON",
            success: function(data) {
                if(data) {
                    $('#is_edit').val("1");
                    $('#kode_unit').val(data.kode_unit).attr('readonly', true);
                    $('#nama_unit').val(data.nama_unit);
                    $('#jenis_unit').val(data.jenis_unit); 
                    $('#wilayah').val(data.wilayah); 
                    $('#jenis').val(data.jenis);
                    $('#alamat').val(data.alamat);
                    $('#modalTitle').text('Edit Unit');
                    myModal.show();
                }
            }
        });
    }

    function deleteUnit(id) {
        Swal.fire({
            title: 'Hapus data?', icon: 'warning', showCancelButton: true, confirmButtonText: 'Ya, Hapus!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "<?= base_url('bisnisunit/delete/') ?>/" + id,
                    type: "GET", dataType: "JSON",
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