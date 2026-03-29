<?php
// ============================================
// FILE: config/koneksi.php
// Fungsi: Konfigurasi koneksi database MySQL
// ============================================

// Konfigurasi database
define('DB_HOST', 'localhost');
define('DB_USER', 'root');        // Ganti sesuai username MySQL kamu
define('DB_PASS', '');            // Ganti sesuai password MySQL kamu
define('DB_NAME', 'buku_tamu_digital');
define('BASE_URL', 'http://localhost/buku-tamu-digital');

// Koneksi ke database menggunakan MySQLi
$koneksi = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Cek apakah koneksi berhasil
if ($koneksi->connect_error) {
    die('
    <div style="font-family:sans-serif;padding:40px;text-align:center;">
        <h2 style="color:#e53e3e;">❌ Koneksi Database Gagal</h2>
        <p>Error: ' . $koneksi->connect_error . '</p>
        <p>Pastikan MySQL sudah berjalan dan konfigurasi database sudah benar.</p>
    </div>
    ');
}

// Set charset ke utf8mb4 agar mendukung karakter khusus
$koneksi->set_charset("utf8mb4");

// Fungsi sanitasi input untuk keamanan (mencegah XSS)
function sanitasi($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

// Fungsi redirect halaman
function redirect($url) {
    header("Location: " . $url);
    exit();
}

// Fungsi cek apakah sudah login
function cekLogin() {
    if (!isset($_SESSION['user_id'])) {
        redirect(BASE_URL . '/auth/login.php');
    }
}

// Fungsi cek apakah role admin
function cekAdmin() {
    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
        redirect(BASE_URL . '/auth/login.php');
    }
}

// Fungsi cek apakah role user
function cekUser() {
    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
        redirect(BASE_URL . '/auth/login.php');
    }
}
?>
