<?php
require_once '../config/database.php';
include 'includes/header.php';
include 'includes/sidebar.php';

$error = "";

// Fungsi Ekstrak ID YouTube
function getYoutubeId($url) {
    preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $url, $match);
    return isset($match[1]) ? $match[1] : false;
}

if (isset($_POST['simpan'])) {
    $title = mysqli_real_escape_string($koneksi, $_POST['title']);
    $category = mysqli_real_escape_string($koneksi, $_POST['category']);
    $type = $_POST['type']; // 'photo' atau 'video'
    $description = mysqli_real_escape_string($koneksi, $_POST['description']);

    // 1. CEK LIMIT (Maksimal 20)
    $cek_jumlah = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM portfolios WHERE type = '$type'");
    $data_jumlah = mysqli_fetch_assoc($cek_jumlah);
    
    if ($data_jumlah['total'] >= 20) {
        $error = "Gagal! Kuota $type sudah penuh (Maksimal 20 item). Hapus data lama dulu.";
    } else {
        
        // LOGIKA VIDEO
        if ($type == 'video') {
            $video_url = $_POST['video_link'];
            $youtube_id = getYoutubeId($video_url);

            if ($youtube_id) {
                // Kita simpan ID-nya saja biar rapi, atau link embed
                $embed_link = "https://www.youtube.com/embed/" . $youtube_id;
                // Kita pakai thumbnail dari youtube untuk kolom 'image' biar di admin tetap ada gambarnya
                $thumb_yt = "https://img.youtube.com/vi/$youtube_id/hqdefault.jpg";

                $query = "INSERT INTO portfolios (title, category, type, image, video_link, description) 
                          VALUES ('$title', '$category', 'video', '$thumb_yt', '$embed_link', '$description')";
                
                if(mysqli_query($koneksi, $query)) {
                    echo "<script>alert('Video berhasil ditambahkan!'); window.location='portofolio.php';</script>";
                }
            } else {
                $error = "Link YouTube tidak valid!";
            }

        } 
        // LOGIKA FOTO
        else {
            $filename = $_FILES['gambar']['name'];
            $tmp_name = $_FILES['gambar']['tmp_name'];
            $filesize = $_FILES['gambar']['size'];
            
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            $valid_ext = ['jpg', 'jpeg', 'png', 'webp'];

            if (!in_array($ext, $valid_ext)) {
                $error = "Format file harus JPG, PNG, atau WEBP!";
            } elseif ($filesize > 2000000) {
                $error = "Ukuran file terlalu besar! Max 2MB.";
            } else {
                $new_name = "porto_" . time() . "." . $ext;
                if (move_uploaded_file($tmp_name, "../uploads/portfolio/" . $new_name)) {
                    $query = "INSERT INTO portfolios (title, category, type, image, description) 
                              VALUES ('$title', '$category', 'photo', '$new_name', '$description')";
                    
                    if(mysqli_query($koneksi, $query)) {
                        echo "<script>alert('Foto berhasil diupload!'); window.location='portofolio.php';</script>";
                    }
                } else {
                    $error = "Gagal upload file.";
                }
            }
        }
    }
}
?>

<div class="main-content">
    <div style="margin-bottom: 20px;">
        <a href="portofolio.php" style="color: #64748b;"><i class="ri-arrow-left-line"></i> Kembali</a>
    </div>

    <div class="card" style="max-width: 700px; margin: 0 auto;">
        <div class="card-head">
            <h3>Tambah Portofolio</h3>
        </div>
        
        <?php if($error): ?>
            <div class="alert alert-danger" style="background:#fee2e2; color:#991b1b; padding:10px; border-radius:8px; margin-bottom:15px;">
                <?= $error; ?>
            </div>
        <?php endif; ?>

        <form action="" method="POST" enctype="multipart/form-data">
            
            <div class="form-group">
                <label>Judul</label>
                <input type="text" name="title" required placeholder="Judul momen..." class="form-control" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:8px;">
            </div>

            <div class="form-group">
                <label>Kategori Acara</label>
                <select name="category" required style="width:100%; padding:10px; border:1px solid #ddd; border-radius:8px;">
                    <option value="Wedding">Wedding</option>
                    <option value="Event">Event</option>
                    <option value="Corporate">Corporate</option>
                </select>
            </div>

            <div class="form-group">
                <label>Jenis Media</label>
                <select name="type" id="mediaType" required onchange="toggleInput()" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:8px; font-weight:bold;">
                    <option value="photo">Foto (Upload)</option>
                    <option value="video">Video (YouTube)</option>
                </select>
            </div>

            <div class="form-group" id="inputPhoto">
                <label>Upload Foto (Max 2MB)</label>
                <input type="file" name="gambar" style="width:100%; padding:10px; background:#f8fafc; border:1px dashed #2563eb; border-radius:8px;">
            </div>

            <div class="form-group" id="inputVideo" style="display: none;">
                <label>Link YouTube</label>
                <input type="text" name="video_link" placeholder="Contoh: https://www.youtube.com/watch?v=xxxx" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:8px;">
                <small style="color: #ef4444;">*Masukkan link lengkap YouTube video.</small>
            </div>

            <div class="form-group">
                <label>Deskripsi Singkat</label>
                <textarea name="description" rows="3" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:8px;"></textarea>
            </div>

            <button type="submit" name="simpan" class="btn-primary" style="margin-top: 20px;">Simpan Portofolio</button>
        </form>
    </div>
</div>

<script>
    function toggleInput() {
        const type = document.getElementById('mediaType').value;
        const inputPhoto = document.getElementById('inputPhoto');
        const inputVideo = document.getElementById('inputVideo');

        if (type === 'video') {
            inputPhoto.style.display = 'none';
            inputVideo.style.display = 'block';
        } else {
            inputPhoto.style.display = 'block';
            inputVideo.style.display = 'none';
        }
    }
</script>

<?php include 'includes/footer.php'; ?>