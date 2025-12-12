<?php
session_start();
require_once 'db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = db_escape($conn, $_POST['email']);
    $password = md5($_POST['password']);

    $query = "SELECT * FROM users WHERE email = '$email' AND password = '$password'";
    $result = db_query($conn, $query);

    if (db_num_rows($result) == 1) {
        $user = db_fetch($result);
        
        if ($user['role'] == 'admin') {
            $_SESSION['admin_id'] = $user['id'];
            $_SESSION['admin_nama'] = $user['nama'];
            $_SESSION['admin_email'] = $user['email'];
            header('Location: admin_dashboard.php');
            exit();
        } else {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_nama'] = $user['nama'];
            $_SESSION['user_email'] = $user['email'];
            header('Location: dashboard.php');
            exit();
        }
    } else {
        $error = 'Email atau password salah!';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Donasi Barang</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div class="auth-container">
            <div class="auth-box">
                <div class="auth-logo">
                    <span class="logo-icon">🎁</span>
                </div>
                <h2>Masuk</h2>
                <p>Masuk ke akun Donasi Barang Anda</p>

                <?php if ($error): ?>
                    <div class="alert alert-error"><?php echo $error; ?></div>
                <?php endif; ?>

                <form method="POST" action="">
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" required placeholder="Masukkan email" autocomplete="email">
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" required placeholder="Masukkan password" autocomplete="current-password">
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">Masuk</button>
                </form>

                <div class="auth-links">
                    <p>Belum punya akun? <a href="register.php">Daftar sekarang</a></p>
                    <p><a href="index.php">Kembali ke Beranda</a></p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
