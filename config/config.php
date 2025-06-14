<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
// File: config/config.php

// Pastikan session selalu dimulai
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Atur zona waktu default ke Waktu Indonesia Barat
date_default_timezone_set('Asia/Jakarta');

// Untuk menampilkan error jika ada
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Konfigurasi Database
$host = "localhost";
$user = "root";
$pass = "";
$db   = "absensi_db";

// Buat koneksi menggunakan MySQLi
$conn = new mysqli($host, $user, $pass, $db);

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi ke database gagal: " . $conn->connect_error);
}

define('JAM_MASUK_KANTOR', '08:00:00'); // Atur jam masuk di sini

?>