<?php
require_once '../config/database.php';
include 'includes/header.php';
include 'includes/sidebar.php';

// Ambil data pengaturan (ID 1)
$query = mysqli_query($koneksi, "SELECT * FROM settings WHERE id = 1");
$data = mysqli_fetch_assoc($query);

// Jika data kosong (pertama kali), insert data default
if(!$data){
    mysqli_query($koneksi, "INSERT INTO settings (id, site_name) VALUES (1, 'Eza Viralindo')");
    echo "<script>window.location='pengaturan.php';</script>";
}

// PROSES UPDATE
if (isset($_POST['update'])) {
    // 1. IDENTITAS WEB
    $site_name = mysqli_real_escape_string($koneksi, $_POST['site_name']);
    $hero_title = mysqli_real_escape_string($koneksi, $_POST['hero_title']);
    $hero_description = mysqli_real_escape_string($koneksi, $_POST['hero_description']);
    $meta_description = mysqli_real_escape_string($koneksi, $_POST['meta_description']);

    // 2. PROFIL
    $owner_name = mysqli_real_escape_string($koneksi, $_POST['owner_name']);
    $owner_description = mysqli_real_escape_string($koneksi, $_POST['owner_description']);

    // 3. KONTAK
    $contact_wa = mysqli_real_escape_string($koneksi, $_POST['contact_wa']);
    $contact_ig = mysqli_real_escape_string($koneksi, $_POST['contact_ig']);
    $contact_email = mysqli_real_escape_string($koneksi, $_POST['contact_email']);
    $contact_youtube = mysqli_real_escape_string($koneksi, $_POST['contact_youtube']);
    $contact_fb = mysqli_real_escape_string($koneksi, $_POST['contact_fb']);

    // Logika Foto Profil
    $foto_lama = $data['owner_photo'];
    $nama_foto = $foto_lama;

    // Jika ada upload foto baru
    if ($_FILES['owner_photo']['name'] != "") {
        $filename = $_FILES['owner_photo']['name'];
        $tmp_name = $_FILES['owner_photo']['tmp_name'];
        $file_ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        $valid_ext = ['jpg', 'jpeg', 'png', 'webp'];

        if (in_array($file_ext, $valid_ext)) {
            // Hapus foto lama
            if ($foto_lama != "" && $foto_lama != "default.jpg" && file_exists("../uploads/profile/" . $foto_lama)) {
                unlink("../uploads/profile/" . $foto_lama);
            }
            // Upload baru
            $nama_foto = "owner_" . time() . "." . $file_ext;
            move_uploaded_file($tmp_name, "../uploads/profile/" . $nama_foto);
        } else {
            echo "<script>alert('Format gambar harus JPG/PNG/WEBP!');</script>";
        }
    }

    // Query Update Lengkap
    $update = mysqli_query($koneksi, "UPDATE settings SET 
        site_name='$site_name', 
        hero_title='$hero_title', 
        hero_description='$hero_description',
        meta_description='$meta_description',
        owner_name='$owner_name',
        owner_description='$owner_description',
        owner_photo='$nama_foto',
        contact_wa='$contact_wa',
        contact_ig='$contact_ig',
        contact_email='$contact_email',
        contact_youtube='$contact_youtube',
        contact_fb='$contact_fb'
        WHERE id = 1");

    if ($update) {
        echo "<script>alert('Pengaturan berhasil disimpan!'); window.location='pengaturan.php';</script>";
    } else {
        echo "<script>alert('Gagal menyimpan perubahan.');</script>";
    }
}
?>

<style>
    .settings-container { max-width: 900px; margin: 0 auto; }
    .form-card { background: white; border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.03); overflow: hidden; border-top: 5px solid #2563eb; }
    
    .section-header { 
        background: #f8fafc; padding: 20px 30px; border-bottom: 1px solid #e2e8f0; 
        display: flex; align-items: center; gap: 15px; 
    }
    .section-header h4 { margin: 0; color: #1e293b; font-size: 1.1rem; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; }
    .section-icon { width: 40px; height: 40px; background: #eff6ff; color: #2563eb; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; }

    .form-body { padding: 30px; }
    
    .form-group { margin-bottom: 20px; }
    .form-group label { display: block; margin-bottom: 8px; font-weight: 600; color: #475569; font-size: 0.9rem; }
    .form-control { width: 100%; padding: 12px; border: 2px solid #e2e8f0; border-radius: 8px; transition: 0.3s; color: #334155; }
    .form-control:focus { border-color: #2563eb; outline: none; box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1); }
    
    .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
    
    .preview-box { text-align: center; background: #f8fafc; padding: 20px; border-radius: 10px; border: 2px dashed #cbd5e1; height: 100%; display: flex; flex-direction: column; justify-content: center; align-items: center; }
    .preview-img { width: 120px; height: 120px; object-fit: cover; border-radius: 50%; border: 4px solid white; box-shadow: 0 5px 15px rgba(0,0,0,0.1); margin-bottom: 15px; }

    .btn-save { background: #2563eb; color: white; padding: 15px 30px; border: none; border-radius: 10px; font-weight: 700; cursor: pointer; display: flex; align-items: center; gap: 10px; font-size: 1rem; transition: 0.3s; margin-left: auto; }
    .btn-save:hover { background: #1d4ed8; transform: translateY(-2px); }

    /* Input Icon Group */
    .input-group { position: relative; }
    .input-group i { position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 1.2rem; }
    .input-group input { padding-left: 45px; }

    @media (max-width: 768px) { .grid-2 { grid-template-columns: 1fr; } }
</style>

<div class="main-content">
    <div class="header-dash">
        <h2>Pengaturan Website</h2>
    </div>

    <div class="settings-container">
        <form action="" method="POST" enctype="multipart/form-data">
            
            <div class="form-card">
                
                <div class="section-header">
                    <div class="section-icon"><i class="ri-global-line"></i></div>
                    <h4>1. Identitas Website</h4>
                </div>
                <div class="form-body">
                    <div class="grid-2">
                        <div class="form-group">
                            <label>Nama Website / Brand</label>
                            <input type="text" name="site_name" class="form-control" value="<?= $data['site_name']; ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Judul Utama (Hero Title)</label>
                            <input type="text" name="hero_title" class="form-control" value="<?= $data['hero_title']; ?>" placeholder="Contoh: MC PROFESSIONAL">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Deskripsi Utama (Hero Description)</label>
                        <textarea name="hero_description" rows="2" class="form-control" placeholder="Teks di bawah judul utama..."><?= $data['hero_description']; ?></textarea>
                    </div>
                    <div class="form-group">
                        <label>Meta Description (Untuk SEO Google)</label>
                        <textarea name="meta_description" rows="2" class="form-control" placeholder="Deskripsi singkat website untuk hasil pencarian Google..."><?= $data['meta_description']; ?></textarea>
                    </div>
                </div>

                <div class="section-header" style="border-top: 1px solid #e2e8f0;">
                    <div class="section-icon"><i class="ri-user-star-line"></i></div>
                    <h4>2. Profil Owner</h4>
                </div>
                <div class="form-body">
                    <div class="grid-2">
                        <div>
                            <div class="form-group">
                                <label>Nama Lengkap / Panggung</label>
                                <input type="text" name="owner_name" class="form-control" value="<?= $data['owner_name']; ?>" required>
                            </div>
                            <div class="form-group">
                                <label>Deskripsi Profil (Tentang Saya)</label>
                                <textarea name="owner_description" rows="6" class="form-control" placeholder="Ceritakan pengalaman dan keahlian Anda..."><?= $data['owner_description']; ?></textarea>
                            </div>
                            <div class="form-group">
                                <label>Ganti Foto Profil</label>
                                <input type="file" name="owner_photo" class="form-control" style="padding: 10px;">
                                <small style="color: #64748b;">Format JPG/PNG. Kosongkan jika tidak ingin mengganti.</small>
                            </div>
                        </div>
                        
                        <div class="preview-box">
                            <label>Foto Saat Ini</label>
                            <?php if($data['owner_photo'] != "" && file_exists("../uploads/profile/".$data['owner_photo'])): ?>
                                <img src="../uploads/profile/<?= $data['owner_photo']; ?>" class="preview-img" alt="Owner">
                            <?php else: ?>
                                <div class="preview-img" style="background:#ddd; display:flex; align-items:center; justify-content:center;">No Foto</div>
                            <?php endif; ?>
                            <small style="color: #2563eb; font-weight: 600;"><?= $data['owner_name']; ?></small>
                        </div>
                    </div>
                </div>

                <div class="section-header" style="border-top: 1px solid #e2e8f0;">
                    <div class="section-icon"><i class="ri-share-line"></i></div>
                    <h4>3. Kontak & Sosial Media</h4>
                </div>
                <div class="form-body">
                    <div class="grid-2">
                        <div class="form-group">
                            <label>WhatsApp (Format: 628xxx)</label>
                            <div class="input-group">
                                <i class="ri-whatsapp-line"></i>
                                <input type="number" name="contact_wa" class="form-control" value="<?= $data['contact_wa']; ?>" placeholder="628123456789">
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Email Address</label>
                            <div class="input-group">
                                <i class="ri-mail-line"></i>
                                <input type="email" name="contact_email" class="form-control" value="<?= $data['contact_email']; ?>" placeholder="admin@domain.com">
                            </div>
                        </div>
                    </div>

                    <div class="grid-2">
                        <div class="form-group">
                            <label>Instagram Username (Tanpa @)</label>
                            <div class="input-group">
                                <i class="ri-instagram-line"></i>
                                <input type="text" name="contact_ig" class="form-control" value="<?= $data['contact_ig']; ?>" placeholder="username">
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Facebook (Link Profile)</label>
                            <div class="input-group">
                                <i class="ri-facebook-circle-line"></i>
                                <input type="text" name="contact_fb" class="form-control" value="<?= $data['contact_fb']; ?>" placeholder="https://facebook.com/username">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Youtube Channel (Link)</label>
                        <div class="input-group">
                            <i class="ri-youtube-line"></i>
                            <input type="text" name="contact_youtube" class="form-control" value="<?= $data['contact_youtube']; ?>" placeholder="https://youtube.com/@channel">
                        </div>
                    </div>
                </div>

                <div style="background: #f8fafc; padding: 20px 30px; border-top: 1px solid #e2e8f0; text-align: right;">
                    <button type="submit" name="update" class="btn-save">
                        <i class="ri-save-3-line"></i> Simpan Semua Perubahan
                    </button>
                </div>

            </div>
        </form>
    </div>
</div>

<?php include 'includes/footer.php'; ?>