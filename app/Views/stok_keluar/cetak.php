<!DOCTYPE html>
<html>
<head>
    <title>Struk Penjualan #<?= $row['id_penjualan'] ?></title>
    <style>
        body { font-family: sans-serif; padding: 20px; line-height: 1.6; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #000; padding-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        table th, table td { padding: 10px; border: 1px solid #ddd; text-align: left; }
        .footer { margin-top: 30px; text-align: right; font-style: italic; }
        @media print { .no-print { display: none; } }
    </style>
</head>
<body onload="window.print()">
    <div class="header">
        <h2>STRUK PENJUALAN</h2>
        <p>POS SYSTEM - Riwayat Stok Keluar</p>
    </div>

    <table>
        <tr><th>ID Penjualan</th><td>#<?= $row['id_penjualan'] ?></td></tr>
        <tr><th>Nama Barang</th><td><?= $row['nama_barang'] ?></td></tr>
        <tr><th>Jumlah Keluar</th><td><?= $row['jumlah_stok_keluar'] ?> Item</td></tr>
        <tr><th>Harga Jual Satuan</th><td>Rp <?= number_format($row['harga_jual'], 0, ',', '.') ?></td></tr>
        <tr><th>Total Bayar</th><td>Rp <?= number_format($row['harga_jual'] * $row['jumlah_stok_keluar'], 0, ',', '.') ?></td></tr>
    </table>

    <div class="footer">
        <p>Dicetak pada: <?= date('d/m/Y H:i') ?></p>
    </div>
    <button class="no-print" onclick="window.print()">Klik untuk Cetak</button>
</body>
</html>