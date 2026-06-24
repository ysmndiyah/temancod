<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('pages/login.php');
}

$pageTitle = 'Dashboard Admin';

// Hitung Statistik
$total_user = $conn->query("SELECT COUNT(*) as c FROM users")->fetch_assoc()['c'];
$total_pendapatan = $conn->query("SELECT COALESCE(SUM(Total_harga),0) as sum FROM Pesanan WHERE Status='selesai'")->fetch_assoc()['sum'];
$menunggu_pembayaran = $conn->query("SELECT COUNT(*) as c FROM Pembayaran WHERE status='Menunggu Verifikasi'")->fetch_assoc()['c'];
$menunggu_verif = $conn->query("SELECT COUNT(*) as c FROM Pesanan WHERE Status='menunggu_verifikasi_admin'")->fetch_assoc()['c'];
$diproses = $conn->query("SELECT COUNT(*) as c FROM Pesanan WHERE Status IN ('companion_sedang_dihubungi', 'diterima_companion', 'berjalan')")->fetch_assoc()['c'];
$selesai = $conn->query("SELECT COUNT(*) as c FROM Pesanan WHERE Status='selesai'")->fetch_assoc()['c'];
$total_companion = $conn->query("SELECT COUNT(*) as c FROM Companions")->fetch_assoc()['c'];

// Ambil Pesanan Terbaru (5 data terakhir)
$query = "
SELECT p.*, 
       u.nama as customer_nama, 
       c_u.nama as companion_nama, 
       c_u.no_hp as companion_wa
FROM Pesanan p
JOIN users u ON p.User_id = u.id
JOIN Companions c ON p.Companion_id = c.Id
JOIN users c_u ON c.User_id = c_u.id
ORDER BY p.Created_at DESC
LIMIT 5
";
$pesanan = $conn->query($query);

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
            <a href="admin_dashboard.php" class="active"><span>🏠</span> Dashboard</a>
            <a href="admin_verifikasi.php"><span>💳</span> Verifikasi Pembayaran</a>
            <a href="admin_pesanan.php"><span>📋</span> Kelola Pesanan</a>
            <a href="admin_companion.php"><span>🤝</span> Kelola Companion</a>
            <a href="admin_users.php"><span>👥</span> User</a>
            <div class="sidebar-title">Akun</div>
            <a href="logout.php"><span>🚪</span> Keluar</a>
        </nav>
    </aside>
    
    <main class="main-content">
        <div class="page-title">Dashboard Admin ⚙️</div>
        <div class="page-subtitle">Ringkasan statistik dan pesanan terbaru.</div>
        
        <div class="stats-row">
            <div class="stat-card">
                <div class="icon">👤</div>
                <div class="num"><?= $total_user ?></div>
                <div class="label">Total User</div>
            </div>
            <div class="stat-card">
                <div class="icon">💰</div>
                <div class="num"><?= formatRupiah($total_pendapatan) ?></div>
                <div class="label">Total Pendapatan</div>
            </div>
            <div class="stat-card">
                <div class="icon">⏳</div>
                <div class="num"><?= $menunggu_pembayaran ?></div>
                <div class="label">Pembayaran Menunggu Verifikasi</div>
            </div>
            <div class="stat-card"><div class="icon">💳</div><div class="num"><?= $menunggu_verif ?></div><div class="label">Menunggu Verifikasi</div></div>
            <div class="stat-card"><div class="icon">⏳</div><div class="num"><?= $diproses ?></div><div class="label">Sedang Diproses</div></div>
            <div class="stat-card"><div class="icon">✅</div><div class="num"><?= $selesai ?></div><div class="label">Selesai</div></div>
            <div class="stat-card"><div class="icon">🤝</div><div class="num"><?= $total_companion ?></div><div class="label">Total Companion</div></div>
        </div>
        
        <div class="card" style="margin-top:24px;">
            <div class="card-header-bar">
                <h3>Pesanan Terbaru</h3>
                <a href="admin_pesanan.php" class="btn btn-outline btn-sm">Lihat Semua</a>
            </div>
            <div class="table-wrapper">
                <?php if ($pesanan->num_rows > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Customer</th>
                            <th>Companion</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($p = $pesanan->fetch_assoc()): ?>
                        <tr>
                            <td data-label="ID">#<?= $p['Id'] ?></td>
                            <td data-label="Customer"><strong><?= htmlspecialchars($p['customer_nama']) ?></strong></td>
                            <td data-label="Companion"><strong><?= htmlspecialchars($p['companion_nama']) ?></strong></td>
                            <td data-label="Total"><strong><?= formatRupiah($p['Total_harga']) ?></strong></td>
                            <td data-label="Status">
                                <?php 
                                $statusLabel = ucwords(str_replace('_', ' ', $p['Status']));
                                if ($p['Status'] === 'diterima_companion') $statusLabel = 'Diterima Companion';
                                ?>
                                <span class="badge badge-<?= $p['Status'] ?>"><?= $statusLabel ?></span>
                            </td>
                            <td data-label="Aksi">
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
                        <div class="icon">📋</div>
                        <h3>Belum ada pesanan masuk</h3>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>
</div>

<?php include '../includes/footer.php'; ?>
