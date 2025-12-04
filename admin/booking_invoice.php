<?php
require_once '../config/database.php';

// Ambil ID
$id = $_GET['id'] ?? null;
$query = mysqli_query($koneksi, "
    SELECT bookings.*, packages.name as package_name, packages.description 
    FROM bookings 
    LEFT JOIN packages ON bookings.package_id = packages.id 
    WHERE bookings.id = '$id'
");
$data = mysqli_fetch_assoc($query);

if(!$data) die("Data tidak ditemukan");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Invoice - <?= $data['booking_code']; ?></title>
    <style>
        body { font-family: 'Helvetica', sans-serif; padding: 40px; color: #333; }
        .invoice-box { max-width: 800px; margin: auto; border: 1px solid #eee; padding: 30px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.15); }
        .header { display: flex; justify-content: space-between; margin-bottom: 40px; }
        .header h1 { margin: 0; color: #2563eb; }
        .info-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .info-table td { padding: 8px; vertical-align: top; }
        .total-box { text-align: right; margin-top: 30px; font-size: 1.2rem; font-weight: bold; }
        .stamp { margin-top: 50px; text-align: right; padding-right: 50px; }
        
        /* Tombol Cetak (Hilang saat diprint) */
        .btn-print { background: #2563eb; color: white; border: none; padding: 10px 20px; cursor: pointer; border-radius: 5px; margin-bottom: 20px; }
        @media print { .btn-print { display: none; } }
    </style>
</head>
<body>

    <button onclick="window.print()" class="btn-print">Cetak Invoice / Simpan PDF</button>

    <div class="invoice-box">
        <div class="header">
            <div>
                <h1>EZA VIRALINDO</h1>
                <p>Professional Master of Ceremony</p>
            </div>
            <div style="text-align: right;">
                <h3>INVOICE</h3>
                <p>No: <?= $data['booking_code']; ?><br>
                Tanggal: <?= date('d M Y'); ?></p>
            </div>
        </div>

        <table class="info-table">
            <tr>
                <td><strong>Kepada Yth:</strong><br><?= $data['client_name']; ?><br><?= $data['client_phone']; ?></td>
                <td><strong>Detail Acara:</strong><br><?= date('d F Y', strtotime($data['event_date'])); ?><br><?= $data['event_location']; ?></td>
            </tr>
        </table>

        <table style="width: 100%; border-top: 2px solid #eee; margin-top: 20px;">
            <tr style="background: #f9f9f9;">
                <th style="text-align: left; padding: 10px;">Deskripsi Layanan</th>
                <th style="text-align: right; padding: 10px;">Harga</th>
            </tr>
            <tr>
                <td style="padding: 10px;">
                    <strong><?= $data['package_name']; ?></strong><br>
                    <small><?= nl2br($data['description'] ?? '-'); ?></small>
                </td>
                <td style="text-align: right; padding: 10px;">Rp <?= number_format($data['total_price'], 0, ',', '.'); ?></td>
            </tr>
        </table>

        <div class="total-box">
            Total Tagihan: Rp <?= number_format($data['total_price'], 0, ',', '.'); ?><br>
            <small style="font-size: 0.9rem; color: <?= ($data['payment_status'] == 'Paid') ? 'green' : 'red'; ?>">Status: <?= $data['payment_status']; ?></small>
        </div>

        <div class="stamp">
            <p>Hormat Kami,</p>
            <br><br>
            <p><strong>Eza Viralindo</strong></p>
        </div>
    </div>

</body>
</html>