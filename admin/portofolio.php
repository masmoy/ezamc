<?php
require_once '../config/database.php';
include 'includes/header.php';
include 'includes/sidebar.php';

// Hitung Jumlah
$q_foto = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM portfolios WHERE type='photo'");
$jum_foto = mysqli_fetch_assoc($q_foto)['total'];

$q_video = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM portfolios WHERE type='video'");
$jum_video = mysqli_fetch_assoc($q_video)['total'];

$query = mysqli_query($koneksi, "SELECT * FROM portfolios ORDER BY id DESC");
?>

<div class="main-content">
    <div class="header-dash">
        <h2>Kelola Portofolio</h2>
        <a href="portofolio_tambah.php" class="btn-primary"><i class="ri-add-line"></i> Tambah Baru</a>
    </div>

    <div style="display: flex; gap: 20px; margin-bottom: 20px;">
        <span class="badge" style="background:#dbeafe; color:#1e40af; font-size:0.9rem;">Foto: <?= $jum_foto ?> / 20</span>
        <span class="badge" style="background:#fee2e2; color:#991b1b; font-size:0.9rem;">Video: <?= $jum_video ?> / 20</span>
    </div>

    <div class="card" style="padding: 0;">
        <div style="overflow-x: auto;">
            <table class="table-custom">
                <thead>
                    <tr>
                        <th>Media</th>
                        <th>Tipe</th>
                        <th>Judul & Kategori</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($data = mysqli_fetch_assoc($query)): ?>
                        <tr>
                            <td>
                                <div style="position: relative; width: 80px; height: 60px;">
                                    <?php if($data['type'] == 'photo'): ?>
                                        <img src="../uploads/portfolio/<?= $data['image']; ?>" style="width:100%; height:100%; object-fit:cover; border-radius:6px;">
                                    <?php else: ?>
                                        <img src="<?= $data['image']; ?>" style="width:100%; height:100%; object-fit:cover; border-radius:6px;">
                                        <div style="position: absolute; top:50%; left:50%; transform:translate(-50%, -50%); color:red; font-size:1.5rem;">
                                            <i class="ri-youtube-fill"></i>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td>
                                <?php if($data['type'] == 'photo'): ?>
                                    <span class="badge" style="background:#f1f5f9; color:#475569;">FOTO</span>
                                <?php else: ?>
                                    <span class="badge" style="background:#fef2f2; color:#ef4444;">VIDEO</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <strong><?= $data['title']; ?></strong><br>
                                <small><?= $data['category']; ?></small>
                            </td>
                            <td>
                                <a href="portofolio_hapus.php?id=<?= $data['id']; ?>" class="btn-sm" style="color:red;" onclick="return confirm('Hapus item ini?')"><i class="ri-delete-bin-line"></i></a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>