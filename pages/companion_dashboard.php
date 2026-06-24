<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';
if (!isLoggedIn() || !isCompanion()) {
    redirect('pages/login.php');
}

$pageTitle = 'Dashboard Companion';
$user_id = $_SESSION['user_id'];

$companion = $conn->query("SELECT * FROM Companions WHERE User_id=$user_id")->fetch_assoc();
if (!$companion) {
    redirect('pages/dashboard.php');
}
$companion_id = $companion['Id'];

if (isset($_GET['action'], $_GET['pid'])) {
    $pid = intval($_GET['pid']);
    $action = $_GET['action'];
    if ($action === 'accept') {
        $conn->query("UPDATE Pesanan SET Status='berjalan' WHERE Id=$pid AND Companion_id=$companion_id");
    } elseif ($action === 'reject') {
        $conn->query("UPDATE Pesanan SET Status='dibatalkan' WHERE Id=$pid AND Companion_id=$companion_id");
    } elseif (in_array($action, ['mulai', 'selesai'], true)) {
        $map = ['mulai' => 'berjalan', 'selesai' => 'selesai'];
        $conn->query("UPDATE Pesanan SET Status='{$map[$action]}' WHERE Id=$pid AND Companion_id=$companion_id");
    }
    header('Location: companion_dashboard.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profil'])) {
    $deskripsi = sanitize($_POST['deskripsi']);
    $harga = floatval($_POST['harga_per_jam']);
    $lokasi = sanitize($_POST['lokasi']);
    $kota = sanitize($_POST['kota']);
    $status = in_array($_POST['status'], ['aktif', 'nonaktif']) ? $_POST['status'] : 'aktif';
    $conn->query("UPDATE Companions SET Deskripsi='$deskripsi', Harga_per_jam=$harga, Lokasi='$lokasi', Kota='$kota', Status='$status' WHERE Id=$companion_id");
    header('Location: companion_dashboard.php?msg=updated');
    exit();
}

$total = $conn->query("SELECT COUNT(*) as c FROM Pesanan WHERE Companion_id=$companion_id")->fetch_assoc()['c'];
$active = $conn->query("SELECT COUNT(*) as c FROM Pesanan WHERE Companion_id=$companion_id AND Status='berjalan'")->fetch_assoc()['c'];
$selesai = $conn->query("SELECT COUNT(*) as c FROM Pesanan WHERE Companion_id=$companion_id AND Status='selesai'")->fetch_assoc()['c'];
$pending = $conn->query("SELECT COUNT(*) as c FROM Pesanan WHERE Companion_id=$companion_id AND Status='diterima_companion'")->fetch_assoc()['c'];
$rating = $companion['Rating'] ?? 0;
$pendapatan = $conn->query("SELECT COALESCE(SUM(Total_harga),0) as s FROM Pesanan WHERE Companion_id=$companion_id AND Status='selesai'")->fetch_assoc()['s'];
$pesanan = $conn->query("SELECT p.*, u.nama as nama_user, u.no_hp FROM Pesanan p JOIN users u ON p.User_id=u.id WHERE p.Companion_id=$companion_id ORDER BY p.Created_at DESC LIMIT 20");
$history = $conn->query("SELECT p.*, u.nama as nama_user FROM Pesanan p JOIN users u ON p.User_id=u.id WHERE p.Companion_id=$companion_id AND p.Status IN ('selesai','dibatalkan') ORDER BY p.Created_at DESC LIMIT 20");

include '../includes/header.php';
?>
<div class="dashboard-wrapper dashboard-companion">
    <aside class="sidebar" style="background: linear-gradient(135deg, #4C1D95 0%, #7C3AED 100%);">
        <div class="dashboard-companion-drawer-header">
            <div style="width:52px;height:52px;background:linear-gradient(135deg,#fff,#d8b4fe);border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:1.4rem;margin-bottom:10px;color:#6D28D9"><?= strtoupper(substr($_SESSION['nama'],0,1)) ?></div>
            <div>
                <div style="color:#fff;font-weight:700"><?= htmlspecialchars($_SESSION['nama']) ?></div>
                <div style="color:rgba(255,255,255,0.6);font-size:0.78rem">Companion</div>
            </div>
            <button type="button" class="dashboard-companion-close" aria-label="Tutup menu">✕</button>
        </div>
        <nav class="sidebar-menu">
            <a href="companion_dashboard.php" class="active"><span>🏠</span> Dashboard</a>
            <a href="#pesanan"><span>📋</span> Pesanan</a>
            <a href="#profil"><span>✏️</span> Edit Profil</a>
            <div class="sidebar-title">Akun</div>
            <a href="logout.php"><span>🚪</span> Keluar</a>
        </nav>
    </aside>
    <main class="main-content">
        <?php
        if (isset($_GET['msg'])) {
            $msg = $_GET['msg'];
            $msgMap = [
                'accepted' => 'Pesanan berhasil diterima.',
                'rejected' => 'Pesanan berhasil ditolak.',
                'updated' => 'Profil berhasil diperbarui.',
            ];
            $alertMessage = $msgMap[$msg] ?? '';
        }
        ?>
        <?php if (!empty($alertMessage)): ?>
        <div class="alert alert-success">&#10003; <?= htmlspecialchars($alertMessage) ?></div>
        <?php endif; ?>
        <div class="dashboard-companion-mobile-topbar">
            <button type="button" class="dashboard-companion-toggle" aria-label="Buka menu companion" aria-expanded="false">☰</button>
            <div class="dashboard-companion-mobile-text">
                <div class="dashboard-companion-mobile-eyebrow">Dashboard Companion</div>
                <div class="dashboard-companion-mobile-title">Area Kerja</div>
            </div>
            <div class="dashboard-companion-mobile-badge">Online</div>
        </div>
        <div class="welcome-card companion-welcome-card">
            <div class="welcome-copy">
                <div class="eyebrow">⚡ Mode Companion</div>
                <h2>Halo, <?= htmlspecialchars(explode(' ', $_SESSION['nama'])[0]) ?>! 🤝</h2>
                <p>Kelola pesanan, tanggapi customer lebih cepat, dan jaga profil layanan agar lebih menarik.</p>
            </div>
            <div class="welcome-actions">
                <a href="#pesanan" class="companion-cta">Lihat Pesanan</a>
                <a href="#profil" class="companion-cta secondary">Edit Profil</a>
            </div>
        </div>
        <div class="page-title">Area kerja companion</div>
        <div class="page-subtitle">Pantau pesanan, respon customer, dan kelola profil layanan kamu.</div>
        <div class="stats-row">
            <div class="stat-card companion-stat-card"><div class="icon">📋</div><div class="num"><?= $total ?></div><div class="label">Total Pesanan</div></div>
            <div class="stat-card companion-stat-card"><div class="icon">⏳</div><div class="num"><?= $pending ?></div><div class="label">Menunggu Respons</div></div>
            <div class="stat-card companion-stat-card"><div class="icon">🚚</div><div class="num"><?= $active ?></div><div class="label">Pesanan Aktif</div></div>
            <div class="stat-card companion-stat-card"><div class="icon">✅</div><div class="num"><?= $selesai ?></div><div class="label">Selesai</div></div>
            <div class="stat-card companion-stat-card"><div class="icon">⭐</div><div class="num"><?= number_format($rating, 1) ?></div><div class="label">Rating</div></div>
            <div class="stat-card companion-stat-card"><div class="icon">💰</div><div class="num" style="font-size:1.2rem"><?= formatRupiah($pendapatan) ?></div><div class="label">Pendapatan</div></div>
        </div>
        <div class="card" id="pesanan" style="margin-bottom:28px">
            <div class="card-header-bar"><h3>📋 Pesanan Masuk & Aktif</h3><span class="badge badge-menunggu"><?= $pending ?> Menunggu</span></div>
            <div class="table-wrapper">
                <?php if ($pesanan->num_rows > 0): ?>
                <table>
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Customer</th>
                        <th>Tanggal</th>
                        <th>Jam</th>
                        <th>Lokasi</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php while ($p = $pesanan->fetch_assoc()): ?>
                    <tr>
                        <td data-label="ID"><?= $p['Id'] ?></td>
                        <td data-label="Customer"><?= htmlspecialchars($p['nama_user']) ?></td>
                        <td data-label="Tanggal"><?= date('d M Y', strtotime($p['Tanggal_jemput'])) ?></td>
                        <td data-label="Jam"><?= substr($p['Jam_mulai'], 0, 5) ?></td>
                        <td data-label="Lokasi"><?= htmlspecialchars($p['Tujuan'] ?? '-') ?></td>
                        <td data-label="Status"><span class="badge badge-<?= $p['Status'] ?>"><?= ucwords(str_replace('_', ' ', $p['Status'])) ?></span></td>
                        <td data-label="Aksi">
                            <div class="table-actions">
                                <?php if ($p['Status'] === 'diterima_companion'): ?>
                                    <a href="?action=accept&pid=<?= $p['Id'] ?>" class="btn btn-success btn-sm" data-confirm="Terima order?">Terima</a>
                                    <a href="?action=reject&pid=<?= $p['Id'] ?>" class="btn btn-danger btn-sm" data-confirm="Tolak order?">Tolak</a>
                                <?php elseif ($p['Status'] === 'berjalan'): ?>
                                    <a href="?action=selesai&pid=<?= $p['Id'] ?>" class="btn btn-dark btn-sm" data-confirm="Tandai selesai?">Selesai</a>
                                <?php endif; ?>
                                <?php if (!empty($p['no_hp'])): ?>
                                    <a href="https://wa.me/62<?= ltrim($p['no_hp'], '0') ?>" target="_blank" class="btn btn-primary btn-sm">Hubungi</a>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                    </tbody>
                </table>
                <?php else: ?>
                <div class="empty-state"><div class="icon">📬</div><h3>Belum ada pesanan</h3></div>
                <?php endif; ?>
            </div>
        </div>
        <div class="card" id="riwayat" style="margin-top:28px">
            <div class="card-header-bar"><h3>📜 Riwayat Pesanan</h3></div>
            <div class="table-wrapper">
                <?php if ($history->num_rows > 0): ?>
                <table>
                    <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Customer</th>
                        <th>Total Harga</th>
                        <th>Status</th>
                        <th>Rating</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php while ($h = $history->fetch_assoc()): ?>
                    <tr>
                        <td data-label="Tanggal"><?= date('d M Y', strtotime($h['Tanggal_jemput'])) ?></td>
                        <td data-label="Customer"><?= htmlspecialchars($h['nama_user']) ?></td>
                        <td data-label="Total Harga"><?= formatRupiah($h['Total_harga']) ?></td>
                        <td data-label="Status"><span class="badge badge-<?= $h['Status'] ?>"><?= ucwords(str_replace('_', ' ', $h['Status'])) ?></span></td>
                        <td data-label="Rating"><?= $h['Rating'] ?? '-' ?></td>
                    </tr>
                    <?php endwhile; ?>
                    </tbody>
                </table>
                <?php else: ?>
                <div class="empty-state"><div class="icon">📭</div><h3>Tidak ada riwayat</h3></div>
                <?php endif; ?>
            </div>
        </div>
        <div class="card" id="profil" style="margin-top:28px">
            <div class="card-header-bar"><h3>✏️ Edit Profil Companion</h3></div>
            <div class="card-body">
                <form method="POST">
                    <input type="hidden" name="update_profil" value="1">
                    <div class="form-row">
                        <div class="form-group"><label class="form-label">Kota</label><input type="text" name="kota" class="form-control" value="<?= htmlspecialchars($companion['Kota'] ?? '') ?>" required></div>
                        <div class="form-group"><label class="form-label">Lokasi Detail</label><input type="text" name="lokasi" class="form-control" value="<?= htmlspecialchars($companion['Lokasi'] ?? '') ?>"></div>
                    </div>
                    <div class="form-row">
                        <div class="form-group"><label class="form-label">Harga per Jam (Rp)</label><input type="number" name="harga_per_jam" class="form-control" value="<?= $companion['Harga_per_jam'] ?>" min="10000" step="5000" required></div>
                        <div class="form-group">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-control">
                                <option value="aktif" <?= $companion['Status'] === 'aktif' ? 'selected' : '' ?>>● Aktif</option>
                                <option value="nonaktif" <?= $companion['Status'] === 'nonaktif' ? 'selected' : '' ?>>○ Nonaktif</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group"><label class="form-label">Deskripsi</label><textarea name="deskripsi" class="form-control" rows="4"><?= htmlspecialchars($companion['Deskripsi'] ?? '') ?></textarea></div>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </form>
            </div>
        </div>
    </main>
</div>
<?php include '../includes/footer.php'; ?>