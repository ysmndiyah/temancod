<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('pages/login.php');
}

$pageTitle = 'Kelola Companion';
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
            <a href="admin_verifikasi.php"><span>💳</span> Verifikasi Pembayaran</a>
            <a href="admin_pesanan.php"><span>📋</span> Kelola Pesanan</a>
            <a href="admin_companion.php" class="active"><span>🤝</span> Kelola Companion</a>
            <a href="admin_users.php"><span>👥</span> User</a>
            <div class="sidebar-title">Akun</div>
            <a href="logout.php"><span>🚪</span> Keluar</a>
        </nav>
    </aside>
    
    <main class="main-content">
        <div class="page-title">Kelola Companion 🤝</div>
        <div class="page-subtitle">Daftar semua companion terdaftar.</div>
<?php
    // Fetch companions with user info
    $sql = "SELECT c.*, u.nama AS companion_nama, u.foto FROM Companions c JOIN users u ON c.User_id = u.id ORDER BY c.Id DESC";
    $result = $conn->query($sql);
?>
            <div class="card-header-bar">
                <h3>Daftar Companion</h3>
            </div>
            <div class="table-wrapper">
                <?php if ($result && $result->num_rows > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Foto</th>
                            <th>Nama</th>
                            <th>Kota</th>
                            <th>Rating</th>
                            <th>Total Order</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($c = $result->fetch_assoc()): ?>
                        <tr>
                            <td><img src="../uploads/<?= htmlspecialchars($c['foto'] ?? 'default.png') ?>" alt="Foto" style="width:48px;height:48px;border-radius:50%;"></td>
                            <td><?= htmlspecialchars($c['companion_nama']) ?></td>
                            <td><?= htmlspecialchars($c['Kota']) ?></td>
                            <td><?= number_format($c['Rating'], 1) ?></td>
                            <td><?= $c['Total_Order'] ?></td>
                            <td><span class="badge <?= $c['Status'] === 'aktif' ? 'badge-green' : 'badge-red' ?>"><?= ucfirst($c['Status']) ?></span></td>
                            <td>
                                <?php if ($c['Status'] === 'aktif'): ?>
                                    <a href="admin_companion_toggle.php?id=<?= $c['Id'] ?>&action=deactivate" class="btn btn-outline btn-sm">Nonaktifkan</a>
                                <?php else: ?>
                                    <a href="admin_companion_toggle.php?id=<?= $c['Id'] ?>&action=activate" class="btn btn-outline btn-sm">Aktifkan</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
                <?php else: ?>
                <div class="empty-state"><div class="icon">📭</div><h3>Tidak ada companion</h3></div>
                <?php endif; ?>
            </div>
            </div>
        </div>
    </main>
</div>

<?php include '../includes/footer.php'; ?>
