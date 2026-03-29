<?php
// ============================================
// FILE: admin/dashboard.php
// Fungsi: Dashboard utama untuk admin
// ============================================

session_start();
require_once '../config/koneksi.php';
cekAdmin(); // Proteksi: hanya admin yang boleh akses

// Ambil statistik untuk kartu dashboard
$total_user     = $koneksi->query("SELECT COUNT(*) as t FROM users WHERE role = 'user'")->fetch_assoc()['t'];
$total_pesan    = $koneksi->query("SELECT COUNT(*) as t FROM buku_tamu")->fetch_assoc()['t'];
$pending        = $koneksi->query("SELECT COUNT(*) as t FROM buku_tamu WHERE status = 'pending'")->fetch_assoc()['t'];
$approved       = $koneksi->query("SELECT COUNT(*) as t FROM buku_tamu WHERE status = 'approved'")->fetch_assoc()['t'];
$rejected       = $koneksi->query("SELECT COUNT(*) as t FROM buku_tamu WHERE status = 'rejected'")->fetch_assoc()['t'];

// Ambil 5 pesan terbaru
$pesan_terbaru  = $koneksi->query("
    SELECT bt.*, u.nama as nama_user 
    FROM buku_tamu bt 
    JOIN users u ON bt.user_id = u.id 
    ORDER BY bt.tanggal DESC 
    LIMIT 5
");

$success = $_SESSION['success'] ?? '';
$error   = $_SESSION['error'] ?? '';
unset($_SESSION['success'], $_SESSION['error']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Buku Tamu Digital</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<div class="dashboard-wrapper">
    <!-- OVERLAY untuk mobile -->
    <div class="sidebar-overlay" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:99;"></div>

    <!-- ===== SIDEBAR ===== -->
    <aside class="sidebar">
        <div class="sidebar-header">
            <div class="sidebar-brand">
                <div class="brand-icon">📖</div>
                <span>Buku Tamu</span>
            </div>
        </div>

        <div class="sidebar-user">
            <div class="sidebar-avatar"><?= strtoupper(substr($_SESSION['nama'], 0, 1)) ?></div>
            <div>
                <div class="sidebar-user-name"><?= htmlspecialchars($_SESSION['nama']) ?></div>
                <div class="sidebar-user-role">👑 <?= $_SESSION['role'] ?></div>
            </div>
        </div>

        <nav class="sidebar-nav">
            <div class="nav-section-title">Menu Admin</div>
            <a href="dashboard.php" class="active"><span class="nav-icon">🏠</span> Dashboard</a>
            <a href="kelola_pesan.php"><span class="nav-icon">💬</span> Kelola Pesan</a>
            <a href="kelola_user.php"><span class="nav-icon">👥</span> Kelola User</a>

            <div class="nav-section-title" style="margin-top:15px;">Akun</div>
            <a href="../index.php"><span class="nav-icon">🌐</span> Lihat Website</a>
            <a href="../auth/logout.php"><span class="nav-icon">🚪</span> Logout</a>
        </nav>
    </aside>

    <!-- ===== MAIN CONTENT ===== -->
    <div class="main-content">
        <div class="top-bar">
            <div class="d-flex align-center gap-1">
                <button class="hamburger" id="hamburger" style="background:none;border:none;cursor:pointer;flex-direction:column;gap:4px;display:flex;padding:5px;">
                    <span style="display:block;width:22px;height:2px;background:var(--dark);border-radius:2px;"></span>
                    <span style="display:block;width:22px;height:2px;background:var(--dark);border-radius:2px;"></span>
                    <span style="display:block;width:22px;height:2px;background:var(--dark);border-radius:2px;"></span>
                </button>
                <h1>Dashboard Admin</h1>
            </div>
            <div class="top-bar-actions">
                <span style="font-size:0.85rem;color:var(--gray);">
                    📅 <?= date('d F Y') ?>
                </span>
                <a href="../auth/logout.php" class="btn btn-danger btn-sm">🚪 Logout</a>
            </div>
        </div>

        <div class="page-content">
            <?php if ($success): ?>
                <div class="alert alert-success">✅ <?= htmlspecialchars($success) ?></div>
            <?php endif; ?>
            <?php if ($error): ?>
                <div class="alert alert-danger">❌ <?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <!-- Sambutan -->
            <div style="margin-bottom:25px;">
                <h2 style="font-size:1.3rem;font-weight:700;color:var(--dark);">
                    Halo, <?= htmlspecialchars($_SESSION['nama']) ?>! 👋
                </h2>
                <p style="color:var(--gray);font-size:0.9rem;">Berikut adalah ringkasan aktivitas website hari ini.</p>
            </div>

            <!-- Kartu Statistik -->
            <div class="stats-cards">
                <div class="stat-card">
                    <div class="icon-box blue">👥</div>
                    <div class="stat-info">
                        <div class="num"><?= $total_user ?></div>
                        <div class="label">Total User</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="icon-box green">💬</div>
                    <div class="stat-info">
                        <div class="num"><?= $total_pesan ?></div>
                        <div class="label">Total Pesan</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="icon-box yellow">⏳</div>
                    <div class="stat-info">
                        <div class="num"><?= $pending ?></div>
                        <div class="label">Pesan Pending</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="icon-box green">✅</div>
                    <div class="stat-info">
                        <div class="num"><?= $approved ?></div>
                        <div class="label">Disetujui</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="icon-box red">❌</div>
                    <div class="stat-info">
                        <div class="num"><?= $rejected ?></div>
                        <div class="label">Ditolak</div>
                    </div>
                </div>
            </div>

            <!-- Tabel Pesan Terbaru -->
            <div class="card">
                <div class="card-header">
                    <h2>💬 Pesan Terbaru</h2>
                    <a href="kelola_pesan.php" class="btn btn-primary btn-sm">Lihat Semua</a>
                </div>
                <div class="table-wrapper">
                    <table>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Pengirim</th>
                                <th>Pesan</th>
                                <th>Status</th>
                                <th>Tanggal</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($pesan_terbaru && $pesan_terbaru->num_rows > 0):
                                $no = 1;
                                while ($row = $pesan_terbaru->fetch_assoc()): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td>
                                        <div style="font-weight:600;font-size:0.875rem;"><?= htmlspecialchars($row['nama']) ?></div>
                                        <div style="font-size:0.75rem;color:var(--gray);"><?= htmlspecialchars($row['email']) ?></div>
                                    </td>
                                    <td style="max-width:250px;">
                                        <div style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap;font-size:0.85rem;">
                                            <?= htmlspecialchars(substr($row['pesan'], 0, 80)) ?>...
                                        </div>
                                    </td>
                                    <td>
                                        <?php if ($row['status'] === 'pending'): ?>
                                            <span class="badge badge-pending">⏳ Pending</span>
                                        <?php elseif ($row['status'] === 'approved'): ?>
                                            <span class="badge badge-approved">✅ Approved</span>
                                        <?php else: ?>
                                            <span class="badge badge-rejected">❌ Rejected</span>
                                        <?php endif; ?>
                                    </td>
                                    <td style="font-size:0.8rem;color:var(--gray);">
                                        <?= date('d/m/Y', strtotime($row['tanggal'])) ?>
                                    </td>
                                    <td>
                                        <div class="table-actions">
                                            <?php if ($row['status'] === 'pending'): ?>
                                                <a href="../proses/approve.php?id=<?= $row['id'] ?>" class="btn btn-success btn-sm">✅</a>
                                                <a href="../proses/reject.php?id=<?= $row['id'] ?>" class="btn btn-danger btn-sm">❌</a>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; else: ?>
                                <tr>
                                    <td colspan="6" class="text-center" style="padding:40px;color:var(--gray);">
                                        📭 Belum ada pesan tamu
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div><!-- /page-content -->
    </div><!-- /main-content -->
</div><!-- /dashboard-wrapper -->

<script src="../assets/js/script.js"></script>
</body>
</html>
