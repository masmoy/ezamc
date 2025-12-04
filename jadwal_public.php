<?php
require_once 'config/database.php';

$query_setting = mysqli_query($koneksi, "SELECT * FROM settings WHERE id = 1");
$setting = mysqli_fetch_assoc($query_setting);

// Logika Waktu
$bulan = isset($_GET['bulan']) ? $_GET['bulan'] : date('m');
$tahun = isset($_GET['tahun']) ? $_GET['tahun'] : date('Y');

$prev_month = date('m', mktime(0, 0, 0, $bulan - 1, 1, $tahun));
$prev_year  = date('Y', mktime(0, 0, 0, $bulan - 1, 1, $tahun));
$next_month = date('m', mktime(0, 0, 0, $bulan + 1, 1, $tahun));
$next_year  = date('Y', mktime(0, 0, 0, $bulan + 1, 1, $tahun));

$jumlah_hari = cal_days_in_month(CAL_GREGORIAN, $bulan, $tahun);
$hari_pertama = date('w', mktime(0, 0, 0, $bulan, 1, $tahun));

// Ambil Data Booking
$query_jadwal = mysqli_query($koneksi, "SELECT * FROM bookings WHERE MONTH(event_date) = '$bulan' AND YEAR(event_date) = '$tahun' AND status != 'Cancelled'");
$bookings = [];
while ($row = mysqli_fetch_assoc($query_jadwal)) {
    $tgl = date('j', strtotime($row['event_date']));
    $bookings[$tgl] = true;
}

$nama_bulan = ['01'=>'Januari', '02'=>'Februari', '03'=>'Maret', '04'=>'April', '05'=>'Mei', '06'=>'Juni', '07'=>'Juli', '08'=>'Agustus', '09'=>'September', '10'=>'Oktober', '11'=>'November', '12'=>'Desember'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jadwal - <?= $setting['site_name']; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');
        :root { --primary: #0f172a; --accent: #14b8a6; --bg-light: #f8fafc; }
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }
        body { background: var(--bg-light); color: #334155; }

        .navbar { background: var(--primary); padding: 1rem 5%; display: flex; justify-content: space-between; align-items: center; position: fixed; width: 100%; top: 0; z-index: 100; box-shadow: 0 4px 20px rgba(0,0,0,0.1); }
        .logo { font-size: 1.4rem; font-weight: 700; color: white; text-decoration: none; }
        .nav-link { color: rgba(255,255,255,0.8); text-decoration: none; font-size: 0.9rem; margin-left: 20px; transition:0.3s; }
        .nav-link:hover { color: var(--accent); }

        .container { max-width: 1000px; margin: 100px auto 50px; padding: 0 20px; }
        .calendar-card { background: white; padding: 30px; border-radius: 16px; box-shadow: 0 10px 40px rgba(0,0,0,0.05); }
        
        .header-cal { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .header-cal h2 { color: var(--primary); font-size: 1.5rem; }
        .btn-nav { background: var(--bg-light); color: var(--primary); padding: 8px 15px; border-radius: 8px; text-decoration: none; font-weight: 600; transition:0.3s; }
        .btn-nav:hover { background: var(--primary); color: white; }

        .calendar-wrapper { overflow-x: auto; }
        .calendar-table { width: 100%; border-collapse: collapse; min-width: 600px; }
        .calendar-table th { padding: 15px; background: #f1f5f9; color: #64748b; font-weight: 600; }
        .calendar-table td { width: 14.28%; height: 120px; border: 1px solid #e2e8f0; padding: 10px; vertical-align: top; position: relative; transition: 0.2s; }
        
        .date-num { font-weight: 700; color: var(--primary); margin-bottom: 5px; display: block; }
        
        /* Status Badge */
        .status-badge { display: block; padding: 5px; border-radius: 5px; font-size: 0.75rem; text-align: center; font-weight: 600; margin-top: 5px; }
        .booked { background: #fee2e2; color: #991b1b; cursor: not-allowed; }
        
        /* Available Date Style */
        .available-cell { cursor: pointer; }
        .available-cell:hover { background: #f0fdfa; border-color: var(--accent); }
        .available-badge { background: #ccfbf1; color: var(--accent); }
        
        /* Past Date */
        .past-date { background: #f8fafc; opacity: 0.6; cursor: not-allowed; }
    </style>
</head>
<body>

    <nav class="navbar">
        <a href="index.php" class="logo"><?= $setting['site_name']; ?></a>
        <div><a href="index.php" class="nav-link">Kembali ke Home</a></div>
    </nav>

    <div class="container">
        <div class="calendar-card">
            <div style="text-align: center; margin-bottom: 30px;">
                <h1 style="color: var(--primary);">Cek Ketersediaan Tanggal</h1>
                <p style="color: #64748b;">Klik pada tanggal yang bertanda <span style="color:var(--accent); font-weight:bold;">Tersedia</span> untuk Booking.</p>
            </div>

            <div class="header-cal">
                <a href="?bulan=<?= $prev_month ?>&tahun=<?= $prev_year ?>" class="btn-nav"><i class="ri-arrow-left-s-line"></i> Prev</a>
                <h2><?= $nama_bulan[$bulan] ?> <?= $tahun ?></h2>
                <a href="?bulan=<?= $next_month ?>&tahun=<?= $next_year ?>" class="btn-nav">Next <i class="ri-arrow-right-s-line"></i></a>
            </div>

            <div class="calendar-wrapper">
                <table class="calendar-table">
                    <thead>
                        <tr>
                            <th style="color: #ef4444;">Minggu</th><th>Senin</th><th>Selasa</th><th>Rabu</th><th>Kamis</th><th>Jumat</th><th>Sabtu</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <?php
                            // Kotak kosong awal bulan
                            for ($i = 0; $i < $hari_pertama; $i++) echo '<td></td>';

                            for ($day = 1; $day <= $jumlah_hari; $day++) {
                                // Ganti baris setiap minggu
                                if (($day + $hari_pertama - 1) % 7 == 0 && $day != 1) echo '</tr><tr>';
                                
                                // Format tanggal lengkap YYYY-MM-DD
                                $full_date = sprintf("%04d-%02d-%02d", $tahun, $bulan, $day);
                                $today = date('Y-m-d');

                                // Cek Status
                                if (isset($bookings[$day])) {
                                    // SUDAH DIBOOKING
                                    echo '<td class="booked-cell">';
                                    echo '<span class="date-num">'.$day.'</span>';
                                    echo '<span class="status-badge booked">Sudah Terisi</span>';
                                    echo '</td>';
                                } elseif ($full_date < $today) {
                                    // TANGGAL LEWAT (Masa Lalu)
                                    echo '<td class="past-date">';
                                    echo '<span class="date-num">'.$day.'</span>';
                                    echo '</td>';
                                } else {
                                    // TERSEDIA (Bisa Diklik)
                                    // Link menuju booking dengan parameter GET tanggal
                                    echo '<td class="available-cell" onclick="window.location=\'booking_public.php?date='.$full_date.'\'" title="Klik untuk Booking">';
                                    echo '<span class="date-num">'.$day.'</span>';
                                    echo '<span class="status-badge available-badge">Tersedia</span>';
                                    echo '</td>';
                                }
                            }
                            ?>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <p style="text-align: center; margin-top: 20px; font-size: 0.9rem; color: #94a3b8;">&copy; 2025 <?= $setting['site_name']; ?></p>
    </div>

</body>
</html>