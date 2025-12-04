<?php
require_once '../config/database.php';
include 'includes/header.php';
include 'includes/sidebar.php';

// PROSES SIMPAN PAKET
if (isset($_POST['simpan'])) {
    $name = mysqli_real_escape_string($koneksi, $_POST['name']);
    $type = mysqli_real_escape_string($koneksi, $_POST['type']);
    $price = $_POST['price'];
    $description = mysqli_real_escape_string($koneksi, $_POST['description']);
    $is_active = $_POST['is_active']; // 1 or 0

    $query = "INSERT INTO packages (name, type, price, description, is_active) VALUES ('$name', '$type', '$price', '$description', '$is_active')";
    
    if (mysqli_query($koneksi, $query)) {
        echo "<script>alert('Paket berhasil ditambahkan!'); window.location='paket.php';</script>";
    } else {
        echo "<script>alert('Gagal menyimpan data: ".mysqli_error($koneksi)."');</script>";
    }
}
?>

<style>
    .form-card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.03);
        padding: 40px;
        border-top: 5px solid #2563eb; /* Biru Admin */
        max-width: 800px;
        margin: 0 auto;
    }
    
    .form-header { text-align: center; margin-bottom: 30px; }
    .form-header h2 { font-size: 1.8rem; color: #1e293b; font-weight: 700; margin-bottom: 5px; }
    .form-header p { color: #64748b; font-size: 0.95rem; }

    .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }

    .form-group { margin-bottom: 20px; }
    .form-group label { display: block; margin-bottom: 8px; font-weight: 500; color: #334155; }
    
    .form-control {
        width: 100%; padding: 12px 15px;
        border: 2px solid #e2e8f0; border-radius: 10px;
        font-size: 0.95rem; color: #334155; transition: 0.3s;
        background: #f8fafc;
    }
    .form-control:focus {
        border-color: #2563eb; background: white; outline: none;
        box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
    }
    textarea.form-control { resize: vertical; min-height: 120px; }

    .btn-submit {
        width: 100%; background: #2563eb; color: white;
        padding: 15px; border: none; border-radius: 10px;
        font-weight: 600; font-size: 1rem; cursor: pointer;
        transition: 0.3s; margin-top: 10px;
        display: flex; align-items: center; justify-content: center; gap: 8px;
    }
    .btn-submit:hover { background: #1d4ed8; transform: translateY(-2px); }

    /* Responsive */
    @media (max-width: 768px) { .grid-2 { grid-template-columns: 1fr; } }
</style>

<div class="main-content">
    
    <div style="margin-bottom: 20px;">
        <a href="paket.php" style="color: #64748b; font-weight: 500; display: flex; align-items: center; gap: 5px;">
            <i class="ri-arrow-left-line"></i> Kembali ke Daftar Paket
        </a>
    </div>

    <div class="form-card">
        <div class="form-header">
            <h2>Tambah Paket Layanan</h2>
            <p>Buat paket baru untuk ditampilkan di Pricelist website.</p>
        </div>

        <form action="" method="POST">
            
            <div class="form-group">
                <label>Nama Paket</label>
                <input type="text" name="name" class="form-control" required placeholder="Contoh: Paket Gold Wedding">
            </div>

            <div class="grid-2">
                <div class="form-group">
                    <label>Kategori Acara</label>
                    <select name="type" class="form-control" required>
                        <option value="">-- Pilih Kategori --</option>
                        <option value="Wedding">Wedding</option>
                        <option value="Event">Event</option>
                        <option value="Corporate">Corporate</option>
                        <option value="Lainnya">Lainnya</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Harga (Rp)</label>
                    <input type="number" name="price" class="form-control" required placeholder="0">
                    <small style="color: #64748b;">Isi 0 jika harga "Custom/Hubungi Admin"</small>
                </div>
            </div>

            <div class="form-group">
                <label>Status Paket</label>
                <select name="is_active" class="form-control" required>
                    <option value="1">Aktif (Tampil di Website)</option>
                    <option value="0">Tidak Aktif (Sembunyikan)</option>
                </select>
            </div>

            <div class="form-group">
                <label>Deskripsi & Fasilitas</label>
                <textarea name="description" class="form-control" required placeholder="Contoh fasilitas:&#10;- MC Resepsi&#10;- Durasi 3 Jam&#10;- Free Transport"></textarea>
                <small style="color: #2563eb;">*Gunakan tombol Enter untuk memisahkan baris poin fasilitas.</small>
            </div>

            <button type="submit" name="simpan" class="btn-submit">
                <i class="ri-save-line"></i> Simpan Paket Baru
            </button>

        </form>
    </div>
</div>

<?php include 'includes/footer.php'; ?>