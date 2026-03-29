<?php
// ============================================
// FILE: proses/proses_tambah.php
// Fungsi: Menyimpan pesan buku tamu baru ke database
// ============================================

session_start();
require_once '../config/koneksi.php';
cekLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect(BASE_URL . '/user/tambah_pesan.php');
}

$user_id = $_SESSION['user_id'];
$nama    = sanitasi($_POST['nama'] ?? '');
$email   = sanitasi($_POST['email'] ?? '');
$pesan   = sanitasi($_POST['pesan'] ?? '');

// Simpan input lama jika error
$_SESSION['old_input'] = compact('nama', 'email', 'pesan');

// Validasi
if (empty($nama) || empty($email) || empty($pesan)) {
    $_SESSION['error'] = 'Semua field wajib diisi!';
    redirect(BASE_URL . '/user/tambah_pesan.php');
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['error'] = 'Format email tidak valid!';
    redirect(BASE_URL . '/user/tambah_pesan.php');
}

if (strlen($pesan) < 10) {
    $_SESSION['error'] = 'Pesan terlalu pendek. Minimal 10 karakter.';
    redirect(BASE_URL . '/user/tambah_pesan.php');
}

if (strlen($pesan) > 500) {
    $_SESSION['error'] = 'Pesan terlalu panjang. Maksimal 500 karakter.';
    redirect(BASE_URL . '/user/tambah_pesan.php');
}

// Simpan ke database dengan status default 'pending'
$stmt = $koneksi->prepare("INSERT INTO buku_tamu (user_id, nama, email, pesan, status) VALUES (?, ?, ?, ?, 'pending')");
$stmt->bind_param("isss", $user_id, $nama, $email, $pesan);

if ($stmt->execute()) {
    $stmt->close();
    unset($_SESSION['old_input']);
    $_SESSION['success'] = 'Pesan berhasil dikirim! Menunggu persetujuan admin.';
    redirect(BASE_URL . '/user/dashboard.php');
} else {
    $stmt->close();
    $_SESSION['error'] = 'Terjadi kesalahan saat menyimpan pesan. Coba lagi.';
    redirect(BASE_URL . '/user/tambah_pesan.php');
}
?>
