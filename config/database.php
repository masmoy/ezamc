<?php
// config/database.php

$host = "localhost";
$user = "root";
$pass = ""; // Kosongkan jika pakai XAMPP default
$db   = "php_ezaviralindo";

// Melakukan koneksi
$koneksi = mysqli_connect($host, $user, $pass, $db);

// Cek koneksi, jika error tampilkan pesan
if (!$koneksi) {
    die("Gagal terhubung ke database: " . mysqli_connect_error());
}

// Set timezone agar waktu sesuai dengan WIB (Jakarta)
date_default_timezone_set('Asia/Jakarta');
?>