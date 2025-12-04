<?php
session_start();
require_once '../config/database.php';

// Ambil Nama Website
$q_set = mysqli_query($koneksi, "SELECT site_name FROM settings WHERE id = 1");
$d_set = mysqli_fetch_assoc($q_set);
$site_name = $d_set['site_name'] ?? 'Eza Viralindo';

if (isset($_SESSION['status']) && $_SESSION['status'] == "login") {
    header("Location: index.php");
    exit;
}

$pesan_error = "";
if (isset($_GET['pesan'])) {
    if ($_GET['pesan'] == "logout") {
        $pesan_sukses = "Anda telah berhasil logout.";
    } else if ($_GET['pesan'] == "belum_login") {
        $pesan_error = "Sesi habis, silakan login kembali.";
    }
}

if (isset($_POST['login'])) {
    $email = mysqli_real_escape_string($koneksi, $_POST['email']);
    $password = $_POST['password'];

    $query = mysqli_query($koneksi, "SELECT * FROM users WHERE email = '$email'");
    if (mysqli_num_rows($query) > 0) {
        $data = mysqli_fetch_assoc($query);
        if (password_verify($password, $data['password'])) {
            $_SESSION['user_id'] = $data['id'];
            $_SESSION['user_name'] = $data['name'];
            $_SESSION['status'] = "login";
            header("Location: index.php");
            exit;
        } else {
            $pesan_error = "Password yang Anda masukkan salah.";
        }
    } else {
        $pesan_error = "Email tidak ditemukan di sistem.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Administrator - <?= $site_name; ?></title>
    <link rel="stylesheet" href="../assets/css/login.css">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
</head>
<body>

    <div class="login-container">
        <div class="login-header">
            <div class="brand-logo">
                <i class="ri-shield-keyhole-line"></i>
            </div>
            <h2>Admin Panel</h2>
            <p>Masuk untuk mengelola website <?= $site_name; ?></p>
        </div>

        <?php if($pesan_error != ""): ?>
            <div class="alert alert-error">
                <i class="ri-error-warning-fill"></i> <?= $pesan_error; ?>
            </div>
        <?php endif; ?>

        <?php if(isset($pesan_sukses)): ?>
            <div class="alert alert-success">
                <i class="ri-checkbox-circle-fill"></i> <?= $pesan_sukses; ?>
            </div>
        <?php endif; ?>

        <form action="" method="POST">
            <div class="form-group">
                <label>Email Address</label>
                <div class="input-wrapper">
                    <input type="email" name="email" placeholder="contoh@admin.com" required autofocus>
                    <i class="ri-mail-line icon-left"></i>
                </div>
            </div>
            
            <div class="form-group">
                <label>Password</label>
                <div class="input-wrapper">
                    <input type="password" name="password" id="passInput" placeholder="••••••••" required>
                    
                    <i class="ri-lock-password-line icon-left"></i>
                    
                    <i class="ri-eye-off-line icon-toggle" id="togglePass" title="Lihat Password"></i>
                </div>
            </div>

            <button type="submit" name="login" class="btn-login">Masuk Dashboard <i class="ri-arrow-right-line"></i></button>
        </form>
    </div>

    <div class="footer-login">
        <p>Copyright &copy; <?= date('Y'); ?> <?= $site_name; ?>. All Rights Reserved.</p>
        <p style="margin-top: 5px; opacity: 0.8;">
            Powered by <a href="https://kdsstudio.my.id" target="_blank">KDS Creative Studio</a>. 
            Developed by <a href="https://ngabdulmuhyi.my.id" target="_blank">MasMoy</a>.
        </p>
    </div>

    <script>
        const togglePass = document.getElementById('togglePass');
        const passInput = document.getElementById('passInput');

        togglePass.addEventListener('click', function () {
            // Cek tipe input saat ini
            const type = passInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passInput.setAttribute('type', type);
            
            // Ubah ikon mata (Terbuka / Tertutup)
            this.classList.toggle('ri-eye-off-line'); // Mata dicoret
            this.classList.toggle('ri-eye-line');     // Mata terbuka
        });
    </script>

</body>
</html>