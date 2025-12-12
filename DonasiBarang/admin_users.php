<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

$query = "SELECT u.*, (SELECT COUNT(*) FROM donasi WHERE user_id = u.id) as total_donasi 
          FROM users u WHERE u.role = 'user' ORDER BY u.id DESC";
$result = db_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola User - Admin</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <header class="dashboard-header admin-header">
            <div class="header-left">
                <h1>👥 Kelola User</h1>
            </div>
            <div class="user-info">
                <span class="user-name">Admin: <?php echo htmlspecialchars($_SESSION['admin_nama']); ?></span>
                <a href="admin_logout.php" class="btn btn-outline btn-sm">Keluar</a>
            </div>
        </header>

        <nav class="dashboard-nav">
            <a href="admin_dashboard.php" class="btn btn-secondary">🏠 Dashboard</a>
            <a href="admin_donasi.php" class="btn btn-primary">📦 Kelola Donasi</a>
        </nav>

        <section class="dashboard-content">
            <h2>Daftar User</h2>
            
            <?php if (isset($_GET['success'])): ?>
                <div class="alert alert-success">User berhasil dihapus!</div>
            <?php endif; ?>

            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Total Donasi</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (db_num_rows($result) > 0): ?>
                            <?php while ($row = db_fetch($result)): ?>
                                <tr>
                                    <td><?php echo $row['id']; ?></td>
                                    <td><strong><?php echo htmlspecialchars($row['nama']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                                    <td><?php echo $row['total_donasi']; ?> barang</td>
                                    <td>
                                        <a href="admin_delete_user.php?id=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin hapus user ini? Semua donasi user juga akan terhapus.')">🗑️ Hapus</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center">Belum ada user terdaftar.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>

        <footer class="footer">
            <p>&copy; 2024 Donasi Barang - Admin Panel</p>
        </footer>
    </div>
</body>
</html>
