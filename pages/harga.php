<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';
$pageTitle = 'Harga';
include '../includes/header.php';
?>
<div class="page-hero">
    <h1>💰 Harga Layanan</h1>
    <p>Sudah termasuk jemput dari rumah dan antar pulang kembali</p>
</div>
<div class="section">
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:24px;max-width:900px;margin:0 auto">
        <div class="card" style="text-align:center">
            <div class="card-body" style="padding:40px 24px">
                <div style="font-size:2rem;margin-bottom:12px">🥉</div>
                <h3 style="font-weight:800;margin-bottom:8px">Basic</h3>
                <div style="font-family:'Syne',sans-serif;font-size:2.5rem;font-weight:800;color:var(--primary);margin-bottom:4px">Rp 35.000</div>
                <div style="color:var(--text-muted);font-size:0.85rem;margin-bottom:24px">per jam</div>
                <ul style="list-style:none;text-align:left;margin-bottom:28px">
                    <li style="padding:8px 0;border-bottom:1px solid var(--border)">✅ Jemput dari rumah</li>
                    <li style="padding:8px 0;border-bottom:1px solid var(--border)">✅ Pendamping COD</li>
                    <li style="padding:8px 0;border-bottom:1px solid var(--border)">✅ Antar pulang ke rumah</li>
                    <li style="padding:8px 0;color:var(--text-muted)">❌ Verifikasi barang detail</li>
                </ul>
                <a href="companions.php" class="btn btn-outline btn-block">Pilih Basic</a>
            </div>
        </div>
        <div class="card" style="border:2px solid var(--primary);text-align:center;position:relative">
            <div style="position:absolute;top:-14px;left:50%;transform:translateX(-50%);background:linear-gradient(135deg,var(--primary),#3B82F6);color:#fff;padding:6px 20px;border-radius:50px;font-size:0.8rem;font-weight:700">⭐ POPULER</div>
            <div class="card-body" style="padding:40px 24px">
                <div style="font-size:2rem;margin-bottom:12px">🥈</div>
                <h3 style="font-weight:800;margin-bottom:8px">Standard</h3>
                <div style="font-family:'Syne',sans-serif;font-size:2.5rem;font-weight:800;color:var(--primary);margin-bottom:4px">Rp 50.000</div>
                <div style="color:var(--text-muted);font-size:0.85rem;margin-bottom:24px">per jam</div>
                <ul style="list-style:none;text-align:left;margin-bottom:28px">
                    <li style="padding:8px 0;border-bottom:1px solid var(--border)">✅ Jemput dari rumah</li>
                    <li style="padding:8px 0;border-bottom:1px solid var(--border)">✅ Pendamping COD</li>
                    <li style="padding:8px 0;border-bottom:1px solid var(--border)">✅ Antar pulang ke rumah</li>
                    <li style="padding:8px 0">✅ Verifikasi barang detail</li>
                </ul>
                <a href="companions.php" class="btn btn-primary btn-block">Pilih Standard</a>
            </div>
        </div>
        <div class="card" style="text-align:center">
            <div class="card-body" style="padding:40px 24px">
                <div style="font-size:2rem;margin-bottom:12px">🥇</div>
                <h3 style="font-weight:800;margin-bottom:8px">Premium</h3>
                <div style="font-family:'Syne',sans-serif;font-size:2.5rem;font-weight:800;color:var(--primary);margin-bottom:4px">Rp 75.000</div>
                <div style="color:var(--text-muted);font-size:0.85rem;margin-bottom:24px">per jam</div>
                <ul style="list-style:none;text-align:left;margin-bottom:28px">
                    <li style="padding:8px 0;border-bottom:1px solid var(--border)">✅ Jemput dari rumah</li>
                    <li style="padding:8px 0;border-bottom:1px solid var(--border)">✅ Pendamping COD penuh</li>
                    <li style="padding:8px 0;border-bottom:1px solid var(--border)">✅ Antar pulang ke rumah</li>
                    <li style="padding:8px 0">✅ Companion berpengalaman</li>
                </ul>
                <a href="companions.php" class="btn btn-outline btn-block">Pilih Premium</a>
            </div>
        </div>
    </div>
</div>
<?php include '../includes/footer.php'; ?>