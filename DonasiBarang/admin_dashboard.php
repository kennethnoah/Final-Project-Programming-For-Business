<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

$total_users = db_fetch(db_query($conn, "SELECT COUNT(*) as total FROM users WHERE role = 'user'"))['total'];
$total_donasi = db_fetch(db_query($conn, "SELECT COUNT(*) as total FROM donasi"))['total'];
$total_tersedia = db_fetch(db_query($conn, "SELECT COUNT(*) as total FROM donasi WHERE status = 'tersedia'"))['total'];
$total_dikirim = db_fetch(db_query($conn, "SELECT COUNT(*) as total FROM donasi WHERE status = 'dikirim'"))['total'];
$total_tersalurkan = db_fetch(db_query($conn, "SELECT COUNT(*) as total FROM donasi WHERE status = 'tersalurkan'"))['total'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Donasi Barang</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <header class="dashboard-header admin-header">
            <div class="header-left">
                <h1>🛠️ Admin Dashboard</h1>
            </div>
            <div class="user-info">
                <span class="user-name">Admin: <?php echo htmlspecialchars($_SESSION['admin_nama']); ?></span>
                <a href="admin_logout.php" class="btn btn-outline btn-sm">Keluar</a>
            </div>
        </header>

        <div class="stats-grid">
            <div class="stat-card stat-users">
                <div class="stat-icon">👥</div>
                <div class="stat-info">
                    <h3><?php echo $total_users; ?></h3>
                    <p>Total User</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">📦</div>
                <div class="stat-info">
                    <h3><?php echo $total_donasi; ?></h3>
                    <p>Total Donasi</p>
                </div>
            </div>
            <div class="stat-card stat-tersedia">
                <div class="stat-icon">🟢</div>
                <div class="stat-info">
                    <h3><?php echo $total_tersedia; ?></h3>
                    <p>Tersedia</p>
                </div>
            </div>
            <div class="stat-card stat-dikirim">
                <div class="stat-icon">🚚</div>
                <div class="stat-info">
                    <h3><?php echo $total_dikirim; ?></h3>
                    <p>Dikirim</p>
                </div>
            </div>
            <div class="stat-card stat-tersalurkan">
                <div class="stat-icon">✅</div>
                <div class="stat-info">
                    <h3><?php echo $total_tersalurkan; ?></h3>
                    <p>Tersalurkan</p>
                </div>
            </div>
        </div>

        <nav class="dashboard-nav">
            <a href="admin_users.php" class="btn btn-primary">👥 Kelola User</a>
            <a href="admin_donasi.php" class="btn btn-primary">📦 Kelola Donasi</a>
            <a href="index.php" class="btn btn-secondary">Beranda</a>
        </nav>

        <section class="dashboard-content">
            <h2>🚚 Donasi Perlu Ditangani</h2>
            <?php
            $pending = db_query($conn, "SELECT d.*, u.nama as user_nama FROM donasi d JOIN users u ON d.user_id = u.id WHERE d.status = 'dikirim' ORDER BY d.tanggal DESC LIMIT 5");
            ?>
            <?php if (db_num_rows($pending) > 0): ?>
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Nama Barang</th>
                                <th>Donor</th>
                                <th>Tanggal</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = db_fetch($pending)): ?>
                                <tr>
                                    <td><strong><?php echo htmlspecialchars($row['nama_barang']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($row['user_nama']); ?></td>
                                    <td><?php echo date('d M Y', strtotime($row['tanggal'])); ?></td>
                                    <td><span class="status-badge status-dikirim">🚚 Dikirim</span></td>
                                    <td>
                                        <a href="admin_upload_bukti.php?id=<?php echo $row['id']; ?>" class="btn btn-success btn-sm">📷 Upload Bukti</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
                <div style="margin-top: 20px;">
                    <a href="admin_donasi.php?status=dikirim" class="btn btn-info">Lihat Semua Donasi Dikirim</a>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <div class="empty-icon">✅</div>
                    <p>Tidak ada donasi yang perlu ditangani.</p>
                </div>
            <?php endif; ?>
        </section>

        <footer class="footer">
            <p>&copy; 2024 Donasi Barang - Admin Panel</p>
        </footer>
    </div>
</body>
</html>
