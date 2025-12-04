<?php
// 1. Panggil Koneksi & Aset Utama
require_once '../config/database.php';
include 'includes/header.php';
include 'includes/sidebar.php';

// --- LOGIKA WORKFLOW (HANDLER POST) ---

// A. Verifikasi Pembayaran oleh Admin
if(isset($_POST['step_verifikasi'])){
    $id = mysqli_real_escape_string($koneksi, $_POST['id']);
    $payment_status = mysqli_real_escape_string($koneksi, $_POST['payment_status']); // Confirmed DP atau Confirmed Full
    $waktu = date('Y-m-d H:i:s');
    
    $query = "UPDATE bookings SET 
              payment_status = '$payment_status', 
              status = 'Menunggu Jadwal TM',
              admin_verified_at = '$waktu'
              WHERE id = '$id'";
    
    mysqli_query($koneksi, $query);
    echo "<script>alert('Pembayaran Diverifikasi! Silakan atur jadwal TM.'); window.location='booking.php';</script>";
}

// B. Atur Jadwal TM
if(isset($_POST['step_atur_tm'])){
    $id = mysqli_real_escape_string($koneksi, $_POST['id']);
    $tm_date = mysqli_real_escape_string($koneksi, $_POST['tm_date']);
    $tm_time = mysqli_real_escape_string($koneksi, $_POST['tm_time']);
    $tm_location = mysqli_real_escape_string($koneksi, $_POST['tm_location']);

    $query = "UPDATE bookings SET 
              status = 'TM Terjadwal', 
              tm_date='$tm_date', 
              tm_time='$tm_time', 
              tm_location='$tm_location' 
              WHERE id = '$id'";
    
    mysqli_query($koneksi, $query);
    echo "<script>alert('Jadwal TM Tersimpan!'); window.location='booking.php';</script>";
}

// C. TM Selesai
if(isset($_POST['step_tm_selesai'])){
    $id = mysqli_real_escape_string($koneksi, $_POST['id']);
    $query = "UPDATE bookings SET status = 'TM Selesai' WHERE id = '$id'";
    mysqli_query($koneksi, $query);
    echo "<script>alert('TM Selesai. Menunggu Hari H.'); window.location='booking.php';</script>";
}

// D. Verifikasi Pelunasan (Dari Confirmed DP ke Confirmed Full)
if(isset($_POST['step_verif_pelunasan'])){
    $id = mysqli_real_escape_string($koneksi, $_POST['id']);
    $waktu = date('Y-m-d H:i:s');
    
    $query = "UPDATE bookings SET 
              payment_status = 'Confirmed Full',
              admin_verified_at = '$waktu'
              WHERE id = '$id'";
    
    mysqli_query($koneksi, $query);
    echo "<script>alert('Pelunasan Diverifikasi!'); window.location='booking.php';</script>";
}

// E. Acara Selesai
if(isset($_POST['step_acara_selesai'])){
    $id = mysqli_real_escape_string($koneksi, $_POST['id']);
    
    // Pastikan pembayaran sudah full
    mysqli_query($koneksi, "UPDATE bookings SET 
        status = 'Selesai', 
        payment_status = 'Confirmed Full' 
        WHERE id = '$id'");
    
    echo "<script>alert('Acara Selesai! Terima kasih.'); window.location='booking.php';</script>";
}

// F. Hapus Data
if(isset($_POST['hapus_data'])){
    $id = mysqli_real_escape_string($koneksi, $_POST['id']);
    mysqli_query($koneksi, "DELETE FROM bookings WHERE id = '$id'");
    echo "<script>alert('Data dihapus!'); window.location='booking.php';</script>";
}

// --- AMBIL DATA BOOKING ---
$query = mysqli_query($koneksi, "
    SELECT bookings.*, packages.name as package_name 
    FROM bookings 
    LEFT JOIN packages ON bookings.package_id = packages.id 
    ORDER BY bookings.event_date DESC
");
?>

<style>
    /* Layout Utama */
    .main-content { padding: 2rem; background: #f8fafc; min-height: 100vh; }
    
    /* Header */
    .header-dash { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
    .header-dash h2 { font-size: 1.5rem; color: #1e293b; font-weight: 700; }
    .btn-add { background: #2563eb; color: white; padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: 600; display: flex; align-items: center; gap: 8px; transition: 0.3s; }
    .btn-add:hover { background: #1d4ed8; }

    /* Card & Table */
    .card-table { background: white; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); overflow: hidden; border: 1px solid #e2e8f0; }
    .table-responsive { overflow-x: auto; }
    .table-custom { width: 100%; border-collapse: collapse; white-space: nowrap; }
    
    .table-custom th { text-align: left; padding: 15px; background: #f1f5f9; color: #64748b; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 600; border-bottom: 1px solid #e2e8f0; }
    .table-custom td { padding: 15px; border-bottom: 1px solid #f1f5f9; color: #334155; font-size: 0.9rem; vertical-align: top; }
    .table-custom tr:last-child td { border-bottom: none; }
    .table-custom tr:hover td { background-color: #f8fafc; }

    /* Badge & Info Styling */
    .client-info strong { display: block; color: #0f172a; font-size: 0.95rem; }
    .client-info small { color: #64748b; font-size: 0.8rem; }
    .booking-code { background: #f1f5f9; color: #475569; padding: 2px 6px; border-radius: 4px; font-family: monospace; font-size: 0.75rem; }
    
    .badge { padding: 5px 10px; border-radius: 20px; font-size: 0.75rem; font-weight: 600; display: inline-block; }
    .bg-green { background: #dcfce7; color: #166534; }
    .bg-red { background: #fee2e2; color: #991b1b; }
    .bg-orange { background: #ffedd5; color: #9a3412; }
    .bg-gray { background: #f3f4f6; color: #4b5563; }

    /* Workflow Buttons (Tombol Aksi Utama) */
    .wf-btn {
        display: block; width: 100%; padding: 8px 10px; border-radius: 6px;
        text-align: center; font-size: 0.75rem; font-weight: 700;
        cursor: pointer; border: none; transition: 0.2s; color: white;
        margin-top: 5px; text-transform: uppercase; letter-spacing: 0.5px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .wf-btn:hover { transform: translateY(-2px); box-shadow: 0 4px 8px rgba(0,0,0,0.15); filter: brightness(110%); }
    
    .btn-step-1 { background: #ef4444; } /* Merah: Konfirmasi */
    .btn-step-2 { background: #f59e0b; } /* Kuning: Atur TM */
    .btn-step-3 { background: #3b82f6; } /* Biru: TM Selesai */
    .btn-step-4 { background: #8b5cf6; } /* Ungu: Acara Selesai */
    .btn-done   { background: #10b981; cursor: default; box-shadow: none; } /* Hijau: Selesai */
    
    .btn-lunas { background: #be185d; color: white; border: none; padding: 4px 10px; border-radius: 4px; font-size: 0.7rem; cursor: pointer; margin-top: 5px; }

    /* Action Icons */
    .action-icons a { display: inline-flex; width: 32px; height: 32px; border-radius: 50%; align-items: center; justify-content: center; text-decoration: none; transition: 0.3s; margin-right: 5px; }
    .btn-edit { background: #eff6ff; color: #2563eb; }
    .btn-edit:hover { background: #2563eb; color: white; }
    .btn-delete { background: #fef2f2; color: #ef4444; }
    .btn-delete:hover { background: #ef4444; color: white; }

    /* MODAL STYLES */
    .modal-overlay {
        position: fixed; top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(0,0,0,0.5); z-index: 9999; display: none;
        align-items: center; justify-content: center; backdrop-filter: blur(2px);
    }
    .modal-card {
        background: white; width: 90%; max-width: 450px;
        padding: 30px; border-radius: 16px; position: relative;
        animation: slideDown 0.3s ease; box-shadow: 0 20px 50px rgba(0,0,0,0.2);
    }
    @keyframes slideDown { from{opacity:0; transform:translateY(-20px);} to{opacity:1; transform:translateY(0);} }
    .close-modal { position: absolute; top: 20px; right: 20px; font-size: 1.5rem; cursor: pointer; color: #94a3b8; transition: 0.3s; }
    .close-modal:hover { color: #ef4444; }
    .modal-title { font-size: 1.2rem; font-weight: 700; margin-bottom: 20px; color: #1e293b; padding-bottom: 15px; border-bottom: 1px solid #f1f5f9; }
    
    /* Form dalam Modal */
    .form-group { margin-bottom: 15px; }
    .form-group label { display: block; margin-bottom: 5px; font-size: 0.85rem; font-weight: 600; color: #475569; }
    .form-control { width: 100%; padding: 10px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 0.9rem; }
    .btn-modal-submit { width: 100%; padding: 12px; background: #0f172a; color: white; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; margin-top: 10px; transition: 0.3s; }
    .btn-modal-submit:hover { background: #1e293b; }
</style>

<div class="main-content">
    <div class="header-dash">
        <h2>Manajemen Booking & Workflow</h2>
        <a href="booking_tambah.php" class="btn-add"><i class="ri-add-circle-line"></i> Booking Baru</a>
    </div>

    <div class="card-table">
        <div class="table-responsive">
            <table class="table-custom">
                <thead>
                    <tr>
                        <th width="25%">Klien & Paket</th>
                        <th width="25%">Detail Acara</th>
                        <th width="20%">Keuangan</th>
                        <th width="20%">Workflow Status</th>
                        <th width="10%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(mysqli_num_rows($query) > 0): ?>
                        <?php while($data = mysqli_fetch_assoc($query)): ?>
                        <tr>
                            <td class="client-info">
                                <strong><?= $data['client_name']; ?></strong>
                                <small class="booking-code"><?= $data['booking_code']; ?></small>
                                <div style="margin-top:5px; color:#2563eb; font-weight:500; font-size:0.8rem;">
                                    <?= $data['package_name']; ?>
                                </div>
                            </td>

                            <td>
                                <strong><?= $data['event_name']; ?></strong>
                                <div style="font-size:0.85rem; color:#64748b; margin-top:4px;">
                                    <i class="ri-calendar-line"></i> <?= date('d M Y', strtotime($data['event_date'])); ?><br>
                                    <i class="ri-map-pin-line"></i> <?= substr($data['event_venue'], 0, 20); ?>..
                                </div>
                            </td>

                            <td>
                                <?php 
                                    $bg = ($data['payment_status'] == 'Paid') ? 'bg-green' : (($data['payment_status'] == 'Down Payment') ? 'bg-orange' : 'bg-red');
                                ?>
                                <span class="badge <?= $bg ?>"><?= $data['payment_status']; ?></span>
                                <div style="font-size:0.75rem; margin-top:5px; color:#64748b;"><?= $data['payment_option']; ?></div>

                                <?php if($data['payment_status'] == 'Down Payment'): ?>
                                    <button onclick="openModal('modal-lunas-<?= $data['id'] ?>')" class="btn-lunas">Verifikasi Lunas</button>
                                <?php endif; ?>
                            </td>

                            <td>
    <!-- Status Display -->
    <div style="background:#f8fafc; padding:10px; border-radius:8px; margin-bottom:10px;">
        <small style="display:block; color:#64748b; font-size:0.7rem; font-weight:600;">STATUS PEMBAYARAN</small>
        <strong style="color:#2563eb; font-size:0.8rem;"><?= $data['payment_status']; ?></strong>
    </div>
    <div style="background:#f0fdf4; padding:10px; border-radius:8px; margin-bottom:10px;">
        <small style="display:block; color:#64748b; font-size:0.7rem; font-weight:600;">STATUS ACARA</small>
        <strong style="color:#16a34a; font-size:0.8rem;"><?= $data['status']; ?></strong>
    </div>

    <!-- Action Buttons -->
    <?php
    $ps = $data['payment_status']; // Payment Status
    $es = $data['status']; // Event Status
    
    // STEP 1: Verifikasi Pembayaran Pertama
    if(($ps == 'Confirmed DP' || $ps == 'Confirmed Full') && $es == 'Menunggu Jadwal TM' && empty($data['admin_verified_at'])) {
        ?>
        <button onclick="openModal('modal-verif-<?= $data['id'] ?>')" class="wf-btn btn-step-1">
            <i class="ri-check-line"></i> Verifikasi Bayar
        </button>
        <?php
    }
    // STEP 2: Atur Jadwal TM
    elseif($ps != 'Menunggu Pembayaran' && $es == 'Menunggu Jadwal TM' && !empty($data['admin_verified_at'])) {
        ?>
        <button onclick="openModal('modal-tm-<?= $data['id'] ?>')" class="wf-btn btn-step-2">
            <i class="ri-calendar-event-fill"></i> Atur Jadwal TM
        </button>
        <?php
    }
    // STEP 3: TM Terjadwal - Tampilkan Info + Button TM Selesai
    elseif($es == 'TM Terjadwal') {
        ?>
        <small style="display:block; text-align:center; color:#059669; font-size:0.7rem; margin-bottom:5px;">
            📅 <?= date('d/m', strtotime($data['tm_date'])); ?> @ <?= $data['tm_time']; ?>
        </small>
        <button onclick="openModal('modal-tm-done-<?= $data['id'] ?>')" class="wf-btn btn-step-3">
            <i class="ri-discuss-line"></i> TM Selesai
        </button>
        
        <!-- Jika masih DP, munculkan tombol verifikasi pelunasan -->
        <?php if($ps == 'Confirmed DP' || $ps == 'Menunggu Verifikasi Pelunasan'): ?>
            <button onclick="openModal('modal-pelunasan-<?= $data['id'] ?>')" class="wf-btn" style="background:#16a34a; margin-top:5px;">
                <i class="ri-money-dollar-circle-line"></i> Verif Pelunasan
            </button>
        <?php endif; ?>
        <?php
    }
    // STEP 4: TM Selesai - Menunggu Acara
    elseif($es == 'TM Selesai') {
        ?>
        <button onclick="openModal('modal-acara-done-<?= $data['id'] ?>')" class="wf-btn btn-step-4">
            <i class="ri-flag-2-fill"></i> Selesaikan Acara
        </button>
        
        <!-- Tombol Pelunasan jika masih DP -->
        <?php if($ps == 'Confirmed DP' || $ps == 'Menunggu Verifikasi Pelunasan'): ?>
            <button onclick="openModal('modal-pelunasan-<?= $data['id'] ?>')" class="wf-btn" style="background:#16a34a; margin-top:5px;">
                <i class="ri-money-dollar-circle-line"></i> Verif Pelunasan
            </button>
        <?php endif; ?>
        <?php
    }
    // FINAL: Acara Selesai
    elseif($es == 'Selesai') {
        ?>
        <button class="wf-btn btn-done">
            <i class="ri-checkbox-circle-line"></i> COMPLETED
        </button>
        <?php
    }
    // DEFAULT: Masih Pending
    else {
        ?>
        <button class="wf-btn" style="background:#94a3b8; cursor:default;" disabled>
            <i class="ri-time-line"></i> Menunggu Customer
        </button>
        <?php
    }
    ?>
</td>

                            <td class="action-icons">
                                <a href="booking_edit.php?id=<?= $data['id']; ?>" class="btn-edit" title="Edit"><i class="ri-pencil-line"></i></a>
                                <form method="POST" style="display:inline;" onsubmit="return confirm('Yakin hapus data ini?');">
                                    <input type="hidden" name="id" value="<?= $data['id']; ?>">
                                    <button type="submit" name="hapus_data" class="btn-delete" style="border:none; width:32px; height:32px; border-radius:50%; cursor:pointer;"><i class="ri-delete-bin-line"></i></button>
                                </form>
                            </td>
                        </tr>

                        <div id="modal-verif-<?= $data['id'] ?>" class="modal-overlay">
                            <div class="modal-card">
                                <i class="ri-close-line close-modal" onclick="closeModal('modal-verif-<?= $data['id'] ?>')"></i>
                                <div class="modal-title">Konfirmasi Booking</div>
                                <p style="color:#64748b; font-size:0.9rem; margin-bottom:15px;">Pastikan dana sudah masuk rekening.</p>
                                <form method="POST">
                                    <input type="hidden" name="id" value="<?= $data['id']; ?>">
                                    <div class="form-group">
                                        <label>Status Pembayaran</label>
                                        <select name="payment_status" class="form-control">
                                            <option value="Down Payment">Down Payment (DP)</option>
                                            <option value="Paid">Full Payment (Lunas)</option>
                                        </select>
                                    </div>
                                    <button type="submit" name="step_verifikasi" class="btn-modal-submit" style="background:#ef4444;">Konfirmasi</button>
                                </form>
                            </div>
                        </div>

                        <div id="modal-tm-<?= $data['id'] ?>" class="modal-overlay">
                            <div class="modal-card">
                                <i class="ri-close-line close-modal" onclick="closeModal('modal-tm-<?= $data['id'] ?>')"></i>
                                <div class="modal-title">Jadwal Technical Meeting</div>
                                <form method="POST">
                                    <input type="hidden" name="id" value="<?= $data['id']; ?>">
                                    <div class="form-group"><label>Tanggal</label><input type="date" name="tm_date" class="form-control" required></div>
                                    <div class="form-group"><label>Jam</label><input type="time" name="tm_time" class="form-control" required></div>
                                    <div class="form-group"><label>Lokasi / Link</label><input type="text" name="tm_location" class="form-control" placeholder="Zoom / Cafe..." required></div>
                                    <button type="submit" name="step_atur_tm" class="btn-modal-submit" style="background:#f59e0b;">Simpan Jadwal</button>
                                </form>
                            </div>
                        </div>

                        <div id="modal-tm-done-<?= $data['id'] ?>" class="modal-overlay">
                            <div class="modal-card">
                                <i class="ri-close-line close-modal" onclick="closeModal('modal-tm-done-<?= $data['id'] ?>')"></i>
                                <div class="modal-title">Konfirmasi TM Selesai</div>
                                <p style="color:#64748b;">Apakah TM sudah selesai dan semua detail acara sudah disepakati?</p>
                                <form method="POST">
                                    <input type="hidden" name="id" value="<?= $data['id']; ?>">
                                    <button type="submit" name="step_tm_selesai" class="btn-modal-submit" style="background:#3b82f6;">Ya, Lanjut</button>
                                </form>
                            </div>
                        </div>

                        <div id="modal-acara-done-<?= $data['id'] ?>" class="modal-overlay">
                            <div class="modal-card">
                                <i class="ri-close-line close-modal" onclick="closeModal('modal-acara-done-<?= $data['id'] ?>')"></i>
                                <div class="modal-title">Selesaikan Acara</div>
                                <p style="color:#64748b;">Tandai acara <strong><?= $data['event_name']; ?></strong> sebagai selesai? Status bayar otomatis menjadi <b>Paid</b>.</p>
                                <form method="POST">
                                    <input type="hidden" name="id" value="<?= $data['id']; ?>">
                                    <button type="submit" name="step_acara_selesai" class="btn-modal-submit" style="background:#8b5cf6;">Ya, Selesaikan</button>
                                </form>
                            </div>
                        </div>

                        <!-- MODAL VERIFIKASI PELUNASAN -->
<div id="modal-pelunasan-<?= $data['id'] ?>" class="modal-overlay">
    <div class="modal-card">
        <i class="ri-close-line close-modal" onclick="closeModal('modal-pelunasan-<?= $data['id'] ?>')"></i>
        <div class="modal-title">Verifikasi Pelunasan</div>
        <p style="color:#64748b;">Konfirmasi bahwa customer <strong><?= $data['client_name']; ?></strong> telah melunasi pembayaran?</p>
        
        <div style="background:#fef3c7; padding:15px; border-radius:8px; margin:15px 0; border-left:4px solid #f59e0b;">
            <strong style="color:#92400e;">Info Pembayaran:</strong><br>
            <small style="color:#78350f;">
                Total: Rp <?= number_format($data['total_price'], 0, ',', '.'); ?><br>
                DP Dibayar: Rp <?= number_format($data['down_payment_amount'], 0, ',', '.'); ?><br>
                <strong>Sisa: Rp <?= number_format($data['total_price'] - $data['down_payment_amount'], 0, ',', '.'); ?></strong>
            </small>
        </div>
        
        <form method="POST">
            <input type="hidden" name="id" value="<?= $data['id']; ?>">
            <button type="submit" name="step_verif_pelunasan" class="btn-modal-submit" style="background:#16a34a;">
                ✓ Ya, Pelunasan Sudah Masuk
            </button>
        </form>
    </div>
</div>

                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="5" class="text-center" style="padding:40px; color:#94a3b8;">Belum ada data booking.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    function openModal(id) { document.getElementById(id).style.display = 'flex'; }
    function closeModal(id) { document.getElementById(id).style.display = 'none'; }
    
    // Tutup modal jika klik luar
    window.onclick = function(e) { 
        if(e.target.classList.contains('modal-overlay')) e.target.style.display = 'none'; 
    }
</script>

<?php include 'includes/footer.php'; ?>