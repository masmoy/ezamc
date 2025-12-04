<?php
require_once 'config/database.php';

// 1. Ambil Setting Website
$query_setting = mysqli_query($koneksi, "SELECT * FROM settings WHERE id = 1");
$setting = mysqli_fetch_assoc($query_setting);

// 2. Ambil Data Paket Aktif untuk JavaScript
$paket_array = [];
$q_paket = mysqli_query($koneksi, "SELECT * FROM packages WHERE is_active = 1 ORDER BY price ASC");
while($p = mysqli_fetch_assoc($q_paket)){
    $paket_array[] = $p;
}

// 3. Tangkap Data Kiriman dari Halaman Lain (Jadwal/Pricelist)
$pre_date = isset($_GET['date']) ? $_GET['date'] : '';
$pre_package_id = isset($_GET['package_id']) ? $_GET['package_id'] : '';
$pre_event_type = ''; 

// Jika ada paket dipilih, cari tahu jenis acaranya
if($pre_package_id) {
    $q_pre = mysqli_query($koneksi, "SELECT type FROM packages WHERE id = '$pre_package_id'");
    $d_pre = mysqli_fetch_assoc($q_pre);
    if($d_pre) { $pre_event_type = $d_pre['type']; }
}

// --- PROSES SIMPAN BOOKING (BACKEND) ---
if (isset($_POST['booking'])) {
    $client_name = mysqli_real_escape_string($koneksi, $_POST['client_name']);
    $client_email = mysqli_real_escape_string($koneksi, $_POST['client_email']);
    $client_phone = mysqli_real_escape_string($koneksi, $_POST['client_phone']);
    $event_type = mysqli_real_escape_string($koneksi, $_POST['event_type']);
    $event_name = mysqli_real_escape_string($koneksi, $_POST['event_name']);
    $event_date = mysqli_real_escape_string($koneksi, $_POST['event_date']);
    $event_venue = mysqli_real_escape_string($koneksi, $_POST['event_venue']);
    $package_id = $_POST['package_id'];
    $notes = mysqli_real_escape_string($koneksi, $_POST['notes']);
    $payment_option = mysqli_real_escape_string($koneksi, $_POST['payment_option']);
    
    // Ambil harga asli
    $cek_paket = mysqli_query($koneksi, "SELECT price FROM packages WHERE id = '$package_id'");
    $data_paket = mysqli_fetch_assoc($cek_paket);
    $total_price = $data_paket['price'];
    
    // Logika DP
    $down_payment = 0;
    if($payment_option == 'Down Payment'){
        $input_dp = (int)$_POST['dp_amount'];
        $min_dp = $total_price * 0.10; 
        if($input_dp < $min_dp){
            echo "<script>alert('Gagal! Nominal DP minimal 10% dari harga paket.'); window.history.back();</script>";
            exit;
        }
        $down_payment = $input_dp;
    } else {
        $down_payment = $total_price; 
    }

    $booking_code = "BOOK-" . date('ymd') . rand(100, 999);
    
    // Status Awal Baru: Menunggu Pembayaran
    $status_awal = 'Menunggu Pembayaran';
    $payment_status_db = 'Unpaid'; 

    $query = "INSERT INTO bookings (
                booking_code, client_name, client_email, client_phone, 
                event_type, event_name, event_date, event_venue, 
                package_id, total_price, down_payment_amount, payment_option,
                status, payment_status, notes
              ) VALUES (
                '$booking_code', '$client_name', '$client_email', '$client_phone',
                '$event_type', '$event_name', '$event_date', '$event_venue',
                '$package_id', '$total_price', '$down_payment', '$payment_option',
                '$status_awal', '$payment_status_db', '$notes'
              )";

    if (mysqli_query($koneksi, $query)) {
        echo "<script>window.location = 'booking_success.php?code=$booking_code';</script>";
    } else {
        echo "<script>alert('Gagal melakukan booking: ".mysqli_error($koneksi)."');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Event - <?= $setting['site_name']; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    
    <style>
        :root {
            --primary: #0f172a;       /* Navy Gelap */
            --accent: #14b8a6;        /* Teal / Hijau Tosca */
            --accent-hover: #0d9488;
            --bg-page: #f8fafc;       /* Abu-abu muda background */
            --text-dark: #334155;
            --text-light: #94a3b8;
            --white: #ffffff;
            --border: #e2e8f0;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }

        body {
            background-color: var(--bg-page);
            color: var(--text-dark);
            min-height: 100vh;
        }

        /* NAVBAR SIMPLE */
        .navbar {
            background: var(--primary);
            padding: 1rem 5%;
            display: flex; justify-content: space-between; align-items: center;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            position: fixed; width: 100%; top: 0; z-index: 100;
        }
        .logo { font-size: 1.4rem; font-weight: 700; color: white; text-decoration: none; letter-spacing: 1px; }
        .nav-link { color: rgba(255,255,255,0.8); text-decoration: none; font-size: 0.9rem; font-weight: 500; transition: 0.3s; margin-left: 20px; }
        .nav-link:hover { color: var(--accent); }

        /* CONTAINER FORM */
        .container {
            max-width: 850px;
            margin: 100px auto 50px;
            padding: 0 20px;
        }

        .booking-card {
            background: var(--white);
            border-radius: 16px;
            box-shadow: 0 20px 50px rgba(0,0,0,0.05);
            padding: 40px;
            border-top: 6px solid var(--accent); /* Aksen warna di atas */
        }

        .form-header { text-align: center; margin-bottom: 40px; }
        .form-header h1 { font-size: 2rem; color: var(--primary); margin-bottom: 10px; font-weight: 700; }
        .form-header p { color: var(--text-light); }

        /* SECTION STYLE */
        .section-box {
            margin-bottom: 35px;
            padding-bottom: 35px;
            border-bottom: 1px dashed var(--border);
        }
        .section-box:last-child { border-bottom: none; }

        .section-title {
            display: flex; align-items: center; gap: 12px;
            font-size: 1.2rem; font-weight: 600; color: var(--primary);
            margin-bottom: 25px;
        }
        .icon-box {
            width: 40px; height: 40px;
            background: #ccfbf1; color: var(--accent);
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.3rem;
        }

        /* GRID SYSTEM */
        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }

        /* INPUT STYLES */
        .form-group { margin-bottom: 20px; position: relative; }
        .form-group label {
            display: block; margin-bottom: 8px; font-weight: 500; color: var(--text-dark); font-size: 0.95rem;
        }
        .form-group small { display: block; margin-top: 5px; color: var(--text-light); font-size: 0.8rem; }

        input, select, textarea {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid var(--border);
            border-radius: 10px;
            font-size: 0.95rem;
            color: var(--primary);
            transition: 0.3s;
            background: #f8fafc;
        }

        input:focus, select:focus, textarea:focus {
            outline: none;
            border-color: var(--accent);
            background: white;
            box-shadow: 0 0 0 4px rgba(20, 184, 166, 0.1);
        }

        /* LOCKED FIELDS (READONLY) */
        input[readonly], select.locked { 
            background-color: #e2e8f0; 
            cursor: not-allowed; 
            border-color: #cbd5e1; 
            color: #64748b; 
            pointer-events: none; /* Mencegah klik */
        }

        /* DP BOX */
        .dp-box {
            background: #fffbeb;
            border: 1px solid #fcd34d;
            padding: 20px;
            border-radius: 10px;
            margin-top: 15px;
            display: none; /* Hidden by default */
            animation: fadeIn 0.5s;
        }
        @keyframes fadeIn { from{ opacity:0; transform:translateY(-10px); } to{ opacity:1; transform:translateY(0); } }
        
        /* PRICE SUMMARY */
        .price-card {
            background: var(--primary);
            color: white;
            padding: 25px;
            border-radius: 12px;
            text-align: right;
            margin-top: 20px;
            position: relative;
            overflow: hidden;
        }
        .price-card::before {
            content: ''; position: absolute; top: -50%; left: -50%; width: 200%; height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.05) 0%, transparent 60%);
        }
        .price-label { font-size: 0.9rem; opacity: 0.8; margin-bottom: 5px; display: block; }
        .price-total { font-size: 2rem; font-weight: 700; color: var(--accent); }

        /* BUTTON */
        .btn-submit {
            display: block; width: 100%; background: var(--accent);
            color: white; font-weight: 700; padding: 16px; border: none;
            border-radius: 10px; font-size: 1.1rem; cursor: pointer;
            margin-top: 30px; transition: 0.3s; text-transform: uppercase; letter-spacing: 1px;
            box-shadow: 0 10px 20px rgba(20, 184, 166, 0.3);
        }
        .btn-submit:hover {
            background: var(--accent-hover); transform: translateY(-3px);
            box-shadow: 0 15px 30px rgba(20, 184, 166, 0.4);
        }

        /* RESPONSIVE */
        @media (max-width: 768px) {
            .grid-2 { grid-template-columns: 1fr; }
            .container { padding: 0 15px; }
            .booking-card { padding: 25px; }
        }
    </style>
</head>
<body>

    <nav class="navbar">
        <a href="index.php" class="logo"><?= $setting['site_name']; ?></a>
        <div>
            <a href="index.php" class="nav-link">Kembali ke Home</a>
        </div>
    </nav>

    <div class="container">
        <div class="booking-card">
            <div class="form-header">
                <h1>Formulir Booking</h1>
                <p>Lengkapi detail acara Anda di bawah ini</p>
            </div>

            <form action="" method="POST" id="bookingForm">
                
                <div class="section-box">
                    <div class="section-title">
                        <div class="icon-box"><i class="ri-user-smile-line"></i></div>
                        Informasi Kontak
                    </div>
                    
                    <div class="form-group">
                        <label>Nama Lengkap (PIC)</label>
                        <input type="text" name="client_name" required placeholder="Contoh: Budi Santoso">
                    </div>

                    <div class="grid-2">
                        <div class="form-group">
                            <label>Email Address</label>
                            <input type="email" name="client_email" required placeholder="email@anda.com">
                        </div>
                        <div class="form-group">
                            <label>No. WhatsApp</label>
                            <input type="number" name="client_phone" required placeholder="0812xxxxxxx">
                        </div>
                    </div>
                </div>

                <div class="section-box">
                    <div class="section-title">
                        <div class="icon-box"><i class="ri-calendar-event-fill"></i></div>
                        Detail Acara
                    </div>

                    <div class="form-group">
                        <label>Jenis Acara</label>
                        <select name="event_type" id="eventType" required onchange="updateFormUI()" <?= $pre_event_type ? 'class="locked"' : '' ?>>
                            <option value="">-- Pilih Kategori Acara --</option>
                            <option value="Wedding">Wedding</option>
                            <option value="Event">Event</option>
                            <option value="Corporate">Corporate</option>
                            <option value="Lainnya">Lainnya</option>
                        </select>
                    </div>

                    <div class="grid-2">
                        <div class="form-group">
                            <label id="eventNameLabel">Nama Acara</label>
                            <input type="text" name="event_name" id="eventNameInput" required placeholder="Nama acara...">
                        </div>
                        <div class="form-group">
                            <label>Tanggal Acara</label>
                            <input type="date" name="event_date" value="<?= $pre_date; ?>" <?= $pre_date ? 'readonly' : ''; ?> required>
                            <?php if($pre_date): ?>
                                <small style="color:var(--accent); font-weight:bold;">*Tanggal terkunci sesuai pilihan di Jadwal.</small>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Lokasi Acara (Venue)</label>
                        <textarea name="event_venue" rows="2" required placeholder="Nama Gedung / Hotel / Alamat Lengkap"></textarea>
                    </div>

                    <div class="form-group">
                        <label>Pilih Paket Layanan</label>
                        <select name="package_id" id="packageSelect" required onchange="calculatePrice()" disabled>
                            <option value="">-- Pilih Jenis Acara Terlebih Dahulu --</option>
                        </select>
                        <?php if($pre_package_id): ?>
                            <small style="color:var(--accent); font-weight:bold;">*Paket otomatis terpilih dari Pricelist.</small>
                        <?php else: ?>
                            <small style="color:#64748b;">*Pricelist muncul sesuai jenis acara yang dipilih.</small>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label>Catatan Tambahan (Opsional)</label>
                        <textarea name="notes" rows="2" placeholder="Tulis request khusus jika ada..."></textarea>
                    </div>
                </div>

                <div class="section-box" style="border-bottom: none;">
                    <div class="section-title">
                        <div class="icon-box"><i class="ri-wallet-3-fill"></i></div>
                        Metode Pembayaran
                    </div>

                    <div class="form-group">
                        <label>Opsi Pembayaran</label>
                        <select name="payment_option" id="paymentOption" required onchange="handlePaymentOption()">
                            <option value="Full Payment">Full Payment (Lunas 100%)</option>
                            <option value="Down Payment">Down Payment (DP)</option>
                        </select>
                    </div>

                    <div id="dpContainer" class="dp-box">
                        <div class="form-group" style="margin-bottom: 10px;">
                            <label style="color: #b45309;">Nominal DP (Minimal 10%)</label>
                            <input type="number" name="dp_amount" id="dpAmount" placeholder="0">
                            <small id="minDpLabel" style="font-weight: bold; color: #b45309;">Min: Rp 0</small>
                        </div>
                        <p style="font-size: 0.85rem; color: #78350f; display: flex; align-items: center; gap: 5px;">
                            <i class="ri-error-warning-fill"></i> Sisa pelunasan wajib dilakukan H-5 sebelum acara.
                        </p>
                    </div>

                    <div class="price-card">
                        <span class="price-label">Total Harga Paket</span>
                        <div class="price-total" id="displayPrice">Rp 0</div>
                    </div>
                </div>

                <button type="submit" name="booking" class="btn-submit">
                    Booking Sekarang <i class="ri-arrow-right-line" style="margin-left: 5px; vertical-align: middle;"></i>
                </button>

            </form>
        </div>

        <p style="text-align: center; margin-top: 30px; color: #94a3b8; font-size: 0.9rem;">
            &copy; 2025 <?= $setting['site_name']; ?>. All Rights Reserved.
        </p>
    </div>

    <script>
        // Data PHP ke JS
        const packages = <?php echo json_encode($paket_array); ?>;
        
        // Data Pre-selected (Dari URL)
        const preType = "<?= $pre_event_type; ?>";
        const prePackage = "<?= $pre_package_id; ?>";

        // Selector Element
        const elType = document.getElementById('eventType');
        const eventNameLabel = document.getElementById('eventNameLabel');
        const eventNameInput = document.getElementById('eventNameInput');
        const elPkg = document.getElementById('packageSelect');
        const elPrice = document.getElementById('displayPrice');
        const paymentOption = document.getElementById('paymentOption');
        const dpContainer = document.getElementById('dpContainer');
        const dpAmount = document.getElementById('dpAmount');
        const minDpLabel = document.getElementById('minDpLabel');
        
        let currentPrice = 0;

        function formatRupiah(angka) {
            return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(angka);
        }

        // Logic 1: Update UI sesuai Jenis Acara
        function updateFormUI(targetPackage = null) {
            const type = elType.value;
            
            // Ubah Label Nama Acara
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
                eventNameLabel.innerHTML = "Nama Acara";
                eventNameInput.placeholder = "Nama Acara...";
            }

            // Filter Paket
            elPkg.innerHTML = '<option value="">-- Pilih Paket --</option>';
            elPkg.disabled = false;

            const filteredPackages = packages.filter(p => p.type === type);
            
            if (filteredPackages.length > 0) {
                filteredPackages.forEach(pkg => {
                    const priceFormatted = new Intl.NumberFormat('id-ID').format(pkg.price);
                    const option = document.createElement('option');
                    option.value = pkg.id;
                    option.text = `${pkg.name} - Rp ${priceFormatted}`;
                    option.dataset.price = pkg.price;
                    elPkg.appendChild(option);
                });
            } else {
                const option = document.createElement('option');
                option.text = "Tidak ada paket tersedia";
                elPkg.appendChild(option);
            }
            
            // Jika ada target paket (dari Pricelist), pilih otomatis
            if(targetPackage) {
                elPkg.value = targetPackage;
                // Kunci Dropdown Paket juga (Opsional, agar user tidak ganti)
                elPkg.classList.add('locked');
                // elPkg.disabled = true; // Jangan disabled biar value tetap terkirim, pakai CSS pointer-events aja
                calculatePrice();
            } else {
                // Reset harga jika manual
                currentPrice = 0;
                elPrice.innerText = "Rp 0";
                handlePaymentOption();
            }
        }

        // Logic 2: Update Harga
        function calculatePrice() {
            const selectedOption = elPkg.options[elPkg.selectedIndex];
            if (selectedOption.dataset.price) {
                currentPrice = parseInt(selectedOption.dataset.price);
                elPrice.innerText = formatRupiah(currentPrice);
            } else {
                currentPrice = 0;
                elPrice.innerText = "Rp 0";
            }
            handlePaymentOption();
        }

        // Logic 3: Handle DP Show/Hide
        function handlePaymentOption() {
            if (paymentOption.value === 'Down Payment' && currentPrice > 0) {
                dpContainer.style.display = 'block';
                const minDp = currentPrice * 0.10;
                dpAmount.value = minDp;
                dpAmount.min = minDp;
                minDpLabel.innerText = "Minimal: " + formatRupiah(minDp);
            } else {
                dpContainer.style.display = 'none';
            }
        }

        // INIT: Jika ada data pre-selected dari URL, jalankan fungsi
        if(preType) {
            elType.value = preType;
            // Kita panggil updateFormUI dengan mengirim ID Paket yg mau dipilih
            updateFormUI(prePackage);
        }

    </script>

</body>
</html>