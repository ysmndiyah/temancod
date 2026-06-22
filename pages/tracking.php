<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';
$pageTitle = 'Tracking';
include '../includes/header.php';
?>
<div class="page-hero">
    <h1>📍 Tracking Perjalanan</h1>
    <p>Pantau status perjalanan dari jemput sampai kembali pulang secara realtime</p>
</div>
<div class="section">
    <div style="max-width:600px;margin:0 auto">
        <?php if (!isLoggedIn()): ?>
            <div style="text-align:center;padding:60px 20px">
                <div style="font-size:3rem;margin-bottom:16px">🔐</div>
                <h3 style="margin-bottom:10px">Login Dulu Ya!</h3>
                <p style="color:var(--text-muted);margin-bottom:20px">Kamu perlu login untuk melihat tracking perjalanan.</p>
                <a href="login.php" class="btn btn-primary">Login Sekarang</a>
            </div>
        <?php else: ?>
            <?php
            $user_id = $_SESSION['user_id'];
            $pesanan = $conn->query("
                SELECT p.*, u.nama as companion_nama, u.no_hp
                FROM Pesanan p
                JOIN Companions c ON p.Companion_id = c.Id
                JOIN users u ON c.User_id = u.id
                WHERE p.User_id = $user_id
                AND p.Status NOT IN ('selesai','dibatalkan')
                ORDER BY p.Created_at DESC
            ");
            ?>
            <?php if ($pesanan->num_rows > 0): ?>
                <?php while ($p = $pesanan->fetch_assoc()): ?>
                <div class="card" style="margin-bottom:20px">
                    <div class="card-header-bar">
                        <h3>Perjalanan #<?= $p['Id'] ?></h3>
                        <span class="badge badge-<?= $p['Status'] ?>"><?= ucfirst($p['Status']) ?></span>
                    </div>
                    <div class="card-body">
                        <p style="margin-bottom:8px"><strong>Companion:</strong> <?= htmlspecialchars($p['companion_nama']) ?></p>
                        <p style="margin-bottom:8px"><strong>Tanggal:</strong> <?= date('d M Y', strtotime($p['Tanggal_jemput'])) ?> pukul <?= substr($p['Jam_mulai'],0,5) ?></p>
                        <p style="margin-bottom:8px"><strong>Alamat Jemput:</strong> <?= htmlspecialchars($p['Lokasi_jemput']) ?></p>
                        <p style="margin-bottom:8px"><strong>Lokasi COD:</strong> <?= htmlspecialchars($p['Tujuan'] ?: '-') ?></p>
                        <p style="margin-bottom:20px"><strong>Total:</strong> <?= formatRupiah($p['Total_harga']) ?></p>
                        <div style="display:flex;justify-content:space-between">
                            <?php
                            $steps = ['menunggu'=>'Menunggu','diterima'=>'Dijemput','berjalan'=>'Di Lokasi COD','selesai'=>'Sampai Rumah'];
                            $keys = array_keys($steps);
                            $current = array_search($p['Status'], $keys);
                            $i = 0;
                            foreach ($steps as $key => $label):
                            $active = $i <= $current;
                            ?>
                            <div style="text-align:center;flex:1">
                                <div style="width:36px;height:36px;border-radius:50%;background:<?= $active ? 'var(--primary)' : '#E5E7EB' ?>;color:<?= $active ? '#fff' : 'var(--text-muted)' ?>;display:flex;align-items:center;justify-content:center;margin:0 auto 6px;font-weight:700"><?= $i+1 ?></div>
                                <div style="font-size:0.72rem;color:<?= $active ? 'var(--primary)' : 'var(--text-muted)' ?>;font-weight:<?= $active ? '700' : '400' ?>"><?= $label ?></div>
                            </div>
                            <?php $i++; endforeach; ?>
                        </div>
                        <?php if ($p['no_hp']): ?>
                            <a href="https://wa.me/62<?= ltrim($p['no_hp'],'0') ?>" target="_blank" class="btn btn-success btn-sm" style="margin-top:16px">📱 Hubungi via WhatsApp</a>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="empty-state">
                    <div class="icon">📍</div>
                    <h3>Tidak ada perjalanan aktif</h3>
                    <p>Kamu belum punya perjalanan yang sedang berlangsung.</p>
                    <a href="companions.php" class="btn btn-primary" style="margin-top:16px">Pesan Companion</a>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>
<?php include '../includes/footer.php'; ?>