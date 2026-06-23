<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('pages/login.php');
}

$pesananId = intval($_GET['id'] ?? 0);
if ($pesananId <= 0) {
    die('ID pesanan tidak valid');
}

// Fetch order details with joins
$sql = "SELECT p.*, u.nama AS customer_nama, u.email AS customer_email, cu.nama AS companion_nama, cu.no_hp AS companion_wa, b.bukti_transfer, b.status AS bukti_status
        FROM Pesanan p
        JOIN users u ON p.User_id = u.id
        JOIN Companions c ON p.Companion_id = c.Id
        JOIN users cu ON c.User_id = cu.id
        LEFT JOIN Pembayaran b ON b.pesanan_id = p.Id
        WHERE p.Id = $pesananId";
$detail = $conn->query($sql)->fetch_assoc();
if (!$detail) {
    die('Pesanan tidak ditemukan');
}

// Handle admin actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['accept_payment'])) {
        $conn->query("UPDATE Pembayaran SET status='Terverifikasi' WHERE pesanan_id=$pesananId");
        $conn->query("UPDATE Pesanan SET Status='diterima_companion' WHERE Id=$pesananId");
    } elseif (isset($_POST['reject_payment'])) {
        $conn->query("UPDATE Pembayaran SET status='Ditolak' WHERE pesanan_id=$pesananId");
        $conn->query("UPDATE Pesanan SET Status='menunggu_pembayaran' WHERE Id=$pesananId");
    } elseif (isset($_POST['mark_done'])) {
        $conn->query("UPDATE Pesanan SET Status='selesai' WHERE Id=$pesananId");
    }
    // Refresh data after action
    header("Location: admin_detail_pesanan.php?id=$pesananId");
    exit();
}

include '../includes/header.php';
?>
<div class="dashboard-wrapper">
    <aside class="sidebar">
        <?php // Sidebar as in other admin pages ?>
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
        <div class="page-title">Detail Pesanan #<?php echo $detail['Id']; ?></div>
        <div class="card" style="margin-top:24px;">
            <table class="table-wrapper" style="width:100%;border-collapse:collapse;">
                <tr><th>ID Pesanan</th><td><?php echo $detail['Id']; ?></td></tr>
                <tr><th>Customer</th><td><?php echo htmlspecialchars($detail['customer_nama']); ?> (<?php echo htmlspecialchars($detail['customer_email']); ?>)</td></tr>
                <tr><th>Companion</th><td><?php echo htmlspecialchars($detail['companion_nama']); ?></td></tr>
                <tr><th>Tanggal</th><td><?php echo htmlspecialchars(date('d M Y', strtotime($detail['Tanggal_jemput']))); ?></td></tr>
                <tr><th>Jam</th><td><?php echo htmlspecialchars($detail['Jam_mulai']); ?></td></tr>
                <tr><th>Lokasi Jemput</th><td><?php echo htmlspecialchars($detail['Lokasi_jemput']); ?></td></tr>
                <tr><th>Lokasi COD</th><td><?php echo htmlspecialchars($detail['Tujuan']); ?></td></tr>
                <tr><th>Detail Barang / Keperluan</th><td><?php echo nl2br(htmlspecialchars($detail['Keperluan'] ?? '-')); ?></td></tr>
                <tr><th>Bukti Transfer</th><td>
                    <?php if (!empty($detail['bukti_transfer'])): ?>
                        <a href="../uploads/<?php echo htmlspecialchars($detail['bukti_transfer']); ?>" target="_blank">Lihat Bukti</a>
                    <?php else: ?>
                        Tidak ada
                    <?php endif; ?>
                </td></tr>
                <tr><th>Status</th><td><span class="badge badge-<?php echo $detail['Status']; ?>"><?php echo ucwords(str_replace('_', ' ', $detail['Status'])); ?></span></td></tr>
            </table>
            <div style="margin-top:16px;display:flex;gap:8px;">
                <?php if ($detail['Status'] === 'menunggu_verifikasi_admin'): ?>
                    <form method="POST"><button name="accept_payment" class="btn btn-success btn-sm">Terima Pembayaran</button></form>
                    <form method="POST"><button name="reject_payment" class="btn btn-danger btn-sm">Tolak Pembayaran</button></form>
                <?php endif; ?>
                <?php if ($detail['Status'] === 'diterima_companion'): ?>
                    <form method="POST"><button name="mark_done" class="btn btn-primary btn-sm">Tandai Selesai</button></form>
                <?php endif; ?>
                <?php if (!empty($detail['companion_wa'])): ?>
                    <?php
                    $msg = "Halo " . $detail['companion_nama'] . ", terdapat pesanan baru dari " . $detail['customer_nama'] . "%0A%0ATanggal: " . date('d M Y', strtotime($detail['Tanggal_jemput'])) . "%0AJam: " . $detail['Jam_mulai'] . "%0ALokasi COD: " . $detail['Tujuan'] . "%0A%0AAdmin TemanCOD";
                    $waLink = "https://wa.me/62" . ltrim($detail['companion_wa'], '0') . "?text=" . urlencode($msg);
                    ?>
                    <a href="<?php echo $waLink; ?>" target="_blank" class="btn btn-outline btn-sm">Hubungi Companion</a>
                <?php endif; ?>
            </div>
        </div>
    </main>
</div>
<?php include '../includes/footer.php'; ?>
?>
