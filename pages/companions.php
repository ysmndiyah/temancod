<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';
$pageTitle = 'Cari Companion';
$kota = sanitize($_GET['kota'] ?? '');
$sort = in_array($_GET['sort'] ?? '', ['rating','harga_asc','harga_desc']) ? $_GET['sort'] : 'rating';
$search = sanitize($_GET['q'] ?? '');
$where = "WHERE c.Status = 'aktif'";
if ($kota) $where .= " AND c.Kota LIKE '%$kota%'";
if ($search) $where .= " AND (u.nama LIKE '%$search%' OR c.Kota LIKE '%$search%')";
$order = match($sort) {
    'harga_asc' => 'c.Harga_per_jam ASC',
    'harga_desc' => 'c.Harga_per_jam DESC',
    default => 'c.Rating DESC',
};
$companions = $conn->query("SELECT c.*, u.nama, u.foto FROM Companions c JOIN users u ON c.User_id = u.id $where ORDER BY $order");
$kotas = $conn->query("SELECT DISTINCT Kota FROM Companions WHERE Status='aktif' ORDER BY Kota");
include '../includes/header.php';
?>
<div class="page-hero">
    <h1>🔍 Cari Companion</h1>
    <p>Temukan teman terpercaya yang siap menemanimu COD barang atau berpergian.</p>
</div>
<div class="section" style="padding-top:40px">
    <div class="search-bar">
        <form method="GET">
            <div class="search-bar-inner">
                <div class="form-group" style="margin:0"><label class="form-label">Cari</label><input type="text" name="q" class="form-control" placeholder="Nama atau kota..." value="<?= htmlspecialchars($search) ?>"></div>
                <div class="form-group" style="margin:0">
                    <label class="form-label">Kota</label>
                    <select name="kota" class="form-control">
                        <option value="">Semua Kota</option>
                        <?php while ($k = $kotas->fetch_assoc()): ?>
                            <option value="<?= htmlspecialchars($k['Kota']) ?>" <?= $kota===$k['Kota']?'selected':'' ?>><?= htmlspecialchars($k['Kota']) ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="form-group" style="margin:0">
                    <label class="form-label">Urutkan</label>
                    <select name="sort" class="form-control">
                        <option value="rating" <?= $sort==='rating'?'selected':'' ?>>⭐ Rating Tertinggi</option>
                        <option value="harga_asc" <?= $sort==='harga_asc'?'selected':'' ?>>💰 Harga Terendah</option>
                        <option value="harga_desc" <?= $sort==='harga_desc'?'selected':'' ?>>💰 Harga Tertinggi</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Cari</button>
            </div>
        </form>
    </div>
    <?php if ($companions && $companions->num_rows > 0): ?>
        <p style="color:var(--text-muted);margin-bottom:24px">Ditemukan <strong><?= $companions->num_rows ?></strong> companion</p>
        <div class="companions-grid">
            <?php while ($c = $companions->fetch_assoc()): ?>
            <div class="companion-card">
                <div class="companion-card-img">👤<span class="status-badge status-aktif">● Aktif</span></div>
                <div class="companion-card-body">
                    <h3><?= htmlspecialchars($c['nama']) ?></h3>
                    <p class="companion-location">📍 <?= htmlspecialchars($c['Lokasi'] ?? '') ?>, <?= htmlspecialchars($c['Kota']) ?></p>
                    <div class="companion-rating">
                        <span class="stars"><?= getStars(round($c['Rating'])) ?></span>
                        <span><?= number_format($c['Rating'],1) ?> · <?= $c['Total_Order'] ?> pesanan</span>
                    </div>
                    <?php if ($c['Deskripsi']): ?><p style="font-size:0.84rem;color:var(--text-muted);margin-bottom:12px"><?= htmlspecialchars(substr($c['Deskripsi'],0,80)) ?>...</p><?php endif; ?>
                    <div class="companion-price"><?= formatRupiah($c['Harga_per_jam']) ?> <span style="font-size:0.75rem;font-weight:500;color:var(--text-muted)">/jam</span></div>
                    <a href="companion_detail.php?id=<?= $c['Id'] ?>" class="btn btn-primary btn-block">Lihat Profil & Pesan</a>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <div class="empty-state"><div class="icon">😔</div><h3>Companion tidak ditemukan</h3><p>Coba ubah filter pencarian.</p></div>
    <?php endif; ?>
</div>
<?php include '../includes/footer.php'; ?>