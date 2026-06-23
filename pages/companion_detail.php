<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';
$id = intval($_GET['id'] ?? 0);
if (!$id) redirect('pages/companions.php');
$companion = $conn->query("SELECT c.*, u.nama, u.foto, u.no_hp FROM Companions c JOIN users u ON c.User_id = u.id WHERE c.Id = $id")->fetch_assoc();
if (!$companion) redirect('pages/companions.php');
$pageTitle = $companion['nama'];
$reviews = $conn->query("SELECT r.*, u.nama as reviewer_nama FROM Reviews r JOIN users u ON r.User_id = u.id WHERE r.Companion_id = $id ORDER BY r.Created_at DESC LIMIT 10");
$error = ''; $success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isLoggedIn()) {
    $tanggal = sanitize($_POST['tanggal_jemput'] ?? '');
    $jam = sanitize($_POST['jam_mulai'] ?? '');
    $durasi = intval($_POST['durasi_jam'] ?? 1);
    $lokasi = sanitize($_POST['lokasi_jemput'] ?? '');
    $tujuan = sanitize($_POST['tujuan'] ?? '');
    $keperluan = sanitize($_POST['keperluan'] ?? '');
    $total = floatval($_POST['total_harga'] ?? 0);
    if (!$tanggal || !$jam || !$lokasi || !$tujuan) {
        $error = 'Mohon lengkapi semua data perjalanan.';
    } else {
        $user_id = $_SESSION['user_id'];
        $stmt = $conn->prepare("INSERT INTO Pesanan (User_id, Companion_id, Tanggal_jemput, Jam_mulai, Durasi_jam, Lokasi_jemput, Tujuan, Keperluan, Total_harga, Status) VALUES (?,?,?,?,?,?,?,?,?, 'menunggu_pembayaran')");
        $stmt->bind_param("iississsd", $user_id, $id, $tanggal, $jam, $durasi, $lokasi, $tujuan, $keperluan, $total);
        if ($stmt->execute()) {
            $pesanan_id = $conn->insert_id;
            redirect("pages/pembayaran.php?id=$pesanan_id");
        } else {
            $error = 'Gagal mengirim pesanan.';
        }
    }
}
include '../includes/header.php';
?>
<div class="page-hero" style="padding:32px 8%">
    <a href="companions.php" style="color:rgba(255,255,255,0.7)">← Kembali</a>
</div>
<div class="detail-grid">
    <div>
        <div class="card" style="margin-bottom:24px">
            <div class="card-body">
                <div class="companion-detail-header">
                    <div class="detail-avatar">👤</div>
                    <div>
                        <h1 style="font-family:'Syne',sans-serif;font-size:1.6rem;font-weight:800;margin-bottom:4px"><?= htmlspecialchars($companion['nama']) ?></h1>
                        <p style="color:var(--text-muted);margin-bottom:8px">📍 <?= htmlspecialchars($companion['Lokasi'] ?? '') ?>, <?= htmlspecialchars($companion['Kota']) ?></p>
                        <span style="color:#FFD700"><?= getStars(round($companion['Rating'])) ?></span>
                        <span style="color:var(--text-muted);font-size:0.9rem"> <?= number_format($companion['Rating'],1) ?> · <?= $companion['Total_Order'] ?> perjalanan</span>
                    </div>
                </div>
                <?php if ($companion['Deskripsi']): ?>
                    <h3 style="font-weight:700;margin-bottom:10px">Tentang Saya</h3>
                    <p style="color:var(--text-muted);line-height:1.8"><?= nl2br(htmlspecialchars($companion['Deskripsi'])) ?></p>
                <?php endif; ?>
            </div>
        </div>
        <div class="card">
            <div class="card-header-bar"><h3>⭐ Ulasan</h3></div>
            <div class="card-body">
                <?php if ($reviews->num_rows > 0): ?>
                    <?php while ($r = $reviews->fetch_assoc()): ?>
                    <div class="review-card">
                        <div class="review-header">
                            <div class="review-avatar"><?= strtoupper(substr($r['reviewer_nama'],0,1)) ?></div>
                            <div class="review-meta">
                                <div class="name"><?= htmlspecialchars($r['reviewer_nama']) ?></div>
                                <div class="date"><?= str_repeat('★',$r['Rating']) ?></div>
                            </div>
                        </div>
                        <p class="review-text"><?= htmlspecialchars($r['Komentar']) ?></p>
                    </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p style="color:var(--text-muted)">Belum ada ulasan.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div>
        <div class="book-card">
            <div class="price-big"><?= formatRupiah($companion['Harga_per_jam']) ?> <span style="font-size:0.9rem;font-weight:500;color:var(--text-muted);font-family:inherit">/jam</span></div>
            <hr style="margin:16px 0;border-color:var(--border)">
            <?php if (!isLoggedIn()): ?>
                <div class="alert alert-info"><a href="login.php" style="font-weight:700">Masuk</a> untuk memesan.</div>
                <a href="login.php" class="btn btn-primary btn-block">Masuk Dulu</a>
            <?php elseif ($_SESSION['role'] === 'companion'): ?>
                <div class="alert alert-warning">Companion tidak bisa memesan companion lain.</div>
            <?php else: ?>
                <?php if ($error): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>
                <?php if ($success): ?><div class="alert alert-success"><?= $success ?></div><?php endif; ?>
                <form method="POST">
                    <input type="hidden" name="total_harga" id="total_harga" value="<?= $companion['Harga_per_jam'] ?>">
                    <input type="hidden" id="harga_per_jam" value="<?= $companion['Harga_per_jam'] ?>">
                    <div class="form-group"><label class="form-label">📅 Tanggal</label><input type="date" name="tanggal_jemput" class="form-control" min="<?= date('Y-m-d') ?>" required></div>
                    <div class="form-row">
                        <div class="form-group"><label class="form-label">⏰ Jam Jemput</label><input type="time" name="jam_mulai" class="form-control" required></div>
                        <div class="form-group">
                            <label class="form-label">⏱ Estimasi Durasi</label>
                            <select name="durasi_jam" id="durasi_jam" class="form-control">
                                <?php for ($i=1;$i<=8;$i++): ?><option value="<?=$i?>"><?=$i?> Jam</option><?php endfor; ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group"><label class="form-label">🏠 Alamat Jemput (Rumahmu)</label><input type="text" name="lokasi_jemput" class="form-control" placeholder="Alamat rumah untuk dijemput" required></div>
                    <div class="form-group"><label class="form-label">📍 Lokasi COD (Tujuan)</label><input type="text" name="tujuan" class="form-control" placeholder="Alamat bertemu penjual" required></div>
                    <div class="form-group"><label class="form-label">📝 Detail Barang & Keperluan</label><textarea name="keperluan" class="form-control" rows="3" placeholder="Contoh: COD HP iPhone dari Facebook Marketplace, harga 3 juta"></textarea></div>
                    <div class="price-summary">
                        <div class="price-row"><span><?= formatRupiah($companion['Harga_per_jam']) ?> × <span id="dur_display">1</span> jam</span><span id="total_display"><?= formatRupiah($companion['Harga_per_jam']) ?></span></div>
                        <div class="price-row"><strong>Total</strong><strong id="total_display2"><?= formatRupiah($companion['Harga_per_jam']) ?></strong></div>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">🤝 Pesan Sekarang</button>
                    <p style="font-size:0.78rem;color:var(--text-muted);text-align:center;margin-top:10px">Companion akan menjemput, menemani, dan mengantarmu pulang</p>
                </form>
            <?php endif; ?>
        </div>
    </div>
</div>
<script>
const durasi = document.getElementById('durasi_jam');
const harga = parseFloat(document.getElementById('harga_per_jam')?.value || 0);
function updatePrice() {
    const d = parseInt(durasi?.value || 1);
    const total = d * harga;
    const fmt = 'Rp ' + total.toLocaleString('id-ID');
    document.getElementById('total_display').textContent = fmt;
    document.getElementById('total_display2').textContent = fmt;
    document.getElementById('dur_display').textContent = d;
    document.getElementById('total_harga').value = total;
}
if (durasi) durasi.addEventListener('change', updatePrice);
</script>
<?php include '../includes/footer.php'; ?>