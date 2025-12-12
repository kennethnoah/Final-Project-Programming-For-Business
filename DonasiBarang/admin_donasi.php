<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

$status_filter = isset($_GET['status']) ? $_GET['status'] : '';
$where = '';
if ($status_filter && in_array($status_filter, ['tersedia', 'dikirim', 'tersalurkan'])) {
    $where = "WHERE d.status = '$status_filter'";
}

$query = "SELECT d.*, u.nama as user_nama, u.email as user_email 
          FROM donasi d 
          JOIN users u ON d.user_id = u.id 
          $where
          ORDER BY d.tanggal DESC";
$result = db_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Donasi - Admin</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <header class="dashboard-header admin-header">
            <div class="header-left">
                <h1>📦 Kelola Donasi</h1>
            </div>
            <div class="user-info">
                <span class="user-name">Admin: <?php echo htmlspecialchars($_SESSION['admin_nama']); ?></span>
                <a href="admin_logout.php" class="btn btn-outline btn-sm">Keluar</a>
            </div>
        </header>

        <nav class="dashboard-nav">
            <a href="admin_dashboard.php" class="btn btn-secondary">🏠 Dashboard</a>
            <a href="admin_users.php" class="btn btn-primary">👥 Kelola User</a>
        </nav>

        <section class="dashboard-content">
            <h2>Semua Donasi</h2>
            
            <?php if (isset($_GET['success'])): ?>
                <div class="alert alert-success">
                    <?php
                    $msg = $_GET['success'];
                    if ($msg == 'delete') echo 'Donasi berhasil dihapus!';
                    elseif ($msg == 'upload') echo 'Bukti penyaluran berhasil diupload!';
                    ?>
                </div>
            <?php endif; ?>

            <div class="filter-section">
                <span>Filter:</span>
                <a href="admin_donasi.php" class="btn btn-sm <?php echo !$status_filter ? 'btn-primary' : 'btn-secondary'; ?>">Semua</a>
                <a href="admin_donasi.php?status=tersedia" class="btn btn-sm <?php echo $status_filter == 'tersedia' ? 'btn-success' : 'btn-secondary'; ?>">📦 Tersedia</a>
                <a href="admin_donasi.php?status=dikirim" class="btn btn-sm <?php echo $status_filter == 'dikirim' ? 'btn-warning' : 'btn-secondary'; ?>">🚚 Dikirim</a>
                <a href="admin_donasi.php?status=tersalurkan" class="btn btn-sm <?php echo $status_filter == 'tersalurkan' ? 'btn-info' : 'btn-secondary'; ?>">✅ Tersalurkan</a>
            </div>

            <div class="donation-grid admin-grid">
                <?php if (db_num_rows($result) > 0): ?>
                    <?php while ($row = db_fetch($result)): ?>
                        <div class="donation-card">
                            <div class="donation-image">
                                <?php if ($row['foto'] && file_exists('uploads/' . $row['foto'])): ?>
                                    <img src="uploads/<?php echo htmlspecialchars($row['foto']); ?>" alt="<?php echo htmlspecialchars($row['nama_barang']); ?>">
                                <?php else: ?>
                                    <div class="no-image">📦</div>
                                <?php endif; ?>
                            </div>
                            <div class="donation-info">
                                <h3><?php echo htmlspecialchars($row['nama_barang']); ?></h3>
                                <p class="description"><?php echo htmlspecialchars($row['deskripsi']); ?></p>
                                <p class="donor">👤 <?php echo htmlspecialchars($row['user_nama']); ?></p>
                                <p class="date">📅 <?php echo date('d M Y', strtotime($row['tanggal'])); ?></p>
                                
                                <span class="status-badge status-<?php echo $row['status']; ?>">
                                    <?php 
                                    if ($row['status'] == 'tersedia') echo '📦 Tersedia';
                                    elseif ($row['status'] == 'dikirim') echo '🚚 Dikirim';
                                    else echo '✅ Tersalurkan';
                                    ?>
                                </span>

                                <?php if ($row['status'] == 'tersalurkan' && $row['bukti_penyaluran']): ?>
                                    <div class="bukti-section">
                                        <p class="bukti-label">📷 Bukti Penyaluran:</p>
                                        <a href="uploads/bukti/<?php echo htmlspecialchars($row['bukti_penyaluran']); ?>" target="_blank" class="bukti-link">
                                            <img src="uploads/bukti/<?php echo htmlspecialchars($row['bukti_penyaluran']); ?>" alt="Bukti" class="bukti-image">
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="donation-actions">
                                <?php if ($row['status'] == 'dikirim'): ?>
                                    <a href="admin_upload_bukti.php?id=<?php echo $row['id']; ?>" class="btn btn-success btn-sm">📷 Upload Bukti</a>
                                <?php endif; ?>
                                <a href="admin_delete_donasi.php?id=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin hapus donasi ini?')">🗑️ Hapus</a>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="empty-state full-width">
                        <div class="empty-icon">📦</div>
                        <p>Tidak ada donasi<?php echo $status_filter ? " dengan status '$status_filter'" : ''; ?>.</p>
                    </div>
                <?php endif; ?>
            </div>
        </section>

        <footer class="footer">
            <p>&copy; 2024 Donasi Barang - Admin Panel</p>
        </footer>
    </div>
</body>
</html>
