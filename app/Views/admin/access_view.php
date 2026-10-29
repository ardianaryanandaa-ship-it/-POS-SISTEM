<?= $this->extend('layout/metronic') ?>

<?= $this->section('content') ?>
<div class="card shadow-sm mb-10">
    <div class="card-header bg-dark">
        <h3 class="card-title text-white fw-bold">
            <i class="ki-duotone ki-shield-tick fs-2 me-3 text-white">
                <span class="path1"></span><span class="path2"></span>
            </i>
            Konfigurasi Matriks Hak Akses Menu
        </h3>
        <div class="card-toolbar">
            <span class="badge badge-light-warning fw-bold">Developer Mode</span>
        </div>
    </div>

    <div class="card-body">
        <div class="notice d-flex bg-light-primary rounded border-primary border border-dashed p-6 mb-6">
            <i class="ki-duotone ki-information-5 fs-2tx text-primary me-4">
                <span class="path1"></span><span class="path2"></span><span class="path3"></span>
            </i>
            <div class="d-flex flex-stack flex-grow-1">
                <div class="fw-semibold">
                    <h4 class="text-gray-900 fw-bold">Petunjuk Penggunaan</h4>
                    <div class="fs-6 text-gray-700">
                        <ul class="mb-0 ps-4">
                            <li>Centang izin untuk setiap role sesuai dengan fungsi yang boleh dijalankan.</li>
                            <li><strong>VIEW</strong> = akses melihat halaman atau data.</li>
                            <li><strong>CREATE</strong> = akses menambah data.</li>
                            <li><strong>UPDATE</strong> = akses mengubah, verifikasi, atau memproses data.</li>
                            <li><strong>DELETE</strong> = akses menghapus atau membatalkan data.</li>
                            <li>Developer otomatis mendapat semua izin, sehingga kolomnya selalu terseleksi dan tidak dapat diubah.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <form action="<?= base_url('hak-akses/update') ?>" method="POST">
            <?= csrf_field() ?>

            <div class="table-responsive">
                <table class="table table-hover table-row-bordered table-row-gray-300 align-middle gs-0 gy-4">
                    <thead>
                        <tr class="fw-bold text-muted bg-light">
                            <th class="ps-4 min-w-200px rounded-start text-dark">Menu / Aksi</th>
                            <th class="min-w-150px text-dark">Permission Slug</th>
                            <?php foreach ($roles as $role): ?>
                                <th class="text-center text-dark">
                                    <?= strtoupper($role['role_name']) ?>
                                    <span class="text-muted fs-8 d-block">ID: <?= $role['id'] ?></span>
                                </th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $groupIndex = -1; ?>
                        <?php foreach ($rules as $r): 
                            $isHeader = (strpos($r['menu'], '--') !== false);
                        ?>
                        <?php if ($isHeader): $groupIndex++; ?>
                            <tr class="bg-light-secondary">
                                <td class="ps-4 text-primary fw-bolder fs-6" colspan="2">
                                    <?= trim(str_replace('--', '', $r['menu'])) ?>
                                </td>
                                <?php foreach ($roles as $role): ?>
                                    <td class="text-center align-middle">
                                        <span class="text-muted">&mdash;</span>
                                    </td>
                                <?php endforeach; ?>
                            </tr>
                        <?php else: ?>
                            <tr data-group-index="<?= $groupIndex ?>">
                                <td class="ps-4">
                                    <div class="d-flex align-items-center">
                                        <div class="symbol symbol-30px me-3">
                                            <span class="symbol-label bg-light-dark">
                                                <i class="ki-duotone ki-abstract-26 fs-4 text-dark"><span class="path1"></span><span class="path2"></span></i>
                                            </span>
                                        </div>
                                        <span class="text-gray-800 fw-bold fs-6"><?= $r['menu'] ?></span>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge badge-light-info fw-bold border border-info border-dashed">
                                        <?= $r['slug'] ?>
                                    </span>
                                </td>
                                <?php foreach ($roles as $role): 
                                    $role_id = $role['id'];
                                    $checked = in_array($role_id . '_' . $r['slug'], $current_permissions) ? 'checked' : '';
                                ?>
                                <td class="text-center">
                                    <div class="form-check form-check-custom form-check-solid form-check-sm justify-content-center">
                                        <input class="form-check-input" type="checkbox"
                                            name="permissions[]"
                                            value="<?= $role_id ?>|<?= $r['slug'] ?>"
                                            <?= $checked ?>
                                            <?= ($role_id == 3) ? 'checked disabled' : '' ?>>
                                        
                                        <?php if($role_id == 3): ?>
                                            <input type="hidden" name="permissions[]" value="3|<?= $r['slug'] ?>">
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <?php endforeach; ?>
                            </tr>
                        <?php endif; ?>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="card-footer d-flex justify-content-end py-6 px-0 bg-transparent border-top border-gray-200">
                <button type="submit" class="btn btn-primary">
                    <i class="ki-duotone ki-disk fs-2 me-2"></i> Simpan Hak Akses Menu
                </button>
            </div>
        </form>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-header bg-primary">
        <h3 class="card-title text-white fw-bold">
            <i class="ki-duotone ki-delivery-3 fs-2 me-3 text-white"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
            Persetujuan Akses Bisnis Unit (Admin/User)
        </h3>
    </div>
    <div class="card-body">
        <form action="<?= base_url('hak-akses/update-unit') ?>" method="POST">
            <?= csrf_field() ?>
            <div class="row g-9">
                <div class="col-md-5 fv-row">
                    <label class="required fs-6 fw-semibold mb-2">Pilih User / Admin</label>
                    <select class="form-select form-select-solid" data-control="select2" name="user_id" data-placeholder="Pilih Pengguna..." required>
                        <option></option>
                        <?php foreach ($all_users as $user): ?>
                            <option value="<?= $user['id'] ?>"><?= $user['full_name'] ?> (<?= $user['username'] ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-5 fv-row">
                    <label class="required fs-6 fw-semibold mb-2">Unit Yang Diizinkan (Berdasarkan Tabel Bisnis Unit)</label>
                    <select class="form-select form-select-solid" data-control="select2" name="units[]" data-placeholder="Pilih Satu atau Lebih Unit..." multiple="multiple" required>
                        <?php foreach ($all_bisnis_unit as $bu): ?>
                            <option value="<?= $bu['kode_unit'] ?>"><?= $bu['nama_unit'] ?> (<?= $bu['kode_unit'] ?>)</option>
                        <?php endforeach; ?>
                    </select>
                    <div class="text-muted fs-7 mt-2">Data baru akan <strong>menambah</strong> daftar akses yang sudah ada.</div>
                </div>

                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="ki-duotone ki-check fs-2"></i> Beri Akses
                    </button>
                </div>
            </div>
        </form>

        <div class="table-responsive mt-10">
            <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
                <thead>
                    <tr class="fw-bold fs-6 text-gray-800 bg-light">
                        <th class="ps-4 rounded-start">Nama User</th>
                        <th>Daftar Kode Unit (Disetujui Developer)</th>
                        <th class="text-end pe-4 rounded-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($unit_akses_map)): ?>
                        <tr><td colspan="3" class="text-center text-muted">Belum ada unit yang didaftarkan oleh Developer.</td></tr>
                    <?php else: ?>
                        <?php foreach($all_users as $user): 
                            if(isset($unit_akses_map[$user['id']])): ?>
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex flex-column">
                                        <span class="text-gray-800 fw-bold"><?= $user['full_name'] ?></span>
                                        <span class="text-muted fs-7"><?= $user['username'] ?></span>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex flex-wrap gap-1">
                                        <?php foreach($unit_akses_map[$user['id']] as $kode): ?>
                                            <span class="badge badge-light-success fw-bold border border-success border-dashed"><?= $kode ?></span>
                                        <?php endforeach; ?>
                                    </div>
                                </td>
                                <td class="text-end pe-4">
                                    <form action="<?= base_url('hak-akses/delete-unit-user') ?>" method="POST" onsubmit="return confirm('Hapus semua akses unit untuk user ini?')">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                        <button type="submit" class="btn btn-icon btn-bg-light btn-active-color-danger btn-sm">
                                            <i class="ki-duotone ki-trash fs-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php /* AJAX submit + modal response */ ?>
<div class="modal fade" id="ajaxResponseModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-sm modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header py-2">
        <h5 class="modal-title" id="ajaxResponseTitle"></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="ajaxResponseBody"></div>
    </div>
  </div>
</div>

<!-- Toast container for AJAX notifications -->
<div id="ajaxToastContainer" class="position-fixed top-0 end-0 p-3" style="z-index:1080"></div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    
    // --- FUNGSIONALITAS 1: SIMPAN MATRIKS HAK AKSES ---
    const matrixForm = document.querySelector('form[action*="hak-akses/update"]');
    if (matrixForm) {
        matrixForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const submitBtn = this.querySelector('button[type="submit"]');
            
            // Loading
            Swal.fire({
                title: 'Menyimpan...',
                text: 'Sedang memperbarui hak akses menu',
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading(); }
            });

            submitBtn.disabled = true;
            const formData = new FormData(this);

            fetch(this.action, {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: data.message,
                        timer: 2000,
                        showConfirmButton: false
                    });
                } else {
                    Swal.fire({ icon: 'error', title: 'Gagal', text: data.message });
                }
            })
            .catch(() => {
                Swal.fire({ icon: 'error', title: 'Error', text: 'Terjadi kesalahan sistem.' });
            })
            .finally(() => { submitBtn.disabled = false; });
        });
    }

    // --- FUNGSIONALITAS 2: TAMBAH AKSES UNIT ---
    const unitForm = document.querySelector('form[action*="hak-akses/update-unit"]');
    if (unitForm) {
        unitForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const submitBtn = this.querySelector('button[type="submit"]');
            
            Swal.fire({ title: 'Memproses...', didOpen: () => { Swal.showLoading(); } });

            const formData = new FormData(this);
            fetch(this.action, {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: data.message,
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        location.reload(); // Reload diperlukan untuk memperbarui tabel daftar user di bawah
                    });
                }
            });
        });
    }

    // --- FUNGSIONALITAS 3: HAPUS AKSES UNIT (AJAX) ---
    document.querySelectorAll('form[action*="delete-unit-user"]').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Semua akses unit untuk user ini akan dihapus!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    const formData = new FormData(this);
                    fetch(this.action, {
                        method: 'POST',
                        headers: { 'X-Requested-With': 'XMLHttpRequest' },
                        body: formData
                    })
                    .then(res => res.json())
                    .then(data => {
                        Swal.fire('Terhapus!', data.message, 'success').then(() => {
                            location.reload();
                        });
                    });
                }
            });
        });
    });

    // --- FUNGSIONALITAS 4: CHECKBOX GROUPING LOGIC ---
    // Logika agar saat header grup diklik (jika Anda menambahkan checkbox di header nanti)
    // Atau jika user klik checkbox di baris biasa.
    document.querySelectorAll('.group-select').forEach(function(cb){
        cb.addEventListener('change', function(){
            const roleId = this.getAttribute('data-role-id');
            const groupIdx = this.getAttribute('data-group-index');
            const checked = this.checked;

            document.querySelectorAll('tr[data-group-index="' + groupIdx + '"]').forEach(function(row){
                const cbx = row.querySelector('input[type="checkbox"][value^="' + roleId + '|"]');
                if (cbx && !cbx.disabled) {
                    cbx.checked = checked;
                }
            });
        });
    });
});
</script>
<?= $this->endSection() ?>