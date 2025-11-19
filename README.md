# API PHP Native

API RESTful sederhana dibangun dengan PHP native untuk praktikum pemrograman jaringan.

## Fitur

- ✅ Authentication JWT
- ✅ CRUD Users dengan pagination & search
- ✅ File upload dengan validasi keamanan
- ✅ Rate limiting
- ✅ CORS middleware
- ✅ Validasi input
- ✅ Error handling konsisten
- ✅ Prepared statements (aman dari SQL injection)

## Persyaratan

- PHP 8.0+
- MySQL/MariaDB
- Apache dengan mod_rewrite

## Instalasi

1. Clone atau download proyek ini
2. Letakkan di folder web server (htdocs/public_html)
3. Buat database `apiphp`
4. Import SQL dari file `database.sql`
5. Sesuaikan konfigurasi di `config/env.php`
6. Akses melalui: `http://localhost/api.php-native/public`

## Database

Jalankan SQL berikut:

```sql
CREATE DATABASE IF NOT EXISTS apiphp CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE apiphp;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('user','admin') DEFAULT 'user',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Password: password123
INSERT INTO users (name, email, password_hash, role) VALUES 
('Admin', 'admin@example.com', '$2y$10$F4C9bQ02Im0b2d7dh/3Gje4d0F1kQv8a0h2u0YqvGk0fQy5cYt2kS', 'admin'),
('User Demo', 'user@example.com', '$2y$10$F4C9bQ02Im0b2d7dh/3Gje4d0F1kQv8a0h2u0YqvGk0fQy5cYt2kS', 'user');"# Praktiku_pemrograman_jaringan" 
