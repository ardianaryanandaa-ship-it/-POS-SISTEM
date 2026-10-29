<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Struk - <?= $sale['invoice_no'] ?></title>
    <style>
        body {
            font-family: 'Courier New', Courier, monospace;
            width: 80mm; 
            margin: 0 auto;
            padding: 10px;
            font-size: 12px;
            color: #000;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .line { border-top: 1px dashed #000; margin: 10px 0; }
        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; }
        .footer { margin-top: 20px; font-size: 10px; }
        .fw-bold { font-weight: bold; }

        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body>

    <div class="no-print" style="margin-bottom: 20px;">
        <button onclick="window.print()">Klik Cetak</button>
        <button onclick="window.close()">Tutup Halaman</button>
    </div>

    <div class="text-center">
        <h3 style="margin-bottom: 5px;">POS SYSTEM RETAIL</h3>
        <p style="margin: 0;">Unit: <?= $sale['nama_unit'] ?? 'Unit Utama' ?></p>
        <p style="margin: 0;">Jl. Raya Kabupaten Kudus No. 123</p>
    </div>

    <div class="line"></div>

    <table>
        <tr>
            <td>No. Nota</td>
            <td>: <?= $sale['invoice_no'] ?></td>
        </tr>
        <tr>
            <td>Tanggal</td>
            <td>: <?= date('d/m/Y H:i', strtotime($sale['created_at'])) ?></td>
        </tr>
        <tr>
            <td>Kasir</td> 
            <td>: <?= session()->get('full_name') ?></td>
        </tr>
    </table>

    <div class="line"></div>

    <table>
        <thead>
            <tr>
                <th colspan="2">Item</th>
                <th class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($details as $item): ?>
            <tr>
                <td colspan="3" style="padding-top: 5px;"><?= $item['nama_barang'] ?></td>
            </tr>
            <tr>
                <td><?= $item['qty'] ?> x <?= number_format($item['price_at_sale'], 0, ',', '.') ?></td>
                <td colspan="2" class="text-right"><?= number_format($item['subtotal'], 0, ',', '.') ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="line"></div>

    <table>
        <!-- Bagian Perhitungan yang diperbaiki posisinya -->
        <tr>
            <td>SUBTOTAL</td>
            <td class="text-right">Rp <?= number_format($sale['total_price'], 0, ',', '.') ?></td>
        </tr>
        
        <?php if($sale['discount'] > 0): ?>
        <tr>
            <td>DISKON (<?= $sale['nama_promo'] ?? 'Promo' ?>)</td>
            <td class="text-right">-Rp <?= number_format($sale['discount'], 0, ',', '.') ?></td>
        </tr>
        <?php endif; ?>

        <tr>
            <td class="fw-bold">TOTAL TAGIHAN</td>
            <td class="text-right fw-bold">Rp <?= number_format($sale['total_price'] - $sale['discount'], 0, ',', '.') ?></td>
        </tr>
        <tr>
            <td>TUNAI/BAYAR</td>
            <td class="text-right">Rp <?= number_format($sale['cash_received'], 0, ',', '.') ?></td>
        </tr>
        <tr>
            <td>KEMBALIAN</td>
            <td class="text-right fw-bold">Rp <?= number_format($sale['cash_change'], 0, ',', '.') ?></td>
        </tr>
    </table>

    <div class="line"></div>

    <div class="text-center footer">
        <p>Metode Pembayaran: <?= strtoupper($sale['payment_method']) ?></p>
        <p>*** TERIMA KASIH ***</p>
        <p>Barang yang sudah dibeli tidak dapat ditukar/dikembalikan</p>
        
        <!-- Barcode Struk Invoice -->
        <div style="margin-top: 15px; display: flex; justify-content: center;">
            <svg id="receipt_barcode"></svg>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
    <script>
        window.onload = function() {
            JsBarcode("#receipt_barcode", "<?= $sale['invoice_no'] ?>", {
                format: "CODE128",
                width: 1.3,
                height: 35,
                displayValue: true,
                fontSize: 10,
                margin: 0
            });
            setTimeout(function() {
                window.print();
            }, 300);
        };
    </script>
</body>
</html>