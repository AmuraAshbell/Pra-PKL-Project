<?php
// ============================================
// FILE: auth/login.php
// Fungsi: Halaman form login pengguna
// ============================================

session_start();
require_once '../config/koneksi.php';

// Jika sudah login, redirect ke dashboard sesuai role
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] === 'admin') {
        redirect(BASE_URL . '/admin/dashboard.php');
    } else {
        redirect(BASE_URL . '/user/dashboard.php');
    }
}

// Ambil pesan error/sukses dari session
$error   = $_SESSION['error'] ?? '';
$success = $_SESSION['success'] ?? '';
unset($_SESSION['error'], $_SESSION['success']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Buku Tamu Digital</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<div class="auth-page">
    <div class="auth-card">
        <div class="auth-header">
            <div class="auth-logo">🔐</div>
            <h1>Selamat Datang!</h1>
            <p>Masuk ke akun Buku Tamu Digital kamu</p>
        </div>

        <!-- Tampilkan pesan error/sukses -->
        <?php if ($error): ?>
            <div class="alert alert-danger">❌ <?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success">✅ <?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <!-- Form Login -->
        <form action="../proses/proses_login.php" method="POST">
            <div class="form-group">
                <label class="form-label" for="username">Username</label>
                <div class="form-control-icon">
                    <span class="icon">👤</span>
                    <input 
                        type="text" 
                        id="username" 
                        name="username" 
                        class="form-control" 
                        placeholder="Masukkan username"
                        required
                        autocomplete="username"
                    >
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="password">Password</label>
                <div class="form-control-icon" style="position:relative;">
                    <span class="icon">🔒</span>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        class="form-control" 
                        placeholder="Masukkan password"
                        required
                        style="padding-right:45px;"
                        autocomplete="current-password"
                    >
                    <button 
                        type="button" 
                        class="toggle-password" 
                        data-target="password"
                        style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;font-size:1rem;"
                    >👁️</button>
                </div>
            </div>

            <button type="submit" class="btn btn-primary w-100 btn-lg mt-2">
                🚀 Masuk Sekarang
            </button>
        </form>

        <div class="auth-footer">
            Belum punya akun? <a href="register.php">Daftar di sini</a>
        </div>

        <div class="auth-footer mt-1">
            <a href="../index.php" style="color:var(--gray);">← Kembali ke Beranda</a>
        </div>

        <!-- Info akun demo -->
        <div style="margin-top:20px;padding:15px;background:var(--gray-light);border-radius:10px;font-size:0.8rem;color:var(--gray);">
            <strong>🔑 Akun Demo:</strong><br>
            Admin: username <code>admin</code> / password <code>password</code><br>
            User: username <code>budi</code> / password <code>password</code>
        </div>
    </div>
</div>

<script src="../assets/js/script.js"></script>
</body>
</html>
