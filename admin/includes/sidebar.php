<div class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        Admin Panel
    </div>
    <ul class="sidebar-menu">
        <li><a href="index.php" class="<?= basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : '' ?>"><i class="ri-dashboard-line" style="margin-right: 10px;"></i> Dashboard</a></li>
        <li><a href="pesan.php"><i class="ri-mail-line" style="margin-right: 10px;"></i> Pesan Masuk</a></li>
        <li><a href="booking.php"><i class="ri-calendar-event-line" style="margin-right: 10px;"></i> Kelola Booking</a></li>
        <li><a href="jadwal.php"><i class="ri-calendar-todo-line" style="margin-right: 10px;"></i> Kalender Jadwal</a></li>
        <li><a href="paket.php"><i class="ri-price-tag-3-line" style="margin-right: 10px;"></i> Kelola Paket</a></li>
        <li><a href="portofolio.php"><i class="ri-image-line" style="margin-right: 10px;"></i> Kelola Portofolio</a></li>
        <li><a href="pengaturan.php"><i class="ri-settings-4-line" style="margin-right: 10px;"></i> Pengaturan Web</a></li>
        <li><a href="logout.php" onclick="return confirm('Yakin ingin keluar?')"><i class="ri-logout-box-line" style="margin-right: 10px;"></i> Logout</a></li>
    </ul>
</div>