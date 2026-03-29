<?php
// ============================================
// FILE: proses/proses_register.php
// Fungsi: Memproses registrasi akun baru
// ============================================

session_start();
require_once '../config/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect(BASE_URL . '/auth/register.php');
}

// Ambil & sanitasi input
$nama     = sanitasi($_POST['nama'] ?? '');
$email    = sanitasi($_POST['email'] ?? '');
$username = sanitasi($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';
$konfirmasi = $_POST['konfirmasi_password'] ?? '';
$role     = 'user'; // Paksa role = user, tidak bisa diubah dari form

// Simpan input lama untuk dikembalikan jika error
$_SESSION['old_input'] = compact('nama', 'email', 'username');

// ===== VALIDASI INPUT =====

// Cek field kosong
if (empty($nama) || empty($email) || empty($username) || empty($password)) {
    $_SESSION['error'] = 'Semua field wajib diisi!';
    redirect(BASE_URL . '/auth/register.php');
}

// Validasi format email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['error'] = 'Format email tidak valid!';
    redirect(BASE_URL . '/auth/register.php');
}

// Validasi username (hanya huruf, angka, underscore)
if (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
    $_SESSION['error'] = 'Username hanya boleh berisi huruf, angka, dan underscore!';
    redirect(BASE_URL . '/auth/register.php');
}

// Validasi panjang password
if (strlen($password) < 6) {
    $_SESSION['error'] = 'Password minimal 6 karakter!';
    redirect(BASE_URL . '/auth/register.php');
}

// Validasi konfirmasi password
if ($password !== $konfirmasi) {
    $_SESSION['error'] = 'Password dan konfirmasi password tidak cocok!';
    redirect(BASE_URL . '/auth/register.php');
}

// Cek apakah email sudah terdaftar
$cek_email = $koneksi->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
$cek_email->bind_param("s", $email);
$cek_email->execute();
$cek_email->store_result();
if ($cek_email->num_rows > 0) {
    $_SESSION['error'] = 'Email sudah terdaftar! Gunakan email lain.';
    $cek_email->close();
    redirect(BASE_URL . '/auth/register.php');
}
$cek_email->close();

// Cek apakah username sudah digunakan
$cek_username = $koneksi->prepare("SELECT id FROM users WHERE username = ? LIMIT 1");
$cek_username->bind_param("s", $username);
$cek_username->execute();
$cek_username->store_result();
if ($cek_username->num_rows > 0) {
    $_SESSION['error'] = 'Username sudah digunakan! Pilih username lain.';
    $cek_username->close();
    redirect(BASE_URL . '/auth/register.php');
}
$cek_username->close();

// ===== SIMPAN KE DATABASE =====

// Hash password dengan bcrypt
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

$stmt = $koneksi->prepare("INSERT INTO users (nama, email, username, password, role) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("sssss", $nama, $email, $username, $hashed_password, $role);

if ($stmt->execute()) {
    $stmt->close();
    unset($_SESSION['old_input']);
    $_SESSION['success'] = 'Registrasi berhasil! Silakan login dengan akun barumu.';
    redirect(BASE_URL . '/auth/login.php');
} else {
    $stmt->close();
    $_SESSION['error'] = 'Terjadi kesalahan. Silakan coba lagi.';
    redirect(BASE_URL . '/auth/register.php');
}
?>
