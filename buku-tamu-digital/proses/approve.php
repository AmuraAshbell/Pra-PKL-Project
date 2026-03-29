<?php
// ============================================
// FILE: proses/approve.php
// Fungsi: Admin menyetujui (approve) pesan tamu
// ============================================

session_start();
require_once '../config/koneksi.php';
cekAdmin();

$id  = (int)($_GET['id'] ?? 0);
$ref = sanitasi($_GET['ref'] ?? 'admin');

if ($id <= 0) {
    redirect(BASE_URL . '/admin/kelola_pesan.php');
}

// Update status menjadi 'approved'
$stmt = $koneksi->prepare("UPDATE buku_tamu SET status = 'approved' WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute() && $stmt->affected_rows > 0) {
    $_SESSION['success'] = 'Pesan berhasil disetujui dan sekarang tampil di website!';
} else {
    $_SESSION['error'] = 'Gagal menyetujui pesan. Pesan mungkin sudah diproses sebelumnya.';
}

$stmt->close();
redirect(BASE_URL . '/admin/kelola_pesan.php');
?>
