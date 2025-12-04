<?php
require_once 'config/database.php';

if (!isset($_GET['code'])) {
    header("Location: index.php");
    exit;
}

$code = $_GET['code'];
$query = mysqli_query($koneksi, "SELECT * FROM bookings WHERE booking_code = '$code'");
$data = mysqli_fetch_assoc($query);

if (!$data) {
    echo "Data booking tidak ditemukan.";
    exit;
}

// Tentukan nominal yang harus dibayar (DP atau Full)
$tagihan = ($data['payment_option'] == 'Down Payment') ? $data['down_payment_amount'] : $data['total_price'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instruksi Pembayaran</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <style>
        body { background: #f1f5f9; }
        .success-card { max-width: 600px; margin: 50px auto; background: white; padding: 40px; border-radius: 20px; text-align: center; box-shadow: 0 10px 30px rgba(0,0,0,0.05); }
        .icon-check { width: 80px; height: 80px; background: #dcfce7; color: #16a34a; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 3rem; margin: 0 auto 20px; }
        .amount-box { background: #f8fafc; padding: 20px; border-radius: 10px; border: 2px dashed #cbd5e1; margin: 20px 0; }
        .amount-box h2 { color: #0f172a; font-size: 2.5rem; margin: 0; }
        .bank-info { margin-top: 30px; text-align: left; background: #fffbeb; padding: 20px; border-radius: 10px; border: 1px solid #fcd34d; }
        .btn-wa { display: block; width: 100%; background: #25D366; color: white; padding: 15px; border-radius: 10px; text-decoration: none; font-weight: bold; margin-top: 20px; }
    </style>
</head>
<body>

    <div class="success-card">
        <div class="icon-check"><i class="ri-check-line"></i></div>
        <h2>Booking Berhasil!</h2>
        <p>Terima kasih <strong><?= $data['client_name']; ?></strong>, jadwal Anda telah kami catat sementara.</p>
        
        <p style="margin-top: 20px;">Silakan lakukan pembayaran <strong><?= $data['payment_option']; ?></strong> sebesar:</p>
        
        <div class="amount-box">
            <h2>Rp <?= number_format($tagihan, 0, ',', '.'); ?></h2>
            <small>Kode Booking: <strong><?= $data['booking_code']; ?></strong></small>
        </div>

        <div class="bank-info">
            <h4 style="margin-bottom: 10px; display: flex; align-items: center; gap: 10px;">
                <i class="ri-bank-card-line"></i> Transfer Bank
            </h4>
            <p><strong>BCA (Bank Central Asia)</strong></p>
            <p style="font-size: 1.2rem; font-weight: bold; letter-spacing: 1px;">123-456-7890</p>
            <p>a.n. Eza Viralindo</p>
        </div>

        <a href="https://wa.me/628123456789?text=Halo admin, saya sudah transfer untuk booking kode <?= $data['booking_code']; ?> sebesar Rp <?= number_format($tagihan, 0, ',', '.'); ?>" class="btn-wa">
            <i class="ri-whatsapp-line"></i> Konfirmasi Pembayaran ke WA
        </a>
        
        <br>
        <a href="index.php" style="color: #64748b; text-decoration: none;">Kembali ke Beranda</a>
    </div>

</body>
</html>