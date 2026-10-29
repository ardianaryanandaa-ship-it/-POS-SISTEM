<?= $this->extend('layout/metronic') ?>

<?= $this->section('content') ?>
<style>
    .form-control.is-valid-border { border: 2px solid #28a745 !important; }
    .form-control.is-invalid-border { border: 2px solid #dc3545 !important; }
</style>

<div class="card shadow-sm">
    <div class="card-header bg-primary">
        <h3 class="card-title text-white">Informasi Personal</h3>
    </div>
    <div class="card-body">
        <form id="form_profile" enctype="multipart/form-data">
            <?= csrf_field() ?>
            
            <div class="row mb-6">
                <label class="col-lg-4 col-form-label fw-semibold fs-6">Avatar</label>
                <div class="col-lg-8">
                    <div class="image-input image-input-outline" data-kt-image-input="true" style="background-image: url('<?= base_url('uploads/avatars/default.jpg') ?>')">
                        <div class="image-input-wrapper w-125px h-125px" style="background-image: url('<?= base_url('uploads/avatars/' . ($user['avatar'] ?? 'default.jpg')) ?>')"></div>
                        <label class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="change" title="Ganti Avatar">
                            <i class="ki-duotone ki-pencil fs-7"><span class="path1"></span><span class="path2"></span></i>
                            <input type="file" name="avatar" accept=".png, .jpg, .jpeg" />
                        </label>
                    </div>
                    <div class="form-text">File types: png, jpg, jpeg.</div>
                </div>
            </div>

            <div class="row mb-6">
                <label class="col-lg-4 col-form-label required fw-semibold fs-6">Nama Lengkap</label>
                <div class="col-lg-8">
                    <input type="text" name="full_name" class="form-control form-control-lg form-control-solid" value="<?= $user['full_name'] ?? '' ?>" required />
                </div>
            </div>

            <div class="row mb-6">
                <label class="col-lg-4 col-form-label fw-semibold fs-6">Role</label>
                <div class="col-lg-8">
                    <input type="text" class="form-control form-control-lg form-control-solid" value="<?= $user['role_name'] ?? '' ?>" readonly />
                </div>
            </div>

            <div class="row mb-6">
                <label class="col-lg-4 col-form-label required fw-semibold fs-6">Email</label>
                <div class="col-lg-8">
                    <input type="email" name="email" class="form-control form-control-lg form-control-solid" value="<?= $user['email'] ?? '' ?>" required />
                </div>
            </div>

            <div class="d-flex justify-content-center gap-3">
                <button type="submit" class="btn btn-success btn-sm" id="btn_update_profile">
                    <i class="ki-duotone ki-save-2 fs-2"><span class="path1"></span><span class="path2"></span></i> Update
                </button>
                <button type="button" class="btn btn-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#modal_password">
                    <i class="ki-duotone ki-key fs-2"><span class="path1"></span><span class="path2"></span></i> Ubah Password
                </button>
            </div>
        </form>
    </div>
</div>

<div class="card shadow-sm mt-5">
    <div class="card-header bg-dark">
        <h3 class="card-title text-white">Log Aktivitas User</h3>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="table_history" class="table table-row-bordered table-row-gray-300 align-middle gs-0 gy-3">
                <thead>
                    <tr class="fw-bold text-muted">
                        <th>No</th>
                        <th>Aktivitas</th>
                        <th>IP Address</th>
                        <th>Waktu</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>1</td>
                        <td>Update Profil</td>
                        <td><?= $_SERVER['REMOTE_ADDR'] ?></td>
                        <td><?= date('Y-m-d H:i:s') ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="modal_password" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-500px">
        <div class="modal-content">
            <form id="form_password">
                <?= csrf_field() ?>
                <div class="modal-header">
                    <h2 class="fw-bold">Ubah Password</h2>
                    <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal"><i class="ki-duotone ki-cross fs-1"></i></div>
                </div>
                <div class="modal-body px-10">
                    <div class="fv-row mb-7">
                        <label class="fs-6 mb-2">Password Lama</label>
                        <div class="position-relative">
                            <input type="password" name="old_password" class="form-control form-control-solid" required />
                        </div>
                    </div>
                    <div class="fv-row mb-7">
                        <label class="fs-6 mb-2">Password Baru</label>
                        <div class="position-relative">
                            <input type="password" id="new_password" name="new_password" class="form-control form-control-solid" placeholder="Insert your password..." required />
                            <span class="btn btn-sm btn-icon position-absolute translate-middle top-50 end-0 me-n2 btn-show-password">
                                <i class="ki-duotone ki-eye fs-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                            </span>
                        </div>
                        <div id="password-hint" class="fs-8 mt-2 text-muted">Min. 8 char, Uppercase & Number.</div>
                    </div>
                    <div class="fv-row mb-7">
                        <label class="fs-6 mb-2">Konfirmasi Password</label>
                        <div class="position-relative">
                            <input type="password" name="confirm_password" class="form-control form-control-solid" required />
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Keluar</button>
                    <button type="submit" class="btn btn-primary" id="btn_save_password">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#table_history').DataTable({
        "pageLength": 5,
        "lengthMenu": [5, 10, 25],
        "language": {
            "search": "Cari:",
            "lengthMenu": "Tampilkan _MENU_ data",
        }
    });

    const passwordInput = document.getElementById('new_password');
    passwordInput.addEventListener('input', function() {
        const val = this.value;
        const hasUpper = /[A-Z]/.test(val);
        const hasNumber = /[0-9]/.test(val);
        const isLongEnough = val.length >= 8;

        if (isLongEnough && hasUpper && hasNumber) {
            this.classList.remove('is-invalid-border');
            this.classList.add('is-valid-border');
        } else {
            this.classList.remove('is-valid-border');
            if(val.length > 0) {
                this.classList.add('is-invalid-border');
            } else {
                this.classList.remove('is-invalid-border');
            }
        }
    });

    $(document).on('click', '.btn-show-password', function() {
        let input = $(this).closest('.position-relative').find('input');
        let icon = $(this).find('i');
        if (input.attr('type') === 'password') {
            input.attr('type', 'text');
            icon.removeClass('ki-eye').addClass('ki-eye-slash');
        } else {
            input.attr('type', 'password');
            icon.removeClass('ki-eye-slash').addClass('ki-eye');
        }
    });

    $('#form_profile').submit(function(e) {
        e.preventDefault();
        let formData = new FormData(this);
        let btn = $('#btn_update_profile');
        btn.attr('disabled', true).html('<span class="spinner-border spinner-border-sm"></span>');
        $.ajax({
            url: '<?= site_url('profile/update') ?>',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            dataType: 'json',
            success: function(res) {
                if (res.status === 'success') {
                    $('.header-user-name').text(res.data.full_name); 
                    $('.header-user-email').text(res.data.email); 
                    $('.header-user-avatar').attr('src', res.data.avatar_url);
                    $('.image-input-wrapper').css('background-image', 'url(' + res.data.avatar_url + ')');
                    Swal.fire({ text: res.pesan, icon: "success", timer: 2000, showConfirmButton: false });
                } else {
                    Swal.fire({ html: res.pesan, icon: "error" });
                }
            },
            complete: function() {
                btn.attr('disabled', false).html('<i class="ki-duotone ki-save-2 fs-2"></i> Update');
            }
        });
    });

    $('#form_password').submit(function(e) {
        e.preventDefault();
        let btn = $('#btn_save_password');
        btn.attr('disabled', true).text('Tunggu...');
        $.ajax({
            url: '<?= site_url('profile/change-password') ?>',
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(res) {
                if (res.status === 'success') {
                    $('#modal_password').modal('hide');
                    $('#form_password')[0].reset();
                    $('#new_password').removeClass('is-valid-border is-invalid-border');
                    Swal.fire({ text: res.pesan, icon: "success" });
                } else {
                    Swal.fire({ html: res.pesan, icon: "error" });
                }
            },
            complete: function() {
                btn.attr('disabled', false).text('Simpan');
            }
        });
    });
});
</script>
<?= $this->endSection() ?>