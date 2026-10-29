<?= $this->extend('layout/metronic'); ?>
<?= $this->section('content'); ?>

<!-- TOOLBAR -->
<div class="d-flex flex-stack mb-6">
    <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
        <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">Marketing Rules</h1>
        <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
            <li class="breadcrumb-item text-muted">Promo, Diskon, & Harga Coret</li>
        </ul>
    </div>
    <button type="button" class="btn btn-primary fw-bold" data-bs-toggle="modal" data-bs-target="#modalTambahPromo">
        <i class="ki-duotone ki-plus fs-2"></i> Buat Promo Baru
    </button>
</div>

<!-- DATA TABLE -->
<div class="card card-flush shadow-sm">
    <div class="card-body py-5">
        <table class="table align-middle table-row-dashed fs-6 gy-5">
            <thead>
                <tr class="text-start text-gray-400 fw-bold fs-7 text-uppercase">
                    <th>Nama Promo</th>
                    <th>Potongan</th>
                    <th>Periode Aktif</th>
                    <th>Jam Aktif</th>
                    <th>Status</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-gray-600 fw-semibold">
                <?php foreach($promos as $p): ?>
                <tr>
                    <td><span class="text-gray-800 fw-bold fs-6"><?= $p['nama_promo'] ?></span></td>
                    <td>
                        <span class="badge badge-light-danger fs-7 fw-bold">
                            <?= ($p['jenis_promo'] == 'Persen') ? $p['nilai_promo'].'%' : 'Rp '.number_format($p['nilai_promo']) ?>
                        </span>
                    </td>
                    <td><?= date('d M', strtotime($p['tgl_mulai'])) ?> - <?= date('d M Y', strtotime($p['tgl_selesai'])) ?></td>
                    <td><code class="text-primary"><?= substr($p['jam_mulai'],0,5) ?> - <?= substr($p['jam_selesai'],0,5) ?></code></td>
                    <td><span class="badge badge-<?= $p['status'] == 'Aktif' ? 'success' : 'secondary' ?>"><?= $p['status'] ?></span></td>
                    <td class="text-center">
                        <a href="<?= base_url('promo/delete/'.$p['id_promo']) ?>" class="btn btn-icon btn-light-danger btn-sm" onclick="return confirm('Hapus promo?')">
                            <i class="ki-duotone ki-trash fs-3"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span></i>
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- MODAL TAMBAH PROMO -->
<div class="modal fade" id="modalTambahPromo" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-750px">
        <div class="modal-content">
            <div class="modal-header bg-primary py-4">
                <h3 class="modal-title text-white fw-bold">Konfigurasi Marketing Rule</h3>
                <div class="btn btn-sm btn-icon btn-active-color-white" data-bs-dismiss="modal">
                    <i class="ki-duotone ki-cross fs-1 text-white"></i>
                </div>
            </div>
            <form action="<?= base_url('promo/save') ?>" method="post">
                <?= csrf_field(); ?>
                <div class="modal-body px-10 py-10">
                    <!-- Step 1: Info Dasar -->
                    <div class="mb-8">
                        <label class="required fw-bold fs-6 mb-2">Nama Program Promo</label>
                        <input type="text" name="nama_promo" class="form-control form-control-solid" placeholder="Contoh: Diskon Ramadhan / Happy Hour" required />
                    </div>

                    <div class="row g-9 mb-8">
                        <div class="col-md-6">
                            <label class="required fw-bold fs-6 mb-2">Jenis Potongan</label>
                            <select name="jenis_promo" class="form-select form-select-solid" required>
                                <option value="Persen">Persentase (%)</option>
                                <option value="Nominal">Nominal Rupiah (Rp)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="required fw-bold fs-6 mb-2">Nilai Diskon</label>
                            <input type="number" name="nilai_promo" class="form-control form-control-solid" placeholder="Contoh: 10 atau 5000" required />
                        </div>
                    </div>

                    <!-- Step 2: Penjadwalan -->
                    <div class="row g-9 mb-8 border-top pt-5">
                        <div class="col-md-6">
                            <label class="required fw-bold fs-6 mb-2">Tanggal Mulai & Selesai</label>
                            <div class="input-group input-group-solid">
                                <input type="date" name="tgl_mulai" class="form-control" required />
                                <span class="input-group-text">-</span>
                                <input type="date" name="tgl_selesai" class="form-control" required />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="fw-bold fs-6 mb-2">Jam Aktif (Optional)</label>
                            <div class="input-group input-group-solid">
                                <input type="time" name="jam_mulai" class="form-control" value="00:00" />
                                <span class="input-group-text">-</span>
                                <input type="time" name="jam_selesai" class="form-control" value="23:59" />
                            </div>
                        </div>
                    </div>

                    <!-- Step 3: Target Diskon (Dinamis) -->
                    <div class="bg-light-info p-6 rounded border border-info border-dashed">
                        <label class="fw-bolder fs-5 mb-5 text-info">Target Promo</label>
                        <div class="d-flex gap-10 mb-5">
                            <label class="form-check form-check-custom form-check-solid">
                                <input class="form-check-input" type="radio" name="target_tipe" value="Barang" checked id="radioBarang" />
                                <span class="form-check-label fw-bold">Barang Spesifik</span>
                            </label>
                            <label class="form-check form-check-custom form-check-solid">
                                <input class="form-check-input" type="radio" name="target_tipe" value="Kategori" id="radioKategori" />
                                <span class="form-check-label fw-bold">Per Kategori</span>
                            </label>
                        </div>

                        <!-- Dropdown Barang -->
                        <div id="sectionBarang">
                            <select name="kode_barang[]" class="form-select form-select-solid" data-control="select2" data-placeholder="Pilih Barang..." multiple="multiple">
                                <?php foreach($barang as $b): ?>
                                    <option value="<?= $b['kode_barang'] ?>"><?= $b['nama_barang'] ?> (<?= $b['kode_barang'] ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Dropdown Kategori -->
                        <div id="sectionKategori" style="display:none;">
                            <select name="id_kategori[]" class="form-select form-select-solid" data-control="select2" data-placeholder="Pilih Kategori..." multiple="multiple">
                                <?php foreach($kategori as $k): ?>
                                    <option value="<?= $k['id_kategori'] ?>"><?= $k['nama_kategori'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary fw-bold">Aktifkan Promo Sekarang</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Logika Toggle Target
    $('input[name="target_tipe"]').change(function() {
        if (this.value == 'Barang') {
            $('#sectionBarang').show();
            $('#sectionKategori').hide();
        } else {
            $('#sectionBarang').hide();
            $('#sectionKategori').show();
        }
    });

    // Inisialisasi Select2 di dalam Modal
    $('#modalTambahPromo').on('shown.bs.modal', function () {
        $('[data-control="select2"]').select2({
            dropdownParent: $('#modalTambahPromo')
        });
    });
});
</script>

<?= $this->endSection(); ?>