<?php
// File: auth/auth.php

// Panggil session_start jika belum aktif 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Jika 'user_id' tidak ada di dalam session,
// artinya pengguna belum login. Paksa kembali ke halaman login.
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}
?>