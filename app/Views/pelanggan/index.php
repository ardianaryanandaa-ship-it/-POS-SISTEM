<?= $this->extend('layout/metronic'); ?>
<?= $this->section('content'); ?>
<div class="card">
    <div class="card-header border-0 pt-6">
        <div class="card-title"><h3>Daftar Member Loyalitas</h3></div>
        <div class="card-toolbar">
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modal_tambah">Tambah Member</button>
        </div>
    </div>
    <div class="card-body">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Nama Pelanggan</th>
                    <th>Telepon</th>
                    <th>Total Poin</th>
                    <th>Tgl Terdaftar</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($pelanggan as $plg): ?>
                <tr>
                    <td><?= $plg['nama_pelanggan'] ?></td>
                    <td><?= $plg['telepon'] ?></td>
                    <td><span class="badge badge-light-success fs-7 fw-bold"><?= number_format($plg['poin']) ?> Poin</span></td>
                    <td><?= date('d M Y', strtotime($plg['created_at'])) ?></td>
                    <td>
                        <a href="<?= base_url('pelanggan/detail/'.$plg['id']) ?>" class="btn btn-sm btn-light-primary">Lihat Performa</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?= $this->endSection(); ?>