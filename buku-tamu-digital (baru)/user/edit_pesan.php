<?php
// ============================================
// FILE: user/edit_pesan.php
// Fungsi: Halaman form edit pesan tamu milik user
//         Hanya pesan berstatus PENDING yang bisa diedit
// ============================================

session_start();
require_once '../config/koneksi.php';
cekLogin();

if ($_SESSION['role'] === 'admin') {
    redirect(BASE_URL . '/admin/dashboard.php');
}

// Ambil ID dari URL
$id      = (int)($_GET['id'] ?? 0);
$user_id = $_SESSION['user_id'];

if ($id <= 0) {
    redirect(BASE_URL . '/user/dashboard.php');
}

// Cari data pesan milik user ini
$stmt = $koneksi->prepare("SELECT * FROM buku_tamu WHERE id = ? AND user_id = ? LIMIT 1");
$stmt->bind_param("ii", $id, $user_id);
$stmt->execute();
$pesan = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Jika tidak ditemukan atau bukan miliknya
if (!$pesan) {
    $_SESSION['error'] = 'Pesan tidak ditemukan atau bukan milikmu!';
    redirect(BASE_URL . '/user/dashboard.php');
}

// Hanya pesan pending yang bisa diedit
if ($pesan['status'] !== 'pending') {
    $_SESSION['error'] = 'Pesan yang sudah diproses tidak dapat diedit!';
    redirect(BASE_URL . '/user/dashboard.php');
}

$success = $_SESSION['success'] ?? '';
$error   = $_SESSION['error'] ?? '';
unset($_SESSION['success'], $_SESSION['error']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Pesan - Buku Tamu Digital</title>
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
                <div class="sidebar-user-role">👤 <?= $_SESSION['role'] ?></div>
            </div>
        </div>
        <nav class="sidebar-nav">
            <div class="nav-section-title">Menu</div>
            <a href="dashboard.php"><span class="nav-icon">🏠</span> Dashboard</a>
            <a href="tambah_pesan.php"><span class="nav-icon">✏️</span> Tulis Pesan</a>
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
                <h1>✏️ Edit Pesan</h1>
            </div>
        </div>

        <div class="page-content">
            <?php if ($error): ?>
                <div class="alert alert-danger">❌ <?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <div style="max-width:650px;">
                <div class="card">
                    <div class="card-header">
                        <h2>📝 Edit Pesan Buku Tamu</h2>
                        <span class="badge badge-pending">⏳ Pending</span>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-warning mb-3">
                            ⚠️ Setelah diedit, pesan akan tetap berstatus Pending dan menunggu review ulang dari admin.
                        </div>

                        <form action="../proses/proses_edit.php" method="POST">
                            <input type="hidden" name="id" value="<?= $pesan['id'] ?>">

                            <div class="form-group">
                                <label class="form-label">Nama Lengkap *</label>
                                <div class="form-control-icon">
                                    <span class="icon">👤</span>
                                    <input 
                                        type="text" 
                                        name="nama" 
                                        class="form-control"
                                        value="<?= htmlspecialchars($pesan['nama']) ?>"
                                        required
                                    >
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Email *</label>
                                <div class="form-control-icon">
                                    <span class="icon">📧</span>
                                    <input 
                                        type="email" 
                                        name="email" 
                                        class="form-control"
                                        value="<?= htmlspecialchars($pesan['email']) ?>"
                                        required
                                    >
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="form-label">
                                    Pesan * 
                                    <span style="color:var(--gray);font-weight:400;font-size:0.8rem;">(<span id="char-count"><?= strlen($pesan['pesan']) ?></span>/500 karakter)</span>
                                </label>
                                <textarea 
                                    id="pesan"
                                    name="pesan" 
                                    class="form-control"
                                    rows="6"
                                    maxlength="500"
                                    required
                                ><?= htmlspecialchars($pesan['pesan']) ?></textarea>
                            </div>

                            <div style="display:flex;gap:12px;flex-wrap:wrap;">
                                <button type="submit" class="btn btn-primary">💾 Simpan Perubahan</button>
                                <a href="dashboard.php" class="btn btn-secondary">← Batal</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="../assets/js/script.js"></script>
</body>
</html>
