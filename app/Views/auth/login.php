<?= $this->extend('auth/mitro_login') ?>

<?= $this->section('content') ?>

<div class="container mt-5">
    <div class="col-md-4 mx-auto">

        <h3 class="text-center mb-4">Login</h3>
        
        <?php if(is_logged_in()): ?>
            <h5>Selamat Datang, <strong><?= user('full_name'); ?></strong></h5>
        <?php endif; ?>

        <?php if(session()->getFlashdata('errors')): ?>
            <div class="alert alert-danger">
                <?= implode('<br>', session()->getFlashdata('errors')) ?>
            </div>
        <?php endif; ?>

        <form method="post" action="<?= base_url('auth/login') ?>">
            <?= csrf_field(); ?>

            <div class="mb-3">
                <label class="form-label">Username / Email</label>
                <input type="text" name="username" 
                    class="form-control <?= (session('errors.username')) ? 'is-invalid' : '' ?>" 
                    value="<?= old('username') ?>" required>
                <?php if (session('errors.username')) : ?>
                    <div class="invalid-feedback">
                        <?= session('errors.username') ?>
                    </div>
                <?php endif ?>
            </div>

            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" 
                    class="form-control <?= (session('errors.password')) ? 'is-invalid' : '' ?>" required>
                <?php if (session('errors.password')) : ?>
                    <div class="invalid-feedback">
                        <?= session('errors.password') ?>
                    </div>
                <?php endif ?>
            </div>

            <div class="mb-3">
    <label class="form-label">Captcha</label>
    <div class="d-flex align-items-center gap-2 mb-2">
        <!-- Memanggil route captcha/generate -->
        <img src="<?= base_url('captcha/generate') ?>" id="captcha-img" 
             style="border: 1px solid #ddd; border-radius: 4px; height: 45px; cursor: pointer;"
             onclick="refreshCaptcha()" title="Klik gambar untuk refresh">
             
        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="refreshCaptcha()">
             Refresh
        </button>
    </div>
    
    <input type="text" name="captcha" 
           class="form-control <?= (session('errors.captcha')) ? 'is-invalid' : '' ?>" 
           placeholder="Ketik kode di atas" required>
           
    <?php if (session('errors.captcha')) : ?>
        <div class="invalid-feedback">
            <?= session('errors.captcha') ?>
        </div>
    <?php endif ?>
</div>

<script>
function refreshCaptcha() {
    var img = document.getElementById('captcha-img');
    // Tambahkan Math.random agar browser selalu mengambil gambar baru (bukan cache)
    img.src = "<?= base_url('captcha/generate') ?>?" + Math.random();
}
</script>

            <!-- Tambahkan ini di bawah input password -->

            <button type="submit" class="btn btn-primary w-100">Login</button>
        </form>

    </div>
</div>
<?= $this->endSection() ?>