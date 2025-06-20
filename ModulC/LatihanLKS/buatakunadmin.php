<?php
// File: create_admin.php
include 'config.php';

try {
    // Cek apakah admin sudah ada
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = 'admin'");
    $stmt->execute();
    $adminExists = $stmt->fetch();

    if ($adminExists) {
        echo "Akun admin sudah ada!";
        exit;
    }

    // Hash password
    $password = 'admin123';
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Buat akun admin
    $stmt = $pdo->prepare("INSERT INTO users (username, password, email) VALUES (?, ?, ?)");
    $stmt->execute(['admin', $hashedPassword, 'admin@example.com']);

    if ($stmt->rowCount() > 0) {
        echo "Akun admin berhasil dibuat!<br>";
        echo "Username: admin<br>";
        echo "Password: admin123";
    } else {
        echo "Gagal membuat akun admin.";
    }
} catch (PDOException $e) {
    echo "Terjadi kesalahan: " . $e->getMessage();
}
?>