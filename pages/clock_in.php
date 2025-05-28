<?php
// File: pages/clock_in.php
require_once '../config/config.php';
require_once '../auth/auth.php';

// Fitur ini hanya untuk karyawan
if ($_SESSION['role'] !== 'karyawan') {
    header("Location: dashboard.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];
$tanggal_hari_ini = date('Y-m-d');

// 1. Cek apakah karyawan sudah absen hari ini untuk mencegah data ganda
$stmt_check = $conn->prepare("SELECT id FROM absensi WHERE user_id = ? AND tanggal = ?");
$stmt_check->bind_param("is", $user_id, $tanggal_hari_ini);
$stmt_check->execute();
$result_check = $stmt_check->get_result();

if ($result_check->num_rows > 0) {
    // Jika sudah ada, kembalikan ke dashboard dengan pesan
    $_SESSION['flash_message'] = "Anda sudah melakukan absensi hari ini.";
    $_SESSION['flash_message_type'] = "warning";
    header("Location: dashboard.php");
    exit;
}
$stmt_check->close();

// 2. Jika belum absen, masukkan data baru ke tabel absensi
$status = 'Hadir';
$stmt_insert = $conn->prepare("INSERT INTO absensi (user_id, nama, tanggal, status) VALUES (?, ?, ?, ?)");
$stmt_insert->bind_param("isss", $user_id, $username, $tanggal_hari_ini, $status);

if ($stmt_insert->execute()) {
    $_SESSION['flash_message'] = "Clock-in berhasil! Terima kasih dan selamat bekerja.";
    $_SESSION['flash_message_type'] = "success";
} else {
    $_SESSION['flash_message'] = "Gagal melakukan clock-in. Silakan coba lagi. Error: " . $stmt_insert->error;
    $_SESSION['flash_message_type'] = "danger";
}
$stmt_insert->close();
$conn->close();

header("Location: dashboard.php");
exit;
?>