<?php
// ============================================
// FILE: admin/kelola_user.php
// Fungsi: Admin melihat dan mengelola semua user
// ============================================

session_start();
require_once '../config/koneksi.php';
cekAdmin();

$search   = sanitasi($_GET['search'] ?? '');
$page     = max(1, (int)($_GET['page'] ?? 1));
$per_page = 10;
$offset   = ($page - 1) * $per_page;

// Query dinamis dengan search
$where = "WHERE role = 'user'";
$params = [];
$types  = '';

if (!empty($search)) {
    $where   .= " AND (nama LIKE ? OR email LIKE ? OR username LIKE ?)";
    $cari     = "%$search%";
    $params[] = $cari;
    $params[] = $cari;
    $params[] = $cari;
    $types   .= 'sss';
}

// Hitung total
$count_stmt = $koneksi->prepare("SELECT COUNT(*) as total FROM users $where");
if (!empty($params)) $count_stmt->bind_param($types, ...$params);
$count_stmt->execute();
$total_data = $count_stmt->get_result()->fetch_assoc()['total'];
$total_page = ceil($total_data / $per_page);
$count_stmt->close();

// Ambil data
$all_params   = $params;
$all_params[] = $per_page;
$all_params[] = $offset;
$all_types    = $types . 'ii';

$stmt = $koneksi->prepare("SELECT u.*, (SELECT COUNT(*) FROM buku_tamu bt WHERE bt.user_id = u.id) as total_pesan FROM users u $where ORDER BY u.created_at DESC LIMIT ? OFFSET ?");
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
    <title>Kelola User - Admin</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<div class="dashboard-wrapper">
    <div class="sidebar-overlay" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:99;"></div>

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
            <a href="dashboard.php"><span class="nav-icon">🏠</span> Dashboard</a>
            <a href="kelola_pesan.php"><span class="nav-icon">💬</span> Kelola Pesan</a>
            <a href="kelola_user.php" class="active"><span class="nav-icon">👥</span> Kelola User</a>
            <div class="nav-section-title" style="margin-top:15px;">Akun</div>
            <a href="../index.php"><span class="nav-icon">🌐</span> Lihat Website</a>
            <a href="../auth/logout.php"><span class="nav-icon">🚪</span> Logout</a>
        </nav>
    </aside>

    <div class="main-content">
        <div class="top-bar">
            <div class="d-flex align-center gap-1">
                <button class="hamburger" id="hamburger" style="background:none;border:none;cursor:pointer;flex-direction:column;gap:4px;display:flex;padding:5px;">
                    <span style="display:block;width:22px;height:2px;background:var(--dark);border-radius:2px;"></span>
                    <span style="display:block;width:22px;height:2px;background:var(--dark);border-radius:2px;"></span>
                    <span style="display:block;width:22px;height:2px;background:var(--dark);border-radius:2px;"></span>
                </button>
                <h1>👥 Kelola User</h1>
            </div>
        </div>

        <div class="page-content">
            <?php if ($success): ?>
                <div class="alert alert-success">✅ <?= htmlspecialchars($success) ?></div>
            <?php endif; ?>

            <div class="card">
                <form method="GET">
                    <div class="filter-bar">
                        <div class="search-input">
                            <span class="icon">🔍</span>
                            <input type="text" name="search" placeholder="Cari nama, email, username..." value="<?= htmlspecialchars($search) ?>">
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm">🔍 Cari</button>
                        <a href="kelola_user.php" class="btn btn-secondary btn-sm">↩️ Reset</a>
                    </div>
                </form>

                <div class="table-wrapper">
                    <table>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nama</th>
                                <th>Email</th>
                                <th>Username</th>
                                <th>Total Pesan</th>
                                <th>Role</th>
                                <th>Bergabung</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($data && $data->num_rows > 0):
                                $no = $offset + 1;
                                while ($row = $data->fetch_assoc()): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td>
                                        <div style="display:flex;align-items:center;gap:10px;">
                                            <div style="width:36px;height:36px;border-radius:50%;background:linear-gradient(135deg,var(--primary),var(--primary-dark));display:flex;align-items:center;justify-content:center;color:white;font-weight:700;font-size:0.85rem;flex-shrink:0;">
                                                <?= strtoupper(substr($row['nama'], 0, 1)) ?>
                                            </div>
                                            <span style="font-weight:600;font-size:0.875rem;"><?= htmlspecialchars($row['nama']) ?></span>
                                        </div>
                                    </td>
                                    <td style="font-size:0.85rem;color:var(--gray);"><?= htmlspecialchars($row['email']) ?></td>
                                    <td><code style="font-size:0.8rem;background:var(--gray-light);padding:3px 8px;border-radius:5px;"><?= htmlspecialchars($row['username']) ?></code></td>
                                    <td>
                                        <span style="font-weight:600;color:var(--primary-dark);"><?= $row['total_pesan'] ?></span>
                                        <span style="font-size:0.75rem;color:var(--gray);"> pesan</span>
                                    </td>
                                    <td><span class="badge badge-user">👤 User</span></td>
                                    <td style="font-size:0.8rem;color:var(--gray);"><?= date('d/m/Y', strtotime($row['created_at'])) ?></td>
                                    <td>
                                        <a href="../proses/proses_hapus_user.php?id=<?= $row['id'] ?>" 
                                           class="btn btn-danger btn-sm btn-delete-confirm"
                                           title="Hapus user ini">
                                            🗑️ Hapus
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; else: ?>
                                <tr>
                                    <td colspan="8">
                                        <div class="empty-state">
                                            <div class="icon">👥</div>
                                            <h3>Belum ada user terdaftar</h3>
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
                        Total <strong><?= $total_data ?></strong> user
                    </div>
                    <div class="pagination-links">
                        <?php for ($i = 1; $i <= $total_page; $i++): ?>
                            <a href="?page=<?= $i ?>&search=<?= urlencode($search) ?>" 
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
