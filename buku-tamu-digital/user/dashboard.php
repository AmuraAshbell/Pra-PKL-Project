<?php
// ============================================
// FILE: user/dashboard.php
// Fungsi: Dashboard untuk user biasa
// ============================================

session_start();
require_once '../config/koneksi.php';
cekLogin(); // Cek harus login

// Jika admin nyasar ke dashboard user, redirect ke admin
if ($_SESSION['role'] === 'admin') {
    redirect(BASE_URL . '/admin/dashboard.php');
}

$user_id = $_SESSION['user_id'];

// Statistik pesan milik user ini
$total_pesan = $koneksi->query("SELECT COUNT(*) as t FROM buku_tamu WHERE user_id = $user_id")->fetch_assoc()['t'];
$pending     = $koneksi->query("SELECT COUNT(*) as t FROM buku_tamu WHERE user_id = $user_id AND status = 'pending'")->fetch_assoc()['t'];
$approved    = $koneksi->query("SELECT COUNT(*) as t FROM buku_tamu WHERE user_id = $user_id AND status = 'approved'")->fetch_assoc()['t'];
$rejected    = $koneksi->query("SELECT COUNT(*) as t FROM buku_tamu WHERE user_id = $user_id AND status = 'rejected'")->fetch_assoc()['t'];

// Search & filter & pagination
$search   = sanitasi($_GET['search'] ?? '');
$filter   = sanitasi($_GET['status'] ?? '');
$page     = max(1, (int)($_GET['page'] ?? 1));
$per_page = 8;
$offset   = ($page - 1) * $per_page;

// Bangun WHERE clause
$where  = "WHERE bt.user_id = ?";
$params = [$user_id];
$types  = 'i';

if (!empty($search)) {
    $where   .= " AND (bt.pesan LIKE ? OR bt.nama LIKE ?)";
    $cari     = "%$search%";
    $params[] = $cari;
    $params[] = $cari;
    $types   .= 'ss';
}

if (!empty($filter) && in_array($filter, ['pending', 'approved', 'rejected'])) {
    $where   .= " AND bt.status = ?";
    $params[] = $filter;
    $types   .= 's';
}

// Hitung total untuk pagination
$cnt_stmt = $koneksi->prepare("SELECT COUNT(*) as total FROM buku_tamu bt $where");
$cnt_stmt->bind_param($types, ...$params);
$cnt_stmt->execute();
$total_data = $cnt_stmt->get_result()->fetch_assoc()['total'];
$total_page = ceil($total_data / $per_page);
$cnt_stmt->close();

// Ambil data
$all_params   = $params;
$all_params[] = $per_page;
$all_params[] = $offset;
$all_types    = $types . 'ii';

$stmt = $koneksi->prepare("SELECT bt.* FROM buku_tamu bt $where ORDER BY bt.tanggal DESC LIMIT ? OFFSET ?");
$stmt->bind_param($all_types, ...$all_params);
$stmt->execute();
$data = $stmt->get_result();
$stmt->close();

$success = $_SESSION['success'] ?? '';
$error   = $_SESSION['error'] ?? '';
unset($_SESSION['success'], $_SESSION['error']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Buku Tamu Digital</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<div class="dashboard-wrapper">
    <div class="sidebar-overlay" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:99;"></div>

    <!-- SIDEBAR -->
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
                <div class="sidebar-user-role">👤 <?= $_SESSION['role'] ?></div>
            </div>
        </div>
        <nav class="sidebar-nav">
            <div class="nav-section-title">Menu</div>
            <a href="dashboard.php" class="active"><span class="nav-icon">🏠</span> Dashboard</a>
            <a href="tambah_pesan.php"><span class="nav-icon">✏️</span> Tulis Pesan</a>
            <div class="nav-section-title" style="margin-top:15px;">Akun</div>
            <a href="../index.php"><span class="nav-icon">🌐</span> Lihat Website</a>
            <a href="../auth/logout.php"><span class="nav-icon">🚪</span> Logout</a>
        </nav>
    </aside>

    <!-- MAIN CONTENT -->
    <div class="main-content">
        <div class="top-bar">
            <div class="d-flex align-center gap-1">
                <button class="hamburger" id="hamburger" style="background:none;border:none;cursor:pointer;flex-direction:column;gap:4px;display:flex;padding:5px;">
                    <span style="display:block;width:22px;height:2px;background:var(--dark);border-radius:2px;"></span>
                    <span style="display:block;width:22px;height:2px;background:var(--dark);border-radius:2px;"></span>
                    <span style="display:block;width:22px;height:2px;background:var(--dark);border-radius:2px;"></span>
                </button>
                <h1>Buku Tamu Saya</h1>
            </div>
            <div class="top-bar-actions">
                <a href="tambah_pesan.php" class="btn btn-primary btn-sm">✏️ Tulis Pesan Baru</a>
            </div>
        </div>

        <div class="page-content">
            <?php if ($success): ?>
                <div class="alert alert-success">✅ <?= htmlspecialchars($success) ?></div>
            <?php endif; ?>
            <?php if ($error): ?>
                <div class="alert alert-danger">❌ <?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <!-- Greeting -->
            <div style="margin-bottom:25px;padding:20px 25px;background:linear-gradient(135deg,#eff6ff,#dbeafe);border-radius:var(--radius);border:1px solid #bfdbfe;">
                <h2 style="font-size:1.2rem;font-weight:700;margin-bottom:4px;">
                    Halo, <?= htmlspecialchars($_SESSION['nama']) ?>! 👋
                </h2>
                <p style="color:var(--gray);font-size:0.875rem;">Pantau status pesan buku tamu yang sudah kamu kirim.</p>
            </div>

            <!-- Statistik User -->
            <div class="stats-cards" style="grid-template-columns:repeat(auto-fit,minmax(160px,1fr));">
                <div class="stat-card">
                    <div class="icon-box blue">💬</div>
                    <div class="stat-info">
                        <div class="num"><?= $total_pesan ?></div>
                        <div class="label">Total Pesan</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="icon-box yellow">⏳</div>
                    <div class="stat-info">
                        <div class="num"><?= $pending ?></div>
                        <div class="label">Pending</div>
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

            <!-- Tabel Pesan -->
            <div class="card">
                <div class="card-header">
                    <h2>📋 Riwayat Pesan</h2>
                    <a href="tambah_pesan.php" class="btn btn-primary btn-sm">+ Tulis Baru</a>
                </div>

                <form method="GET">
                    <div class="filter-bar">
                        <div class="search-input">
                            <span class="icon">🔍</span>
                            <input type="text" name="search" placeholder="Cari pesan..." value="<?= htmlspecialchars($search) ?>">
                        </div>
                        <select name="status" class="form-control" style="width:auto;min-width:150px;">
                            <option value="">Semua Status</option>
                            <option value="pending"  <?= $filter === 'pending'  ? 'selected' : '' ?>>⏳ Pending</option>
                            <option value="approved" <?= $filter === 'approved' ? 'selected' : '' ?>>✅ Approved</option>
                            <option value="rejected" <?= $filter === 'rejected' ? 'selected' : '' ?>>❌ Rejected</option>
                        </select>
                        <button type="submit" class="btn btn-primary btn-sm">Cari</button>
                        <a href="dashboard.php" class="btn btn-secondary btn-sm">Reset</a>
                    </div>
                </form>

                <div class="table-wrapper">
                    <table>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nama & Email</th>
                                <th>Pesan</th>
                                <th>Status</th>
                                <th>Tanggal</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($data && $data->num_rows > 0):
                                $no = $offset + 1;
                                while ($row = $data->fetch_assoc()): ?>
                                <tr>
                                    <td style="font-weight:600;color:var(--gray);"><?= $no++ ?></td>
                                    <td>
                                        <div style="font-weight:600;font-size:0.875rem;"><?= htmlspecialchars($row['nama']) ?></div>
                                        <div style="font-size:0.75rem;color:var(--gray);"><?= htmlspecialchars($row['email']) ?></div>
                                    </td>
                                    <td style="max-width:250px;">
                                        <div style="font-size:0.85rem;color:var(--gray);overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                                            <?= htmlspecialchars(substr($row['pesan'], 0, 80)) ?>
                                            <?= strlen($row['pesan']) > 80 ? '...' : '' ?>
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
                                    <td style="font-size:0.8rem;color:var(--gray);white-space:nowrap;">
                                        <?= date('d/m/Y', strtotime($row['tanggal'])) ?>
                                    </td>
                                    <td>
                                        <div class="table-actions">
                                            <?php if ($row['status'] === 'pending'): ?>
                                                <a href="edit_pesan.php?id=<?= $row['id'] ?>" 
                                                   class="btn btn-warning btn-sm" title="Edit">✏️</a>
                                            <?php endif; ?>
                                            <a href="../proses/proses_hapus.php?id=<?= $row['id'] ?>&ref=user" 
                                               class="btn btn-danger btn-sm btn-delete-confirm" title="Hapus">🗑️</a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; else: ?>
                                <tr>
                                    <td colspan="6">
                                        <div class="empty-state">
                                            <div class="icon">📝</div>
                                            <h3>Belum ada pesan</h3>
                                            <p>Yuk tulis pesan pertamamu!</p>
                                            <a href="tambah_pesan.php" class="btn btn-primary mt-2">✏️ Tulis Sekarang</a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <?php if ($total_page > 1): ?>
                <div class="pagination">
                    <div style="color:var(--gray);font-size:0.85rem;">
                        Menampilkan <?= min($offset + 1, $total_data) ?>–<?= min($offset + $per_page, $total_data) ?> dari <strong><?= $total_data ?></strong>
                    </div>
                    <div class="pagination-links">
                        <?php for ($i = 1; $i <= $total_page; $i++): ?>
                            <a href="?page=<?= $i ?>&search=<?= urlencode($search) ?>&status=<?= urlencode($filter) ?>"
                               class="page-link <?= $i === $page ? 'active' : '' ?>">
                                <?= $i ?>
                            </a>
                        <?php endfor; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>

        </div>
    </div>
</div>

<script src="../assets/js/script.js"></script>
</body>
</html>
