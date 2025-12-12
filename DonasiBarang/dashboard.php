<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$query = "SELECT * FROM donasi WHERE user_id = $user_id ORDER BY tanggal DESC";
$result = db_query($conn, $query);

$count_tersedia = 0;
$count_dikirim = 0;
$count_tersalurkan = 0;

$count_query = "SELECT status, COUNT(*) as total FROM donasi WHERE user_id = $user_id GROUP BY status";
$count_result = db_query($conn, $count_query);
while ($row = db_fetch($count_result)) {
    if ($row['status'] == 'tersedia') $count_tersedia = $row['total'];
    if ($row['status'] == 'dikirim') $count_dikirim = $row['total'];
    if ($row['status'] == 'tersalurkan') $count_tersalurkan = $row['total'];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Donasi Barang</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <header class="dashboard-header">
            <div class="header-left">
                <h1>🎁 Dashboard Donasi</h1>
            </div>
            <div class="user-info">
                <span class="user-name">Halo, <?php echo htmlspecialchars($_SESSION['user_nama']); ?>!</span>
                <a href="logout.php" class="btn btn-outline btn-sm">Keluar</a>
            </div>
        </header>

        <div class="stats-grid">
            <div class="stat-card stat-tersedia">
                <div class="stat-icon">📦</div>
                <div class="stat-info">
                    <h3><?php echo $count_tersedia; ?></h3>
                    <p>Tersedia</p>
                </div>
            </div>
            <div class="stat-card stat-dikirim">
                <div class="stat-icon">🚚</div>
                <div class="stat-info">
                    <h3><?php echo $count_dikirim; ?></h3>
                    <p>Dikirim</p>
                </div>
            </div>
            <div class="stat-card stat-tersalurkan">
                <div class="stat-icon">✅</div>
                <div class="stat-info">
                    <h3><?php echo $count_tersalurkan; ?></h3>
                    <p>Tersalurkan</p>
                </div>
            </div>
        </div>

        <nav class="dashboard-nav">
            <a href="tambah.php" class="btn btn-primary">+ Tambah Donasi</a>
            <a href="index.php" class="btn btn-secondary">Beranda</a>
        </nav>

        <section class="dashboard-content">
            <h2>Donasi Saya</h2>

            <?php if (isset($_GET['success'])): ?>
                <div class="alert alert-success">
                    <?php
                    $msg = $_GET['success'];
                    if ($msg == 'add') echo 'Donasi berhasil ditambahkan!';
                    elseif ($msg == 'edit') echo 'Donasi berhasil diperbarui!';
                    elseif ($msg == 'delete') echo 'Donasi berhasil dihapus!';
                    elseif ($msg == 'kirim') echo 'Donasi berhasil ditandai sebagai dikirim!';
                    ?>
                </div>
            <?php endif; ?>

            <?php if (db_num_rows($result) > 0): ?>
                <div class="donation-grid">
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
                                            <img src="uploads/bukti/<?php echo htmlspecialchars($row['bukti_penyaluran']); ?>" alt="Bukti Penyaluran" class="bukti-image">
                                            <span class="bukti-text">Klik untuk lihat bukti</span>
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="donation-actions">
                                <?php if ($row['status'] == 'tersedia'): ?>
                                    <a href="edit.php?id=<?php echo $row['id']; ?>" class="btn btn-secondary btn-sm">✏️ Edit</a>
                                    <a href="delete.php?id=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin hapus donasi ini?')">🗑️ Hapus</a>
                                    <a href="kirim.php?id=<?php echo $row['id']; ?>" class="btn btn-success btn-sm" onclick="return confirm('Tandai sebagai dikirim?')">🚚 Kirim</a>
                                <?php elseif ($row['status'] == 'dikirim'): ?>
                                    <span class="info-text">Menunggu penyaluran...</span>
                                <?php else: ?>
                                    <span class="info-text success-text">Terima kasih! Donasi Anda telah tersalurkan.</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <div class="empty-icon">📦</div>
                    <p>Anda belum memiliki donasi.</p>
                    <a href="tambah.php" class="btn btn-primary">Tambah Donasi Pertama</a>
                </div>
            <?php endif; ?>
        </section>

        <footer class="footer">
            <p>&copy; 2024 Donasi Barang</p>
        </footer>
    </div>
</body>
</html>
