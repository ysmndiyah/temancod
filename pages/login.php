<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

if (isLoggedIn()) {
    redirect('');
}

$pageTitle = 'Masuk';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = isset($_POST['email']) ? sanitize($_POST['email']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    if ($email === '' || $password === '') {

        $error = 'Email dan password wajib diisi!';

    } else {

        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

echo "<pre>";
var_dump($user);
echo "</pre>";

if ($user) {

    echo "Password yang diketik : " . $password . "<br>";
    echo "Hash di database : " . $user['password'] . "<br>";

    if (password_verify($password, $user['password'])) {
        echo "<h2>PASSWORD COCOK</h2>";
    } else {
        echo "<h2>PASSWORD TIDAK COCOK</h2>";
    }

} else {
    echo "<h2>EMAIL TIDAK DITEMUKAN</h2>";
}

exit();

        if ($user && password_verify($password, $user['password'])) {

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['nama'] = $user['nama'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'];

            if ($user['role'] === 'companion') {
                $stmt2 = $conn->prepare("SELECT Id FROM Companions WHERE User_id = ?");
                $stmt2->bind_param("i", $user['id']);
                $stmt2->execute();
                $c = $stmt2->get_result()->fetch_assoc();
                if ($c) {
                    $_SESSION['companion_id'] = $c['Id'];
                }
            }

            redirect('pages/dashboard.php');

        } else {
            $error = 'Email atau password salah.';
        }
    }
}

include '../includes/header.php';
?>

<div class="auth-wrapper">
    <div class="auth-card">
        <h2>Selamat Datang 👋</h2>
        <p class="subtitle">Masuk ke akun TemanCOD kamu</p>

        <?php if ($error !== '') { ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php } ?>

        <form method="POST" action="">

            <div class="form-group">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" placeholder="email@gmail.com" required>
            </div>

            <div class="form-group">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" placeholder="Password kamu" required>
            </div>

            <button type="submit" class="btn btn-primary btn-block" style="margin-top:8px">Masuk</button>

        </form>

        <p class="auth-link">Belum punya akun? <a href="register.php">Daftar gratis</a></p>
        <p class="auth-link" style="margin-top:8px"><a href="register.php?role=companion">Ingin jadi Companion? Daftar di sini →</a></p>
    </div>
</div>

<?php include '../includes/footer.php'; ?>