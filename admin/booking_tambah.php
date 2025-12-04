<?php
require_once '../config/database.php';
include 'includes/header.php';
include 'includes/sidebar.php';

// Ambil data jenis acara dari database (dynamic)
$types_array = [];
$q_types = mysqli_query($koneksi, "SELECT DISTINCT type FROM packages ORDER BY type ASC");
while($t = mysqli_fetch_assoc($q_types)){
    $types_array[] = $t['type'];
}

// Ambil data paket lengkap untuk JavaScript
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
    $event_venue = mysqli_real_escape_string($koneksi, $_POST['event_venue']);
    $package_id = $_POST['package_id'];
    $notes = mysqli_real_escape_string($koneksi, $_POST['notes']);
    
    // Logika Pembayaran & Status
    $payment_option_input = $_POST['payment_option'];
    $payment_status_input = $_POST['payment_status'];
    $event_status_input = mysqli_real_escape_string($koneksi, $_POST['event_status']);
    
    // Ambil harga paket
    $cek_paket = mysqli_query($koneksi, "SELECT price FROM packages WHERE id = '$package_id'");
    $data_paket = mysqli_fetch_assoc($cek_paket);
    $total_price = $data_paket['price'];
    
    // Tentukan DP Amount
    if($payment_option_input == 'Down Payment'){
        $down_payment = $_POST['dp_amount'] ? $_POST['dp_amount'] : ($total_price * 0.1);
    } else {
        $down_payment = $total_price;
    }

    $booking_code = "BOOK-" . date('ymd') . rand(100, 999);
    
    // Set waktu verifikasi jika admin langsung konfirmasi
    $admin_verified = ($payment_status_input != 'Menunggu Pembayaran') ? date('Y-m-d H:i:s') : NULL;

    $query = "INSERT INTO bookings (
                booking_code, client_name, client_email, client_phone, 
                event_type, event_name, event_date, event_venue, 
                package_id, total_price, down_payment_amount, payment_option,
                status, payment_status, notes, admin_verified_at
              ) VALUES (
                '$booking_code', '$client_name', '$client_email', '$client_phone',
                '$event_type', '$event_name', '$event_date', '$event_venue',
                '$package_id', '$total_price', '$down_payment', '$payment_option_input',
                '$event_status_input', '$payment_status_input', '$notes', ".($admin_verified ? "'$admin_verified'" : "NULL")."
              )";

    if (mysqli_query($koneksi, $query)) {
        echo "<script>alert('✓ Booking berhasil ditambahkan!'); window.location='booking.php';</script>";
    } else {
        echo "<script>alert('✗ Gagal: ".mysqli_error($koneksi)."');</script>";
    }
}
?>

<style>
    .booking-card {
        background: white; border-radius: 20px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.08); 
        padding: 40px; max-width: 1000px; margin: 0 auto;
        border-top: 6px solid #2563eb;
    }
    
    .form-header { 
        text-align: center; margin-bottom: 40px; 
        padding-bottom: 20px; border-bottom: 2px solid #f1f5f9;
    }
    .form-header h2 { 
        font-size: 2rem; color: #0f172a; font-weight: 800; 
        margin-bottom: 8px; display: flex; align-items: center; 
        justify-content: center; gap: 15px;
    }
    .form-header p { color: #64748b; font-size: 1rem; }

    /* Section Box */
    .section-box { 
        background: #f8fafc; padding: 30px; border-radius: 15px; 
        margin-bottom: 25px; border: 1px solid #e2e8f0;
    }
    .section-title { 
        display: flex; align-items: center; gap: 12px; 
        font-size: 1.2rem; font-weight: 700; color: #1e293b; 
        margin-bottom: 25px; padding-bottom: 15px; 
        border-bottom: 2px solid #e2e8f0;
    }
    .icon-box-form { 
        width: 45px; height: 45px; background: #2563eb; 
        color: white; border-radius: 12px; display: flex; 
        align-items: center; justify-content: center; 
        font-size: 1.4rem; box-shadow: 0 4px 12px rgba(37,99,235,0.3);
    }

    /* Grid System */
    .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
    .grid-3 { display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px; }

    /* Form Control */
    .form-group { margin-bottom: 20px; }
    .form-group label { 
        display: block; margin-bottom: 8px; font-weight: 600; 
        color: #334155; font-size: 0.9rem;
        display: flex; align-items: center; gap: 8px;
    }
    .label-required::after {
        content: '*'; color: #ef4444; margin-left: 4px; font-weight: bold;
    }
    
    .form-control {
        width: 100%; padding: 12px 15px;
        border: 2px solid #e2e8f0; border-radius: 10px;
        font-size: 0.95rem; color: #334155; transition: 0.3s;
        background: white; font-family: 'Poppins', sans-serif;
    }
    .form-control:focus {
        border-color: #2563eb; background: #eff6ff; outline: none;
        box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
    }
    textarea.form-control { resize: vertical; min-height: 100px; }

    /* Status Section */
    .status-grid {
        display: grid; grid-template-columns: 1fr 1fr; gap: 20px;
    }
    .status-card {
        background: white; padding: 20px; border-radius: 12px;
        border: 2px solid #e2e8f0; position: relative; overflow: hidden;
    }
    .status-card::before {
        content: ''; position: absolute; top: 0; left: 0;
        width: 4px; height: 100%; background: #2563eb;
    }
    .status-card.payment::before { background: #f59e0b; }
    .status-card-title {
        font-size: 0.75rem; color: #64748b; font-weight: 600;
        text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 10px;
    }

    /* Price Display */
    .price-display {
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
        color: white; padding: 25px; border-radius: 15px;
        text-align: center; margin-top: 20px; position: relative;
        overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.2);
    }
    .price-display::before {
        content: ''; position: absolute; top: -50%; right: -50%;
        width: 200%; height: 200%;
        background: radial-gradient(circle, rgba(37,99,235,0.3) 0%, transparent 60%);
    }
    .price-label { 
        font-size: 0.9rem; opacity: 0.8; margin-bottom: 8px; 
        position: relative; z-index: 1;
    }
    .price-value { 
        font-size: 2.5rem; font-weight: 800; color: #fbbf24; 
        position: relative; z-index: 1;
    }

    /* DP Box */
    .dp-box {
        background: #fffbeb; border: 2px dashed #fbbf24;
        padding: 20px; border-radius: 12px; margin-top: 15px;
        display: none; animation: slideDown 0.3s ease;
    }
    @keyframes slideDown { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }

    /* Submit Button */
    .btn-submit {
        width: 100%; background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
        color: white; padding: 18px; border: none; border-radius: 12px;
        font-weight: 700; font-size: 1.1rem; cursor: pointer;
        transition: 0.3s; margin-top: 30px;
        display: flex; align-items: center; justify-content: center; gap: 12px;
        box-shadow: 0 10px 25px rgba(37,99,235,0.4);
        text-transform: uppercase; letter-spacing: 1px;
    }
    .btn-submit:hover {
        transform: translateY(-3px);
        box-shadow: 0 15px 35px rgba(37,99,235,0.5);
    }
    .btn-submit:active { transform: scale(0.98); }

    /* Info Alert */
    .info-alert {
        background: #dbeafe; border-left: 4px solid #2563eb;
        padding: 15px 20px; border-radius: 8px; margin-bottom: 20px;
        display: flex; align-items: start; gap: 12px;
    }
    .info-alert i { font-size: 1.5rem; color: #2563eb; }
    .info-alert-content { flex: 1; }
    .info-alert-title { 
        font-weight: 700; color: #1e40af; margin-bottom: 5px; 
        font-size: 0.9rem;
    }
    .info-alert-text { color: #1e40af; font-size: 0.85rem; line-height: 1.6; }

    @media (max-width: 768px) { 
        .grid-2, .grid-3, .status-grid { grid-template-columns: 1fr; }
        .booking-card { padding: 25px; }
    }
</style>

<div class="main-content">
    
    <div style="margin-bottom: 20px;">
        <a href="booking.php" style="color: #64748b; font-weight: 600; display: flex; align-items: center; gap: 8px; text-decoration: none; transition: 0.3s;">
            <i class="ri-arrow-left-line"></i> Kembali ke Daftar Booking
        </a>
    </div>

    <div class="booking-card">
        <div class="form-header">
            <h2>
                <i class="ri-calendar-check-line" style="color: #2563eb;"></i>
                Tambah Booking Baru
            </h2>
            <p>Formulir input booking untuk vendor atau manual entry oleh admin</p>
        </div>

        <div class="info-alert">
            <i class="ri-information-line"></i>
            <div class="info-alert-content">
                <div class="info-alert-title">💡 Panduan Pengisian</div>
                <div class="info-alert-text">
                    • Isi data klien dan detail acara dengan lengkap<br>
                    • Tentukan status pembayaran & acara sesuai kondisi real<br>
                    • Jika customer sudah bayar, pilih "Confirmed DP/Full" dan status acara "Menunggu Jadwal TM"
                </div>
            </div>
        </div>

        <form action="" method="POST">
            
            <!-- SECTION 1: DATA KLIEN -->
            <div class="section-box">
                <div class="section-title">
                    <div class="icon-box-form"><i class="ri-user-star-line"></i></div>
                    1. Data Klien / PIC
                </div>
                
                <div class="form-group">
                    <label class="label-required">Nama Lengkap (Penanggung Jawab)</label>
                    <input type="text" name="client_name" class="form-control" required placeholder="Contoh: Budi Santoso">
                </div>

                <div class="grid-2">
                    <div class="form-group">
                        <label class="label-required">Email Address</label>
                        <input type="email" name="client_email" class="form-control" required placeholder="email@vendor.com">
                    </div>
                    <div class="form-group">
                        <label class="label-required">No. WhatsApp</label>
                        <input type="tel" name="client_phone" class="form-control" required placeholder="628123456789">
                    </div>
                </div>
            </div>

            <!-- SECTION 2: DETAIL ACARA -->
            <div class="section-box">
                <div class="section-title">
                    <div class="icon-box-form"><i class="ri-calendar-event-line"></i></div>
                    2. Detail Acara
                </div>

                <div class="grid-2">
                    <div class="form-group">
                        <label class="label-required">Jenis Acara</label>
                        <select name="event_type" id="eventType" class="form-control" required onchange="updateFormUI()">
                            <option value="">-- Pilih Kategori --</option>
                            <?php foreach($types_array as $type): ?>
                                <option value="<?= $type; ?>"><?= $type; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="label-required" id="eventNameLabel">Nama Acara</label>
                        <input type="text" name="event_name" id="eventNameInput" class="form-control" required placeholder="Nama acara...">
                    </div>
                </div>

                <div class="grid-2">
                    <div class="form-group">
                        <label class="label-required">Tanggal Acara</label>
                        <input type="date" name="event_date" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="label-required">Pilih Paket Layanan</label>
                        <select name="package_id" id="packageSelect" class="form-control" required onchange="calculatePrice()" disabled>
                            <option value="">-- Pilih Jenis Acara Dulu --</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="label-required">Lokasi Acara (Venue)</label>
                    <textarea name="event_venue" class="form-control" required placeholder="Nama Gedung / Hotel / Alamat Lengkap"></textarea>
                </div>

                <div class="form-group">
                    <label><i class="ri-sticky-note-line"></i> Catatan Internal (Opsional)</label>
                    <textarea name="notes" class="form-control" rows="3" placeholder="Catatan khusus admin atau request customer..."></textarea>
                </div>
            </div>

            <!-- SECTION 3: STATUS PEMBAYARAN & ACARA -->
            <div class="section-box">
                <div class="section-title">
                    <div class="icon-box-form"><i class="ri-settings-3-line"></i></div>
                    3. Atur Status Awal
                </div>

                <div class="status-grid">
                    <!-- Status Pembayaran -->
                    <div class="status-card payment">
                        <div class="status-card-title">
                            <i class="ri-wallet-3-line"></i> Status Pembayaran
                        </div>
                        <div class="form-group" style="margin-bottom: 15px;">
                            <label>Opsi Pembayaran</label>
                            <select name="payment_option" id="paymentOption" class="form-control" onchange="handlePaymentOption()">
                                <option value="Full Payment">Full Payment (Lunas 100%)</option>
                                <option value="Down Payment">Down Payment (DP)</option>
                            </select>
                        </div>
                        <div class="form-group" style="margin-bottom: 0;">
                            <label>Status Pembayaran</label>
                            <select name="payment_status" class="form-control">
                                <option value="Menunggu Pembayaran">Menunggu Pembayaran</option>
                                <option value="Confirmed DP">Confirmed DP (Sudah Bayar DP)</option>
                                <option value="Confirmed Full">Confirmed Full (Sudah Lunas)</option>
                            </select>
                        </div>
                        
                        <div id="dpContainer" class="dp-box">
                            <label style="color: #92400e; font-weight: 700; display: block; margin-bottom: 8px;">
                                <i class="ri-money-dollar-circle-line"></i> Nominal DP (Min 10%)
                            </label>
                            <input type="number" name="dp_amount" id="dpAmount" class="form-control" placeholder="0" style="border-color: #fbbf24;">
                            <small id="minDpLabel" style="font-weight: 600; color: #92400e; display: block; margin-top: 8px;">Min: Rp 0</small>
                        </div>
                    </div>

                    <!-- Status Acara -->
                    <div class="status-card">
                        <div class="status-card-title">
                            <i class="ri-flag-2-line"></i> Status Acara
                        </div>
                        <div class="form-group" style="margin-bottom: 0;">
                            <label>Status Workflow</label>
                            <select name="event_status" class="form-control">
                                <option value="Pending">Pending (Belum Ada Aksi)</option>
                                <option value="Menunggu Jadwal TM">Menunggu Jadwal TM</option>
                                <option value="TM Terjadwal">TM Terjadwal</option>
                                <option value="TM Selesai">TM Selesai</option>
                                <option value="Selesai">Selesai (Acara Selesai)</option>
                            </select>
                        </div>
                        <div style="background: #fef3c7; padding: 15px; border-radius: 8px; margin-top: 15px; border-left: 4px solid #f59e0b;">
                            <small style="color: #78350f; font-size: 0.8rem; line-height: 1.6;">
                                <strong>💡 Tips:</strong><br>
                                • Pilih "Pending" jika customer belum bayar<br>
                                • Pilih "Menunggu Jadwal TM" jika sudah bayar
                            </small>
                        </div>
                    </div>
                </div>

                <!-- Display Harga -->
                <div class="price-display">
                    <div class="price-label">Total Nilai Paket</div>
                    <div class="price-value" id="displayPrice">Rp 0</div>
                </div>
            </div>

            <button type="submit" name="simpan" class="btn-submit">
                <i class="ri-save-3-fill"></i> Simpan Data Booking
            </button>

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
    const paymentOption = document.getElementById('paymentOption');
    const dpContainer = document.getElementById('dpContainer');
    const dpAmount = document.getElementById('dpAmount');
    const minDpLabel = document.getElementById('minDpLabel');
    
    let currentPrice = 0;

    function formatRupiah(angka) {
        return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(angka);
    }

    function updateFormUI() {
        const type = eventType.value;
        
        // Ubah Label
        if (type === 'Wedding') {
            eventNameLabel.innerHTML = '<i class="ri-heart-line"></i> Nama Kedua Mempelai <span style="color:#ef4444;">*</span>';
            eventNameInput.placeholder = "Contoh: Romeo & Juliet";
        } else if (type === 'Corporate') {
            eventNameLabel.innerHTML = '<i class="ri-building-line"></i> Nama Perusahaan <span style="color:#ef4444;">*</span>';
            eventNameInput.placeholder = "Contoh: PT. Maju Mundur";
        } else if (type === 'Event') {
            eventNameLabel.innerHTML = '<i class="ri-calendar-event-line"></i> Nama Event <span style="color:#ef4444;">*</span>';
            eventNameInput.placeholder = "Contoh: Festival Musik 2025";
        } else {
            eventNameLabel.innerHTML = 'Nama Acara <span style="color:#ef4444;">*</span>';
            eventNameInput.placeholder = "Nama Acara...";
        }

        // Filter Paket
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
            option.text = "Tidak ada paket tersedia";
            packageSelect.appendChild(option);
        }
        
        currentPrice = 0;
        displayPrice.innerText = "Rp 0";
        handlePaymentOption();
    }

    function calculatePrice() {
        const selectedOption = packageSelect.options[packageSelect.selectedIndex];
        if (selectedOption.dataset.price) {
            currentPrice = parseInt(selectedOption.dataset.price);
            displayPrice.innerText = formatRupiah(currentPrice);
        } else {
            currentPrice = 0;
            displayPrice.innerText = "Rp 0";
        }
        handlePaymentOption();
    }

    function handlePaymentOption() {
        if (paymentOption.value === 'Down Payment' && currentPrice > 0) {
            dpContainer.style.display = 'block';
            const minDp = Math.round(currentPrice * 0.10);
            dpAmount.value = minDp;
            dpAmount.min = minDp;
            minDpLabel.innerText = "Minimal: " + formatRupiah(minDp);
        } else {
            dpContainer.style.display = 'none';
        }
    }
</script>

<?php include 'includes/footer.php'; ?>