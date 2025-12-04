<?php
require_once '../config/database.php';
include 'includes/header.php';
include 'includes/sidebar.php';

$id = $_GET['id'];
$query = mysqli_query($koneksi, "SELECT * FROM packages WHERE id = '$id'");
$data = mysqli_fetch_assoc($query);

if (isset($_POST['update'])) {
    $name = mysqli_real_escape_string($koneksi, $_POST['name']);
    $type = mysqli_real_escape_string($koneksi, $_POST['type']);
    $price = $_POST['price'];
    $description = mysqli_real_escape_string($koneksi, $_POST['description']);

    $update = mysqli_query($koneksi, "UPDATE packages SET name='$name', type='$type', price='$price', description='$description' WHERE id='$id'");
    
    if ($update) {
        echo "<script>alert('Paket berhasil diperbarui!'); window.location='paket.php';</script>";
    } else {
        echo "<script>alert('Gagal update data.');</script>";
    }
}
?>

<div class="main-content">
    <div class="header-dash">
        <h2>Edit Paket</h2>
    </div>

    <div style="background: white; padding: 2rem; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.02); max-width: 600px;">
        <form action="" method="POST">
            <div class="form-group">
                <label>Nama Paket</label>
                <input type="text" name="name" required value="<?= $data['name']; ?>">
            </div>

            <div class="form-group">
                <label>Kategori Acara</label>
                <select name="type" style="width:100%; padding:0.75rem; border:1px solid #d1d5db; border-radius:8px;" required>
                    <option value="Wedding" <?= ($data['type'] == 'Wedding') ? 'selected' : '' ?>>Wedding</option>
                    <option value="Corporate" <?= ($data['type'] == 'Corporate') ? 'selected' : '' ?>>Corporate / Kantor</option>
                    <option value="Birthday" <?= ($data['type'] == 'Birthday') ? 'selected' : '' ?>>Birthday</option>
                    <option value="Event" <?= ($data['type'] == 'Event') ? 'selected' : '' ?>>Event Umum</option>
                </select>
            </div>

            <div class="form-group">
                <label>Harga (Rp)</label>
                <input type="number" name="price" required value="<?= $data['price']; ?>">
            </div>

            <div class="form-group">
                <label>Deskripsi / Fasilitas</label>
                <textarea name="description" rows="5" style="width:100%; padding:0.75rem; border:1px solid #d1d5db; border-radius:8px;" required><?= $data['description']; ?></textarea>
            </div>

            <div style="margin-top: 2rem;">
                <button type="submit" name="update" class="btn-primary" style="border:none; cursor:pointer;">Simpan Perubahan</button>
                <a href="paket.php" style="margin-left: 10px; color: #6b7280;">Batal</a>
            </div>
        </form>
    </div>
</div>

<?php include 'includes/footer.php'; ?>