<?php
require_once '../config/database.php';
include 'includes/header.php';
include 'includes/sidebar.php';

// Ambil data pesan dari database, urutkan dari yang terbaru
$query = mysqli_query($koneksi, "SELECT * FROM messages ORDER BY created_at DESC");
?>

<div class="main-content">
    <div class="header-dash">
        <div style="display: flex; align-items: center;">
            <i class="ri-menu-line menu-toggle" style="font-size: 1.5rem; margin-right: 1rem; cursor: pointer; display: none;"></i>
            <h2>Pesan Masuk</h2>
        </div>
    </div>

    <div style="background: white; padding: 1.5rem; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.02);">
        
        <div style="overflow-x: auto;">
            <table class="table-custom">
                <thead>
                    <tr>
                        <th width="5%">No</th>
                        <th width="20%">Tanggal</th>
                        <th width="20%">Pengirim</th>
                        <th width="35%">Isi Pesan (Cuplikan)</th>
                        <th width="10%">Status</th>
                        <th width="10%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(mysqli_num_rows($query) > 0): ?>
                        <?php $no = 1; while($data = mysqli_fetch_assoc($query)): ?>
                        <tr>
                            <td><?= $no++; ?></td>
                            <td><?= date('d M Y H:i', strtotime($data['created_at'])); ?></td>
                            <td>
                                <strong><?= htmlspecialchars($data['name']); ?></strong><br>
                                <small style="color: #6b7280;"><?= htmlspecialchars($data['email']); ?></small>
                            </td>
                            <td>
                                <?= substr(htmlspecialchars($data['message']), 0, 50) . '...'; ?>
                            </td>
                            <td>
                                <?php if($data['is_read'] == 0): ?>
                                    <span class="badge badge-red">Belum Dibaca</span>
                                <?php else: ?>
                                    <span class="badge badge-green">Sudah Dibaca</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="pesan_detail.php?id=<?= $data['id']; ?>" class="btn-sm btn-blue" title="Baca"><i class="ri-eye-line"></i></a>
                                <a href="pesan_hapus.php?id=<?= $data['id']; ?>" class="btn-sm btn-red" onclick="return confirm('Hapus pesan ini?')" title="Hapus"><i class="ri-delete-bin-line"></i></a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 20px;">Belum ada pesan masuk.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="footer">
        <p>Copyright &copy; 2025 Eza Viralindo. Powered by KDS Creative Studio.</p>
    </div>
</div>

<style>
.table-custom { width: 100%; border-collapse: collapse; min-width: 600px; /* Agar di HP tidak gepeng */ }
.table-custom th, .table-custom td { padding: 12px 15px; border-bottom: 1px solid #e5e7eb; text-align: left; font-size: 0.95rem; }
.table-custom th { background-color: #f9fafb; font-weight: 600; color: #374151; }
.badge { padding: 4px 10px; border-radius: 20px; font-size: 0.75rem; font-weight: 600; }
.badge-red { background-color: #fee2e2; color: #991b1b; }
.badge-green { background-color: #d1fae5; color: #065f46; }
.btn-sm { padding: 5px 10px; border-radius: 5px; color: white; margin-right: 5px; display: inline-block; }
.btn-blue { background-color: #2563eb; }
.btn-red { background-color: #ef4444; }
</style>

<?php include 'includes/footer.php'; ?>