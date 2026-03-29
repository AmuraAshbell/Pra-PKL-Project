<?php
// ============================================
// FILE: proses/proses_login.php
// Fungsi: Memproses form login, validasi user
// ============================================

session_start();
require_once '../config/koneksi.php';

// Hanya terima method POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect(BASE_URL . '/auth/login.php');
}

// Ambil dan sanitasi input
$username = sanitasi($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

// Validasi: field tidak boleh kosong
if (empty($username) || empty($password)) {
    $_SESSION['error'] = 'Username dan password wajib diisi!';
    redirect(BASE_URL . '/auth/login.php');
}

// Cari user di database dengan prepared statement (cegah SQL Injection)
$stmt = $koneksi->prepare("SELECT * FROM users WHERE username = ? LIMIT 1");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$user   = $result->fetch_assoc();
$stmt->close();

// Cek apakah user ditemukan dan password cocok
if ($user && password_verify($password, $user['password'])) {
    // Login berhasil - simpan data ke session
    $_SESSION['user_id']  = $user['id'];
    $_SESSION['nama']     = $user['nama'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['email']    = $user['email'];
    $_SESSION['role']     = $user['role'];

    // Redirect sesuai role
    if ($user['role'] === 'admin') {
        redirect(BASE_URL . '/admin/dashboard.php');
    } else {
        redirect(BASE_URL . '/user/dashboard.php');
    }
} else {
    // Login gagal
    $_SESSION['error'] = 'Username atau password salah. Silakan coba lagi.';
    redirect(BASE_URL . '/auth/login.php');
}
?>
