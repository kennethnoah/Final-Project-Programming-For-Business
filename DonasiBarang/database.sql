-- Database Donasi Barang
-- Jalankan file ini di phpMyAdmin atau MySQL CLI

CREATE DATABASE IF NOT EXISTS donasi_db;
USE donasi_db;

-- Tabel Users
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100),
    email VARCHAR(100) UNIQUE,
    password VARCHAR(255),
    role ENUM('user','admin') DEFAULT 'user'
);

-- Tabel Donasi
CREATE TABLE donasi (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    nama_barang VARCHAR(150),
    deskripsi TEXT,
    foto VARCHAR(255),
    bukti_penyaluran VARCHAR(255) DEFAULT NULL,
    status ENUM('tersedia','dikirim','tersalurkan') DEFAULT 'tersedia',
    tanggal TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- ADMIN DEFAULT
-- Email: admin@donasi.com | Password: admin123
INSERT INTO users (nama, email, password, role)
VALUES ('Admin', 'admin@donasi.com', MD5('admin123'), 'admin');

-- USER CONTOH
-- budi@example.com / budi123
-- siti@example.com / siti123
-- andi@example.com / andi123
INSERT INTO users (nama, email, password, role)
VALUES
('Budi Santoso', 'budi@example.com', MD5('budi123'), 'user'),
('Siti Aminah', 'siti@example.com', MD5('siti123'), 'user'),
('Andi Wijaya', 'andi@example.com', MD5('andi123'), 'user');

-- DATA DONASI CONTOH
INSERT INTO donasi (user_id, nama_barang, deskripsi, foto, status)
VALUES
(2, 'Pakaian Layak Pakai', 'Baju dewasa dan anak, 1 kardus besar', 'contoh1.jpg', 'tersedia'),
(3, 'Tas Sekolah', 'Tas kondisi bagus, resleting normal.', 'contoh2.jpg', 'dikirim'),
(4, 'Mainan Edukasi', 'Mainan edukatif usia 3-5 tahun.', 'contoh3.jpg', 'tersalurkan'),
(2, 'Sepatu Olahraga', 'Sepatu ukuran 42, masih sangat layak.', 'contoh4.jpg', 'tersedia'),
(3, 'Kompor Gas', 'Kompor gas 1 tungku Rinnai.', 'contoh5.jpg', 'tersalurkan');

-- BUKTI PENYALURAN
UPDATE donasi SET bukti_penyaluran = 'bukti1.jpg' WHERE id = 3;
UPDATE donasi SET bukti_penyaluran = 'bukti2.jpg' WHERE id = 5;
