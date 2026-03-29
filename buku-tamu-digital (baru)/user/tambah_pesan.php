<?php
// ============================================
// FILE: user/tambah_pesan.php
// Fungsi: Halaman form untuk user menambah pesan
// ============================================

session_start();
require_once '../config/koneksi.php';
cekLogin();

if ($_SESSION['role'] === 'admin') {
    redirect(BASE_URL . '/admin/dashboard.php');
}

$success = $_SESSION['success'] ?? '';
$error   = $_SESSION['error'] ?? '';
$old     = $_SESSION['old_input'] ?? [];
unset($_SESSION['success'], $_SESSION['error'], $_SESSION['old_input']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tulis Pesan - Buku Tamu Digital</title>
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
            <a href="tambah_pesan.php" class="active"><span class="nav-icon">✏️</span> Tulis Pesan</a>
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
                <h1>✏️ Tulis Pesan Baru</h1>
            </div>
        </div>

        <div class="page-content">
            <?php if ($success): ?>
                <div class="alert alert-success">✅ <?= htmlspecialchars($success) ?></div>
            <?php endif; ?>
            <?php if ($error): ?>
                <div class="alert alert-danger">❌ <?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <div style="max-width:650px;">
                <div class="card">
                    <div class="card-header">
                        <h2>📝 Form Pesan Buku Tamu</h2>
                    </div>
                    <div class="card-body">
                        <!-- Info status -->
                        <div class="alert alert-info mb-3">
                            ℹ️ Pesanmu akan ditinjau oleh admin sebelum ditampilkan ke publik. Status awal: <strong>Pending</strong>
                        </div>

                        <form action="../proses/proses_tambah.php" method="POST">
                            <div class="form-group">
                                <label class="form-label">Nama Lengkap *</label>
                                <div class="form-control-icon">
                                    <span class="icon">👤</span>
                                    <input 
                                        type="text" 
                                        name="nama" 
                                        class="form-control"
                                        placeholder="Nama kamu"
                                        value="<?= htmlspecialchars($old['nama'] ?? $_SESSION['nama']) ?>"
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
                                        placeholder="email@kamu.com"
                                        value="<?= htmlspecialchars($old['email'] ?? $_SESSION['email']) ?>"
                                        required
                                    >
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="form-label">
                                    Pesan * 
                                    <span style="color:var(--gray);font-weight:400;font-size:0.8rem;">(<span id="char-count">0</span>/500 karakter)</span>
                                </label>
                                <textarea 
                                    id="pesan"
                                    name="pesan" 
                                    class="form-control"
                                    placeholder="Tuliskan pesan kunjunganmu di sini..."
                                    rows="6"
                                    maxlength="500"
                                    required
                                ><?= htmlspecialchars($old['pesan'] ?? '') ?></textarea>
                            </div>

                            <div style="display:flex;gap:12px;flex-wrap:wrap;">
                                <button type="submit" class="btn btn-primary">
                                    🚀 Kirim Pesan
                                </button>
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
