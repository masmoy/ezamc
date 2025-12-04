<?php
require_once '../config/database.php';
include 'includes/header.php';
include 'includes/sidebar.php';

$id = $_GET['id'];
$query = mysqli_query($koneksi, "SELECT * FROM bookings WHERE id = '$id'");
$data = mysqli_fetch_assoc($query);

// PROSES UPDATE MANUAL
if (isset($_POST['update'])) {
    $client_name = $_POST['client_name'];
    $event_name = $_POST['event_name'];
    $event_date = $_POST['event_date'];
    $event_venue = $_POST['event_venue'];
    $status = $_POST['status']; // Edit status manual jika perlu
    $payment_status = $_POST['payment_status'];

    $q = "UPDATE bookings SET 
          client_name='$client_name', event_name='$event_name', 
          event_date='$event_date', event_venue='$event_venue', 
          status='$status', payment_status='$payment_status' 
          WHERE id='$id'";
          
    if(mysqli_query($koneksi, $q)){
        echo "<script>alert('Perubahan disimpan!'); window.location='booking.php';</script>";
    }
}
?>

<div class="main-content">
    <div class="header-dash">
        <h2>Edit Booking</h2>
        <a href="booking.php" class="btn-primary">Kembali</a>
    </div>

    <div class="card" style="max-width:800px;">
        <form method="POST">
            
            <div style="background:#fdf2f8; padding:20px; border-radius:10px; margin-bottom:20px; border:1px solid #fbcfe8;">
                <h4 style="margin-bottom:15px; color:#be185d;">Manual Override Status</h4>
                <div class="form-group">
                    <label>Status Workflow</label>
                    <select name="status" class="form-control">
                        <option value="Menunggu Konfirmasi" <?= ($data['status']=='Menunggu Konfirmasi')?'selected':'';?>>Menunggu Konfirmasi</option>
                        <option value="Menunggu Jadwal TM" <?= ($data['status']=='Menunggu Jadwal TM')?'selected':'';?>>Menunggu Jadwal TM</option>
                        <option value="TM Terjadwal" <?= ($data['status']=='TM Terjadwal')?'selected':'';?>>TM Terjadwal</option>
                        <option value="Menunggu Acara" <?= ($data['status']=='Menunggu Acara')?'selected':'';?>>Menunggu Acara</option>
                        <option value="Acara Selesai" <?= ($data['status']=='Acara Selesai')?'selected':'';?>>Acara Selesai</option>
                        <option value="Cancelled" <?= ($data['status']=='Cancelled')?'selected':'';?>>Cancelled</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Pembayaran</label>
                    <select name="payment_status" class="form-control">
                        <option value="Unpaid" <?= ($data['payment_status']=='Unpaid')?'selected':'';?>>Unpaid</option>
                        <option value="Down Payment" <?= ($data['payment_status']=='Down Payment')?'selected':'';?>>Down Payment</option>
                        <option value="Paid" <?= ($data['payment_status']=='Paid')?'selected':'';?>>Paid (Lunas)</option>
                    </select>
                </div>
            </div>

            <div class="form-group"><label>Nama Klien</label><input type="text" name="client_name" value="<?= $data['client_name']; ?>" class="form-control"></div>
            <div class="form-group"><label>Nama Acara</label><input type="text" name="event_name" value="<?= $data['event_name']; ?>" class="form-control"></div>
            <div class="form-group"><label>Tanggal</label><input type="date" name="event_date" value="<?= $data['event_date']; ?>" class="form-control"></div>
            <div class="form-group"><label>Venue</label><textarea name="event_venue" class="form-control"><?= $data['event_venue']; ?></textarea></div>

            <button type="submit" name="update" class="btn-primary" style="width:100%;">Simpan Perubahan Manual</button>
        </form>
    </div>
</div>
<?php include 'includes/footer.php'; ?>