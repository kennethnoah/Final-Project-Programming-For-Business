<?php
session_start();
require_once 'db.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = db_escape($conn, $_POST['nama']);
    $email = db_escape($conn, $_POST['email']);
    $password = md5($_POST['password']);
    $confirm = md5($_POST['confirm_password']);

    if ($password != $confirm) {
        $error = 'Password tidak cocok!';
    } else {
        $check = db_query($conn, "SELECT id FROM users WHERE email = '$email'");
        if (db_num_rows($check) > 0) {
            $error = 'Email sudah terdaftar!';
        } else {
            $query = "INSERT INTO users (nama, email, password, role) VALUES ('$nama', '$email', '$password', 'user')";
            if (db_query($conn, $query)) {
                $success = 'Registrasi berhasil! Silakan login.';
            } else {
                $error = 'Gagal mendaftar. Coba lagi.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar - Donasi Barang</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div class="auth-container">
            <div class="auth-box">
                <div class="auth-logo">
                    <span class="logo-icon">🎁</span>
                </div>
                <h2>Daftar Akun</h2>
                <p>Buat akun untuk mulai berdonasi</p>

                <?php if ($error): ?>
                    <div class="alert alert-error"><?php echo $error; ?></div>
                <?php endif; ?>
                <?php if ($success): ?>
                    <div class="alert alert-success"><?php echo $success; ?></div>
                <?php endif; ?>

                <form method="POST" action="">
                    <div class="form-group">
                        <label for="nama">Nama Lengkap</label>
                        <input type="text" id="nama" name="nama" required placeholder="Masukkan nama lengkap" autocomplete="name">
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" required placeholder="Masukkan email" autocomplete="email">
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" required placeholder="Masukkan password" autocomplete="new-password">
                    </div>
                    <div class="form-group">
                        <label for="confirm_password">Konfirmasi Password</label>
                        <input type="password" id="confirm_password" name="confirm_password" required placeholder="Ulangi password" autocomplete="new-password">
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">Daftar</button>
                </form>

                <div class="auth-links">
                    <p>Sudah punya akun? <a href="login.php">Masuk</a></p>
                    <p><a href="index.php">Kembali ke Beranda</a></p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
