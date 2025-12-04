<?php
$password = "admin123";

// Generate hash
$hash = password_hash($password, PASSWORD_DEFAULT);

// Tampilkan hasilnya
echo "Password: $password<br>";
echo "Hash: $hash<br>";
?>
