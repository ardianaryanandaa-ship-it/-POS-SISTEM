<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"><title>Surat Jalan SIT</title>
    <style>
        body{font-family:sans-serif;padding:30px;font-size:12px}
        table{width:100%;border-collapse:collapse;margin-top:20px}
        table,th,td{border:1px solid black;padding:8px}
        th{background:#f2f2f2}.text-center{text-align:center}
    </style>
</head>
<body onload="window.print()">
    <div style="text-align:center">
        <h2>SURAT JALAN (STOCK INTERNAL TRANSFER)</h2>
        <p>No SIT: <b>#<?= $header['no_trf'] ?></b> | Tanggal: <?= date('d/m/Y', strtotime($header['tanggal'])) ?></p>
    </div>
    <div style="margin-top:20px">
        <p><b>Unit Pengirim:</b> <?= $header['unit_asal'] ?></p>
        <p><b>Unit Penerima:</b> <?= $header['unit_tujuan'] ?></p>
        <p><b>Petugas/Kurir:</b> <?= $header['penerima'] ?></p>
    </div>
    <table>
        <thead><tr><th>No</th><th>Kode Barang</th><th>Nama Barang</th><th class="text-center">Jumlah</th></tr></thead>
        <tbody>
            <?php $no=1; foreach($items as $i): ?>
            <tr><td class="text-center"><?= $no++ ?></td><td><?= $i['kode_barang'] ?></td><td><?= $i['nama_barang'] ?></td><td class="text-center"><?= $i['jumlah'] ?></td></tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <div style="margin-top:50px; display: flex; justify-content: space-between; text-align: center;">
        <div style="width: 200px;"><p>Pengirim,</p><br><br><p>( ............ )</p></div>
        <div style="width: 200px;"><p>Penerima Cabang,</p><br><br><p>( <?= $header['penerima'] ?> )</p></div>
    </div>
</body>
</html>