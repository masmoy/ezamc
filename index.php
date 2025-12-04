<?php
require_once 'config/database.php';

// A. LOGIKA KONFIRMASI PEMBAYARAN OLEH CUSTOMER
if (isset($_POST['konfirmasi_bayar'])) {
    $kode = $_POST['kode_booking'];
    // Update status agar Admin tahu user sudah bayar
    mysqli_query($koneksi, "UPDATE bookings SET status = 'Menunggu Konfirmasi' WHERE booking_code = '$kode'");
    echo "<script>alert('Konfirmasi terkirim! Admin akan segera memverifikasi pembayaran Anda.'); window.location='index.php#status-result';</script>";
}

// B. LOGIKA CEK STATUS
$status_result = null;
if (isset($_POST['check_status'])) {
    $email = mysqli_real_escape_string($koneksi, $_POST['email']);
    $q_status = mysqli_query($koneksi, "SELECT * FROM bookings WHERE client_email = '$email' ORDER BY id DESC LIMIT 1");
    $data = mysqli_fetch_assoc($q_status);
    
    if ($data) {
        // Tentukan Progress Bar & Pesan
        $progress = 10;
        $status_msg = "Booking diterima, silakan lakukan pembayaran.";
        $class_status = "text-red";
        
        // Logika Tampilan Berdasarkan Status
        if($data['status'] == 'Menunggu Pembayaran') { 
            $progress = 10; 
            $status_msg = "Menunggu Pembayaran & Konfirmasi Anda.";
            $btn_confirm = true; // Munculkan tombol konfirmasi
        }
        elseif($data['status'] == 'Menunggu Konfirmasi') { 
            $progress = 25; 
            $status_msg = "Sedang diverifikasi oleh Admin."; 
            $btn_confirm = false;
        }
        elseif($data['status'] == 'Menunggu Jadwal TM') { 
            $progress = 40; 
            $status_msg = "Pembayaran Diterima. Menunggu jadwal TM dari Admin."; 
        }
        elseif($data['status'] == 'TM Terjadwal') { 
            $progress = 60; 
            $status_msg = "Jadwal TM: ".date('d M Y', strtotime($data['tm_date']))." jam ".$data['tm_time']." di ".$data['tm_location']; 
        }
        elseif($data['status'] == 'Menunggu Acara') { 
            $progress = 80; 
            $status_msg = "TM Selesai. Bersiap menuju Hari H!"; 
        }
        elseif($data['status'] == 'Acara Selesai' || $data['status'] == 'Selesai') { 
            $progress = 100; 
            $status_msg = "Acara Telah Selesai. Terima kasih!"; 
        }

        // Render HTML Card Timeline
        $status_html = '
        <div style="background:white; color:#333; padding:30px; border-radius:15px; margin-top:30px; text-align:left; box-shadow:0 10px 40px rgba(0,0,0,0.2); position:relative;">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; border-bottom:1px solid #eee; padding-bottom:15px;">
                <div>
                    <h3 style="color:#0f172a; margin:0; font-size:1.2rem;">'.$data['event_name'].'</h3>
                    <small style="color:#64748b;">Kode: <b>'.$data['booking_code'].'</b></small>
                </div>
                <div style="text-align:right;">
                    <span style="display:block; font-weight:bold; color:#14b8a6;">'.$data['payment_status'].'</span>
                    <small>'.$data['payment_option'].'</small>
                </div>
            </div>

            <div style="background:#f1f5f9; height:8px; border-radius:5px; margin:25px 0; overflow:hidden;">
                <div style="background:#14b8a6; height:100%; width:'.$progress.'%; transition:1s;"></div>
            </div>
            
            <div style="display:flex; justify-content:space-between; font-size:0.75rem; color:#94a3b8; font-weight:600; text-transform:uppercase;">
                <span>Booking</span>
                <span>Verifikasi</span>
                <span>TM</span>
                <span>Acara</span>
                <span>Selesai</span>
            </div>

            <div style="background:#eff6ff; padding:20px; border-radius:10px; border-left:5px solid #2563eb; margin-top:25px;">
                <strong style="display:block; color:#1e40af; margin-bottom:5px; font-size:0.9rem;">Status Terkini:</strong>
                <p style="margin:0; color:#334155; font-size:1rem;">'.$status_msg.'</p>
            </div>';

            // Jika status masih Menunggu Pembayaran, munculkan tombol Konfirmasi
            if(isset($btn_confirm) && $btn_confirm == true){
                $status_html .= '
                <div style="margin-top:20px; text-align:center;">
                    <form method="POST">
                        <input type="hidden" name="kode_booking" value="'.$data['booking_code'].'">
                        <button type="submit" name="konfirmasi_bayar" style="background:#f59e0b; color:white; border:none; padding:12px 25px; border-radius:30px; font-weight:bold; cursor:pointer; width:100%; transition:0.3s;">
                            <i class="ri-check-double-line"></i> Saya Sudah Bayar (Konfirmasi)
                        </button>
                    </form>
                    <small style="display:block; margin-top:10px; color:#64748b;">Klik tombol di atas jika sudah transfer DP/Full.</small>
                </div>';
            }

        $status_html .= '</div>';
    } else {
        $status_result = "Booking tidak ditemukan untuk email tersebut.";
    }
}

$query_setting = mysqli_query($koneksi, "SELECT * FROM settings WHERE id = 1");
$setting = mysqli_fetch_assoc($query_setting);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $setting['site_name']; ?></title>
    <meta name="description" content="<?= $setting['meta_description']; ?>">
    
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"/>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap');

        :root {
            --dark-navy: #0f172a;
            --teal-main: #14b8a6;
            --teal-hover: #0d9488;
            --yellow-btn: #f59e0b;
            --text-gray: #64748b;
            --bg-light: #f8fafc;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; scroll-behavior: smooth; }
        body { background-color: white; color: #334155; overflow-x: hidden; }

        /* NAVBAR */
        .navbar {
            display: flex; justify-content: space-between; align-items: center;
            padding: 1rem 5%; position: fixed; width: 100%; top: 0; z-index: 1000;
            transition: 0.3s; background: transparent;
        }
        .navbar.scrolled { background: var(--dark-navy); box-shadow: 0 4px 20px rgba(0,0,0,0.1); }
        .logo { font-size: 1.5rem; font-weight: 700; color: white; text-decoration: none; }
        .nav-links { display: flex; gap: 25px; list-style: none; align-items: center; }
        .nav-links a { color: rgba(255,255,255,0.9); text-decoration: none; font-size: 0.95rem; font-weight: 500; transition: 0.3s; }
        .nav-links a:hover { color: var(--teal-main); }
        .btn-login-nav { background: var(--yellow-btn); color: var(--dark-navy); padding: 8px 20px; border-radius: 5px; font-weight: 600; font-size: 0.9rem; transition: 0.3s; }
        
        /* HERO */
        .hero {
            height: 85vh; /* Tinggi ideal tidak full layar */
            min-height: 500px;
            background: radial-gradient(circle at center, rgba(15, 23, 42, 0.8), var(--dark-navy)), url('assets/img/hero-bg.jpg');
            background-size: cover; background-position: center; background-attachment: fixed;
            display: flex; flex-direction: column; justify-content: center; align-items: center;
            text-align: center; color: white; padding-top: 60px;
        }
        .hero h1 { font-size: 3rem; font-weight: 800; margin-bottom: 10px; text-transform: uppercase; letter-spacing: 2px; }
        .hero p { font-size: 1.1rem; color: #cbd5e1; max-width: 700px; line-height: 1.6; }

        /* GENERAL SECTION */
        section { padding: 4rem 5%; }
        .section-title { text-align: center; margin-bottom: 3rem; }
        .section-title h2 { font-size: 2rem; margin-bottom: 10px; color: var(--dark-navy); font-weight: 800; }
        
        /* ABOUT & FILOSOFI */
        .about { display: flex; align-items: center; justify-content: center; gap: 60px; }
        .about-img img { width: 280px; height: 280px; border-radius: 50%; object-fit: cover; border: 8px solid #f1f5f9; box-shadow: 0 10px 30px rgba(0,0,0,0.1); }
        .about-text h2 { font-size: 2.2rem; color: var(--dark-navy); margin-bottom: 15px; font-weight: 700; }
        .btn-teal { display: inline-block; background: var(--teal-main); color: white; padding: 12px 30px; border-radius: 5px; font-weight: 600; text-decoration: none; transition: 0.3s; }
        
        .filosofi-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 25px; margin-top: 30px; }
        .filosofi-item { text-align: center; padding: 25px; background: white; border-radius: 12px; box-shadow: 0 5px 20px rgba(0,0,0,0.03); border: 1px solid #f1f5f9; transition: 0.3s; }
        .filosofi-item:hover { transform: translateY(-5px); border-color: var(--teal-main); }
        .filosofi-icon { font-size: 2rem; color: var(--teal-main); margin-bottom: 15px; }

        /* STATS */
        .stats { background-color: var(--dark-navy); color: white; padding: 3rem 5%; }
        .stats-container { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; text-align: center; }
        .stat-box { padding: 20px; border-right: 1px solid rgba(255,255,255,0.1); }
        .stat-box:last-child { border-right: none; }
        .stat-box .number { font-size: 2.5rem; font-weight: 800; color: var(--teal-main); }

        /* --- SLIDESHOW CERDAS (UKURAN SAMA & AUTO) --- */
        .swiper { width: 100%; padding-bottom: 50px; }
        .swiper-slide { 
            width: 300px; 
            height: 350px; /* Tinggi KUNCI agar semua sama */
            border-radius: 15px; 
            overflow: hidden; 
            position: relative; 
            box-shadow: 0 10px 20px rgba(0,0,0,0.1); 
            background: #000; /* Background hitam untuk video/foto */
        }
        /* Memaksa gambar mengisi penuh kotak (Smart Sizing) */
        .swiper-slide img { 
            width: 100%; 
            height: 100%; 
            object-fit: cover; /* KUNCI: Potong gambar otomatis biar rapi */
            object-position: center;
        }
        .swiper-slide iframe { width: 100%; height: 100%; border: none; }
        
        .slide-overlay { 
            position: absolute; bottom: 0; left: 0; width: 100%; 
            padding: 20px; background: linear-gradient(to top, rgba(0,0,0,0.9), transparent); 
            color: white; 
        }

        /* PRICELIST TABS */
        .bg-light { background: var(--bg-light); }
        .tabs-header { display: flex; justify-content: center; gap: 10px; margin-bottom: 40px; flex-wrap: wrap; }
        .tab-btn { background: white; border: 1px solid #e2e8f0; padding: 10px 25px; border-radius: 30px; cursor: pointer; font-weight: 600; color: var(--text-gray); transition: 0.3s; }
        .tab-btn.active, .tab-btn:hover { background: var(--teal-main); color: white; border-color: var(--teal-main); box-shadow: 0 5px 15px rgba(20, 184, 166, 0.3); }
        .tab-content { display: none; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 30px; animation: fadeIn 0.5s; }
        .tab-content.active { display: grid; }
        
        .price-card { background: white; padding: 30px; border-radius: 15px; text-align: center; border: 1px solid #e2e8f0; transition: 0.3s; position: relative; overflow: hidden; }
        .price-card:hover { border-color: var(--teal-main); transform: translateY(-5px); box-shadow: 0 10px 30px rgba(0,0,0,0.05); }
        .price-tag { font-size: 2rem; font-weight: 800; color: var(--teal-main); margin: 15px 0; }
        .price-features { text-align: left; list-style: none; margin: 20px 0; }
        .price-features li { margin-bottom: 10px; font-size: 0.9rem; color: #475569; display: flex; align-items: start; gap: 8px; }
        .price-features li i { color: var(--teal-main); margin-top: 3px; }
        .btn-price { display: block; width: 100%; padding: 12px; border: 2px solid var(--dark-navy); color: var(--dark-navy); font-weight: 700; text-decoration: none; border-radius: 8px; transition: 0.3s; }
        .price-card:hover .btn-price { background: var(--dark-navy); color: white; }
        @keyframes fadeIn { from{opacity:0; transform:translateY(10px);} to{opacity:1; transform:translateY(0);} }

        /* CEK STATUS & CTA */
        .check-status-section { background: var(--dark-navy); padding: 4rem 5%; text-align: center; color: white; }
        .status-form { max-width: 500px; margin: 0 auto; display: flex; gap: 10px; }
        .status-form input { flex: 1; padding: 15px; border-radius: 8px; border: none; outline: none; }
        .status-form button { background: var(--teal-main); color: white; border: none; padding: 0 25px; border-radius: 8px; font-weight: 700; cursor: pointer; }
        
        .cta-green { background: var(--teal-main); padding: 4rem 5%; text-align: center; color: white; }
        .cta-buttons { display: flex; justify-content: center; gap: 20px; margin-top: 30px; }
        .btn-white { background: white; color: var(--teal-main); padding: 12px 25px; border-radius: 5px; font-weight: 600; text-decoration: none; }
        .btn-dark { background: var(--dark-navy); color: white; padding: 12px 25px; border-radius: 5px; font-weight: 600; text-decoration: none; }

        /* FOOTER */
        footer { background: #020617; color: white; padding: 4rem 5% 2rem; text-align: center; }
        .social-icons a { display: inline-flex; width: 40px; height: 40px; border: 1px solid rgba(255,255,255,0.2); border-radius: 50%; align-items: center; justify-content: center; color: white; margin: 0 5px; transition: 0.3s; text-decoration: none; }
        .social-icons a:hover { background: var(--teal-main); border-color: var(--teal-main); }

        /* RESPONSIVE */
        @media (max-width: 768px) {
            .about { flex-direction: column; }
            .filosofi-grid, .stats-container { grid-template-columns: 1fr; }
            .status-form { flex-direction: column; }
            .status-form button { padding: 15px; }
            .menu-toggle { display: block; color: white; font-size: 1.8rem; cursor: pointer; }
            .nav-links { display: none; position: absolute; top: 100%; right: 0; background: var(--dark-navy); width: 100%; flex-direction: column; padding: 20px; }
            .nav-links.active { display: flex; }
            .hero { height: auto; padding: 100px 20px; }
        }
    </style>
</head>
<body>

    <nav class="navbar">
        <a href="#" class="logo"><?= $setting['site_name']; ?></a>
        <div class="menu-toggle" onclick="toggleMenu()"><i class="ri-menu-3-line"></i></div>
        <ul class="nav-links">
            <li><a href="#home">Home</a></li>
            <li><a href="#about">Tentang</a></li>
            <li><a href="#portfolio">Galeri</a></li>
            <li><a href="#pricelist">Pricelist</a></li>
            <li><a href="jadwal_public.php">Jadwal</a></li>
            <li><a href="booking_public.php">Booking</a></li>
            <li><a href="admin/login.php" class="btn-login-nav">Admin Login</a></li>
        </ul>
    </nav>

    <section id="home" class="hero">
        <h1><?= strtoupper($setting['site_name']); ?> MC</h1>
        <p><?= $setting['hero_description']; ?></p>
    </section>

    <section id="about">
        <div class="about">
            <div class="about-img">
                <?php if($setting['owner_photo'] && file_exists("uploads/profile/".$setting['owner_photo'])): ?>
                    <img src="uploads/profile/<?= $setting['owner_photo']; ?>" alt="Owner">
                <?php else: ?>
                    <img src="https://ui-avatars.com/api/?name=<?= urlencode($setting['owner_name']); ?>&background=0f172a&color=fff&size=300" alt="Avatar">
                <?php endif; ?>
            </div>
            <div class="about-text">
                <h3>Tentang Saya</h3>
                <h2><?= $setting['owner_name']; ?></h2>
                <p><?= nl2br($setting['owner_description']); ?></p>
            </div>
        </div>
        <div class="filosofi-grid">
            <div class="filosofi-item"><div class="filosofi-icon"><i class="ri-flashlight-fill"></i></div><h4>Energi Positif</h4><p>Membangun suasana hidup dan menyenangkan.</p></div>
            <div class="filosofi-item"><div class="filosofi-icon"><i class="ri-time-fill"></i></div><h4>Tepat Waktu</h4><p>Menjaga rundown berjalan presisi.</p></div>
            <div class="filosofi-item"><div class="filosofi-icon"><i class="ri-heart-fill"></i></div><h4>Personal</h4><p>Memahami keinginan klien sepenuh hati.</p></div>
        </div>
    </section>

    <section class="stats">
        <div class="section-title"><h2 style="color: white;">Spesialisasi</h2></div>
        <div class="stats-container">
            <div class="stat-box"><h3>Wedding</h3><span class="number">500+</span><p>Acara Selesai</p></div>
            <div class="stat-box"><h3>Event</h3><span class="number">150+</span><p>Acara Selesai</p></div>
            <div class="stat-box"><h3>Corporate</h3><span class="number">100+</span><p>Acara Selesai</p></div>
            <div class="stat-box"><h3>Lainnya</h3><span class="number">200+</span><p>Acara Selesai</p></div>
        </div>
    </section>

    <section id="portfolio" class="bg-light">
        <div class="section-title">
            <h2>Momen Terbaik</h2>
            <p>Geser untuk melihat keseruan acara</p>
        </div>

        <h3 style="text-align: center; margin-bottom: 20px; color: var(--dark-navy);">Galeri Foto</h3>
        <div class="swiper mySwiper">
            <div class="swiper-wrapper">
                <?php
                $q_foto = mysqli_query($koneksi, "SELECT * FROM portfolios WHERE type='photo' ORDER BY id DESC LIMIT 20");
                while($p = mysqli_fetch_assoc($q_foto)): ?>
                    <div class="swiper-slide">
                        <img src="uploads/portfolio/<?= $p['image']; ?>" alt="<?= $p['title']; ?>">
                        <div class="slide-overlay">
                            <h4><?= $p['title']; ?></h4>
                            <small><?= $p['category']; ?></small>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
            <div class="swiper-pagination"></div>
        </div>

        <?php 
        $q_video = mysqli_query($koneksi, "SELECT * FROM portfolios WHERE type='video' ORDER BY id DESC LIMIT 20");
        if(mysqli_num_rows($q_video) > 0): 
        ?>
            <h3 style="text-align: center; margin: 50px 0 20px; color: var(--dark-navy);">Galeri Video</h3>
            <div class="swiper mySwiper">
                <div class="swiper-wrapper">
                    <?php while($v = mysqli_fetch_assoc($q_video)): ?>
                        <div class="swiper-slide">
                            <iframe src="<?= $v['video_link']; ?>" allowfullscreen></iframe>
                        </div>
                    <?php endwhile; ?>
                </div>
                <div class="swiper-pagination"></div>
            </div>
        <?php endif; ?>
    </section>

    <section id="pricelist">
        <div class="section-title"><h2>Pricelist Paket</h2></div>
        <div class="tabs-header">
            <button class="tab-btn active" onclick="openTab('Wedding')">Wedding</button>
            <button class="tab-btn" onclick="openTab('Event')">Event</button>
            <button class="tab-btn" onclick="openTab('Corporate')">Corporate</button>
            <button class="tab-btn" onclick="openTab('Lainnya')">Lainnya</button>
        </div>
        <?php 
        $categories = ['Wedding', 'Event', 'Corporate', 'Lainnya'];
        foreach($categories as $index => $cat): 
            $display = ($index == 0) ? 'active' : '';
        ?>
            <div id="<?= $cat ?>" class="tab-content <?= $display ?>">
                <?php
                $q_paket = mysqli_query($koneksi, "SELECT * FROM packages WHERE type='$cat' AND is_active=1 ORDER BY price ASC");
                if(mysqli_num_rows($q_paket) > 0):
                    while($paket = mysqli_fetch_assoc($q_paket)): ?>
                        <div class="price-card">
                            <h3 class="price-title"><?= $paket['name']; ?></h3>
                            <div class="price-tag"><?= ($paket['price'] > 0) ? 'Rp '.number_format($paket['price']/1000, 0).'K' : 'Custom'; ?></div>
                            <ul class="price-features">
                                <?php foreach(explode("\n", $paket['description']) as $f): if(trim($f)!="") echo "<li><i class='ri-check-line'></i> $f</li>"; endforeach; ?>
                            </ul>
                            <a href="booking_public.php?package_id=<?= $paket['id']; ?>" class="btn-price">Pilih Paket</a>
                        </div>
                    <?php endwhile; 
                else: ?>
                    <p style="text-align:center; width:100%;">Belum ada paket.</p>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </section>

    <section class="check-status-section">
        <h2>Cek Status Booking</h2>
        <p>Masukkan alamat email untuk melihat progres acara Anda.</p>
        <form action="#status-result" method="POST" class="status-form">
            <input type="email" name="email" placeholder="Email Anda..." required>
            <button type="submit" name="check_status">Cek Sekarang</button>
        </form>
        
        <div id="status-result" style="max-width: 600px; margin: 0 auto;">
            <?= isset($status_html) ? $status_html : ''; ?>
        </div>
    </section>

    <section class="cta-green">
        <h2>Mari Ciptakan Momen Luar Biasa</h2>
        <div class="cta-buttons">
            <a href="jadwal_public.php" class="btn-white">Cek Jadwal</a>
            <a href="https://wa.me/<?= $setting['contact_wa']; ?>" class="btn-dark">Hubungi WhatsApp</a>
        </div>
    </section>

    <footer>
        <h2 style="margin-bottom: 10px;"><?= $setting['site_name']; ?></h2>
        <div class="social-icons">
            <?php if($setting['contact_ig']) echo "<a href='https://instagram.com/{$setting['contact_ig']}' target='_blank'><i class='ri-instagram-line'></i></a>"; ?>
            <?php if($setting['contact_wa']) echo "<a href='https://wa.me/{$setting['contact_wa']}' target='_blank'><i class='ri-whatsapp-line'></i></a>"; ?>
            <?php if($setting['contact_youtube']) echo "<a href='{$setting['contact_youtube']}' target='_blank'><i class='ri-youtube-fill'></i></a>"; ?>
        </div>
        <div style="margin-top: 40px; border-top: 1px solid rgba(255,255,255,0.1); padding-top: 20px; font-size: 0.8rem; color: #64748b;">
            &copy; 2025 <?= $setting['site_name']; ?>. All Rights Reserved.
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script>
        // Navbar Scroll
        window.addEventListener("scroll", function(){ document.querySelector(".navbar").classList.toggle("scrolled", window.scrollY > 50); });
        function toggleMenu(){ document.querySelector(".nav-links").classList.toggle("active"); }

        // Tabs
        function openTab(catName) {
            var i, x, tablinks;
            x = document.getElementsByClassName("tab-content");
            for (i = 0; i < x.length; i++) { x[i].classList.remove("active"); }
            tablinks = document.getElementsByClassName("tab-btn");
            for (i = 0; i < tablinks.length; i++) { tablinks[i].className = tablinks[i].className.replace(" active", ""); }
            document.getElementById(catName).classList.add("active");
            event.currentTarget.className += " active";
        }

        // Swiper Slideshow (AUTO & FIXED SIZE)
        var swiper = new Swiper(".mySwiper", {
            slidesPerView: "auto", // Agar lebar ikut konten
            centeredSlides: true, // Fokus tengah
            spaceBetween: 20,
            loop: true,
            autoplay: {
                delay: 2500,
                disableOnInteraction: false,
            },
            pagination: { el: ".swiper-pagination", clickable: true },
            breakpoints: {
                640: { slidesPerView: 2, centeredSlides: false },
                1024: { slidesPerView: 3, centeredSlides: false },
            },
        });
    </script>
</body>
</html>