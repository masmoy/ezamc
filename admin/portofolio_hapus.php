<?php
require_once '../config/database.php';
session_start();

if (!isset($_SESSION['status']) || $_SESSION['status'] != "login") {
    header("Location: login.php");
    exit;
}

if(isset($_GET['id'])){
    $id = $_GET['id'];

    // 1. Ambil nama file gambar dulu dari database
    $query = mysqli_query($koneksi, "SELECT image FROM portfolios WHERE id = '$id'");
    $data = mysqli_fetch_assoc($query);
    $filename = $data['image'];

    // 2. Hapus data dari database
    $hapus = mysqli_query($koneksi, "DELETE FROM portfolios WHERE id = '$id'");

    if($hapus){
        // 3. Hapus file fisik di folder (Jika filenya ada)
        $path = "../uploads/portfolio/" . $filename;
        if(file_exists($path)){
            unlink($path); // unlink = perintah PHP untuk delete file
        }

        header("Location: portofolio.php");
    } else {
        echo "Gagal menghapus data.";
    }
}
?>