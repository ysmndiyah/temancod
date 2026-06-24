<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';
if (isLoggedIn()) redirect('');
$pageTitle = 'Daftar';
$role = isset($_GET['role']) && $_GET['role'] === 'companion' ? 'companion' : 'user';
$isCompanionRole = $role === 'companion';
$roleTitle = $isCompanionRole ? 'Daftar Jadi Companion' : 'Buat Akun Baru';
$roleSubtitle = $isCompanionRole ? 'Bergabung sebagai companion dan bantu customer saat proses COD.' : 'Bergabung dan mulai gunakan layanan TemanCOD dengan lebih praktis.';
$roleEyebrow = $isCompanionRole ? 'Companion' : 'Daftar Sekarang';
$roleHeadline = $isCompanionRole ? 'Siap membantu customer saat COD?' : 'Buat akun dan mulai gunakan layanan TemanCOD dengan lebih praktis.';
$roleDescription = $isCompanionRole ? 'Saat customer memesan, admin akan memverifikasi pembayaran lalu menghubungi kamu lewat WhatsApp untuk mulai membantu.' : 'Jadilah pengguna yang nyaman atau companion yang siap membantu perjalanan pelanggan.';
$roleButtonLabel = $isCompanionRole ? 'Daftar Jadi Companion' : 'Daftar Sekarang';
$error = ''; $success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = sanitize($_POST['nama'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $no_hp = sanitize($_POST['no_hp'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';
    $reg_role = in_array($_POST['role'], ['user','companion']) ? $_POST['role'] : 'user';
    if (!$nama || !$email || !$password) {
        $error = 'Semua field wajib diisi!';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Format email tidak valid.';
    } elseif (strlen($password) < 6) {
        $error = 'Password minimal 6 karakter.';
    } elseif ($password !== $confirm) {
        $error = 'Konfirmasi password tidak cocok.';
    } else {
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            $error = 'Email sudah terdaftar.';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (nama, email, password, no_hp, role) VALUES (?,?,?,?,?)");
            $stmt->bind_param("sssss", $nama, $email, $hash, $no_hp, $reg_role);
            if ($stmt->execute()) {
                $user_id = $conn->insert_id;
                if ($reg_role === 'companion') {
                    $harga = 50000; $kota = 'Belum diisi';
                    $ins = $conn->prepare("INSERT INTO Companions (User_id, Harga_per_jam, Kota) VALUES (?,?,?)");
                    $ins->bind_param("ids", $user_id, $harga, $kota);
                    $ins->execute();
                }
                $success = 'Pendaftaran berhasil! Silakan masuk.';
            } else {
                $error = 'Gagal mendaftar, coba lagi.';
            }
        }
    }
}
include '../includes/header.php';
?>
<div class="auth-wrapper">
    <div class="auth-shell">
        <div class="auth-illustration">
            <span class="eyebrow" id="roleEyebrow"><?= htmlspecialchars($roleEyebrow) ?></span>
            <h3 id="roleHeadline"><?= htmlspecialchars($roleHeadline) ?></h3>
            <p id="roleDescription"><?= htmlspecialchars($roleDescription) ?></p>
            <ul class="auth-benefits" id="roleBenefits">
                <li>🧭 Booking lebih terarah</li>
                <li>💬 Komunikasi lebih mudah</li>
                <li>✅ Status order tetap terpantau</li>
            </ul>
        </div>

        <div class="auth-card">
            <h2 id="registerTitle"><?= htmlspecialchars($roleTitle) ?></h2>
            <p class="subtitle" id="registerSubtitle"><?= htmlspecialchars($roleSubtitle) ?></p>
            <?php if ($error): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>
            <?php if ($success): ?><div class="alert alert-success"><?= $success ?> <a href="login.php">Masuk sekarang</a></div><?php endif; ?>
            <?php if (!$success): ?>
            <form method="POST">
                <div class="form-group"><label class="form-label">Nama Lengkap</label><input type="text" name="nama" class="form-control" placeholder="Nama kamu" required></div>
                <div class="form-group"><label class="form-label">Email</label><input type="email" name="email" class="form-control" placeholder="email@gmail.com" required></div>
                <div class="form-group"><label class="form-label">No. HP / WhatsApp</label><input type="text" name="no_hp" class="form-control" placeholder="08xxxxxxxxxx"></div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Password</label>
                        <div class="password-input-wrap">
                            <input type="password" name="password" id="passwordField" class="form-control" placeholder="Min. 6 karakter" required>
                            <button type="button" class="password-toggle" data-target="passwordField" aria-label="Tampilkan password" style="background:#fff; box-shadow:0 0 0 1px rgba(15,23,42,0.12);">👁</button>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Konfirmasi Password</label>
                        <div class="password-input-wrap">
                            <input type="password" name="confirm_password" id="confirmPasswordField" class="form-control" placeholder="Ulangi password" required>
                            <button type="button" class="password-toggle" data-target="confirmPasswordField" aria-label="Tampilkan password" style="background:#fff; box-shadow:0 0 0 1px rgba(15,23,42,0.12);">👁</button>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Daftar Sebagai</label>
                    <select name="role" id="roleSelect" class="form-control">
                        <option value="user" <?= $role==='user'?'selected':'' ?>>👤 Pengguna</option>
                        <option value="companion" <?= $role==='companion'?'selected':'' ?>>🤝 Companion</option>
                    </select>
                </div>
                <div class="auth-role-card" id="roleInfoCard">
                    <div class="auth-role-pill"><?= $isCompanionRole ? '🤝 Companion' : '👤 Pengguna' ?></div>
                    <div><strong><?= $isCompanionRole ? 'Hubungan dengan admin' : 'Alur akun' ?></strong><br><?= $isCompanionRole ? 'Admin akan memverifikasi pembayaran dan menghubungi kamu lewat WhatsApp saat ada order yang masuk.' : 'Akun kamu akan dipakai untuk memesan dan mengikuti status pesanan secara terarah.' ?></div>
                </div>
                <button type="submit" id="submitBtn" class="btn btn-primary btn-block" style="margin-top:16px"><?= htmlspecialchars($roleButtonLabel) ?></button>
            </form>
            <?php endif; ?>
            <p class="auth-link">Sudah punya akun? <a href="login.php">Masuk di sini</a></p>
        </div>
    </div>
</div>
<?php include '../includes/footer.php'; ?>