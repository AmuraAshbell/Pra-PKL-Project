-- ============================================
-- DATABASE: buku_tamu_digital
-- Project: Buku Tamu Digital
-- Untuk: Portofolio Siswa SMK RPL
-- ============================================

CREATE DATABASE IF NOT EXISTS buku_tamu_digital
CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE buku_tamu_digital;

-- ============================================
-- TABEL USERS
-- ============================================
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'user') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- TABEL BUKU TAMU
-- ============================================
CREATE TABLE IF NOT EXISTS buku_tamu (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    nama VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL,
    pesan TEXT NOT NULL,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    tanggal TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- DATA AWAL: AKUN ADMIN & USER
-- ============================================

-- Password untuk admin: admin123
-- Password untuk user: user123
INSERT INTO users (nama, email, username, password, role) VALUES
('Administrator', 'admin@bukutamu.com', 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin'),
('Budi Santoso', 'budi@email.com', 'budi', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user');

-- Catatan: Hash di atas menggunakan 'password' sebagai password default
-- Untuk keamanan, ganti password setelah pertama login

-- ============================================
-- DATA CONTOH BUKU TAMU
-- ============================================
INSERT INTO buku_tamu (user_id, nama, email, pesan, status) VALUES
(2, 'Budi Santoso', 'budi@email.com', 'Website ini sangat bagus dan membantu sekali. Terima kasih sudah membuat platform buku tamu digital yang modern!', 'approved'),
(2, 'Budi Santoso', 'budi@email.com', 'Saya sangat terkesan dengan desain dan fitur yang ada. Semoga terus berkembang dan semakin baik ke depannya.', 'approved'),
(2, 'Budi Santoso', 'budi@email.com', 'Pesan ini masih menunggu persetujuan admin.', 'pending');
