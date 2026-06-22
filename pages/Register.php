<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';
if (isLoggedIn()) redirect('');
$pageTitle = 'Daftar';
$role = isset($_GET['role']) && $_GET['role'] === 'companion' ? 'companion' : 'user';
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
    <div class="auth-card">
        <h2>Buat Akun Baru</h2>
        <p class="subtitle">Bergabung dan mulai gunakan layanan TemanCOD</p>
        <?php if ($error): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>
        <?php if ($success): ?><div class="alert alert-success"><?= $success ?> <a href="login.php">Masuk sekarang</a></div><?php endif; ?>
        <?php if (!$success): ?>
        <form method="POST">
            <div class="form-group"><label class="form-label">Nama Lengkap</label><input type="text" name="nama" class="form-control" placeholder="Nama kamu" required></div>
            <div class="form-group"><label class="form-label">Email</label><input type="email" name="email" class="form-control" placeholder="email@gmail.com" required></div>
            <div class="form-group"><label class="form-label">No. HP / WhatsApp</label><input type="text" name="no_hp" class="form-control" placeholder="08xxxxxxxxxx"></div>
            <div class="form-row">
                <div class="form-group"><label class="form-label">Password</label><input type="password" name="password" class="form-control" placeholder="Min. 6 karakter" required></div>
                <div class="form-group"><label class="form-label">Konfirmasi Password</label><input type="password" name="confirm_password" class="form-control" placeholder="Ulangi password" required></div>
            </div>
            <div class="form-group">
                <label class="form-label">Daftar Sebagai</label>
                <select name="role" class="form-control">
                    <option value="user" <?= $role==='user'?'selected':'' ?>>👤 Pengguna</option>
                    <option value="companion" <?= $role==='companion'?'selected':'' ?>>🤝 Companion</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary btn-block">Daftar Sekarang</button>
        </form>
        <?php endif; ?>
        <p class="auth-link">Sudah punya akun? <a href="login.php">Masuk di sini</a></p>
    </div>
</div>
<?php include '../includes/footer.php'; ?>