<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('pages/login.php');
}

$pageTitle = 'Verifikasi Pembayaran';

// Handle aksi TERIMA
if (isset($_POST['action']) && $_POST['action'] === 'terima') {
    $pid = intval($_POST['pesanan_id']);
    $conn->query("UPDATE Pesanan SET Status='companion_sedang_dihubungi' WHERE Id=$pid AND Status='menunggu_verifikasi_admin'");
    $conn->query("UPDATE Pembayaran SET status='Terverifikasi' WHERE pesanan_id=$pid");
    header("Location: admin_verifikasi.php?msg=terima");
    exit();
}

// Handle aksi TOLAK
if (isset($_POST['action']) && $_POST['action'] === 'tolak') {
    $pid = intval($_POST['pesanan_id']);
    // Update Pembayaran menjadi Ditolak
    $conn->query("UPDATE Pembayaran SET status='Ditolak' WHERE pesanan_id=$pid AND status='Menunggu Verifikasi'");
    // Kembalikan status Pesanan ke menunggu_pembayaran
    $conn->query("UPDATE Pesanan SET Status='menunggu_pembayaran' WHERE Id=$pid");
    header("Location: admin_verifikasi.php?msg=tolak");
    exit();
}

include '../includes/header.php';
?>

<div class="dashboard-wrapper">
    <aside class="sidebar">
        <div style="padding:0 8px 24px;border-bottom:1px solid rgba(255,255,255,0.1);margin-bottom:20px">
            <div style="width:52px;height:52px;background:var(--primary);border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:1.4rem;margin-bottom:10px">👑</div>
            <div style="color:#fff;font-weight:700"><?= htmlspecialchars($_SESSION['nama']) ?></div>
            <div style="color:rgba(255,255,255,0.5);font-size:0.78rem">Administrator</div>
        </div>
        <nav class="sidebar-menu">
            <a href="admin_dashboard.php"><span>🏠</span> Dashboard</a>
            <a href="admin_verifikasi.php" class="active"><span>💳</span> Verifikasi Pembayaran</a>
            <a href="admin_pesanan.php"><span>📋</span> Kelola Pesanan</a>
            <a href="admin_companion.php"><span>🤝</span> Kelola Companion</a>
            <a href="admin_users.php"><span>👥</span> User</a>
            <div class="sidebar-title">Akun</div>
            <a href="logout.php"><span>🚪</span> Keluar</a>
        </nav>
    </aside>
    
    <main class="main-content">
        <div class="page-title">Verifikasi Pembayaran 💳</div>
        <div class="page-subtitle">Periksa bukti transfer dan konfirmasi pembayaran customer.</div>
        
        <?php if (isset($_GET['msg'])): ?>
            <?php if ($_GET['msg'] === 'terima'): ?>
                <div class="alert alert-success">Pembayaran diterima. Status pesanan diperbarui.</div>
            <?php elseif ($_GET['msg'] === 'tolak'): ?>
                <div class="alert alert-danger">Pembayaran ditolak. Pesanan dikembalikan ke status menunggu pembayaran.</div>
            <?php endif; ?>
        <?php endif; ?>

        <?php 
        // Jika sedang melihat detail satu pesanan
        if (isset($_GET['id'])): 
            $pid = intval($_GET['id']);
            $query = "
            SELECT p.*, 
                   u.nama as customer_nama, 
                   c_u.nama as companion_nama, 
                   pmb.bukti_transfer
            FROM Pesanan p
            JOIN users u ON p.User_id = u.id
            JOIN Companions c ON p.Companion_id = c.Id
            JOIN users c_u ON c.User_id = c_u.id
            JOIN Pembayaran pmb ON p.Id = pmb.pesanan_id
            WHERE p.Id = $pid AND p.Status = 'menunggu_verifikasi_admin'
            ";
            $detail = $conn->query($query)->fetch_assoc();
            
            if ($detail):
        ?>
            <div class="card" style="margin-top:24px; max-width: 600px;">
                <div class="card-header-bar">
                    <h3>Detail Verifikasi #<?= $detail['Id'] ?></h3>
                    <a href="admin_verifikasi.php" class="btn btn-outline btn-sm">Kembali</a>
                </div>
                <div class="card-body">
                    <p><strong>Customer:</strong> <?= htmlspecialchars($detail['customer_nama']) ?></p>
                    <p><strong>Companion:</strong> <?= htmlspecialchars($detail['companion_nama']) ?></p>
                    <p><strong>Total:</strong> <?= formatRupiah($detail['Total_harga']) ?></p>
                    
                    <hr style="margin: 20px 0; border-color: var(--border);">
                    
                    <h4 style="margin-bottom: 10px;">Bukti Transfer:</h4>
                    <div style="text-align: center; background: #f8fafc; padding: 10px; border-radius: 8px; border: 1px solid #e2e8f0; margin-bottom: 20px;">
                        <?php if (pathinfo($detail['bukti_transfer'], PATHINFO_EXTENSION) === 'pdf'): ?>
                            <a href="../uploads/<?= htmlspecialchars($detail['bukti_transfer']) ?>" target="_blank" class="btn btn-primary">Lihat Dokumen PDF</a>
                        <?php else: ?>
                            <img src="../uploads/<?= htmlspecialchars($detail['bukti_transfer']) ?>" alt="Bukti Transfer" style="max-width: 100%; max-height: 400px; border-radius: 4px;">
                        <?php endif; ?>
                    </div>
                    
                    <form method="POST" style="display: flex; gap: 10px;">
                        <input type="hidden" name="pesanan_id" value="<?= $detail['Id'] ?>">
                        <button type="submit" name="action" value="terima" class="btn btn-primary" style="flex: 1;" onclick="return confirm('Terima pembayaran ini?');">✅ Terima</button>
                        <button type="submit" name="action" value="tolak" class="btn btn-danger" style="flex: 1;" onclick="return confirm('Tolak pembayaran ini? Customer akan diminta upload ulang.');">❌ Tolak</button>
                    </form>
                </div>
            </div>
            
        <?php 
            else: 
                echo '<div class="alert alert-warning">Data tidak ditemukan atau sudah diverifikasi. <a href="admin_verifikasi.php">Kembali</a></div>';
            endif;
        else: 
            // Tampilan List Menunggu Verifikasi
            $query_list = "
            SELECT p.*, 
                   u.nama as customer_nama, 
                   c_u.nama as companion_nama, 
                   pmb.bukti_transfer
            FROM Pesanan p
            JOIN users u ON p.User_id = u.id
            JOIN Companions c ON p.Companion_id = c.Id
            JOIN users c_u ON c.User_id = c_u.id
            JOIN Pembayaran pmb ON p.Id = pmb.pesanan_id
            WHERE p.Status = 'menunggu_verifikasi_admin' AND pmb.status = 'Menunggu Verifikasi'
            ORDER BY pmb.created_at DESC
            ";
            $list_verif = $conn->query($query_list);
        ?>
        
            <div class="card" style="margin-top:24px;">
                <div class="card-header-bar">
                    <h3>Antrean Verifikasi</h3>
                </div>
                <div class="table-wrapper">
                    <?php if ($list_verif->num_rows > 0): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Customer</th>
                                <th>Companion</th>
                                <th>Total</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($v = $list_verif->fetch_assoc()): ?>
                            <tr>
                                <td>#<?= $v['Id'] ?></td>
                                <td><strong><?= htmlspecialchars($v['customer_nama']) ?></strong></td>
                                <td><?= htmlspecialchars($v['companion_nama']) ?></td>
                                <td><strong><?= formatRupiah($v['Total_harga']) ?></strong></td>
                                <td>
                                    <a href="admin_verifikasi.php?id=<?= $v['Id'] ?>" class="btn btn-primary btn-sm">Periksa Bukti</a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                    <?php else: ?>
                        <div class="empty-state">
                            <div class="icon">✨</div>
                            <h3>Semua pembayaran sudah diverifikasi</h3>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

        <?php endif; ?>
        
    </main>
</div>

<?php include '../includes/footer.php'; ?>
