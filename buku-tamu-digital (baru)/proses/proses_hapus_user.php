<?php
// ============================================
// FILE: proses/proses_hapus_user.php
// Fungsi: Admin menghapus akun user
//         (cascade: semua pesan miliknya ikut terhapus)
// ============================================

session_start();
require_once '../config/koneksi.php';
cekAdmin(); // Hanya admin

$id = (int)($_GET['id'] ?? 0);

if ($id <= 0) {
    redirect(BASE_URL . '/admin/kelola_user.php');
}

// Tidak boleh hapus diri sendiri
if ($id === $_SESSION['user_id']) {
    $_SESSION['error'] = 'Kamu tidak bisa menghapus akun kamu sendiri!';
    redirect(BASE_URL . '/admin/kelola_user.php');
}

// Pastikan yang dihapus bukan admin lain
$cek = $koneksi->prepare("SELECT role FROM users WHERE id = ? LIMIT 1");
$cek->bind_param("i", $id);
$cek->execute();
$target = $cek->get_result()->fetch_assoc();
$cek->close();

if (!$target) {
    $_SESSION['error'] = 'User tidak ditemukan!';
    redirect(BASE_URL . '/admin/kelola_user.php');
}

if ($target['role'] === 'admin') {
    $_SESSION['error'] = 'Admin tidak dapat dihapus melalui fitur ini!';
    redirect(BASE_URL . '/admin/kelola_user.php');
}

// Hapus user (pesan ikut terhapus karena ON DELETE CASCADE)
$stmt = $koneksi->prepare("DELETE FROM users WHERE id = ? AND role = 'user'");
$stmt->bind_param("i", $id);

if ($stmt->execute() && $stmt->affected_rows > 0) {
    $_SESSION['success'] = 'User berhasil dihapus beserta semua pesannya.';
} else {
    $_SESSION['error'] = 'Gagal menghapus user!';
}

$stmt->close();
redirect(BASE_URL . '/admin/kelola_user.php');
?>
