<?= $this->extend('layout/metronic'); ?>
<?= $this->section('content'); ?>

<!-- CSS Khusus agar posisi naik dan tabel rapi -->
<style>
    #kt_app_content_container { margin-top: -30px !important; }
    .table-container { background: white; border-radius: 8px; padding: 15px; }
    /* Agar header tabel tidak pecah saat kolom sangat banyak */
    th { white-space: nowrap; } 
</style>

<!-- FILTER BAR (TANPA RELOAD) -->
<div class="card mb-5 shadow-sm mt-n10">
    <div class="card-body py-3">
        <div class="d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center">
                <div class="me-3 fw-bold fs-7">Filter Wilayah:</div>
                <select id="filterWilayah" class="form-select form-select-solid form-select-sm w-200px">
                    <option value="all">Semua Wilayah</option>
                    <?php foreach($list_wilayah as $w) : ?>
                        <option value="<?= $w['wilayah'] ?>"><?= $w['wilayah'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="text-muted fs-8">Gunakan filter untuk menyaring tampilan unit</div>
        </div>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body py-5">
        <div class="table-responsive">
            <table id="tableInventory" class="table table-bordered align-middle gs-5 gy-3 border-gray-200">
                <thead>
                    <tr class="fw-bold text-muted bg-light text-center">
                        <th class="min-w-200px text-start bg-light">Informasi Barang</th>
                        
                        <?php foreach($units as $u) : ?>
                            <th class="unit-column min-w-125px" data-wilayah="<?= $u['wilayah'] ?>">
                                <span class="text-gray-800 d-block fs-8"><?= $u['nama_unit'] ?></span>
                                <span class="badge badge-light-<?= $u['jenis_unit'] == 'GUDANG' ? 'primary' : 'success' ?> fs-10">
                                    <?= $u['jenis_unit'] ?>
                                </span>
                            </th>
                        <?php endforeach; ?>
                        
                        <th class="min-w-150px bg-light-danger text-danger fs-7">SISA DI GUDANG</th>
                    </tr>
                </thead>
                <tbody>
    <?php foreach($items as $it) : ?>
    <tr>
        <td class="bg-white">
            <div class="fw-bold text-gray-800 fs-7"><?= $it['nama_barang'] ?></div>
            <code class="fs-10"><?= $it['kode_barang'] ?></code>
        </td>

        <?php 
            $kb = $it['kode_barang'];
            $hitung_total_gudang = 0; // Variabel penampung baru
        ?>

        <?php foreach($units as $u) : ?>
            <td class="unit-column text-center" data-wilayah="<?= $u['wilayah'] ?>">
                <?php 
                    $ku = $u['kode_unit'];
                    
                    // Ambil saldo
                    $saldo = $stok_matrix[$kb][$ku] ?? 0;

                    // Fallback Varian (Jika saldo 0 dan ini adalah varian)
                    if($saldo == 0 && !empty($it['parent_kode'])) {
                        $saldo = $stok_matrix[$it['parent_kode']][$ku] ?? 0;
                    }

                    // AKUMULASI: Jika unit ini adalah GUDANG, tambahkan ke total pink
                    if($u['jenis_unit'] == 'GUDANG') {
                        $hitung_total_gudang += $saldo;
                    }
                ?>
                
                <?php if($u['jenis_unit'] == 'GUDANG'): ?>
                    <b class="text-primary"><?= number_format($saldo) ?></b>
                <?php else: ?>
                    <span class="<?= $saldo > 0 ? 'fw-bold text-dark' : 'text-gray-300' ?>">
                        <?= number_format($saldo) ?>
                    </span>
                <?php endif; ?>
            </td>
        <?php endforeach; ?>

        <!-- KOLOM SISA DI GUDANG (PINK) -->
        <td class="text-center bg-light-danger">
            <?php 
                // Gunakan hasil perhitungan otomatis di atas
                $total_pink = $hitung_total_gudang;
            ?>
            <span class="fw-bolder text-danger fs-4"><?= number_format($total_pink) ?></span>
        </td>
    </tr>
    <?php endforeach; ?>
</tbody>
            </table>
        </div>
    </div>
</div>

<!-- Load DataTables Scripts -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/fixedcolumns/4.2.2/css/fixedColumns.bootstrap5.min.css">
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/fixedcolumns/4.2.2/js/dataTables.fixedColumns.min.js"></script>

<script>
$(document).ready(function() {
    var table = $('#tableInventory').DataTable({
        scrollX: true,
        scrollCollapse: true,
        paging: true,
        info: true,
        ordering: true,
        pageLength: 10,
        fixedColumns: {
            left: 1,
            right: 1
        },
        columnDefs: [
            { className: "text-center", targets: "_all" }
        ],
        language: {
            search: "Cari Barang:"
        }
    });

    $('#filterWilayah').on('change', function() {
        var selectedWilayah = $(this).val();

        $('#tableInventory thead th.unit-column').each(function(index) {
            var colIndex = index + 1; 
            var wilayah = $(this).data('wilayah');

            if (selectedWilayah === 'all' || wilayah === selectedWilayah) {
                table.column(colIndex).visible(true);
            } else {
                table.column(colIndex).visible(false);
            }
        });
        
        table.columns.adjust().draw();
    });
});
</script>

<?= $this->endSection(); ?>