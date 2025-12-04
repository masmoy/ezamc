<?php
require_once '../config/database.php';
include 'includes/header.php';
include 'includes/sidebar.php';

$id = mysqli_real_escape_string($koneksi, $_GET['id']);
$query = mysqli_query($koneksi, "SELECT bookings.*, packages.name as package_name, packages.type as package_type 
                                  FROM bookings 
                                  LEFT JOIN packages ON bookings.package_id = packages.id 
                                  WHERE bookings.id = '$id'");
$data = mysqli_fetch_assoc($query);

if(!$data) {
    echo "<script>alert('Data tidak ditemukan!'); window.location='booking.php';</script>";
    exit;
}

// PROSES UPDATE
if (isset($_POST['update'])) {
    $client_name = mysqli_real_escape_string($koneksi, $_POST['client_name']);
    $client_email = mysqli_real_escape_string($koneksi, $_POST['client_email']);
    $client_phone = mysqli_real_escape_string($koneksi, $_POST['client_phone']);
    $event_name = mysqli_real_escape_string($koneksi, $_POST['event_name']);
    $event_date = mysqli_real_escape_string($koneksi, $_POST['event_date']);
    $event_venue = mysqli_real_escape_string($koneksi, $_POST['event_venue']);
    $notes = mysqli_real_escape_string($koneksi, $_POST['notes']);
    
    // Status
    $payment_status = mysqli_real_escape_string($koneksi, $_POST['payment_status']);
    $event_status = mysqli_real_escape_string($koneksi, $_POST['event_status']);
    
    // TM Info (opsional)
    $tm_date = !empty($_POST['tm_date']) ? mysqli_real_escape_string($koneksi, $_POST['tm_date']) : NULL;
    $tm_time = !empty($_POST['tm_time']) ? mysqli_real_escape_string($koneksi, $_POST['tm_time']) : NULL;
    $tm_location = !empty($_POST['tm_location']) ? mysqli_real_escape_string($koneksi, $_POST['tm_location']) : NULL;

    $q = "UPDATE bookings SET 
          client_name='$client_name', 
          client_email='$client_email',
          client_phone='$client_phone',
          event_name='$event_name', 
          event_date='$event_date', 
          event_venue='$event_venue',
          notes='$notes',
          payment_status='$payment_status',
          status='$event_status'";
    
    // Tambah TM info jika ada
    if($tm_date) $q .= ", tm_date='$tm_date'";
    if($tm_time) $q .= ", tm_time='$tm_time'";
    if($tm_location) $q .= ", tm_location='$tm_location'";
    
    $q .= " WHERE id='$id'";
          
    if(mysqli_query($koneksi, $q)){
        echo "<script>alert('✓ Perubahan berhasil disimpan!'); window.location='booking.php';</script>";
    } else {
        echo "<script>alert('✗ Gagal update: ".mysqli_error($koneksi)."');</script>";
    }
}
?>

<style>
    .edit-container { max-width: 1100px; margin: 0 auto; }
    
    .edit-header {
        background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
        color: white; padding: 30px; border-radius: 16px;
        margin-bottom: 30px; display: flex; justify-content: space-between;
        align-items: center; box-shadow: 0 10px 30px rgba(0,0,0,0.2);
    }
    .edit-header h1 { 
        font-size: 1.8rem; margin: 0; display: flex; 
        align-items: center; gap: 15px; font-weight: 800;
    }
    .btn-back {
        background: rgba(255,255,255,0.2); backdrop-filter: blur(10px);
        padding: 10px 20px; border-radius: 8px; color: white;
        text-decoration: none; font-weight: 600; transition: 0.3s;
        display: flex; align-items: center; gap: 8px;
    }
    .btn-back:hover { background: rgba(255,255,255,0.3); }

    /* Info Card */
    .info-card {
        background: white; padding: 25px; border-radius: 12px;
        margin-bottom: 25px; box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        border-left: 5px solid #2563eb;
    }
    .info-grid {
        display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px;
    }
    .info-item {
        padding: 15px; background: #f8fafc; border-radius: 8px;
   }
.info-label {
font-size: 0.75rem; color: #64748b; font-weight: 600;
text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 5px;
}
.info-value {
font-size: 1rem; color: #0f172a; font-weight: 700;
}

/* Section Card */
.section-card {
    background: white; padding: 30px; border-radius: 16px;
    margin-bottom: 25px; box-shadow: 0 4px 15px rgba(0,0,0,0.05);
    border: 1px solid #e2e8f0;
}
.section-card-header {
    display: flex; align-items: center; gap: 12px;
    padding-bottom: 20px; margin-bottom: 25px;
    border-bottom: 2px solid #f1f5f9;
}
.section-icon {
    width: 45px; height: 45px; background: #eff6ff;
    color: #2563eb; border-radius: 12px; display: flex;
    align-items: center; justify-content: center;
    font-size: 1.5rem;
}
.section-title {
    font-size: 1.2rem; font-weight: 700; color: #1e293b;
}

/* Form Styling */
.form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px; }
.form-group { margin-bottom: 20px; }
.form-group label {
    display: block; margin-bottom: 8px; font-weight: 600;
    color: #475569; font-size: 0.9rem;
}
.form-control {
    width: 100%; padding: 12px 15px; border: 2px solid #e2e8f0;
    border-radius: 10px; font-size: 0.95rem; transition: 0.3s;
    font-family: 'Poppins', sans-serif;
}
.form-control:focus {
    border-color: #2563eb; outline: none;
    box-shadow: 0 0 0 4px rgba(37,99,235,0.1);
}
textarea.form-control { min-height: 100px; resize: vertical; }

/* Status Override Box */
.status-override {
    background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
    padding: 25px; border-radius: 12px; margin-bottom: 25px;
    border: 2px solid #fbbf24;
}
.status-override-title {
    font-size: 1.1rem; font-weight: 700; color: #92400e;
    margin-bottom: 15px; display: flex; align-items: center; gap: 10px;
}
.status-grid {
    display: grid; grid-template-columns: 1fr 1fr; gap: 15px;
}

/* TM Section */
.tm-section {
    background: #f0fdf4; padding: 20px; border-radius: 12px;
    border: 2px solid #86efac; margin-top: 20px;
}
.tm-title {
    font-size: 1rem; font-weight: 700; color: #166534;
    margin-bottom: 15px; display: flex; align-items: center; gap: 8px;
}
.tm-grid {
    display: grid; grid-template-columns: 1fr 1fr 2fr; gap: 15px;
}

/* Action Buttons */
.btn-save {
    background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
    color: white; padding: 16px 40px; border: none;
    border-radius: 10px; font-weight: 700; font-size: 1.1rem;
    cursor: pointer; width: 100%; transition: 0.3s;
    display: flex; align-items: center; justify-content: center; gap: 10px;
    box-shadow: 0 10px 25px rgba(37,99,235,0.3);
    text-transform: uppercase; letter-spacing: 1px;
}
.btn-save:hover {
    transform: translateY(-2px);
    box-shadow: 0 15px 35px rgba(37,99,235,0.4);
}

@media (max-width: 768px) {
    .form-row, .status-grid, .info-grid, .tm-grid { grid-template-columns: 1fr; }
}

</style>
<div class="main-content">
    <div class="edit-container">
<div class="edit-header">
        <h1>
            <i class="ri-edit-box-line"></i>
            Edit Booking
        </h1>
        <a href="booking.php" class="btn-back">
            <i class="ri-arrow-left-line"></i> Kembali
        </a>
    </div>

    <!-- Info Card -->
    <div class="info-card">
        <div class="info-grid">
            <div class="info-item">
                <div class="info-label">Kode Booking</div>
                <div class="info-value"><?= $data['booking_code']; ?></div>
            </div>
            <div class="info-item">
                <div class="info-label">Paket</div>
                <div class="info-value"><?= $data['package_name'] ?? 'N/A'; ?></div>
            </div>
            <div class="info-item">
                <div class="info-label">Total Harga</div>
                <div class="info-value" style="color: #2563eb;">Rp <?= number_format($data['total_price'], 0, ',', '.'); ?></div>
            </div>
        </div>
    </div>

    <form method="POST">
        
        <!-- Status Override -->
        <div class="status-override">
            <div class="status-override-title">
                <i class="ri-settings-5-fill"></i>
                Manual Status Override
            </div>
            <p style="color: #92400e; font-size: 0.85rem; margin-bottom: 15px;">
                Ubah status secara manual jika diperlukan (override workflow otomatis)
            </p>
            <div class="status-grid">
                <div class="form-group" style="margin-bottom: 0;">
                    <label style="color: #92400e;">Status Pembayaran</label>
                    <select name="payment_status" class="form-control" style="border-color: #fbbf24;">
                        <option value="Menunggu Pembayaran" <?= ($data['payment_status']=='Menunggu Pembayaran')?'selected':'';?>>Menunggu Pembayaran</option>
                        <option value="Confirmed DP" <?= ($data['payment_status']=='Confirmed DP')?'selected':'';?>>Confirmed DP</option>
                        <option value="Confirmed Full" <?= ($data['payment_status']=='Confirmed Full')?'selected':'';?>>Confirmed Full (Lunas)</option>
                        <option value="Menunggu Verifikasi Pelunasan" <?= ($data['payment_status']=='Menunggu Verifikasi Pelunasan')?'selected':'';?>>Menunggu Verifikasi Pelunasan</option>
                    </select>
                </div>
                <div class="form-group" style="margin-bottom: 0;">
                    <label style="color: #92400e;">Status Acara</label>
                    <select name="event_status" class="form-control" style="border-color: #fbbf24;">
                        <option value="Pending" <?= ($data['status']=='Pending')?'selected':'';?>>Pending</option>
                        <option value="Menunggu Jadwal TM" <?= ($data['status']=='Menunggu Jadwal TM')?'selected':'';?>>Menunggu Jadwal TM</option>
                        <option value="TM Terjadwal" <?= ($data['status']=='TM Terjadwal')?'selected':'';?>>TM Terjadwal</option>
                        <option value="TM Selesai" <?= ($data['status']=='TM Selesai')?'selected':'';?>>TM Selesai</option>
                        <option value="Selesai" <?= ($data['status']=='Selesai')?'selected':'';?>>Selesai (Acara Selesai)</option>
                        <option value="Cancelled" <?= ($data['status']=='Cancelled')?'selected':'';?>>Cancelled</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Section 1: Data Klien -->
        <div class="section-card">
            <div class="section-card-header">
                <div class="section-icon"><i class="ri-user-3-line"></i></div>
                <div class="section-title">Data Klien</div>
            </div>
            
            <div class="form-group">
                <label>Nama Lengkap</label>
                <input type="text" name="client_name" value="<?= htmlspecialchars($data['client_name']); ?>" class="form-control" required>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="client_email" value="<?= htmlspecialchars($data['client_email']); ?>" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>No. WhatsApp</label>
                    <input type="tel" name="client_phone" value="<?= htmlspecialchars($data['client_phone']); ?>" class="form-control" required>
                </div>
            </div>
        </div>

        <!-- Section 2: Detail Acara -->
        <div class="section-card">
            <div class="section-card-header">
                <div class="section-icon"><i class="ri-calendar-line"></i></div>
                <div class="section-title">Detail Acara</div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label>Nama Acara</label>
                    <input type="text" name="event_name" value="<?= htmlspecialchars($data['event_name']); ?>" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Tanggal Acara</label>
                    <input type="date" name="event_date" value="<?= $data['event_date']; ?>" class="form-control" required>
                </div>
            </div>
            
            <div class="form-group">
                <label>Lokasi / Venue</label>
                <textarea name="event_venue" class="form-control" required><?= htmlspecialchars($data['event_venue']); ?></textarea>
            </div>
            
            <div class="form-group">
                <label>Catatan Internal</label>
                <textarea name="notes" class="form-control"><?= htmlspecialchars($data['notes']); ?></textarea>
            </div>

            <!-- TM Section (Opsional Edit) -->
            <div class="tm-section">
                <div class="tm-title">
                    <i class="ri-time-line"></i>
                    Informasi Technical Meeting (Opsional)
                </div>
                <div class="tm-grid">
                    <div class="form-group" style="margin-bottom: 0;">
                        <label style="color: #166534; font-size: 0.85rem;">Tanggal TM</label>
                        <input type="date" name="tm_date" value="<?= $data['tm_date'] ?? ''; ?>" class="form-control" style="border-color: #86efac;">
                    </div>
                    <div class="form-group" style="margin-bottom: 0;">
                        <label style="color: #166534; font-size: 0.85rem;">Jam TM</label>
                        <input type="time" name="tm_time" value="<?= $data['tm_time'] ?? ''; ?>" class="form-control" style="border-color: #86efac;">
                    </div>
                    <div class="form-group" style="margin-bottom: 0;">
                        <label style="color: #166534; font-size: 0.85rem;">Lokasi TM</label>
                        <input type="text" name="tm_location" value="<?= htmlspecialchars($data['tm_location'] ?? ''); ?>" class="form-control" placeholder="Zoom / Cafe / Office..." style="border-color: #86efac;">
                    </div>
                </div>
            </div>
        </div>

        <button type="submit" name="update" class="btn-save">
            <i class="ri-save-3-fill"></i>
            Simpan Semua Perubahan
        </button>

    </form>
</div>

</div>
<?php include 'includes/footer.php'; ?>
