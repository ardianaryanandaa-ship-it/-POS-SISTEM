<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"><title>Nota Penerimaan Barang</title>
    <style>
        body{font-family:sans-serif;padding:40px;font-size:12px}
        table{width:100%;border-collapse:collapse;margin-top:20px}
        table,th,td{border:1px solid black;padding:10px}
        th{background:#f2f2f2; text-align:center}
        .text-center{text-align:center}
        .hilang{color:red; font-weight:bold}
    </style>
</head>
<body onload="window.print()">
    <div style="text-align:center">
        <h1>NOTA PENERIMAAN BARANG</h1>
        <p>No Referensi: <b>#<?= $header['no_referensi'] ?></b></p>
        <hr>
        <table style="border:none; width:60%; margin:auto; text-align:left">
            <tr>
                <td width="30%">Tanggal Nota</td><td>: <?= $header['tanggal_masuk'] ?></td>
            </tr>
            <tr>
                <td>Vendor / Supplier</td><td>: <b><?= $header['nama_vendor'] ?? '-' ?></b></td> <!-- TAMBAHAN VENDOR -->
            </tr>
            <tr>
                <td>Unit Bisnis</td><td>: <?= $header['nama_unit'] ?></td>
            </tr>
            <tr>
                <td>Penerima</td><td>: <?= $header['penerima'] ?></td>
            </tr>
        </table>
    </div>

    <table>
        <thead>
            <tr>
                <th width="30px">No</th>
                <th>Nama Barang</th>
                <th class="text-center" width="80px">Jml Faktur</th>
                <th class="text-center" width="80px">Jml Terima</th>
                <th class="text-center" width="100px">Selisih Stock</th>
                <th>Alasan / Keterangan</th>
            </tr>
        </thead>
        <tbody>
            <?php $no=1; foreach($items as $i): 
                $selisih = $i['jumlah_faktur'] - $i['jumlah'];
            ?>
            <tr>
                <td class="text-center"><?= $no++ ?></td>
                <td><?= $i['nama_barang'] ?></td>
                <td class="text-center"><?= $i['jumlah_faktur'] ?></td>
                <td class="text-center" style="background:#e8f5e9"><b><?= $i['jumlah'] ?></b></td>
                <td class="text-center <?= $selisih > 0 ? 'hilang' : '' ?>">
                    <?= ($selisih > 0) ? $selisih : '0' ?>
                </td>
                <td><?= $i['alasan_hilang'] ? $i['alasan_hilang'] : '-' ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div style="margin-top:50px; float:right; text-align:center; width:250px">
        <p>Dicetak pada: <?= date('d/m/Y H:i') ?></p>
        <p>Petugas Verifikasi,</p>
        <br><br><br>
        <p><b>( <?= $header['verifikator'] ?? 'Admin' ?> )</b></p>
    </div>
</body>
</html>