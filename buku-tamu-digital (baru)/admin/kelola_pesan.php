<?php
// ============================================
// FILE: admin/kelola_pesan.php
// Fungsi: Admin mengelola semua pesan tamu
//         (approve, reject, edit, hapus, search, filter, pagination)
// ============================================

session_start();
require_once '../config/koneksi.php';
cekAdmin();

// ===== PARAMETER SEARCH, FILTER, PAGINATION =====
$search    = sanitasi($_GET['search'] ?? '');
$filter    = sanitasi($_GET['status'] ?? '');
$page      = max(1, (int)($_GET['page'] ?? 1));
$per_page  = 10;
$offset    = ($page - 1) * $per_page;

// ===== BANGUN QUERY DINAMIS =====
$where = "WHERE 1=1";
$params = [];
$types  = '';

if (!empty($search)) {
    $where   .= " AND (bt.nama LIKE ? OR bt.email LIKE ? OR bt.pesan LIKE ?)";
    $cari     = "%$search%";
    $params[] = $cari;
    $params[] = $cari;
    $params[] = $cari;
    $types   .= 'sss';
}

if (!empty($filter) && in_array($filter, ['pending', 'approved', 'rejected'])) {
    $where   .= " AND bt.status = ?";
    $params[] = $filter;
    $types   .= 's';
}

// Hitung total data (untuk pagination)
$count_sql  = "SELECT COUNT(*) as total FROM buku_tamu bt JOIN users u ON bt.user_id = u.id $where";
$count_stmt = $koneksi->prepare($count_sql);
if (!empty($params)) {
    $count_stmt->bind_param($types, ...$params);
}
$count_stmt->execute();
$total_data = $count_stmt->get_result()->fetch_assoc()['total'];
$total_page = ceil($total_data / $per_page);
$count_stmt->close();

// Ambil data dengan pagination
$data_sql  = "SELECT bt.*, u.nama as nama_user FROM buku_tamu bt JOIN users u ON bt.user_id = u.id $where ORDER BY bt.tanggal DESC LIMIT ? OFFSET ?";
$data_stmt = $koneksi->prepare($data_sql);

// Gabungkan parameter dengan limit & offset
$all_params   = $params;
$all_params[] = $per_page;
$all_params[] = $offset;
$all_types    = $types . 'ii';

$data_stmt->bind_param($all_types, ...$all_params);
$data_stmt->execute();
$data = $data_stmt->get_result();
$data_stmt->close();

$success = $_SESSION['success'] ?? '';
$error   = $_SESSION['error'] ?? '';
unset($_SESSION['success'], $_SESSION['error']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Pesan - Admin</title>
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
                <div class="sidebar-user-role">👑 <?= $_SESSION['role'] ?></div>
            </div>
        </div>
        <nav class="sidebar-nav">
            <div class="nav-section-title">Menu Admin</div>
            <a href="dashboard.php"><span class="nav-icon">🏠</span> Dashboard</a>
            <a href="kelola_pesan.php" class="active"><span class="nav-icon">💬</span> Kelola Pesan</a>
            <a href="kelola_user.php"><span class="nav-icon">👥</span> Kelola User</a>
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
                <h1>💬 Kelola Pesan Tamu</h1>
            </div>
        </div>

        <div class="page-content">
            <?php if ($success): ?>
                <div class="alert alert-success">✅ <?= htmlspecialchars($success) ?></div>
            <?php endif; ?>
            <?php if ($error): ?>
                <div class="alert alert-danger">❌ <?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <div class="card">
                <!-- Search & Filter Bar -->
                <form method="GET" action="">
                    <div class="filter-bar">
                        <div class="search-input">
                            <span class="icon">🔍</span>
                            <input 
                                type="text" 
                                name="search" 
                                placeholder="Cari nama, email, atau pesan..."
                                value="<?= htmlspecialchars($search) ?>"
                            >
                        </div>
                        <select name="status" class="form-control" style="width:auto;min-width:160px;">
                            <option value="">Semua Status</option>
                            <option value="pending"  <?= $filter === 'pending'  ? 'selected' : '' ?>>⏳ Pending</option>
                            <option value="approved" <?= $filter === 'approved' ? 'selected' : '' ?>>✅ Approved</option>
                            <option value="rejected" <?= $filter === 'rejected' ? 'selected' : '' ?>>❌ Rejected</option>
                        </select>
                        <button type="submit" class="btn btn-primary btn-sm">🔍 Cari</button>
                        <a href="kelola_pesan.php" class="btn btn-secondary btn-sm">↩️ Reset</a>
                    </div>
                </form>

                <!-- Tabel Pesan -->
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
                            <?php if ($data && $data->num_rows > 0):
                                $no = $offset + 1;
                                while ($row = $data->fetch_assoc()): ?>
                                <tr>
                                    <td style="font-weight:600;color:var(--gray);"><?= $no++ ?></td>
                                    <td>
                                        <div style="font-weight:600;font-size:0.875rem;"><?= htmlspecialchars($row['nama']) ?></div>
                                        <div style="font-size:0.75rem;color:var(--gray);"><?= htmlspecialchars($row['email']) ?></div>
                                        <div style="font-size:0.7rem;color:var(--primary-dark);margin-top:2px;">by: <?= htmlspecialchars($row['nama_user']) ?></div>
                                    </td>
                                    <td style="max-width:280px;">
                                        <div style="font-size:0.85rem;line-height:1.5;color:var(--gray);">
                                            <?php
                                            $pesan_text = htmlspecialchars($row['pesan']);
                                            echo strlen($row['pesan']) > 100
                                                ? substr($pesan_text, 0, 100) . '...'
                                                : $pesan_text;
                                            ?>
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
                                        <?= date('d/m/Y H:i', strtotime($row['tanggal'])) ?>
                                    </td>
                                    <td>
                                        <div class="table-actions">
                                            <?php if ($row['status'] === 'pending'): ?>
                                                <a href="../proses/approve.php?id=<?= $row['id'] ?>&ref=admin" 
                                                   class="btn btn-success btn-sm" title="Setujui">✅</a>
                                                <a href="../proses/reject.php?id=<?= $row['id'] ?>&ref=admin" 
                                                   class="btn btn-warning btn-sm" title="Tolak">❌</a>
                                            <?php endif; ?>
                                            <a href="../proses/proses_hapus.php?id=<?= $row['id'] ?>&ref=admin" 
                                               class="btn btn-danger btn-sm btn-delete-confirm" title="Hapus">🗑️</a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; else: ?>
                                <tr>
                                    <td colspan="6">
                                        <div class="empty-state">
                                            <div class="icon">🔍</div>
                                            <h3>Tidak ada pesan ditemukan</h3>
                                            <p>Coba ubah kata kunci atau filter yang digunakan</p>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- PAGINATION -->
                <?php if ($total_page > 1): ?>
                <div class="pagination">
                    <div style="color:var(--gray);font-size:0.85rem;">
                        Menampilkan <?= min($offset + 1, $total_data) ?>–<?= min($offset + $per_page, $total_data) ?> 
                        dari <strong><?= $total_data ?></strong> data
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
