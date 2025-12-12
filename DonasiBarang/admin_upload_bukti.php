<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

if (!isset($_GET['id'])) {
    header('Location: admin_donasi.php');
    exit();
}

$id = (int)$_GET['id'];
$query = "SELECT d.*, u.nama as user_nama FROM donasi d JOIN users u ON d.user_id = u.id WHERE d.id = $id";
$result = db_query($conn, $query);

if (db_num_rows($result) == 0) {
    header('Location: admin_donasi.php');
    exit();
}

$donasi = db_fetch($result);
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!isset($_FILES['bukti']) || $_FILES['bukti']['error'] != 0) {
        $error = 'Pilih file bukti terlebih dahulu!';
    } else {
        $allowed = ['jpg', 'jpeg', 'png'];
        $filename = $_FILES['bukti']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        $size = $_FILES['bukti']['size'];

        if (!in_array($ext, $allowed)) {
            $error = 'Format file harus JPG atau PNG!';
        } elseif ($size > 2 * 1024 * 1024) {
            $error = 'Ukuran file maksimal 2MB!';
        } else {
            $newname = 'bukti_' . time() . '_' . rand(1000, 9999) . '.' . $ext;
            if (move_uploaded_file($_FILES['bukti']['tmp_name'], 'uploads/bukti/' . $newname)) {
                if ($donasi['bukti_penyaluran'] && file_exists('uploads/bukti/' . $donasi['bukti_penyaluran'])) {
                    unlink('uploads/bukti/' . $donasi['bukti_penyaluran']);
                }
                
                db_query($conn, "UPDATE donasi SET bukti_penyaluran = '$newname', status = 'tersalurkan' WHERE id = $id");
                header('Location: admin_donasi.php?success=upload');
                exit();
            } else {
                $error = 'Gagal upload bukti!';
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
    <title>Upload Bukti Penyaluran - Admin</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <header class="dashboard-header admin-header">
            <div class="header-left">
                <h1>📷 Upload Bukti Penyaluran</h1>
            </div>
            <div class="user-info">
                <span class="user-name">Admin: <?php echo htmlspecialchars($_SESSION['admin_nama']); ?></span>
                <a href="admin_logout.php" class="btn btn-outline btn-sm">Keluar</a>
            </div>
        </header>

        <nav class="dashboard-nav">
            <a href="admin_donasi.php" class="btn btn-secondary">← Kembali ke Donasi</a>
        </nav>

        <section class="form-section">
            <h2>Detail Donasi</h2>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>

            <div class="donasi-preview">
                <div class="preview-grid">
                    <div class="preview-image">
                        <?php if ($donasi['foto'] && file_exists('uploads/' . $donasi['foto'])): ?>
                            <img src="uploads/<?php echo htmlspecialchars($donasi['foto']); ?>" alt="Foto Barang">
                        <?php else: ?>
                            <div class="no-image">📦</div>
                        <?php endif; ?>
                    </div>
                    <div class="preview-info">
                        <p><strong>Nama Barang:</strong> <?php echo htmlspecialchars($donasi['nama_barang']); ?></p>
                        <p><strong>Deskripsi:</strong> <?php echo htmlspecialchars($donasi['deskripsi']); ?></p>
                        <p><strong>Donor:</strong> <?php echo htmlspecialchars($donasi['user_nama']); ?></p>
                        <p><strong>Status:</strong> 
                            <span class="status-badge status-<?php echo $donasi['status']; ?>">
                                <?php 
                                if ($donasi['status'] == 'tersedia') echo '📦 Tersedia';
                                elseif ($donasi['status'] == 'dikirim') echo '🚚 Dikirim';
                                else echo '✅ Tersalurkan';
                                ?>
                            </span>
                        </p>
                    </div>
                </div>
            </div>

            <form method="POST" action="" enctype="multipart/form-data" class="donation-form">
                <div class="form-group">
                    <label for="bukti">Upload Bukti Penyaluran</label>
                    <input type="file" id="bukti" name="bukti" accept=".jpg,.jpeg,.png" required>
                    <small>Format: JPG/PNG, Maksimal 2MB</small>
                </div>
                <button type="submit" class="btn btn-success btn-block">✅ Upload & Tandai Tersalurkan</button>
            </form>
        </section>

        <footer class="footer">
            <p>&copy; 2024 Donasi Barang - Admin Panel</p>
        </footer>
    </div>
</body>
</html>
