<?php
// ============================================
// FILE: index.php
// Fungsi: Landing Page / Halaman Utama
// ============================================

session_start();
require_once 'config/koneksi.php';

// Ambil statistik untuk ditampilkan di landing page
$total_pesan    = $koneksi->query("SELECT COUNT(*) as total FROM buku_tamu")->fetch_assoc()['total'] ?? 0;
$approved_pesan = $koneksi->query("SELECT COUNT(*) as total FROM buku_tamu WHERE status = 'approved'")->fetch_assoc()['total'] ?? 0;
$pending_pesan  = $koneksi->query("SELECT COUNT(*) as total FROM buku_tamu WHERE status = 'pending'")->fetch_assoc()['total'] ?? 0;
$total_user     = $koneksi->query("SELECT COUNT(*) as total FROM users WHERE role = 'user'")->fetch_assoc()['total'] ?? 0;

// Ambil pesan terbaru yang sudah approved untuk ditampilkan
$pesan_terbaru = $koneksi->query("
    SELECT bt.*, u.nama as nama_user 
    FROM buku_tamu bt 
    JOIN users u ON bt.user_id = u.id 
    WHERE bt.status = 'approved' 
    ORDER BY bt.tanggal DESC 
    LIMIT 6
");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buku Tamu Digital - Platform Pesan Kunjungan Online</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>📖</text></svg>">
</head>
<body>

<!-- ============================================
     NAVBAR
     ============================================ -->
<nav class="navbar">
    <a href="index.php" class="navbar-brand">
        <div class="brand-icon">📖</div>
        <span>Buku Tamu Digital</span>
    </a>

    <ul class="navbar-menu">
        <li><a href="#home" class="active">Home</a></li>
        <li><a href="#tentang">Tentang</a></li>
        <li><a href="#buku-tamu">Buku Tamu</a></li>
    </ul>

    <div class="navbar-actions">
        <?php if (isset($_SESSION['user_id'])): ?>
            <?php if ($_SESSION['role'] === 'admin'): ?>
                <a href="admin/dashboard.php" class="btn btn-primary">🏠 Dashboard</a>
            <?php else: ?>
                <a href="user/dashboard.php" class="btn btn-primary">🏠 Dashboard</a>
            <?php endif; ?>
        <?php else: ?>
            <a href="auth/login.php" class="btn btn-outline">Login</a>
            <a href="auth/register.php" class="btn btn-primary">Register</a>
        <?php endif; ?>
    </div>

    <!-- Hamburger untuk mobile -->
    <div class="hamburger" id="hamburger">
        <span></span>
        <span></span>
        <span></span>
    </div>
</nav>

<!-- ============================================
     HERO SECTION
     ============================================ -->
<section class="hero" id="home">
    <div class="hero-content">
        <div class="hero-text">
            <div class="hero-badge">
                ✨ Platform Modern
            </div>
            <h1 class="hero-title">
                Selamat Datang di<br>
                <span>Buku Tamu Digital</span>
            </h1>
            <p class="hero-desc">
                Platform modern untuk meninggalkan pesan kunjungan secara online. 
                Aman, mudah, dan dapat dimoderasi oleh admin sebelum dipublikasikan.
            </p>
            <div class="hero-cta">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="<?= $_SESSION['role'] === 'admin' ? 'admin/dashboard.php' : 'user/tambah_pesan.php' ?>" class="btn btn-primary btn-lg">
                        ✏️ Isi Buku Tamu
                    </a>
                    <a href="<?= $_SESSION['role'] === 'admin' ? 'admin/dashboard.php' : 'user/dashboard.php' ?>" class="btn btn-outline btn-lg">
                        🏠 Dashboard
                    </a>
                <?php else: ?>
                    <a href="auth/register.php" class="btn btn-primary btn-lg">
                        ✏️ Isi Buku Tamu
                    </a>
                    <a href="auth/login.php" class="btn btn-outline btn-lg">
                        🔐 Login
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <div class="hero-visual">
            <div class="hero-card-float">
                <div class="card-icon">📖</div>
                <h3 style="font-weight:700;margin-bottom:5px;">Buku Tamu Digital</h3>
                <p style="color:var(--gray);font-size:0.85rem;margin-bottom:15px;">Tinggalkan pesan kunjunganmu</p>
                <div class="hero-stats-mini">
                    <div class="stat-mini">
                        <div class="num"><?= $total_pesan ?></div>
                        <div class="label">Total Pesan</div>
                    </div>
                    <div class="stat-mini">
                        <div class="num"><?= $approved_pesan ?></div>
                        <div class="label">Disetujui</div>
                    </div>
                    <div class="stat-mini">
                        <div class="num"><?= $pending_pesan ?></div>
                        <div class="label">Pending</div>
                    </div>
                    <div class="stat-mini">
                        <div class="num"><?= $total_user ?></div>
                        <div class="label">Pengguna</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ============================================
     FITUR WEBSITE
     ============================================ -->
<section class="section section-alt" id="tentang">
    <div class="container">
        <div class="section-header">
            <div class="section-tag">🚀 Fitur Kami</div>
            <h2 class="section-title">Mengapa Memilih Platform Ini?</h2>
            <p class="section-desc">Dilengkapi dengan berbagai fitur canggih untuk pengalaman buku tamu yang modern dan aman.</p>
        </div>

        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon blue">✏️</div>
                <h3>Isi Buku Tamu Online</h3>
                <p>Tinggalkan pesan kunjunganmu secara online kapan saja dan di mana saja tanpa batas waktu.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon green">🔐</div>
                <h3>Sistem Login Aman</h3>
                <p>Keamanan akun terjamin dengan sistem enkripsi password dan autentikasi sesi yang handal.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon yellow">✅</div>
                <h3>Moderasi Pesan Admin</h3>
                <p>Setiap pesan melewati proses review oleh admin sebelum ditampilkan ke publik.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon purple">📊</div>
                <h3>Status Pesan Real-time</h3>
                <p>Pantau status pesanmu secara real-time: pending, disetujui, atau ditolak.</p>
            </div>
        </div>
    </div>
</section>

<!-- ============================================
     STATISTIK
     ============================================ -->
<div class="stats-section">
    <div class="stats-grid">
        <div class="stat-item">
            <span class="num" data-target="<?= $total_pesan ?>">0</span>
            <span class="label">Total Pesan Tamu</span>
        </div>
        <div class="stat-item">
            <span class="num" data-target="<?= $approved_pesan ?>">0</span>
            <span class="label">Pesan Disetujui</span>
        </div>
        <div class="stat-item">
            <span class="num" data-target="<?= $pending_pesan ?>">0</span>
            <span class="label">Menunggu Review</span>
        </div>
        <div class="stat-item">
            <span class="num" data-target="<?= $total_user ?>">0</span>
            <span class="label">Pengguna Terdaftar</span>
        </div>
    </div>
</div>

<!-- ============================================
     PESAN TAMU TERBARU
     ============================================ -->
<section class="section" id="buku-tamu">
    <div class="container">
        <div class="section-header">
            <div class="section-tag">💬 Buku Tamu</div>
            <h2 class="section-title">Pesan Tamu Terbaru</h2>
            <p class="section-desc">Inilah pesan-pesan dari tamu yang sudah disetujui admin.</p>
        </div>

        <?php if ($pesan_terbaru && $pesan_terbaru->num_rows > 0): ?>
            <div class="messages-grid">
                <?php while ($pesan = $pesan_terbaru->fetch_assoc()): ?>
                    <div class="message-card">
                        <span class="quote-icon">❝</span>
                        <p class="pesan-text"><?= htmlspecialchars($pesan['pesan']) ?></p>
                        <div class="user-info">
                            <div class="avatar"><?= strtoupper(substr($pesan['nama'], 0, 1)) ?></div>
                            <div>
                                <div class="user-name"><?= htmlspecialchars($pesan['nama']) ?></div>
                                <div class="user-date">🕐 <?= date('d M Y', strtotime($pesan['tanggal'])) ?></div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <div class="icon">📭</div>
                <h3>Belum ada pesan tamu</h3>
                <p>Jadilah yang pertama mengisi buku tamu!</p>
                <a href="auth/register.php" class="btn btn-primary mt-2">Daftar & Isi Sekarang</a>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- ============================================
     FOOTER
     ============================================ -->
<footer class="footer">
    <p>
        📖 <strong style="color:white;">Buku Tamu Digital</strong> &copy; <?= date('Y') ?> — 
        Dibuat dengan ❤️ untuk project SMK RPL
    </p>
</footer>

<script src="assets/js/script.js"></script>
</body>
</html>
