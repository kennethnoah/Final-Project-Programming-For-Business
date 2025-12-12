<?php
require_once 'db.php';

$query = "SELECT d.*, u.nama as user_nama FROM donasi d JOIN users u ON d.user_id = u.id WHERE d.status = 'tersedia' ORDER BY d.tanggal DESC LIMIT 6";
$result = db_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donasi Barang - Platform Donasi</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <header class="header">
            <h1>Donasi Barang</h1>
            <p>Platform donasi barang untuk membantu sesama</p>
            <nav class="nav">
                <a href="login.php" class="btn btn-primary">Masuk</a>
                <a href="register.php" class="btn btn-secondary">Daftar</a>
            </nav>
        </header>

        <section class="hero">
            <h2>Berbagi Kebaikan</h2>
            <p>Donasikan barang layak pakai Anda untuk membantu mereka yang membutuhkan. Setiap barang yang Anda berikan akan disalurkan kepada yang membutuhkan.</p>
        </section>

        <section class="features">
            <div class="feature-card">
                <div class="feature-icon">📦</div>
                <h3>Donasi Mudah</h3>
                <p>Upload foto barang dan deskripsi dengan mudah</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">🚚</div>
                <h3>Pengiriman Terpantau</h3>
                <p>Pantau status donasi Anda dari tersedia hingga tersalurkan</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">📷</div>
                <h3>Bukti Penyaluran</h3>
                <p>Dapatkan bukti foto saat barang donasi Anda telah disalurkan</p>
            </div>
        </section>

        <section class="how-it-works">
            <h2>Cara Kerja</h2>
            <div class="steps">
                <div class="step">
                    <div class="step-number">1</div>
                    <h4>Daftar Akun</h4>
                    <p>Buat akun gratis untuk mulai berdonasi</p>
                </div>
                <div class="step">
                    <div class="step-number">2</div>
                    <h4>Upload Barang</h4>
                    <p>Foto dan deskripsikan barang yang ingin didonasikan</p>
                </div>
                <div class="step">
                    <div class="step-number">3</div>
                    <h4>Kirim Donasi</h4>
                    <p>Tandai barang sebagai dikirim saat siap</p>
                </div>
                <div class="step">
                    <div class="step-number">4</div>
                    <h4>Lihat Bukti</h4>
                    <p>Dapatkan bukti foto saat barang tersalurkan</p>
                </div>
            </div>
        </section>

        <section class="dashboard-content">
            <h2>Donasi Terbaru</h2>
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
                                <p class="donor">Oleh: <?php echo htmlspecialchars($row['user_nama']); ?></p>
                                <span class="status-badge status-tersedia">Tersedia</span>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <p>Belum ada donasi tersedia. Jadilah yang pertama berdonasi!</p>
                    <a href="register.php" class="btn btn-primary">Daftar Sekarang</a>
                </div>
            <?php endif; ?>
        </section>

        <footer class="footer">
            <p>&copy; 2024 Donasi Barang. Berbagi untuk sesama.</p>
        </footer>
    </div>
</body>
</html>
