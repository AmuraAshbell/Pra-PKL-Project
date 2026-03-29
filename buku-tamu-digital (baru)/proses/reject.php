<?php
// ============================================
// FILE: proses/reject.php
// Fungsi: Admin menolak (reject) pesan tamu
// ============================================

session_start();
require_once '../config/koneksi.php';
cekAdmin();

$id  = (int)($_GET['id'] ?? 0);
$ref = sanitasi($_GET['ref'] ?? 'admin');

if ($id <= 0) {
    redirect(BASE_URL . '/admin/kelola_pesan.php');
}

// Update status menjadi 'rejected'
$stmt = $koneksi->prepare("UPDATE buku_tamu SET status = 'rejected' WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute() && $stmt->affected_rows > 0) {
    $_SESSION['success'] = 'Pesan berhasil ditolak.';
} else {
    $_SESSION['error'] = 'Gagal menolak pesan. Pesan mungkin sudah diproses sebelumnya.';
}

$stmt->close();
redirect(BASE_URL . '/admin/kelola_pesan.php');
?>
