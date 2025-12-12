<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

if (!isset($_GET['id'])) {
    header('Location: dashboard.php');
    exit();
}

$id = (int)$_GET['id'];
$user_id = $_SESSION['user_id'];

$query = "SELECT * FROM donasi WHERE id = $id AND user_id = $user_id AND status = 'tersedia'";
$result = db_query($conn, $query);

if (db_num_rows($result) == 0) {
    header('Location: dashboard.php');
    exit();
}

$donasi = db_fetch($result);
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_barang = db_escape($conn, $_POST['nama_barang']);
    $deskripsi = db_escape($conn, $_POST['deskripsi']);
    $foto = $donasi['foto'];

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
                if ($donasi['foto'] && file_exists('uploads/' . $donasi['foto'])) {
                    unlink('uploads/' . $donasi['foto']);
                }
                $foto = $newname;
            } else {
                $error = 'Gagal upload foto!';
            }
        }
    }

    if (!$error) {
        $query = "UPDATE donasi SET nama_barang = '$nama_barang', deskripsi = '$deskripsi', foto = '$foto' WHERE id = $id";
        if (db_query($conn, $query)) {
            header('Location: dashboard.php?success=edit');
            exit();
        } else {
            $error = 'Gagal memperbarui donasi!';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Donasi - Donasi Barang</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <header class="dashboard-header">
            <div class="header-left">
                <h1>✏️ Edit Donasi</h1>
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
            <h2>Edit Donasi</h2>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>

            <form method="POST" action="" enctype="multipart/form-data" class="donation-form">
                <div class="form-group">
                    <label for="nama_barang">Nama Barang</label>
                    <input type="text" id="nama_barang" name="nama_barang" required value="<?php echo htmlspecialchars($donasi['nama_barang']); ?>">
                </div>
                <div class="form-group">
                    <label for="deskripsi">Deskripsi</label>
                    <textarea id="deskripsi" name="deskripsi" rows="4" required><?php echo htmlspecialchars($donasi['deskripsi']); ?></textarea>
                </div>
                
                <?php if ($donasi['foto'] && file_exists('uploads/' . $donasi['foto'])): ?>
                <div class="current-image">
                    <p>Foto Saat Ini:</p>
                    <img src="uploads/<?php echo htmlspecialchars($donasi['foto']); ?>" alt="Foto" style="max-width: 200px; border-radius: 8px;">
                </div>
                <?php endif; ?>
                
                <div class="form-group">
                    <label for="foto">Ganti Foto (opsional)</label>
                    <input type="file" id="foto" name="foto" accept=".jpg,.jpeg,.png">
                    <small>Format: JPG/PNG, Maksimal 2MB</small>
                </div>
                <button type="submit" class="btn btn-primary btn-block">💾 Simpan Perubahan</button>
            </form>
        </section>

        <footer class="footer">
            <p>&copy; 2024 Donasi Barang</p>
        </footer>
    </div>
</body>
</html>
