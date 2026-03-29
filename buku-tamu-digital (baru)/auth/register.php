<?php
// ============================================
// FILE: auth/register.php
// Fungsi: Halaman form registrasi pengguna baru
// ============================================

session_start();
require_once '../config/koneksi.php';

// Jika sudah login, redirect ke dashboard
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] === 'admin') {
        redirect(BASE_URL . '/admin/dashboard.php');
    } else {
        redirect(BASE_URL . '/user/dashboard.php');
    }
}

$error   = $_SESSION['error'] ?? '';
$success = $_SESSION['success'] ?? '';
$old     = $_SESSION['old_input'] ?? [];
unset($_SESSION['error'], $_SESSION['success'], $_SESSION['old_input']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Buku Tamu Digital</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<div class="auth-page">
    <div class="auth-card" style="max-width:500px;">
        <div class="auth-header">
            <div class="auth-logo">📝</div>
            <h1>Buat Akun Baru</h1>
            <p>Daftar dan mulai gunakan Buku Tamu Digital</p>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-danger">❌ <?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert-success">✅ <?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <form action="../proses/proses_register.php" method="POST">
            <div class="form-group">
                <label class="form-label">Nama Lengkap</label>
                <div class="form-control-icon">
                    <span class="icon">👤</span>
                    <input 
                        type="text" 
                        name="nama" 
                        class="form-control" 
                        placeholder="Masukkan nama lengkap"
                        value="<?= htmlspecialchars($old['nama'] ?? '') ?>"
                        required
                    >
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Email</label>
                <div class="form-control-icon">
                    <span class="icon">📧</span>
                    <input 
                        type="email" 
                        name="email" 
                        class="form-control" 
                        placeholder="contoh@email.com"
                        value="<?= htmlspecialchars($old['email'] ?? '') ?>"
                        required
                    >
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Username</label>
                <div class="form-control-icon">
                    <span class="icon">🏷️</span>
                    <input 
                        type="text" 
                        name="username" 
                        class="form-control" 
                        placeholder="Pilih username unik"
                        value="<?= htmlspecialchars($old['username'] ?? '') ?>"
                        required
                        pattern="[a-zA-Z0-9_]+"
                        title="Hanya huruf, angka, dan underscore"
                    >
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Password</label>
                <div class="form-control-icon" style="position:relative;">
                    <span class="icon">🔒</span>
                    <input 
                        type="password" 
                        id="password"
                        name="password" 
                        class="form-control" 
                        placeholder="Minimal 6 karakter"
                        required
                        minlength="6"
                        style="padding-right:45px;"
                    >
                    <button type="button" class="toggle-password" data-target="password"
                        style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;font-size:1rem;">👁️</button>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Konfirmasi Password</label>
                <div class="form-control-icon">
                    <span class="icon">🔒</span>
                    <input 
                        type="password" 
                        name="konfirmasi_password" 
                        class="form-control" 
                        placeholder="Ulangi password kamu"
                        required
                    >
                </div>
            </div>

            <!-- Role tersembunyi, default user -->
            <input type="hidden" name="role" value="user">

            <button type="submit" class="btn btn-primary w-100 btn-lg mt-2">
                📝 Daftar Sekarang
            </button>
        </form>

        <div class="auth-footer">
            Sudah punya akun? <a href="login.php">Login di sini</a>
        </div>
        <div class="auth-footer mt-1">
            <a href="../index.php" style="color:var(--gray);">← Kembali ke Beranda</a>
        </div>
    </div>
</div>

<script src="../assets/js/script.js"></script>
</body>
</html>
