<?php
require_once '../config/database.php';
session_start();

if (!isset($_SESSION['status']) || $_SESSION['status'] != "login") {
    header("Location: login.php");
    exit;
}

if(isset($_GET['id'])){
    $id = $_GET['id'];
    $hapus = mysqli_query($koneksi, "DELETE FROM bookings WHERE id = '$id'");
    
    if($hapus){
        header("Location: booking.php");
    } else {
        echo "Gagal menghapus data.";
    }
}
?>