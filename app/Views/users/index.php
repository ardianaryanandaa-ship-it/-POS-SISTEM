<?= $this->extend('layout/metronic') ?>

<?= $this->section('content') ?>
<div class="card shadow-sm">
    <div class="card-header">
        <h3 class="card-title"><i class="ki-duotone ki-user fs-2 me-2"><span class="path1"></span><span class="path2"></span></i> Daftar User</h3>
        <div class="card-toolbar">
            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modal_user">
                Tambah User
            </button>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-row-bordered table-row-gray-300 align-middle gs-0 gy-3" id="table_users">
                <thead>
                    <tr class="fw-bold text-muted">
                        <th class="w-100px">Aksi</th>
                        <th>No</th>
                        <th class="w-50px">Foto</th>
                        <th>Username</th>
                        <th>Nama Lengkap</th>
                        <th>Role</th>
                        <th>Email</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; foreach ($users as $u) : ?>
                    <tr>
                        <td>
                            <button class="btn btn-icon btn-primary btn-sm btn-edit-user" data-id="<?= $u['id'] ?>"><i class="ki-duotone ki-pencil fs-3"><span class="path1"></span><span class="path2"></span></i></button>
                            <button class="btn btn-icon btn-warning btn-sm btn-reset-password" data-id="<?= $u['id'] ?>" data-username="<?= esc($u['username']) ?>" title="Reset Password">
    <i class="ki-duotone ki-key fs-3"><span class="path1"></span><span class="path2"></span></i>
</button>
                        </td>
                        <td><?= $no++ ?></td>
                        <td>
                            <div class="symbol symbol-circle symbol-45px">
                                <?php if (!empty($u['avatar']) && file_exists('uploads/avatars/' . $u['avatar'])) : ?>
                                    <img src="<?= base_url('uploads/avatars/' . $u['avatar']) ?>" alt="user" />
                                <?php else : ?>
                                    <div class="symbol-label fs-3 bg-light-primary text-primary">
                                        <?= strtoupper(substr($u['username'], 0, 1)) ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td><?= esc($u['username']) ?></td>
                        <td><?= esc($u['full_name'] ?? '-') ?></td>
                        <td><?= esc($u['role_name'] ?? '-') ?></td>
                        <td><?= esc($u['email']) ?></td>
                        <td>
                            <?php $status = $u['status'] ?? 'Inactive'; ?>
                            <span class="badge badge-light-<?= ($status == 'Active' ? 'success' : 'danger') ?>"><?= $status ?></span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah/Edit -->
<div class="modal fade" id="modal_user" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-650px">
        <div class="modal-content">
            <form id="form_user" method="post">
                <?= csrf_field() ?>
                <div class="modal-header">
                    <h2 class="fw-bold" id="modal_title">Tambah User</h2>
                    <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                        <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                    </div>
                </div>
                <div class="modal-body px-10">
                    <div class="fv-row mb-7"><label class="required fs-6 fw-semibold mb-2">Username</label><input type="text" name="username" class="form-control form-control-solid" required /></div>
                    <div class="fv-row mb-7"><label class="required fs-6 fw-semibold mb-2">Nama Lengkap</label><input type="text" name="full_name" class="form-control form-control-solid" required /></div>
                    <div class="fv-row mb-7"><label class="required fs-6 fw-semibold mb-2">Email</label><input type="email" name="email" class="form-control form-control-solid" required /></div>
                    <div class="row mb-7">
                        <div class="col-md-6">
                            <label class="required fs-6 mb-2">Role</label>
                            <select name="role_id" class="form-select form-select-solid" required>
                                <option value="">Pilih Role</option>
                                <?php foreach($roles as $r): ?>
                                    <option value="<?= $r['id'] ?>"><?= $r['role_name'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="required fs-6 mb-2">Status</label>
                            <select name="status" class="form-select form-select-solid">
                                <option value="Active">Active</option>
                                <option value="Inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                    <div class="fv-row mb-7">
                        <label class="fs-6 fw-semibold mb-2" id="label_pass">Password</label>
                        <input type="password" name="password" class="form-control form-control-solid" />
                        <small class="text-muted d-none" id="hint_pass">*Kosongkan jika tidak ingin mengganti password</small>
                    </div>
                </div>
                <div class="modal-footer"><button type="submit" class="btn btn-primary" id="btn_submit">Simpan Data</button></div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Reset Password -->
<div class="modal fade" id="modal_reset_password" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-450px">
        <div class="modal-content">
            <form id="form_reset_password" method="post">
                <?= csrf_field() ?>
                <div class="modal-header">
                    <h2 class="fw-bold">Reset Password</h2>
                    <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                        <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                    </div>
                </div>
                <div class="modal-body px-10">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Username:</label>
                        <input type="text" id="reset_username" class="form-control form-control-solid" readonly>
                    </div>
                    <div class="fv-row mb-7">
                        <label class="required fs-6 fw-semibold mb-2">Password Baru</label>
                        <input type="password" name="password" class="form-control form-control-solid" placeholder="Masukkan password baru" required />
                    </div>
                    <div class="fv-row mb-7">
                        <label class="required fs-6 fw-semibold mb-2">Konfirmasi Password Baru</label>
                        <input type="password" name="conf_password" class="form-control form-control-solid" placeholder="Ulangi password" required />
                    </div>
                </div>
                <div class="modal-footer text-center">
                    <button type="button" class="btn btn-light me-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning" id="btn_submit_reset">Update Password</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    if ($.fn.DataTable.isDataTable('#table_users')) {
        $('#table_users').DataTable().destroy();
    }
    $('#table_users').DataTable({
        "info": false,
        "pageLength": 10,
        "lengthChange": true
    });

    $('[data-bs-target="#modal_user"]').click(function() {
        $('#form_user')[0].reset();
        $('#modal_title').text('Tambah User');
        $('#label_pass').text('Password');
        $('#hint_pass').addClass('d-none');
        $('input[name="password"]').attr('required', true);
        $('#form_user').attr('action', '<?= base_url('users/store') ?>');
    });

    $(document).on('click', '.btn-edit-user', function() {
        const id = $(this).data('id');
        $('#form_user').attr('action', '<?= base_url('users/update') ?>/' + id);
        $('#modal_title').text('Ubah Data User');
        $('#label_pass').text('Ganti Password');
        $('#hint_pass').removeClass('d-none');
        $('input[name="password"]').attr('required', false);

        $.get('<?= base_url('users/get_json') ?>/' + id, function(data) {
            $('input[name="username"]').val(data.username);
            $('input[name="full_name"]').val(data.full_name || '');
            $('input[name="email"]').val(data.email);
            $('select[name="role_id"]').val(data.role_id);
            $('select[name="status"]').val(data.status || 'Active');
            $('#modal_user').modal('show');
        });
    });

    // Menangani klik tombol Reset Password (Kunci)
$(document).on('click', '.btn-reset-password', function() {
    const id = $(this).data('id');
    const username = $(this).data('username');
    
    $('#form_reset_password')[0].reset();
    $('#reset_username').val(username);
    $('#form_reset_password').attr('action', '<?= base_url('users/reset_password') ?>/' + id);
    $('#modal_reset_password').modal('show');
});

// Submit Form Reset Password
$('#form_reset_password').on('submit', function(e) {
    e.preventDefault();
    const btn = $('#btn_submit_reset');
    const pass = $('input[name="password"]').val();
    const conf = $('input[name="conf_password"]').val();

    if(pass !== conf) {
        Swal.fire({ text: "Konfirmasi password tidak cocok!", icon: "error" });
        return;
    }

    btn.attr('disabled', true).html('<span class="spinner-border spinner-border-sm align-middle ms-2"></span> Memproses...');

    $.post($(this).attr('action'), $(this).serialize(), function(res) {
        if (res.status === 'success') {
            $('#modal_reset_password').modal('hide');
            Swal.fire({ text: "Password berhasil diperbarui", icon: "success" });
        } else {
            Swal.fire({ text: res.pesan || "Gagal reset password", icon: "error" });
        }
        btn.attr('disabled', false).text('Update Password');
    }).fail(function() {
        Swal.fire({ text: "Terjadi kesalahan sistem", icon: "error" });
        btn.attr('disabled', false).text('Update Password');
    });
});

    $('#form_user').off('submit').on('submit', function(e) {
        e.preventDefault();
        const btn = $('#btn_submit');
        const formAction = $(this).attr('action');
        btn.attr('disabled', true).html('<span class="spinner-border spinner-border-sm align-middle ms-2"></span> Memproses...');

        $.post(formAction, $(this).serialize(), function(res) {
            if (res.status === 'success') {
                $('#modal_user').modal('hide');
                
                // Menentukan pesan berdasarkan aksi (Tambah atau Update)
                const isUpdate = formAction.includes('update');
                
                if (isUpdate) {
                    Swal.fire({ text: res.pesan, icon: "success" }).then(() => location.reload());
                } else {
                    // Pilihan khusus saat Tambah User baru agar Developer bisa langsung atur Unit Akses
                    Swal.fire({
                        title: "User Berhasil Dibuat!",
                        text: res.pesan + " Apakah Anda ingin langsung mengatur izin Unit Bisnis untuk user ini?",
                        icon: "success",
                        showCancelButton: true,
                        confirmButtonText: "Ya, Atur Hak Akses",
                        cancelButtonText: "Nanti Saja",
                        customClass: {
                            confirmButton: "btn btn-primary",
                            cancelButton: "btn btn-active-light"
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = '<?= base_url("hak-akses") ?>';
                        } else {
                            location.reload();
                        }
                    });
                }
            } else {
                Swal.fire({ text: res.pesan, icon: "error" });
                btn.attr('disabled', false).text('Simpan Data');
            }
        }).fail(function() {
            Swal.fire({ text: "Terjadi kesalahan sistem", icon: "error" });
            btn.attr('disabled', false).text('Simpan Data');
        });
    });
});
</script>
<?= $this->endSection() ?>