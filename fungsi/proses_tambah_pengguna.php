<?php
// File: fungsi/proses_tambah_pengguna.php

require_once '../config/config.php'; 
require_once '../auth/auth.php'; 

// Hanya admin yang dapat mengakses
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    $_SESSION['flash_message'] = "Anda tidak memiliki hak akses untuk halaman ini.";
    $_SESSION['flash_message_type'] = "danger";
    header("Location: ../halaman/dasbor.php");
    exit;
}

$page_title = "Tambah Pengguna Baru"; 
$form_username = '';
$form_role = '';
$message = '';
$message_type = '';

// Ambil flash message dari session jika ada 
if (isset($_SESSION['flash_message']) && !isset($_POST['add_user_submit'])) {
    $message = $_SESSION['flash_message'];
    $message_type = $_SESSION['flash_message_type'] ?? 'info';
    unset($_SESSION['flash_message']);
    unset($_SESSION['flash_message_type']);
}

// Proses form jika disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_user_submit'])) {
    $form_username = trim($_POST['username']);
    $password = $_POST['password'];
    $form_role = $_POST['role'];

    // Validasi input
    if (empty($form_username) || empty($password) || empty($form_role)) {
        $message = "Semua kolom wajib diisi (Username, Password, Role).";
        $message_type = 'danger';
    } elseif (!in_array($form_role, ['admin', 'karyawan'])) {
        $message = "Role yang dipilih tidak valid.";
        $message_type = 'danger';
    } else {
        // Cek apakah username sudah ada
        $stmt_check = $conn->prepare("SELECT id FROM users WHERE username = ?");
        $stmt_check->bind_param("s", $form_username);
        $stmt_check->execute();
        $stmt_check->store_result();

        if ($stmt_check->num_rows > 0) {
            $message = "Username '" . htmlspecialchars($form_username) . "' sudah ada. Silakan pilih yang lain.";
            $message_type = 'danger';
        } else {
            // Hash password untuk keamanan
            $password_hash = password_hash($password, PASSWORD_DEFAULT);

            // Masukkan pengguna baru ke database
            $stmt_insert = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
            $stmt_insert->bind_param("sss", $form_username, $password_hash, $form_role);

            if ($stmt_insert->execute()) {
                $_SESSION['flash_message'] = "Pengguna '" . htmlspecialchars($form_username) . "' berhasil ditambahkan.";
                $_SESSION['flash_message_type'] = 'success';
                header("Location: ../halaman/manajemen_pengguna.php"); 
                exit;
            } else {
                $message = "Gagal menambahkan pengguna. Error: " . $stmt_insert->error;
                $message_type = 'danger';
            }
            $stmt_insert->close();
        }
        $stmt_check->close();
    }
}
