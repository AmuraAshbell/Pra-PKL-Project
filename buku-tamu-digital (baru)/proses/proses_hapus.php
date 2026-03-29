<?php
// ============================================
// FILE: proses/proses_hapus.php
// Fungsi: Menghapus pesan buku tamu
//         Admin bisa hapus semua, user hanya miliknya
// ============================================

session_start();
require_once '../config/koneksi.php';
cekLogin();

$id  = (int)($_GET['id'] ?? 0);
$ref = sanitasi($_GET['ref'] ?? 'user'); // 'admin' atau 'user'

if ($id <= 0) {
    redirect(BASE_URL . '/user/dashboard.php');
}

if ($_SESSION['role'] === 'admin') {
    // Admin boleh hapus pesan siapapun
    $stmt = $koneksi->prepare("DELETE FROM buku_tamu WHERE id = ?");
    $stmt->bind_param("i", $id);
} else {
    // User hanya boleh hapus pesan miliknya sendiri
    $user_id = $_SESSION['user_id'];
    $stmt = $koneksi->prepare("DELETE FROM buku_tamu WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $id, $user_id);
}

if ($stmt->execute() && $stmt->affected_rows > 0) {
    $stmt->close();
    $_SESSION['success'] = 'Pesan berhasil dihapus!';
} else {
    $stmt->close();
    $_SESSION['error'] = 'Pesan tidak ditemukan atau tidak bisa dihapus!';
}

// Redirect sesuai asal halaman
if ($ref === 'admin') {
    redirect(BASE_URL . '/admin/kelola_pesan.php');
} else {
    redirect(BASE_URL . '/user/dashboard.php');
}
?>
