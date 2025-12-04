<?php
require_once '../config/database.php';
include 'includes/header.php';
include 'includes/sidebar.php';

// Cek apakah ada ID di URL
if(!isset($_GET['id'])){
    header("Location: pesan.php");
    exit;
}

$id = $_GET['id'];

// 1. Update status pesan jadi "Sudah Dibaca" (is_read = 1)
mysqli_query($koneksi, "UPDATE messages SET is_read = 1 WHERE id = '$id'");

// 2. Ambil data pesan
$query = mysqli_query($koneksi, "SELECT * FROM messages WHERE id = '$id'");
$data = mysqli_fetch_assoc($query);

// Jika pesan tidak ditemukan (misal id ngasal)
if(!$data){
    echo "<script>alert('Pesan tidak ditemukan!'); window.location='pesan.php';</script>";
    exit;
}
?>

<div class="main-content">
    <div class="header-dash">
        <div style="display: flex; align-items: center;">
            <i class="ri-menu-line menu-toggle" style="font-size: 1.5rem; margin-right: 1rem; cursor: pointer; display: none;"></i>
            <h2>Detail Pesan</h2>
        </div>
    </div>

    <div style="background: white; padding: 2rem; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.02); max-width: 800px;">
        
        <div style="margin-bottom: 1.5rem; border-bottom: 1px solid #eee; padding-bottom: 1rem;">
            <label style="color: #6b7280; font-size: 0.9rem;">Pengirim:</label>
            <h3 style="color: #1f2937; margin-top: 5px;"><?= htmlspecialchars($data['name']); ?></h3>
            <p style="color: #2563eb;"><?= htmlspecialchars($data['email']); ?> | <?= htmlspecialchars($data['phone'] ?? '-'); ?></p>
        </div>

        <div style="margin-bottom: 1.5rem;">
            <label style="color: #6b7280; font-size: 0.9rem; display: block; margin-bottom: 10px;">Waktu Kirim:</label>
            <div style="display: flex; align-items: center; color: #374151;">
                <i class="ri-time-line" style="margin-right: 8px;"></i> 
                <?= date('d F Y, H:i', strtotime($data['created_at'])); ?> WIB
            </div>
        </div>

        <div style="margin-bottom: 2rem;">
            <label style="color: #6b7280; font-size: 0.9rem; display: block; margin-bottom: 10px;">Isi Pesan:</label>
            <div style="background: #f9fafb; padding: 1.5rem; border-radius: 8px; border: 1px solid #e5e7eb; line-height: 1.6; color: #374151;">
                <?= nl2br(htmlspecialchars($data['message'])); ?>
            </div>
        </div>

        <div>
            <a href="pesan.php" class="btn-back"><i class="ri-arrow-left-line"></i> Kembali</a>
            <a href="https://wa.me/<?= $data['phone']; ?>" target="_blank" class="btn-wa"><i class="ri-whatsapp-line"></i> Balas via WA</a>
            <a href="mailto:<?= $data['email']; ?>" class="btn-mail"><i class="ri-mail-send-line"></i> Balas via Email</a>
        </div>

    </div>
</div>

<style>
.btn-back { background: #6b7280; color: white; padding: 10px 20px; border-radius: 6px; display: inline-block; margin-right: 10px; }
.btn-wa { background: #25D366; color: white; padding: 10px 20px; border-radius: 6px; display: inline-block; margin-right: 10px; }
.btn-mail { background: #2563eb; color: white; padding: 10px 20px; border-radius: 6px; display: inline-block; }
</style>

<?php include 'includes/footer.php'; ?>