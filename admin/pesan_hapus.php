<?php
require_once '../config/database.php';
session_start();

// Cek login
if (!isset($_SESSION['status']) || $_SESSION['status'] != "login") {
    header("Location: login.php");
    exit;
}

if(isset($_GET['id'])){
    $id = $_GET['id'];
    
    // Query Hapus
    $hapus = mysqli_query($koneksi, "DELETE FROM messages WHERE id = '$id'");

    if($hapus){
        // Redirect kembali ke pesan.php
        header("Location: pesan.php");
    } else {
        echo "Gagal menghapus data.";
    }
}
?>