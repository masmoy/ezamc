<?php 
// 1. Panggil Koneksi & Aset Utama
require_once '../config/database.php';
include 'includes/header.php'; 
include 'includes/sidebar.php'; 

// --- LOGIKA PHP UNTUK DATA DASHBOARD ---

// A. Hitung Pesan Belum Dibaca
$q_pesan = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM messages WHERE is_read = 0");
$d_pesan = mysqli_fetch_assoc($q_pesan);

// B. Hitung Total Booking (Semua Status)
$q_book = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM bookings");
$d_book = mysqli_fetch_assoc($q_book);

// C. Hitung Jadwal Bulan Ini (Acara Aktif)
$bulan_ini = date('m');
$tahun_ini = date('Y');
$q_jadwal = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM bookings WHERE MONTH(event_date) = '$bulan_ini' AND YEAR(event_date) = '$tahun_ini' AND status != 'Cancelled'");
$d_jadwal = mysqli_fetch_assoc($q_jadwal);

// D. Hitung Total Pemasukan (Hanya yang Lunas/Paid)
$q_omset = mysqli_query($koneksi, "SELECT SUM(total_price) as total FROM bookings WHERE payment_status = 'Paid'");
$d_omset = mysqli_fetch_assoc($q_omset);
$omset = $d_omset['total'] ?? 0;

// E. Ambil 5 Booking Terbaru untuk Tabel
$q_recent = mysqli_query($koneksi, "SELECT * FROM bookings ORDER BY id DESC LIMIT 5");
?>

<div class="main-content">
    
    <div class="header-dash">
        <div class="welcome-text">
            <h1>Halo, <?= $_SESSION['user_name']; ?>! 👋</h1>
            <p>Berikut adalah ringkasan performa bisnis Anda hari ini.</p>
        </div>
        <div class="date-display">
            <i class="ri-calendar-event-line"></i> <?= date('d F Y'); ?>
        </div>
    </div>

    <div class="cards-grid">
        <div class="card card-hover">
            <div class="card-head">
                <span>Pesan Baru</span>
                <div class="icon-box blue"><i class="ri-mail-unread-line"></i></div>
            </div>
            <div class="card-body">
                <h3><?= $d_pesan['total']; ?></h3>
                <small>Belum dibaca</small>
            </div>
        </div>

        <div class="card card-hover">
            <div class="card-head">
                <span>Jadwal Bulan Ini</span>
                <div class="icon-box green"><i class="ri-calendar-check-line"></i></div>
            </div>
            <div class="card-body">
                <h3><?= $d_jadwal['total']; ?></h3>
                <small>Acara mendatang</small>
            </div>
        </div>

        <div class="card card-hover">
            <div class="card-head">
                <span>Total Booking</span>
                <div class="icon-box purple"><i class="ri-file-list-3-line"></i></div>
            </div>
            <div class="card-body">
                <h3><?= $d_book['total']; ?></h3>
                <small>Semua waktu</small>
            </div>
        </div>

        <div class="card card-hover">
            <div class="card-head">
                <span>Total Pemasukan</span>
                <div class="icon-box orange"><i class="ri-money-dollar-circle-line"></i></div>
            </div>
            <div class="card-body">
                <h3 style="font-size: 1.8rem;">Rp <?= number_format($omset, 0, ',', '.'); ?></h3>
                <small>Status Lunas (Paid)</small>
            </div>
        </div>
    </div>

    <div class="recent-section">
        <div class="section-header">
            <h3>Booking Terbaru Masuk</h3>
            <a href="booking.php" class="btn-sm">Lihat Semua Data</a>
        </div>
        
        <div class="table-responsive">
            <table class="table-custom">
                <thead>
                    <tr>
                        <th width="40%">Info Klien</th>
                        <th width="25%">Tanggal Acara</th>
                        <th width="20%">Status</th>
                        <th width="15%" style="text-align: right;">Detail</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(mysqli_num_rows($q_recent) > 0): ?>
                        <?php while($row = mysqli_fetch_assoc($q_recent)): ?>
                        <tr>
                            <td>
                                <div class="client-info">
                                    <div class="client-avatar">
                                        <?= strtoupper(substr($row['client_name'], 0, 1)); ?>
                                    </div>
                                    <div>
                                        <strong><?= htmlspecialchars($row['client_name']); ?></strong>
                                        <span class="booking-code"><?= $row['booking_code']; ?></span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div style="display: flex; align-items: center; gap: 8px; color: #475569;">
                                    <i class="ri-calendar-line"></i>
                                    <?= date('d M Y', strtotime($row['event_date'])); ?>
                                </div>
                            </td>
                            <td>
                                <?php 
                                    // Logika Warna Badge Status
                                    $st = $row['status'];
                                    $color = 'orange'; // Default Pending
                                    if($st == 'Confirmed') $color = 'green';
                                    if($st == 'Cancelled') $color = 'red';
                                    if($st == 'Completed') $color = 'blue'; // Misal ada status completed
                                ?>
                                <span class="badge badge-<?= $color ?>"><?= $st; ?></span>
                            </td>
                            <td style="text-align: right;">
                                <a href="booking_edit.php?id=<?= $row['id']; ?>" class="btn-icon-circle" title="Lihat Detail">
                                    <i class="ri-arrow-right-s-line"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="text-center" style="padding: 40px; color: #94a3b8;">
                                <i class="ri-inbox-archive-line" style="font-size: 2rem; display: block; margin-bottom: 10px;"></i>
                                Belum ada data booking terbaru.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="footer">
        <p>Copyright &copy; <?= date('Y'); ?> Eza Viralindo. All Rights Reserved.</p>
        <p class="credits">
            Powered by <a href="https://kdsstudio.my.id" target="_blank">KDS Creative Studio</a>. 
            Developed by <a href="https://ngabdulmuhyi.my.id" target="_blank">MasMoy</a>.
        </p>
    </div>

</div> <?php include 'includes/footer.php'; ?>