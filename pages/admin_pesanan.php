<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('pages/login.php');
}

$pageTitle = 'Kelola Pesanan';
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
            <a href="admin_pesanan.php" class="active"><span>📋</span> Kelola Pesanan</a>
            <a href="admin_companion.php"><span>🤝</span> Kelola Companion</a>
            <a href="admin_users.php"><span>👥</span> User</a>
            <div class="sidebar-title">Akun</div>
            <a href="logout.php"><span>🚪</span> Keluar</a>
        </nav>
    </aside>
    
    <main class="main-content">
        <div class="page-title">Kelola Pesanan 📋</div>
        <div class="page-subtitle">Daftar semua pesanan di TemanCOD.</div>
        
        <div class="card" style="margin-top:24px;">
    <div class="card-header-bar">
        <h3>Daftar Pesanan</h3>
        <form method="GET" style="display:flex;gap:8px;align-items:center;">
            <label for="status_filter" style="font-weight:600;">Filter Status:</label>
            <select name="status" id="status_filter" onchange="this.form.submit()" class="select">
                <option value="" <?php if (!isset($_GET['status'])) echo 'selected'; ?>>Semua</option>
                <?php
                $statusEnum = ['menunggu_pembayaran','menunggu_verifikasi_admin','companion_sedang_dihubungi','diterima_companion','berjalan','selesai','dibatalkan'];
                foreach ($statusEnum as $s) {
                    $selected = (isset($_GET['status']) && $_GET['status'] === $s) ? 'selected' : '';
                    echo "<option value=\"$s\" $selected>" . ucwords(str_replace('_', ' ', $s)) . "</option>";
                }
                ?>
            </select>
        </form>
    </div>
    <div class="table-wrapper">
        <?php
        $filter = '';
        if (!empty($_GET['status'])) {
            $status = $conn->real_escape_string($_GET['status']);
            $filter = "WHERE p.Status = '$status'";
        }
        $sql = "SELECT p.*, u.nama AS customer_nama, cu.nama AS companion_nama, cu.no_hp AS companion_wa FROM Pesanan p
                JOIN users u ON p.User_id = u.id
                JOIN Companions c ON p.Companion_id = c.Id
                JOIN users cu ON c.User_id = cu.id $filter ORDER BY p.Created_at DESC";
        $result = $conn->query($sql);
        if ($result && $result->num_rows > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Customer</th>
                    <th>Companion</th>
                    <th>Tanggal</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($p = $result->fetch_assoc()): 
                    // Format date and total using helpers
                    $dateFormatted = formatIndonesianDate($p['Tanggal_jemput']);
                    $totalFormatted = formatRupiah($p['Total_harga']);
                    $badgeClass = getBadgeClass($p['Status']);
                ?>
                <tr>
                    <td>#<?= $p['Id'] ?></td>
                    <td><strong><?= htmlspecialchars($p['customer_nama']) ?></strong></td>
                    <td><strong><?= htmlspecialchars($p['companion_nama']) ?></strong></td>
                    <td><?= $dateFormatted ?></td>
                    <td><strong><?= $totalFormatted ?></strong></td>
                    <td><span class="badge <?= $badgeClass ?>"><?= ucwords(str_replace('_', ' ', $p['Status'])) ?></span></td>
                    <td>
                        <a href="admin_detail_pesanan.php?id=<?= $p['Id'] ?>" class="btn btn-outline btn-sm">Detail</a>
                        <?php if ($p['Status'] === 'menunggu_verifikasi_admin'): ?>
                        <a href="admin_verifikasi.php?id=<?= $p['Id'] ?>" class="btn btn-primary btn-sm">Verifikasi Pembayaran</a>
                        <?php endif; ?>
                        <?php if ($p['Status'] === 'companion_sedang_dihubungi' && !empty($p['companion_wa'])): ?>
                        <a href="https://wa.me/62<?= ltrim($p['companion_wa'], '0') ?>" target="_blank" class="btn btn-success btn-sm" style="background:#25D366;border:none">Hubungi Companion</a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <?php else: ?>
        <div class="empty-state">
            <div class="icon">📭</div>
            <h3>Belum ada data</h3>
        </div>
<?php endif; ?>
    </div>
</div>
    </main>
</div>

<?php include '../includes/footer.php'; ?>
