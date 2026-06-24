<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';
if (!isLoggedIn()) redirect('pages/login.php');
$pageTitle = 'Dashboard';
$user_id = $_SESSION['user_id'];
$total = $conn->query("SELECT COUNT(*) as c FROM Pesanan WHERE User_id=$user_id")->fetch_assoc()['c'];
$selesai = $conn->query("SELECT COUNT(*) as c FROM Pesanan WHERE User_id=$user_id AND Status='selesai'")->fetch_assoc()['c'];
$berjalan = $conn->query("SELECT COUNT(*) as c FROM Pesanan WHERE User_id=$user_id AND Status IN ('menunggu_pembayaran','menunggu_verifikasi_admin','companion_sedang_dihubungi','diterima_companion','berjalan')")->fetch_assoc()['c'];
$pesanan = $conn->query("SELECT p.*, u.nama as companion_nama, u.no_hp as companion_wa FROM Pesanan p JOIN Companions c ON p.Companion_id=c.Id JOIN users u ON c.User_id=u.id WHERE p.User_id=$user_id ORDER BY p.Created_at DESC LIMIT 10");
if (isset($_GET['batal'])) {
    $pid = intval($_GET['batal']);
    $conn->query("UPDATE Pesanan SET Status='dibatalkan' WHERE Id=$pid AND User_id=$user_id AND Status='menunggu_pembayaran'");
    header("Location: dashboard.php"); exit();
}
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['review'])) {
    $pid = intval($_POST['pesanan_id']); $rating = intval($_POST['rating']);
    $kom = sanitize($_POST['komentar']); $cid = intval($_POST['companion_id']);
    $exists = $conn->query("SELECT Id FROM Reviews WHERE Pesanan_id=$pid")->num_rows;
    if (!$exists && $rating >= 1 && $rating <= 5) {
        $stmt = $conn->prepare("INSERT INTO Reviews (Pesanan_id, User_id, Companion_id, Rating, Komentar) VALUES (?,?,?,?,?)");
        $stmt->bind_param("iiiis", $pid, $user_id, $cid, $rating, $kom);
        $stmt->execute();
        $avg = $conn->query("SELECT AVG(Rating) as r FROM Reviews WHERE Companion_id=$cid")->fetch_assoc();
        $conn->query("UPDATE Companions SET Rating={$avg['r']} WHERE Id=$cid");
        header("Location: dashboard.php?msg=review"); exit();
    }
}
include '../includes/header.php';
?>
<div class="dashboard-wrapper">
    <aside class="sidebar">
        <div style="padding:0 8px 24px;border-bottom:1px solid rgba(255,255,255,0.1);margin-bottom:20px">
            <div style="width:52px;height:52px;background:var(--primary);border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:1.4rem;margin-bottom:10px"><?= strtoupper(substr($_SESSION['nama'],0,1)) ?></div>
            <div style="color:#fff;font-weight:700"><?= htmlspecialchars($_SESSION['nama']) ?></div>
            <div style="color:rgba(255,255,255,0.5);font-size:0.78rem">Pengguna</div>
        </div>
        <nav class="sidebar-menu">
            <a href="dashboard.php" class="active"><span>🏠</span> Dashboard</a>
            <a href="companions.php"><span>🔍</span> Cari Companion</a>
            <div class="sidebar-title">Akun</div>
            <a href="logout.php"><span>🚪</span> Keluar</a>
        </nav>
    </aside>
    <main class="main-content">
        <div class="welcome-card">
            <div>
                <div class="eyebrow">Dashboard kamu</div>
                <h2>Halo, <?= htmlspecialchars(explode(' ',$_SESSION['nama'])[0]) ?>! 👋</h2>
                <p>Semoga perjalanan kamu tetap nyaman. Berikut ringkasan pesanan dan tombol cepat untuk melanjutkan aktivitas.</p>
            </div>
            <div class="welcome-actions">
                <a href="companions.php">+ Pesan Baru</a>
                <a href="#riwayat-pesanan">Lihat Riwayat</a>
            </div>
        </div>
        <div class="page-title">Ringkasan aktivitas</div>
        <div class="page-subtitle">Kamu memiliki <?= $total ?> pesanan, dengan <?= $berjalan ?> pesanan sedang berjalan atau menunggu tindak lanjut.</div>
        <?php if (isset($_GET['msg']) && $_GET['msg']==='review'): ?><div class="alert alert-success">Ulasan berhasil dikirim!</div><?php endif; ?>
        <div class="stats-row">
            <div class="stat-card"><div class="icon">📋</div><div class="num"><?= $total ?></div><div class="label">Total Pesanan</div></div>
            <div class="stat-card"><div class="icon">✅</div><div class="num"><?= $selesai ?></div><div class="label">Selesai</div></div>
            <div class="stat-card"><div class="icon">⏳</div><div class="num"><?= $berjalan ?></div><div class="label">Berjalan</div></div>
        </div>
        <div class="card" id="riwayat-pesanan">
            <div class="card-header-bar"><h3>Riwayat Pesanan</h3><a href="companions.php" class="btn btn-primary btn-sm">+ Pesan Baru</a></div>
            <div class="table-wrapper">
                <?php if ($pesanan->num_rows > 0): ?>
                <table>
                    <thead><tr><th>#</th><th>Companion</th><th>Tanggal</th><th>Durasi</th><th>Total</th><th>Status</th><th>Aksi</th></tr></thead>
                    <tbody>
                        <?php $no=1; while ($p=$pesanan->fetch_assoc()): ?>
                        <tr>
                            <td data-label="#"><?= $no++ ?></td>
                            <td data-label="Companion"><strong><?= htmlspecialchars($p['companion_nama']) ?></strong></td>
                            <td data-label="Tanggal"><?= date('d M Y',strtotime($p['Tanggal_jemput'])) ?></td>
                            <td data-label="Durasi"><?= $p['Durasi_jam'] ?> jam</td>
                            <td data-label="Total"><strong><?= formatRupiah($p['Total_harga']) ?></strong></td>
                            <td data-label="Status">
                                <?php 
                                $statusLabel = ucwords(str_replace('_', ' ', $p['Status']));
                                if ($p['Status'] === 'diterima_companion') $statusLabel = 'Diterima Companion';
                                ?>
                                <span class="badge badge-<?= $p['Status'] ?>"><?= $statusLabel ?></span>
                            </td>
                            <td data-label="Aksi">
                                <div class="table-actions">
                                <?php if ($p['Status']==='menunggu_pembayaran'): ?>
                                    <a href="pembayaran.php?id=<?= $p['Id'] ?>" class="btn btn-primary btn-sm">Bayar</a>
                                    <a href="?batal=<?= $p['Id'] ?>" class="btn btn-danger btn-sm" data-confirm="Yakin batalkan?">Batal</a>
                                <?php elseif (in_array($p['Status'], ['diterima_companion', 'berjalan', 'selesai']) && !empty($p['companion_wa'])): ?>
                                    <a href="https://wa.me/62<?= ltrim($p['companion_wa'], '0') ?>" target="_blank" class="btn btn-success btn-sm" style="background:#25D366;border:none">WA Companion</a>
                                    <?php if ($p['Status']==='selesai'): ?>
                                        <?php $hr=$conn->query("SELECT Id FROM Reviews WHERE Pesanan_id={$p['Id']}")->num_rows; ?>
                                        <?php if (!$hr): ?><button class="btn btn-sm" style="background:#FFD700;color:#000" onclick="openReview(<?= $p['Id'] ?>,<?= $p['Companion_id'] ?>)">⭐ Ulasan</button><?php else: ?><span style="color:var(--success);font-size:0.82rem">✓ Diulas</span><?php endif; ?>
                                    <?php endif; ?>
                                <?php elseif ($p['Status']==='selesai'): ?>
                                    <?php $hr=$conn->query("SELECT Id FROM Reviews WHERE Pesanan_id={$p['Id']}")->num_rows; ?>
                                    <?php if (!$hr): ?><button class="btn btn-sm" style="background:#FFD700;color:#000" onclick="openReview(<?= $p['Id'] ?>,<?= $p['Companion_id'] ?>)">⭐ Ulasan</button><?php else: ?><span style="color:var(--success);font-size:0.82rem">✓ Diulas</span><?php endif; ?>
                                <?php else: ?><span style="color:var(--text-muted)">—</span><?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
                <?php else: ?><div class="empty-state"><div class="icon">📋</div><h3>Belum ada pesanan</h3><a href="companions.php" class="btn btn-primary" style="margin-top:16px">Cari Companion</a></div><?php endif; ?>
            </div>
        </div>
    </main>
</div>
<div id="reviewModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:9999;align-items:center;justify-content:center">
    <div style="background:#fff;border-radius:20px;padding:36px;width:100%;max-width:440px;margin:20px">
        <h3 style="font-family:'Syne',sans-serif;font-size:1.4rem;margin-bottom:20px">⭐ Beri Ulasan</h3>
        <form method="POST">
            <input type="hidden" name="review" value="1">
            <input type="hidden" name="pesanan_id" id="r_pesanan_id">
            <input type="hidden" name="companion_id" id="r_companion_id">
            <div class="form-group">
                <label class="form-label">Rating</label>
                <div style="display:flex;gap:8px;font-size:1.8rem" id="starPicker">
                    <?php for($i=1;$i<=5;$i++): ?><span style="cursor:pointer;color:#ddd" onclick="setRating(<?=$i?>)">★</span><?php endfor; ?>
                </div>
                <input type="hidden" name="rating" id="r_rating" value="5">
            </div>
            <div class="form-group"><label class="form-label">Komentar</label><textarea name="komentar" class="form-control" rows="3" placeholder="Bagaimana pengalamanmu?"></textarea></div>
            <div style="display:flex;gap:12px">
                <button type="submit" class="btn btn-primary" style="flex:1">Kirim</button>
                <button type="button" class="btn btn-outline" onclick="closeReview()">Batal</button>
            </div>
        </form>
    </div>
</div>
<script>
function openReview(pid,cid) { document.getElementById('r_pesanan_id').value=pid; document.getElementById('r_companion_id').value=cid; document.getElementById('reviewModal').style.display='flex'; setRating(5); }
function closeReview() { document.getElementById('reviewModal').style.display='none'; }
function setRating(val) { document.getElementById('r_rating').value=val; document.querySelectorAll('#starPicker span').forEach((s,i)=>{ s.style.color=i<val?'#FFD700':'#ddd'; }); }
</script>
<?php include '../includes/footer.php'; ?>