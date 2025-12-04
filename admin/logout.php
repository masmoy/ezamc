<?php
session_start();

// Hapus semua session yang tersimpan
$_SESSION = [];
session_unset();
session_destroy();

// Redirect (lempar) kembali ke halaman login dengan pesan
header("Location: login.php?pesan=logout");
exit;
?>