<?= $this->extend('layout/metronic') ?>

<?= $this->section('content') ?>
<div class="card card-flush shadow-sm">
    <div class="card-header border-0 pt-6">
        <div class="card-title">
            <h3 class="card-label">Daftar Barang</h3>
        </div>
        <div class="card-toolbar">
            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modal_tambah">
                <i class="ki-duotone ki-plus fs-2"></i> Tambah Barang
            </button>
        </div>
    </div>
    
    <div class="card-body py-4">
        <div class="table-responsive">
            <table id="table_barang" class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
                <thead>
                    <tr class="fw-bolder text-muted">
                        <th class="w-50px">ID</th>
                        <th class="min-w-150px">Nama Barang</th>
                        <th class="min-w-100px text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($barang as $b) : ?>
                    <tr>
                        <td><?= $b['id_barang']; ?></td>
                        <td><span class="text-dark fw-bolder text-hover-primary fs-6"><?= $b['nama_barang']; ?></span></td>
                        <td class="text-end">
                            <button type="button" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1 btn-edit" 
                                    data-id="<?= $b['id_barang']; ?>" data-nama="<?= $b['nama_barang']; ?>">
                                <i class="bi bi-pencil-square"></i>
                            </button>
                            <button type="button" class="btn btn-icon btn-bg-light btn-active-color-danger btn-sm btn-delete" 
                                    data-url="<?= base_url('barang/hapus/' . $b['id_barang']); ?>">
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah -->
<div class="modal fade" tabindex="-1" id="modal_tambah">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="form_tambah">
                <?= csrf_field(); ?>
                <div class="modal-header">
                    <h3 class="modal-title">Tambah Barang Baru</h3>
                    <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal">
                        <i class="ki-duotone ki-cross fs-1"></i>
                    </div>
                </div>
                <div class="modal-body">
                    <div class="fv-row mb-7">
                        <label class="required fw-semibold fs-6 mb-2">Nama Barang</label>
                        <input type="text" name="nama_barang" class="form-control form-control-solid" placeholder="Contoh: Meja Kayu" required />
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary" id="btn_simpan">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit -->
<div class="modal fade" tabindex="-1" id="modal_edit">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="form_edit">
                <?= csrf_field(); ?>
                <div class="modal-header">
                    <h3 class="modal-title">Edit Nama Barang</h3>
                    <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal">
                        <i class="ki-duotone ki-cross fs-1"></i>
                    </div>
                </div>
                <div class="modal-body">
                    <div class="fv-row mb-7">
                        <label class="required fw-semibold fs-6 mb-2">Nama Barang</label>
                        <input type="text" name="nama_barang" id="edit_nama" class="form-control form-control-solid" required />
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="btn_update">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        // Inisialisasi DataTables (Hancurkan dulu jika sudah ada untuk menghindari duplikasi saat SPA)
        if ($.fn.DataTable.isDataTable('#table_barang')) {
            $('#table_barang').DataTable().destroy();
        }
        var table = $('#table_barang').DataTable();

        // Fungsi pusat untuk menangani respon (Update CSRF agar tidak stuck)
        function handleResponse(res, modalId) {
            // Update CSRF Token di semua form agar submit berikutnya tidak error 403
            if (res.token) {
                $('input[name="<?= csrf_token() ?>"]').val(res.token);
            }

            if (res.status === 'success') {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: res.pesan,
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => {
                    if(modalId) $(modalId).modal('hide');
                    // Reload konten via sistem SPA Layout Anda
                    $('.menu-link.active').click(); 
                });
            } else {
                Swal.fire('Gagal', res.pesan, 'error');
            }
        }

        // TAMBAH DATA
        $('#form_tambah').off('submit').on('submit', function(e) {
            e.preventDefault();
            const btn = $('#btn_simpan');
            btn.attr('disabled', true).text('Tunggu...');

            $.ajax({
                url: '<?= base_url('barang/simpan'); ?>',
                type: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: (res) => handleResponse(res, '#modal_tambah'),
                error: () => Swal.fire('Error', 'Terjadi kesalahan sistem', 'error'),
                complete: () => btn.attr('disabled', false).text('Simpan')
            });
        });

        // EDIT DATA (FILL FORM)
        $(document).off('click', '.btn-edit').on('click', '.btn-edit', function() {
            const id = $(this).data('id');
            const nama = $(this).data('nama');
            $('#edit_nama').val(nama);
            $('#form_edit').data('id', id); // Simpan ID menggunakan .data()
            $('#modal_edit').modal('show');
        });

        // UPDATE DATA
        $('#form_edit').off('submit').on('submit', function(e) {
            e.preventDefault();
            const id = $(this).data('id');
            const btn = $('#btn_update');
            btn.attr('disabled', true).text('Tunggu...');

            $.ajax({
                url: '<?= base_url('barang/ubah'); ?>/' + id,
                type: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: (res) => handleResponse(res, '#modal_edit'),
                error: () => Swal.fire('Error', 'Terjadi kesalahan sistem', 'error'),
                complete: () => btn.attr('disabled', false).text('Update')
            });
        });

        // HAPUS DATA
        $(document).off('click', '.btn-delete').on('click', '.btn-delete', function() {
            const url = $(this).data('url');
            Swal.fire({
                title: 'Yakin hapus?',
                text: "Data akan dihapus permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: url,
                        type: 'GET',
                        dataType: 'json',
                        success: (res) => handleResponse(res),
                        error: () => Swal.fire('Error', 'Gagal menghapus data', 'error')
                    });
                }
            });
        });
    });
</script>
<?= $this->endSection() ?>