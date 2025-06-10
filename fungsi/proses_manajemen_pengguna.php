<?php
// File: fungsi/proses_manajemen_pengguna.php

require_once '../config/config.php'; 
require_once '../auth/auth.php'; 

// Hanya admin yang bisa akses
if ($_SESSION['role'] !== 'admin') {
    header("Location: ../halaman/dasbor.php");
    exit;
}

$page_title = "Manajemen Pengguna";
$current_user_id = $_SESSION['user_id'];
$message = '';
$message_type = '';

// Mengambil pesan flash dari session jika ada
if (isset($_SESSION['flash_message'])) {
    $message = $_SESSION['flash_message'];
    $message_type = $_SESSION['flash_message_type'] ?? 'info';
    unset($_SESSION['flash_message']);
    unset($_SESSION['flash_message_type']);
}

// Mengambil semua data pengguna dari database
$stmt_users = $conn->prepare("SELECT id, username, role FROM users ORDER BY role ASC, username ASC");
$stmt_users->execute();
$result_users = $stmt_users->get_result();


