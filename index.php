<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';
$pageTitle = 'Beranda';
$companions = $conn->query("SELECT c.*, u.nama, u.foto FROM Companions c JOIN users u ON c.User_id = u.id WHERE c.Status = 'aktif' ORDER BY c.Rating DESC LIMIT 6");
include 'includes/header.php';
?>
<section class="hero">
    <div class="hero-content">
        <div class="hero-badge">🔥 Platform #1 Pengaman Transaksi COD</div>
        <h1>Jemput, Temani, <span>Antar Pulang.</span> Aman Sampai Rumah.</h1>
        <p>Companion kami jemput kamu dari rumah, menemani selama transaksi COD berlangsung, lalu mengantarmu pulang dengan selamat. Aman dari berangkat sampai sampai rumah lagi.</p>
        <div class="hero-buttons">
            <a href="pages/companions.php" class="btn btn-primary">🔍 Cari Companion Sekarang</a>
            <a href="pages/register.php?role=companion" class="btn btn-outline">💼 Jadi Companion</a>
        </div>
        <div class="hero-stats">
            <div class="stat-item"><div class="stat-num">500+</div><div class="stat-label">Companion Aktif</div></div>
            <div class="stat-item"><div class="stat-num">2.000+</div><div class="stat-label">Perjalanan Aman Selesai</div></div>
            <div class="stat-item"><div class="stat-num">4.9★</div><div class="stat-label">Rating Rata-rata</div></div>
        </div>
    </div>
    <div class="hero-image">
        <div class="hero-card-float">
            <div class="card-header">
                <div class="hero-avatar">👩</div>
                <div>
                    <h3>Sari Dewi</h3>
                    <p>📍 Bandung Kota</p>
                    <div class="star-rating">★★★★★ <small style="color:#6B7280">4.8</small></div>
                </div>
            </div>
            <div class="companion-tags">
                <span class="tag tag-primary">Jemput-Antar</span>
                <span class="tag">Pendamping COD</span>
                <span class="tag">Aman Sampai Rumah</span>
            </div>
            <div style="display:flex;justify-content:space-between;align-items:center">
                <div style="font-weight:800;color:var(--primary);font-size:1.1rem">Rp 50.000<small style="font-weight:400;color:#6B7280;font-size:0.75rem">/jam</small></div>
                <a href="pages/companions.php" class="btn btn-primary btn-sm">Pesan</a>
            </div>
        </div>
        <div class="float-badge float-badge-1">✅ Terverifikasi & Aman</div>
        <div class="float-badge float-badge-2">🏠 Jemput & Antar Pulang</div>
    </div>
</section>

<section class="section section-alt">
    <div class="section-header">
        <div class="section-label">Cara Kerja</div>
        <h2>Aman dari Rumah, Sampai Rumah Lagi</h2>
        <p>Companion menjemputmu, menemani transaksi, dan mengantarmu pulang. Satu rangkaian perjalanan yang aman.</p>
    </div>
    <div class="steps-grid">
        <div class="step-card">
            <div class="step-num">1</div>
            <div class="step-icon">🏠</div>
            <h3>Companion Jemput dari Rumah</h3>
            <p>Companion datang ke alamat rumahmu sesuai jadwal yang sudah dipesan, siap mengantarmu pergi.</p>
        </div>
        <div class="step-card">
            <div class="step-num">2</div>
            <div class="step-icon">🛡️</div>
            <h3>Menemani Selama Transaksi</h3>
            <p>Companion mendampingimu di lokasi COD, membantu mengecek barang, dan menjaga keamanan transaksi.</p>
        </div>
        <div class="step-card">
            <div class="step-num">3</div>
            <div class="step-icon">🚗</div>
            <h3>Diantar Pulang dengan Aman</h3>
            <p>Setelah transaksi selesai, companion mengantarmu kembali pulang ke rumah dengan selamat.</p>
        </div>
        <div class="step-card">
            <div class="step-num">4</div>
            <div class="step-icon">⭐</div>
            <h3>Beri Rating & Ulasan</h3>
            <p>Setelah sampai rumah dengan aman, berikan rating dan ulasan untuk companion.</p>
        </div>
    </div>
</section>

<section class="section">
    <div class="section-header">
        <div class="section-label">Companion Terbaik</div>
        <h2>Temukan Pengaman Perjalanan Terpercaya</h2>
        <p>Companion kami telah terverifikasi dan memiliki rating tinggi dalam mengantar-jemput dengan aman.</p>
    </div>
    <div class="companions-grid">
        <?php if ($companions && $companions->num_rows > 0): ?>
            <?php while ($c = $companions->fetch_assoc()): ?>
            <div class="companion-card">
                <div class="companion-card-img">👤<span class="status-badge status-aktif">● Aktif</span></div>
                <div class="companion-card-body">
                    <h3><?= htmlspecialchars($c['nama']) ?></h3>
                    <p class="companion-location">📍 <?= htmlspecialchars($c['Lokasi'] ?? '') ?>, <?= htmlspecialchars($c['Kota']) ?></p>
                    <div class="companion-rating">
                        <span class="stars"><?= getStars(round($c['Rating'])) ?></span>
                        <span><?= number_format($c['Rating'],1) ?> · <?= $c['Total_Order'] ?> perjalanan</span>
                    </div>
                    <div class="companion-price"><?= formatRupiah($c['Harga_per_jam']) ?> <span style="font-size:0.75rem;font-weight:500;color:var(--text-muted)">/jam</span></div>
                    <a href="pages/companion_detail.php?id=<?= $c['Id'] ?>" class="btn btn-primary btn-block">Lihat & Pesan</a>
                </div>
            </div>
            <?php endwhile; ?>
        <?php endif; ?>
    </div>
    <div style="text-align:center;margin-top:40px">
        <a href="pages/companions.php" class="btn btn-outline">Lihat Semua Companion →</a>
    </div>
</section>

<section class="section section-alt">
    <div class="section-header">
        <div class="section-label">Testimoni</div>
        <h2>Kata Mereka yang Sudah Pakai</h2>
    </div>
    <div class="testi-grid">
        <div class="testi-card"><div class="testi-stars">★★★★★</div><p class="testi-text">"Dijemput dari rumah, ditemani pas COD, terus diantar balik. Tenang banget gak takut ketipu!"</p><div class="testi-author"><div class="testi-avatar">A</div><div><div class="testi-name">Andi P.</div><div class="testi-role">Pengguna di Bandung</div></div></div></div>
        <div class="testi-card"><div class="testi-stars">★★★★★</div><p class="testi-text">"Awalnya takut COD sendirian, sekarang ada yang jemput dan antar pulang juga. Aman dari awal sampai akhir!"</p><div class="testi-author"><div class="testi-avatar">M</div><div><div class="testi-name">Maya S.</div><div class="testi-role">Pengguna di Jakarta</div></div></div></div>
        <div class="testi-card"><div class="testi-stars">★★★★☆</div><p class="testi-text">"Companion nya sigap, nemenin cek barang, terus aku diantar pulang sampai depan rumah. Recommended!"</p><div class="testi-author"><div class="testi-avatar">R</div><div><div class="testi-name">Rizky F.</div><div class="testi-role">Pengguna di Surabaya</div></div></div></div>
    </div>
</section>

<section class="cta-section">
    <h2>Siap Mulai Perjalanan Aman?</h2>
    <p>Daftar sekarang gratis dan temukan companion yang siap jemput, temani, dan antar kamu pulang dengan selamat.</p>
    <div style="display:flex;gap:16px;justify-content:center;flex-wrap:wrap">
        <a href="pages/register.php" class="btn btn-white">Daftar Gratis Sekarang</a>
        <a href="pages/register.php?role=companion" class="btn btn-outline" style="border-color:rgba(255,255,255,0.5);color:#fff">Jadi Companion →</a>
    </div>
</section>

<?php include 'includes/footer.php'; ?>