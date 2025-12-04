<?php
// 1. Panggil Struktur Admin
require_once '../config/database.php';
include 'includes/header.php';
include 'includes/sidebar.php';

// --- AMBIL DATA JENIS ACARA DARI DATABASE (DYNAMIC) ---
// Kita ambil jenis kategori unik yang ada di tabel packages
$types_array = [];
$q_types = mysqli_query($koneksi, "SELECT DISTINCT type FROM packages ORDER BY type ASC");
while($t = mysqli_fetch_assoc($q_types)){
    $types_array[] = $t['type'];
}

// --- AMBIL DATA PAKET LENGKAP UNTUK JAVASCRIPT ---
$paket_array = [];
$q_paket = mysqli_query($koneksi, "SELECT * FROM packages WHERE is_active = 1 ORDER BY price ASC");
while($p = mysqli_fetch_assoc($q_paket)){
    $paket_array[] = $p;
}

// PROSES SIMPAN DATA
if (isset($_POST['simpan'])) {
    $client_name = mysqli_real_escape_string($koneksi, $_POST['client_name']);
    $client_email = mysqli_real_escape_string($koneksi, $_POST['client_email']);
    $client_phone = mysqli_real_escape_string($koneksi, $_POST['client_phone']);
    
    $event_type = mysqli_real_escape_string($koneksi, $_POST['event_type']);
    $event_name = mysqli_real_escape_string($koneksi, $_POST['event_name']);
    $event_date = mysqli_real_escape_string($koneksi, $_POST['event_date']);
    $event_location = mysqli_real_escape_string($koneksi, $_POST['event_location']);
    $package_id = $_POST['package_id'];
    $notes = mysqli_real_escape_string($koneksi, $_POST['notes']);
    
    // Logika Pembayaran Khusus Admin
    $payment_option_input = $_POST['payment_option']; // 'Full' atau 'Waiting'
    
    // Ambil harga paket
    $cek_paket = mysqli_query($koneksi, "SELECT price FROM packages WHERE id = '$package_id'");
    $data_paket = mysqli_fetch_assoc($cek_paket);
    $total_price = $data_paket['price'];
    
    // Tentukan Status
    if($payment_option_input == 'Waiting'){
        $payment_status = 'Unpaid';
        $payment_option = 'Waiting Payment';
        $down_payment = 0;
    } else {
        $payment_status = 'Paid';
        $payment_option = 'Full Payment';
        $down_payment = $total_price;
    }

    $booking_code = "BOOK-" . date('ymd') . rand(100, 999);

    $query = "INSERT INTO bookings (
                booking_code, client_name, client_email, client_phone, 
                event_type, event_name, event_date, event_venue, 
                package_id, total_price, down_payment_amount, payment_option,
                status, payment_status, notes
              ) VALUES (
                '$booking_code', '$client_name', '$client_email', '$client_phone',
                '$event_type', '$event_name', '$event_date', '$event_location',
                '$package_id', '$total_price', '$down_payment', '$payment_option',
                'Confirmed', '$payment_status', '$notes'
              )";

    if (mysqli_query($koneksi, $query)) {
        echo "<script>alert('Booking Vendor berhasil ditambahkan!'); window.location='booking.php';</script>";
    } else {
        echo "<script>alert('Gagal: ".mysqli_error($koneksi)."');</script>";
    }
}
?>

<style>
    .booking-card {
        background: white; border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.03); padding: 40px;
        border-top: 5px solid #2563eb; max-width: 900px; margin: 0 auto;
    }
    .form-header { text-align: center; margin-bottom: 30px; }
    .form-header h2 { font-size: 1.8rem; color: #1e293b; font-weight: 700; margin-bottom: 5px; }
    .section-box { margin-bottom: 30px; padding-bottom: 30px; border-bottom: 1px dashed #e2e8f0; }
    .section-box:last-child { border-bottom: none; }
    .section-title { display: flex; align-items: center; gap: 12px; font-size: 1.1rem; font-weight: 600; color: #0f172a; margin-bottom: 20px; }
    .icon-box-form { width: 35px; height: 35px; background: #eff6ff; color: #2563eb; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; }
    .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
    .form-control { width: 100%; padding: 12px 15px; border: 2px solid #e2e8f0; border-radius: 10px; font-size: 0.95rem; color: #334155; transition: 0.3s; background: #f8fafc; }
    .form-control:focus { border-color: #2563eb; background: white; outline: none; box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1); }
    .price-card { background: #0f172a; color: white; padding: 20px; border-radius: 12px; text-align: right; margin-top: 20px; position: relative; overflow: hidden; }
    .price-card::before { content: ''; position: absolute; top: -50%; left: -50%; width: 200%; height: 200%; background: radial-gradient(circle, rgba(255,255,255,0.05) 0%, transparent 60%); }
    .price-total { font-size: 1.8rem; font-weight: 700; color: #fbbf24; }
    .btn-submit { width: 100%; background: #2563eb; color: white; padding: 15px; border: none; border-radius: 10px; font-weight: 600; font-size: 1rem; cursor: pointer; transition: 0.3s; margin-top: 20px; display: flex; align-items: center; justify-content: center; gap: 10px; }
    .btn-submit:hover { background: #1d4ed8; transform: translateY(-2px); }
    @media (max-width: 768px) { .grid-2 { grid-template-columns: 1fr; } }
</style>

<div class="main-content">
    <div style="margin-bottom: 20px;">
        <a href="booking.php" style="color: #64748b; font-weight: 500; display: flex; align-items: center; gap: 5px;">
            <i class="ri-arrow-left-line"></i> Kembali ke Daftar
        </a>
    </div>

    <div class="booking-card">
        <div class="form-header">
            <h2>Input Booking Vendor / Manual</h2>
            <p>Formulir khusus admin untuk input jadwal kerja sama atau manual.</p>
        </div>

        <form action="" method="POST">
            
            <div class="section-box">
                <div class="section-title"><div class="icon-box-form"><i class="ri-user-star-line"></i></div> Data Klien / Vendor</div>
                <div class="form-group"><label>Nama PIC / Klien</label><input type="text" name="client_name" class="form-control" required placeholder="Nama Penanggung Jawab"></div>
                <div class="grid-2">
                    <div class="form-group"><label>Email</label><input type="email" name="client_email" class="form-control" placeholder="email@vendor.com"></div>
                    <div class="form-group"><label>No. WhatsApp</label><input type="number" name="client_phone" class="form-control" required placeholder="0812xxxx"></div>
                </div>
            </div>

            <div class="section-box">
                <div class="section-title"><div class="icon-box-form"><i class="ri-calendar-event-line"></i></div> Detail Acara</div>

                <div class="form-group">
                    <label>Jenis Acara</label>
                    <select name="event_type" id="eventType" class="form-control" required onchange="updateFormUI()">
                        <option value="">-- Pilih Jenis Acara --</option>
                        
                        <?php foreach($types_array as $type): ?>
                            <option value="<?= $type; ?>"><?= $type; ?></option>
                        <?php endforeach; ?>

                    </select>
                </div>

                <div class="grid-2">
                    <div class="form-group"><label id="eventNameLabel">Nama Acara</label><input type="text" name="event_name" id="eventNameInput" class="form-control" required placeholder="Nama acara..."></div>
                    <div class="form-group"><label>Tanggal Acara</label><input type="date" name="event_date" class="form-control" required></div>
                </div>

                <div class="form-group"><label>Lokasi Acara (Venue)</label><textarea name="event_location" rows="2" class="form-control" required placeholder="Nama Gedung / Hotel / Alamat"></textarea></div>

                <div class="form-group">
                    <label>Pilih Paket</label>
                    <select name="package_id" id="packageSelect" class="form-control" required onchange="calculatePrice()" disabled>
                        <option value="">-- Pilih Jenis Acara Dulu --</option>
                    </select>
                </div>

                <div class="form-group"><label>Catatan Internal</label><textarea name="notes" rows="2" class="form-control" placeholder="Catatan khusus admin..."></textarea></div>
            </div>

            <div class="section-box" style="border-bottom: none;">
                <div class="section-title"><div class="icon-box-form"><i class="ri-wallet-3-line"></i></div> Status Pembayaran</div>
                <div class="form-group">
                    <label>Opsi Pembayaran</label>
                    <select name="payment_option" class="form-control" required>
                        <option value="Waiting">Waiting Payment (Bayar Setelah Acara)</option>
                        <option value="Full">Full Payment (Sudah Lunas)</option>
                    </select>
                    <small style="color: #64748b;">*Pilih 'Waiting Payment' jika tagihan dibayar nanti (Invoice).</small>
                </div>
                <div class="price-card">
                    <span style="font-size: 0.9rem; opacity: 0.8;">Nilai Proyek / Paket</span>
                    <div class="price-total" id="displayPrice">Rp 0</div>
                </div>
            </div>

            <button type="submit" name="simpan" class="btn-submit"><i class="ri-save-line"></i> Simpan Data Booking</button>
        </form>
    </div>
</div>

<script>
    const packages = <?php echo json_encode($paket_array); ?>;
    
    const eventType = document.getElementById('eventType');
    const eventNameLabel = document.getElementById('eventNameLabel');
    const eventNameInput = document.getElementById('eventNameInput');
    const packageSelect = document.getElementById('packageSelect');
    const displayPrice = document.getElementById('displayPrice');
    
    function updateFormUI() {
        const type = eventType.value;
        
        // Ubah Label Sesuai Tipe yang Dikenali
        if (type === 'Wedding') {
            eventNameLabel.innerHTML = "Nama Kedua Mempelai";
            eventNameInput.placeholder = "Contoh: Romeo & Juliet";
        } else if (type === 'Corporate') {
            eventNameLabel.innerHTML = "Nama Perusahaan";
            eventNameInput.placeholder = "Contoh: PT. Maju Mundur";
        } else if (type === 'Event') {
            eventNameLabel.innerHTML = "Nama Event";
            eventNameInput.placeholder = "Contoh: Pensi Sekolah";
        } else {
            // Default untuk 'Lainnya' atau kategori baru yang ditambahkan admin
            eventNameLabel.innerHTML = "Nama Acara / Kegiatan";
            eventNameInput.placeholder = "Nama Acara...";
        }

        // Filter Paket Dinamis
        packageSelect.innerHTML = '<option value="">-- Pilih Paket --</option>';
        packageSelect.disabled = false;

        const filteredPackages = packages.filter(p => p.type === type);
        
        if (filteredPackages.length > 0) {
            filteredPackages.forEach(pkg => {
                const priceFormatted = new Intl.NumberFormat('id-ID').format(pkg.price);
                const option = document.createElement('option');
                option.value = pkg.id;
                option.text = `${pkg.name} - Rp ${priceFormatted}`;
                option.dataset.price = pkg.price;
                packageSelect.appendChild(option);
            });
        } else {
            const option = document.createElement('option');
            option.text = "Tidak ada paket tersedia untuk kategori ini";
            packageSelect.appendChild(option);
        }
        
        displayPrice.innerText = "Rp 0";
    }

    function calculatePrice() {
        const selectedOption = packageSelect.options[packageSelect.selectedIndex];
        if (selectedOption.dataset.price) {
            const price = parseInt(selectedOption.dataset.price);
            displayPrice.innerText = "Rp " + new Intl.NumberFormat('id-ID').format(price);
        } else {
            displayPrice.innerText = "Rp 0";
        }
    }
</script>

<?php include 'includes/footer.php'; ?>