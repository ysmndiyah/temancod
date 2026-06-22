<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';
if (!isLoggedIn() || !isCompanion()) redirect('pages/login.php');
$pageTitle = 'Dashboard Companion';
$user_id = $_SESSION['user_id'];
$companion = $conn->query("SELECT * FROM Companions WHERE User_id=$user_id")->fetch_assoc();
$companion_id = $companion['Id'];
if (isset($_GET['action']) && isset($_GET['pid'])) {
    $pid = intval($_GET['pid']); $action = $_GET['action'];
    $map = ['terima'=>'diterima','mulai'=>'berjalan','selesai'=>'selesai','tolak'=>'dibatalkan'];
    if (isset($map[$action])) $conn->query("UPDATE Pesanan SET Status='{$map[$action]}' WHERE Id=$pid AND Companion_id=$companion_id");
    header("Location: companion_dashboard.php"); exit();
}
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['update_profil'])) {
    $deskripsi = sanitize($_POST['deskripsi']); $harga = floatval($_POST['harga_per_jam']);
    $lokasi = sanitize($_POST['lokasi']); $kota = sanitize($_POST['kota']);
    $status = in_array($_POST['status'],['aktif','nonaktif']) ? $_POST['status'] : 'aktif';
    $conn->query("UPDATE Companions SET Deskripsi='$deskripsi', Harga_per_jam=$harga, Lokasi='$lokasi', Kota='$kota', Status='$status' WHERE Id=$companion_id");
    header("Location: companion_dashboard.php?msg=updated"); exit();
}
$total = $conn->query("SELECT COUNT(*) as c FROM Pesanan WHERE Companion_id=$companion_id")->fetch_assoc()['c'];
$selesai = $conn->query("SELECT COUNT(*) as c FROM Pesanan WHERE Companion_id=$companion_id AND Status='selesai'")->fetch_assoc()['c'];
$pending = $conn->query("SELECT COUNT(*) as c FROM Pesanan WHERE Companion_id=$companion_id AND Status='menunggu'")->fetch_assoc()['c'];
$pendapatan = $conn->query("SELECT COALESCE(SUM(Total_harga),0) as s FROM Pesanan WHERE Companion_id=$companion_id AND Status='selesai'")->fetch_assoc()['s'];
$pesanan = $conn->query("SELECT p.*, u.nama as nama_user, u.no_hp FROM Pesanan p JOIN users u ON p.User_id=u.id WHERE p.Companion_id=$companion_id ORDER BY p.Created_at DESC LIMIT 20");
$companion = $conn->query("SELECT * FROM Companions WHERE User_id=$user_id")->fetch_assoc();
include '../includes/header.php';
?>
<div class="dashboard-wrapper">
    <aside class="sidebar">
        <div style="padding:0 8px 24px;border-bottom:1px solid rgba(255,255,255,0.1);margin-bottom:20px">
            <div style="width:52px;height:52px;background:var(--primary);border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:1.4rem;margin-bottom:10px"><?= strtoupper(substr($_SESSION['nama'],0,1)) ?></div>
            <div style="color:#fff;font-weight:700"><?= htmlspecialchars($_SESSION['nama']) ?></div>
            <div style="color:rgba(255,255,255,0.5);font-size:0.78rem">Companion</div>
        </div>
        <nav class="sidebar-menu">
            <a href="companion_dashboard.php" class="active"><span>🏠</span> Dashboard</a>
            <a href="#pesanan"><span>📋</span> Pesanan Masuk</a>
            <a href="#profil"><span>✏️</span> Edit Profil</a>
            <div class="sidebar-title">Akun</div>
            <a href="logout.php"><span>🚪</span> Keluar</a>
        </nav>
    </aside>
    <main class="main-content">
        <div class="page-title">Dashboard Companion 🤝</div>
        <div class="page-subtitle">Kelola pesanan dan profil kamu.</div>
        <?php if (isset($_GET['msg']) && $_GET['msg']==='updated'): ?><div class="alert alert-success">Profil berhasil diperbarui!</div><?php endif; ?>
        <div class="stats-row">
            <div class="stat-card"><div class="icon">📋</div><div class="num"><?= $total ?></div><div class="label">Total Pesanan</div></div>
            <div class="stat-card"><div class="icon">⏳</div><div class="num"><?= $pending ?></div><div class="label">Menunggu</div></div>
            <div class="stat-card"><div class="icon">✅</div><div class="num"><?= $selesai ?></div><div class="label">Selesai</div></div>
            <div class="stat-card"><div class="icon">💰</div><div class="num" style="font-size:1.2rem"><?= formatRupiah($pendapatan) ?></div><div class="label">Pendapatan</div></div>
        </div>
        <div class="card" id="pesanan" style="margin-bottom:28px">
            <div class="card-header-bar"><h3>📋 Pesanan Masuk</h3><span class="badge badge-menunggu"><?= $pending ?> Menunggu</span></div>
            <div class="table-wrapper">
                <?php if ($pesanan->num_rows > 0): ?>
                <table>
                    <thead><tr><th>Pemesan</th><th>Tanggal</th><th>Lokasi</th><th>Keperluan</th><th>Total</th><th>Status</th><th>Aksi</th></tr></thead>
                    <tbody>
                        <?php while ($p=$pesanan->fetch_assoc()): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($p['nama_user']) ?></strong><br><?php if($p['no_hp']): ?><a href="https://wa.me/62<?= ltrim($p['no_hp'],'0') ?>" target="_blank" style="color:var(--success);font-size:0.8rem">📱 WA</a><?php endif; ?></td>
                            <td><?= date('d M Y',strtotime($p['Tanggal_jemput'])) ?><br><small><?= substr($p['Jam_mulai'],0,5) ?> · <?= $p['Durasi_jam'] ?> jam</small></td>
                            <td style="font-size:0.85rem"><?= htmlspecialchars($p['Lokasi_jemput']) ?></td>
                            <td style="font-size:0.83rem;color:var(--text-muted)"><?= htmlspecialchars(substr($p['Keperluan']??'-',0,50)) ?></td>
                            <td><strong><?= formatRupiah($p['Total_harga']) ?></strong></td>
                            <td><span class="badge badge-<?= $p['Status'] ?>"><?= ucfirst($p['Status']) ?></span></td>
                            <td>
                                <?php if ($p['Status']==='menunggu'): ?>
                                    <a href="?action=terima&pid=<?= $p['Id'] ?>" class="btn btn-success btn-sm" data-confirm="Terima pesanan?">Terima</a>
                                    <a href="?action=tolak&pid=<?= $p['Id'] ?>" class="btn btn-danger btn-sm" data-confirm="Tolak pesanan?" style="margin-top:4px">Tolak</a>
                                <?php elseif ($p['Status']==='diterima'): ?>
                                    <a href="?action=mulai&pid=<?= $p['Id'] ?>" class="btn btn-primary btn-sm" data-confirm="Mulai perjalanan?">Mulai</a>
                                <?php elseif ($p['Status']==='berjalan'): ?>
                                    <a href="?action=selesai&pid=<?= $p['Id'] ?>" class="btn btn-dark btn-sm" data-confirm="Tandai selesai?">Selesai</a>
                                <?php else: ?><span style="color:var(--text-muted)">—</span><?php endif; ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
                <?php else: ?><div class="empty-state"><div class="icon">📬</div><h3>Belum ada pesanan</h3></div><?php endif; ?>
            </div>
        </div>
        <div class="card" id="profil">
            <div class="card-header-bar"><h3>✏️ Edit Profil</h3></div>
            <div class="card-body">
                <form method="POST">
                    <input type="hidden" name="update_profil" value="1">
                    <div class="form-row">
                        <div class="form-group"><label class="form-label">Kota</label><input type="text" name="kota" class="form-control" value="<?= htmlspecialchars($companion['Kota']??'') ?>" required></div>
                        <div class="form-group"><label class="form-label">Lokasi Detail</label><input type="text" name="lokasi" class="form-control" value="<?= htmlspecialchars($companion['Lokasi']??'') ?>"></div>
                    </div>
                    <div class="form-row">
                        <div class="form-group"><label class="form-label">Harga per Jam (Rp)</label><input type="number" name="harga_per_jam" class="form-control" value="<?= $companion['Harga_per_jam'] ?>" min="10000" step="5000" required></div>
                        <div class="form-group">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-control">
                                <option value="aktif" <?= $companion['Status']==='aktif'?'selected':'' ?>>● Aktif</option>
                                <option value="nonaktif" <?= $companion['Status']==='nonaktif'?'selected':'' ?>>○ Nonaktif</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group"><label class="form-label">Deskripsi</label><textarea name="deskripsi" class="form-control" rows="4"><?= htmlspecialchars($companion['Deskripsi']??'') ?></textarea></div>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </form>
            </div>
        </div>
    </main>
</div>
<?php include '../includes/footer.php'; ?>