<?php
require_once '../config/database.php';
include 'includes/header.php';
include 'includes/sidebar.php';

$query = mysqli_query($koneksi, "SELECT * FROM packages ORDER BY price ASC");
?>

<div class="main-content">
    <div class="header-dash">
        <div style="display: flex; align-items: center;">
            <i class="ri-menu-line menu-toggle" style="font-size: 1.5rem; margin-right: 1rem; cursor: pointer; display: none;"></i>
            <h2>Kelola Paket Layanan</h2>
        </div>
        <a href="paket_tambah.php" class="btn-primary"><i class="ri-add-line"></i> Tambah Paket</a>
    </div>

    <div style="background: white; padding: 1.5rem; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.02);">
        <div style="overflow-x: auto;">
            <table class="table-custom">
                <thead>
                    <tr>
                        <th width="5%">No</th>
                        <th width="20%">Nama Paket</th>
                        <th width="15%">Kategori</th>
                        <th width="20%">Harga</th>
                        <th width="30%">Deskripsi / Fitur</th>
                        <th width="10%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(mysqli_num_rows($query) > 0): ?>
                        <?php $no = 1; while($data = mysqli_fetch_assoc($query)): ?>
                        <tr>
                            <td><?= $no++; ?></td>
                            <td><strong><?= $data['name']; ?></strong></td>
                            <td>
                                <span class="badge" style="background-color: #e0f2fe; color: #0369a1;">
                                    <?= $data['type']; ?>
                                </span>
                            </td>
                            <td style="font-weight: bold; color: #2563eb;">
                                Rp <?= number_format($data['price'], 0, ',', '.'); ?>
                            </td>
                            <td><?= nl2br($data['description']); ?></td>
                            <td>
                                <a href="paket_edit.php?id=<?= $data['id']; ?>" class="btn-sm btn-blue"><i class="ri-pencil-line"></i></a>
                                <a href="paket_hapus.php?id=<?= $data['id']; ?>" class="btn-sm btn-red" onclick="return confirm('Yakin hapus paket ini?')"><i class="ri-delete-bin-line"></i></a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="6" class="text-center">Belum ada paket layanan.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
.btn-primary { background: #2563eb; color: white; padding: 10px 20px; border-radius: 8px; font-weight: 500; display: inline-flex; align-items: center; gap: 5px; }
.btn-primary:hover { background: #1d4ed8; }
</style>

<?php include 'includes/footer.php'; ?>