<?php
// ============================================
// FILE: proses/proses_edit.php
// Fungsi: Update pesan buku tamu (user hanya bisa edit miliknya)
// ============================================

session_start();
require_once '../config/koneksi.php';
cekLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect(BASE_URL . '/user/dashboard.php');
}

$id      = (int)($_POST['id'] ?? 0);
$user_id = $_SESSION['user_id'];
$nama    = sanitasi($_POST['nama'] ?? '');
$email   = sanitasi($_POST['email'] ?? '');
$pesan   = sanitasi($_POST['pesan'] ?? '');

// Validasi
if ($id <= 0 || empty($nama) || empty($email) || empty($pesan)) {
    $_SESSION['error'] = 'Data tidak lengkap atau tidak valid!';
    redirect(BASE_URL . '/user/edit_pesan.php?id=' . $id);
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['error'] = 'Format email tidak valid!';
    redirect(BASE_URL . '/user/edit_pesan.php?id=' . $id);
}

// Cek apakah pesan milik user ini dan statusnya pending
$cek = $koneksi->prepare("SELECT id FROM buku_tamu WHERE id = ? AND user_id = ? AND status = 'pending' LIMIT 1");
$cek->bind_param("ii", $id, $user_id);
$cek->execute();
$cek->store_result();

if ($cek->num_rows === 0) {
    $cek->close();
    $_SESSION['error'] = 'Pesan tidak ditemukan atau tidak dapat diedit!';
    redirect(BASE_URL . '/user/dashboard.php');
}
$cek->close();

// Update pesan
$stmt = $koneksi->prepare("UPDATE buku_tamu SET nama = ?, email = ?, pesan = ? WHERE id = ? AND user_id = ?");
$stmt->bind_param("sssii", $nama, $email, $pesan, $id, $user_id);

if ($stmt->execute()) {
    $stmt->close();
    $_SESSION['success'] = 'Pesan berhasil diperbarui!';
    redirect(BASE_URL . '/user/dashboard.php');
} else {
    $stmt->close();
    $_SESSION['error'] = 'Gagal memperbarui pesan. Coba lagi.';
    redirect(BASE_URL . '/user/edit_pesan.php?id=' . $id);
}
?>
