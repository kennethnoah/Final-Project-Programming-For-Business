<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_barang = db_escape($conn, $_POST['nama_barang']);
    $deskripsi = db_escape($conn, $_POST['deskripsi']);
    $user_id = $_SESSION['user_id'];
    $foto = '';

    if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png'];
        $filename = $_FILES['foto']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        $size = $_FILES['foto']['size'];

        if (!in_array($ext, $allowed)) {
            $error = 'Format file harus JPG atau PNG!';
        } elseif ($size > 2 * 1024 * 1024) {
            $error = 'Ukuran file maksimal 2MB!';
        } else {
            $newname = time() . '_' . rand(1000, 9999) . '.' . $ext;
            if (move_uploaded_file($_FILES['foto']['tmp_name'], 'uploads/' . $newname)) {
                $foto = $newname;
            } else {
                $error = 'Gagal upload foto!';
            }
        }
    }

    if (!$error) {
        $query = "INSERT INTO donasi (user_id, nama_barang, deskripsi, foto, status) VALUES ($user_id, '$nama_barang', '$deskripsi', '$foto', 'tersedia')";
        if (db_query($conn, $query)) {
            header('Location: dashboard.php?success=add');
            exit();
        } else {
            $error = 'Gagal menambahkan donasi!';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Donasi - Donasi Barang</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <header class="dashboard-header">
            <div class="header-left">
                <h1>📦 Tambah Donasi</h1>
            </div>
            <div class="user-info">
                <span class="user-name"><?php echo htmlspecialchars($_SESSION['user_nama']); ?></span>
                <a href="logout.php" class="btn btn-outline btn-sm">Keluar</a>
            </div>
        </header>

        <nav class="dashboard-nav">
            <a href="dashboard.php" class="btn btn-secondary">← Kembali</a>
        </nav>

        <section class="form-section">
            <h2>Form Donasi Baru</h2>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>

            <form method="POST" action="" enctype="multipart/form-data" class="donation-form">
                <div class="form-group">
                    <label for="nama_barang">Nama Barang</label>
                    <input type="text" id="nama_barang" name="nama_barang" required placeholder="Contoh: Pakaian Layak Pakai">
                </div>
                <div class="form-group">
                    <label for="deskripsi">Deskripsi</label>
                    <textarea id="deskripsi" name="deskripsi" rows="4" required placeholder="Jelaskan kondisi dan detail barang"></textarea>
                </div>
                <div class="form-group">
                    <label for="foto">Foto Barang</label>
                    <input type="file" id="foto" name="foto" accept=".jpg,.jpeg,.png">
                    <small>Format: JPG/PNG, Maksimal 2MB (opsional)</small>
                </div>
                <button type="submit" class="btn btn-primary btn-block">+ Tambah Donasi</button>
            </form>
        </section>

        <footer class="footer">
            <p>&copy; 2024 Donasi Barang</p>
        </footer>
    </div>
</body>
</html>
