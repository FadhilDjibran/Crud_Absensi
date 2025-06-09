<?php
// File: fungsi/proses_pengajuan_hadir.php
// Skrip ini menangani logika backend saat karyawan melakukan "Clock In".

require_once '../config/config.php'; // Memuat konfigurasi dan memulai session
require_once '../auth/auth.php';     // Memastikan hanya pengguna yang terautentikasi

// Pastikan pengguna adalah karyawan (bukan admin)
if ($_SESSION['role'] === 'admin') {
    $_SESSION['flash_message'] = "Admin tidak dapat melakukan clock-in melalui fitur ini.";
    $_SESSION['flash_message_type'] = "warning";
    header("Location: ../halaman/dasbor.php");
    exit;
}

$current_user_id = $_SESSION['user_id'];
$current_username = $_SESSION['username']; // Nama karyawan dari session
$tanggal_hari_ini = date('Y-m-d');
$status_diajukan = 'Hadir'; // Status yang diajukan adalah "Hadir"

// --- Pengecekan Duplikasi ---
// 1. Cek apakah karyawan sudah memiliki pengajuan 'pending' untuk hari ini
$stmt_check_pengajuan = $conn->prepare(
    "SELECT id FROM pengajuanAbsensi WHERE user_id = ? AND tanggal = ? AND status_review = 'pending'"
);
if (!$stmt_check_pengajuan) {
    error_log("MySQLi prepare error (pengajuan check): " . $conn->error);
    $_SESSION['flash_message'] = "Terjadi kesalahan pada sistem. Silakan coba lagi nanti. (Error Code: PCK01)";
    $_SESSION['flash_message_type'] = "danger";
    header("Location: ../halaman/dasbor.php");
    exit;
}
$stmt_check_pengajuan->bind_param("is", $current_user_id, $tanggal_hari_ini);
$stmt_check_pengajuan->execute();
if ($stmt_check_pengajuan->get_result()->num_rows > 0) {
    $_SESSION['flash_message'] = "Anda sudah mengajukan absensi yang sedang menunggu persetujuan untuk hari ini.";
    $_SESSION['flash_message_type'] = "warning";
    header("Location: ../halaman/dasbor.php");
    exit;
}
$stmt_check_pengajuan->close();

// 2. Cek apakah karyawan sudah memiliki absensi final (disetujui) di tabel absensi untuk hari ini
$stmt_check_absensi = $conn->prepare(
    "SELECT id FROM absensi WHERE user_id = ? AND tanggal = ?"
);
if (!$stmt_check_absensi) {
    error_log("MySQLi prepare error (absensi check): " . $conn->error);
    $_SESSION['flash_message'] = "Terjadi kesalahan pada sistem. Silakan coba lagi nanti. (Error Code: PCK02)";
    $_SESSION['flash_message_type'] = "danger";
    header("Location: ../halaman/dasbor.php");
    exit;
}
$stmt_check_absensi->bind_param("is", $current_user_id, $tanggal_hari_ini);
$stmt_check_absensi->execute();
if ($stmt_check_absensi->get_result()->num_rows > 0) {
    $_SESSION['flash_message'] = "Absensi Anda untuk hari ini sudah tercatat dan disetujui.";
    $_SESSION['flash_message_type'] = "info";
    header("Location: ../halaman/dasbor.php");
    exit;
}
$stmt_check_absensi->close();


// --- Proses Pengajuan ---
// PERUBAHAN: Tangkap jam sekarang dan tentukan kondisinya
$jam_sekarang = date('H:i:s');
$kondisi_masuk = ($jam_sekarang > JAM_MASUK_KANTOR) ? 'Terlambat' : 'Tepat Waktu';

// Jika lolos semua pengecekan, buat pengajuan absensi baru
// PERUBAHAN: Query INSERT sekarang menyertakan jam_masuk dan kondisi_masuk
$stmt_insert_pengajuan = $conn->prepare(
    "INSERT INTO pengajuanAbsensi (user_id, nama, tanggal, jam_masuk, status_diajukan, kondisi_masuk, status_review, bukti_file) 
     VALUES (?, ?, ?, ?, ?, ?, 'pending', NULL)"
);

if (!$stmt_insert_pengajuan) {
    error_log("MySQLi prepare error (insert pengajuan): " . $conn->error);
    $_SESSION['flash_message'] = "Terjadi kesalahan pada sistem. Silakan coba lagi nanti. (Error Code: PCK03)";
    $_SESSION['flash_message_type'] = "danger";
    header("Location: ../halaman/dasbor.php");
    exit;
}

// PERBAIKAN: bind_param disesuaikan menjadi "isssss" untuk (id, nama, tanggal, jam, status, kondisi)
$stmt_insert_pengajuan->bind_param("isssss", $current_user_id, $current_username, $tanggal_hari_ini, $jam_sekarang, $status_diajukan, $kondisi_masuk);

if ($stmt_insert_pengajuan->execute()) {
    // PERBAIKAN: Pesan sukses sekarang menyertakan kondisi masuk
    $_SESSION['flash_message'] = "Clock-in berhasil diajukan (" . htmlspecialchars($kondisi_masuk) . "). Pengajuan Anda sedang menunggu review admin.";
    $_SESSION['flash_message_type'] = "success";
} else {
    error_log("MySQLi execute error (insert pengajuan): " . $stmt_insert_pengajuan->error);
    $_SESSION['flash_message'] = "Gagal mengajukan clock-in. Silakan coba lagi.";
    $_SESSION['flash_message_type'] = "danger";
}
$stmt_insert_pengajuan->close();
$conn->close();

header("Location: ../halaman/dasbor.php");
exit;
?>
