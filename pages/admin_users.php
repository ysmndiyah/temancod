<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('pages/login.php');
}

$pageTitle = 'Kelola User';
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
            <a href="admin_companion.php"><span>🤝</span> Kelola Companion</a>
            <a href="admin_users.php" class="active"><span>👥</span> User</a>
            <div class="sidebar-title">Akun</div>
            <a href="logout.php"><span>🚪</span> Keluar</a>
        </nav>
    </aside>
    
    <main class="main-content">
        <div class="page-title">Kelola User 👥</div>
        <div class="page-subtitle">Daftar semua pengguna terdaftar.</div>
<?php
    // Fetch all users
    $search = '';
    if (!empty($_GET['q'])) {
        $search = $conn->real_escape_string($_GET['q']);
        $sql = "SELECT * FROM users WHERE (nama LIKE '%$search%' OR email LIKE '%$search%') ORDER BY id DESC";
    } else {
        $sql = "SELECT * FROM users ORDER BY id DESC";
    }
    $result = $conn->query($sql);
?>
        <div class="card" style="margin-top:24px;">
            <div class="card-header-bar">
                <h3>Daftar User</h3>
                <form method="GET" style="margin-left:auto;">
                    <input type="text" name="q" placeholder="Cari nama atau email" value="<?= htmlspecialchars($search) ?>" class="input" style="padding:6px 12px;border-radius:4px;border:1px solid var(--border);"/>
                    <button type="submit" class="btn btn-primary btn-sm">Cari</button>
                </form>
            </div>
            <div class="table-wrapper">
                <?php if ($result && $result->num_rows > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Role</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($u = $result->fetch_assoc()): ?>
                        <tr>
                            <td>#<?= $u['id'] ?></td>
                            <td><?= htmlspecialchars($u['nama']) ?></td>
                            <td><?= htmlspecialchars($u['email']) ?></td>
                            <td><?= ucfirst($u['role']) ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
                <?php else: ?>
                <div class="empty-state"><div class="icon">📭</div><h3>Tidak ada user</h3></div>
                <?php endif; ?>
            </div>
        </div>
    </main>
</div>

<?php include '../includes/footer.php'; ?>
