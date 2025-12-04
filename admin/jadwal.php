<?php
require_once '../config/database.php';
include 'includes/header.php';
include 'includes/sidebar.php';

// 1. Logika Waktu (Bulan & Tahun)
$bulan = isset($_GET['bulan']) ? $_GET['bulan'] : date('m');
$tahun = isset($_GET['tahun']) ? $_GET['tahun'] : date('Y');

// Navigasi Bulan (Sebelum & Sesudah)
$prev_month = date('m', mktime(0, 0, 0, $bulan - 1, 1, $tahun));
$prev_year  = date('Y', mktime(0, 0, 0, $bulan - 1, 1, $tahun));
$next_month = date('m', mktime(0, 0, 0, $bulan + 1, 1, $tahun));
$next_year  = date('Y', mktime(0, 0, 0, $bulan + 1, 1, $tahun));

// Data Kalender
$jumlah_hari = cal_days_in_month(CAL_GREGORIAN, $bulan, $tahun);
$hari_pertama = date('w', mktime(0, 0, 0, $bulan, 1, $tahun)); // 0 = Minggu, 6 = Sabtu

// 2. Ambil Data Booking Bulan Ini
$query_jadwal = mysqli_query($koneksi, "
    SELECT * FROM bookings 
    WHERE MONTH(event_date) = '$bulan' 
    AND YEAR(event_date) = '$tahun' 
    AND status != 'Cancelled'
");

// Masukkan data ke array biar mudah dicek nanti
$bookings = [];
while ($row = mysqli_fetch_assoc($query_jadwal)) {
    // Index array pakai tanggalnya (misal: 2025-10-25)
    $tgl = date('j', strtotime($row['event_date'])); // Ambil tanggalnya saja (1-31)
    $bookings[$tgl][] = $row; // Simpan data (bisa lebih dari 1 acara sehari)
}

// Nama Bulan Indo
$nama_bulan = [
    '01'=>'Januari', '02'=>'Februari', '03'=>'Maret', '04'=>'April', '05'=>'Mei', '06'=>'Juni',
    '07'=>'Juli', '08'=>'Agustus', '09'=>'September', '10'=>'Oktober', '11'=>'November', '12'=>'Desember'
];
?>

<div class="main-content">
    <div class="header-dash">
        <div style="display: flex; align-items: center;">
            <i class="ri-menu-line menu-toggle" style="font-size: 1.5rem; margin-right: 1rem; cursor: pointer; display: none;"></i>
            <h2>Kalender Jadwal</h2>
        </div>
    </div>

    <div style="background: white; padding: 1.5rem; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.02);">
        
        <div class="calendar-header">
            <a href="?bulan=<?= $prev_month ?>&tahun=<?= $prev_year ?>" class="btn-nav"><i class="ri-arrow-left-s-line"></i></a>
            <h3><?= $nama_bulan[$bulan] ?> <?= $tahun ?></h3>
            <a href="?bulan=<?= $next_month ?>&tahun=<?= $next_year ?>" class="btn-nav"><i class="ri-arrow-right-s-line"></i></a>
        </div>

        <div class="calendar-wrapper">
            <table class="calendar-table">
                <thead>
                    <tr>
                        <th class="red-text">Minggu</th>
                        <th>Senin</th>
                        <th>Selasa</th>
                        <th>Rabu</th>
                        <th>Kamis</th>
                        <th>Jumat</th>
                        <th>Sabtu</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <?php
                        // Kotak Kosong sebelum tanggal 1
                        for ($i = 0; $i < $hari_pertama; $i++) {
                            echo '<td class="empty"></td>';
                        }

                        // Loop Tanggal 1 sampai Selesai
                        for ($day = 1; $day <= $jumlah_hari; $day++) {
                            // Cek ganti baris setiap hari Minggu (kolom ke-0 sudah lewat, total modul 7 == 0)
                            if (($day + $hari_pertama - 1) % 7 == 0 && $day != 1) {
                                echo '</tr><tr>';
                            }

                            // Cek hari ini
                            $is_today = ($day == date('j') && $bulan == date('m') && $tahun == date('Y')) ? 'today' : '';

                            echo '<td class="'.$is_today.'">';
                            echo '<span class="date-num">'.$day.'</span>';

                            // Cek apakah ada booking di tanggal ini
                            if (isset($bookings[$day])) {
                                foreach ($bookings[$day] as $acara) {
                                    $statusColor = ($acara['status'] == 'Confirmed') ? '#d1fae5' : '#ffedd5'; // Hijau / Kuning
                                    $textColor = ($acara['status'] == 'Confirmed') ? '#065f46' : '#9a3412';
                                    
                                    echo '<a href="booking_edit.php?id='.$acara['id'].'" class="event-badge" style="background:'.$statusColor.'; color:'.$textColor.';">';
                                    echo '<small>'.substr($acara['client_name'], 0, 10).'..</small>';
                                    echo '</a>';
                                }
                            }
                            echo '</td>';
                        }

                        // Kotak Kosong sisa setelah tanggal terakhir
                        $sisa_kolom = (7 - ($jumlah_hari + $hari_pertama) % 7) % 7;
                        for ($i = 0; $i < $sisa_kolom; $i++) {
                            echo '<td class="empty"></td>';
                        }
                        ?>
                    </tr>
                </tbody>
            </table>
        </div>

        <div style="margin-top: 20px; font-size: 0.85rem; color: #666;">
            <span style="display:inline-block; width:12px; height:12px; background:#d1fae5; margin-right:5px; border-radius:2px;"></span> Confirmed
            <span style="display:inline-block; width:12px; height:12px; background:#ffedd5; margin-left:15px; margin-right:5px; border-radius:2px;"></span> Pending
        </div>

    </div>
</div>

<style>
.calendar-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
.calendar-header h3 { margin: 0; color: #1e3a8a; font-size: 1.5rem; }
.btn-nav { background: #f3f4f6; padding: 5px 15px; border-radius: 8px; color: #374151; font-size: 1.2rem; transition: 0.3s; }
.btn-nav:hover { background: #dbeafe; color: #2563eb; }

/* Wrapper agar scrollable di HP */
.calendar-wrapper { overflow-x: auto; }

.calendar-table { width: 100%; border-collapse: collapse; min-width: 800px; /* Minimal lebar agar tidak gepeng di HP */ }
.calendar-table th { padding: 15px; background: #f9fafb; border: 1px solid #e5e7eb; font-weight: 600; color: #374151; }
.calendar-table td { 
    width: 14.28%; /* 100% dibagi 7 hari */
    height: 120px; /* Tinggi kotak tanggal */
    border: 1px solid #e5e7eb; 
    padding: 10px; 
    vertical-align: top; 
    position: relative;
    background: white;
}

.calendar-table td:hover { background-color: #f9fafb; }
.calendar-table td.empty { background-color: #f9fafb; } /* Hari bulan lain */
.calendar-table td.today { background-color: #eff6ff; border: 2px solid #2563eb; } /* Hari ini */

.date-num { font-weight: bold; color: #374151; display: block; margin-bottom: 5px; }
.red-text { color: #ef4444; }

.event-badge {
    display: block;
    margin-bottom: 4px;
    padding: 2px 6px;
    border-radius: 4px;
    font-size: 0.75rem;
    font-weight: 600;
    text-decoration: none;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    transition: transform 0.2s;
}
.event-badge:hover { transform: scale(1.02); opacity: 0.9; }
</style>

<?php include 'includes/footer.php'; ?>