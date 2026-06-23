<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

if (!isLoggedIn()) redirect('pages/login.php');

$pesanan_id = intval($_GET['id'] ?? 0);
if (!$pesanan_id) redirect('pages/dashboard.php');

$user_id = $_SESSION['user_id'];
$pesanan = $conn->query("SELECT * FROM Pesanan WHERE Id = $pesanan_id AND User_id = $user_id")->fetch_assoc();

if (!$pesanan) redirect('pages/dashboard.php');

// Jika sudah bukan menunggu pembayaran
if ($pesanan['Status'] !== 'menunggu_pembayaran') {
    header("Location: dashboard.php");
    exit();
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['bukti_transfer'])) {
    $file = $_FILES['bukti_transfer'];
    
    if ($file['error'] === 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'pdf'];
        $filename = $_FILES['bukti_transfer']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if (in_array($ext, $allowed)) {
            $new_filename = 'bukti_' . $pesanan_id . '_' . time() . '.' . $ext;
            $upload_dir = '../uploads/';
            
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            if (move_uploaded_file($file['tmp_name'], $upload_dir . $new_filename)) {
                // Insert ke tabel Pembayaran
                $stmt = $conn->prepare("INSERT INTO Pembayaran (pesanan_id, bukti_transfer, status) VALUES (?, ?, 'Menunggu Verifikasi')");
                $stmt->bind_param("is", $pesanan_id, $new_filename);
                $stmt->execute();
                
                // Update tabel Pesanan
                $conn->query("UPDATE Pesanan SET Status = 'menunggu_verifikasi_admin' WHERE Id = $pesanan_id");
                
                $success = 'Pembayaran berhasil dikirim. Admin akan memverifikasi pembayaran dan menghubungi companion.';
            } else {
                $error = 'Gagal mengupload file bukti transfer.';
            }
        } else {
            $error = 'Format file tidak diizinkan. Harap upload JPG, PNG, atau PDF.';
        }
    } else {
        $error = 'Terjadi kesalahan saat mengupload file.';
    }
}

$pageTitle = 'Pembayaran';
include '../includes/header.php';
?>

<div class="container" style="padding:40px 5%; max-width: 600px; margin: auto;">
    <div class="card">
        <div class="card-header-bar">
            <h3>💳 Pembayaran Manual</h3>
        </div>
        <div class="card-body">
            <?php if ($success): ?>
                <div class="alert alert-success">
                    <?= $success ?>
                </div>
                <div style="margin-top:20px;text-align:center;">
                    <a href="dashboard.php" class="btn btn-primary">Kembali ke Dashboard</a>
                </div>
            <?php else: ?>
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?= $error ?></div>
                <?php endif; ?>
                
                <p style="margin-bottom: 16px; color: var(--text-muted);">Silakan transfer pembayaran ke rekening berikut sesuai dengan total harga pesanan Anda.</p>
                
                <div style="background: #f8fafc; padding: 20px; border-radius: 8px; border: 1px solid #e2e8f0; margin-bottom: 24px; text-align: center;">
                    <h4 style="font-size: 1.2rem; margin-bottom: 8px;">Bank BCA</h4>
                    <p style="font-size: 1.5rem; font-weight: 800; letter-spacing: 2px; color: var(--primary); margin-bottom: 4px;">1234567890</p>
                    <p style="color: var(--text-muted); margin-bottom: 16px;">a.n TemanCOD</p>
                    
                    <div style="font-size: 0.9rem; color: var(--text-muted);">Total Pembayaran:</div>
                    <div style="font-size: 1.8rem; font-weight: 700; color: var(--text);"><?= formatRupiah($pesanan['Total_harga']) ?></div>
                </div>

                <form method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label class="form-label">Upload Bukti Transfer</label>
                        <input type="file" name="bukti_transfer" class="form-control" accept="image/*,.pdf" required>
                        <small style="color: var(--text-muted); display: block; margin-top: 6px;">Format yang didukung: JPG, PNG, PDF</small>
                    </div>
                    
                    <button type="submit" class="btn btn-primary btn-block" style="margin-top: 16px;">Upload Bukti Transfer</button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
