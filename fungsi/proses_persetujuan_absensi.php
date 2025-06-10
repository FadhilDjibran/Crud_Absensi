<?php
// File: fungsi/proses_persetujuan_absensi.php

require_once '../config/config.php'; 
require_once '../auth/auth.php';     

// Hanya admin yang bisa mengakses
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    $_SESSION['flash_message'] = "Anda tidak memiliki hak akses untuk tindakan ini.";
    $_SESSION['flash_message_type'] = "danger";
    header("Location: ../auth/login.php"); 
    exit;
}

// Memastikan ID pengajuan ada
if (!isset($_GET['id']) || !is_numeric($_GET['id']) || !isset($_GET['aksi'])) {
    $_SESSION['flash_message'] = "Parameter tidak valid untuk memproses pengajuan.";
    $_SESSION['flash_message_type'] = "danger";
    header("Location: ../halaman/dasbor.php");
    exit;
}

$pengajuan_id = (int)$_GET['id'];
$aksi = $_GET['aksi']; // 'setujui' atau 'tolak'
$admin_user_id = $_SESSION['user_id']; // ID admin yang melakukan review

// Mengambil detail pengajuan dari database
$stmt_get_pengajuan = $conn->prepare("SELECT user_id, nama, tanggal, status_diajukan, status_review, bukti_file, jam_masuk, kondisi_masuk FROM pengajuanAbsensi WHERE id = ?");
if (!$stmt_get_pengajuan) {
    $_SESSION['flash_message'] = "Gagal mempersiapkan query untuk mengambil data pengajuan: " . $conn->error;
    $_SESSION['flash_message_type'] = "danger";
    header("Location: ../halaman/dasbor.php");
    exit;
}

$stmt_get_pengajuan->bind_param("i", $pengajuan_id);
$stmt_get_pengajuan->execute();
$result_pengajuan = $stmt_get_pengajuan->get_result();
$pengajuan = $result_pengajuan->fetch_assoc();
$stmt_get_pengajuan->close();

if (!$pengajuan) {
    $_SESSION['flash_message'] = "Pengajuan absensi dengan ID tersebut tidak ditemukan.";
    $_SESSION['flash_message_type'] = "warning";
    header("Location: ../halaman/dasbor.php");
    exit;
}

// Memastikan pengajuan masih dalam status 'pending' sebelum diproses
if ($pengajuan['status_review'] !== 'pending') {
    $_SESSION['flash_message'] = "Pengajuan ini sudah pernah direview sebelumnya.";
    $_SESSION['flash_message_type'] = "info";
    header("Location: ../halaman/dasbor.php");
    exit;
}

$conn->begin_transaction();

try {
    // Jika setujui
    if ($aksi === 'setujui') {
        // 1. Update status_review di tabel pengajuanAbsensi menjadi 'disetujui'
        $stmt_update_pengajuan = $conn->prepare("UPDATE pengajuanAbsensi SET status_review = 'disetujui' WHERE id = ?");
        if (!$stmt_update_pengajuan) throw new Exception("Gagal mempersiapkan query update pengajuan: " . $conn->error);
        
        $stmt_update_pengajuan->bind_param("i", $pengajuan_id);
        if (!$stmt_update_pengajuan->execute()) throw new Exception("Gagal mengupdate status pengajuan: " . $stmt_update_pengajuan->error);
        $stmt_update_pengajuan->close();

        // 2. Masukkan data ke tabel absensi 
        $stmt_insert_absensi = $conn->prepare(
            "INSERT INTO absensi (user_id, nama, tanggal, jam_masuk, status, kondisi_masuk, bukti_file) VALUES (?, ?, ?, ?, ?, ?, ?)"
        );
        if (!$stmt_insert_absensi) throw new Exception("Gagal mempersiapkan query insert absensi: " . $conn->error);

        // Menggunakan data dari pengajuan untuk dimasukkan ke tabel absensi
        $stmt_insert_absensi->bind_param(
            "issssss", 
            $pengajuan['user_id'], 
            $pengajuan['nama'], 
            $pengajuan['tanggal'], 
            $pengajuan['jam_masuk'], 
            $pengajuan['status_diajukan'],
            $pengajuan['kondisi_masuk'],
            $pengajuan['bukti_file']
        );
        if (!$stmt_insert_absensi->execute()) throw new Exception("Gagal memasukkan data ke tabel absensi: " . $stmt_insert_absensi->error);
        $stmt_insert_absensi->close();

        $_SESSION['flash_message'] = "Pengajuan absensi untuk " . htmlspecialchars($pengajuan['nama']) . " (" . htmlspecialchars($pengajuan['status_diajukan']) . ") berhasil disetujui.";
        $_SESSION['flash_message_type'] = "success";

    // Jika tolak
    } elseif ($aksi === 'tolak') {
        // 1. Update status_review di tabel pengajuanAbsensi menjadi 'ditolak'
        $stmt_update_pengajuan = $conn->prepare("UPDATE pengajuanAbsensi SET status_review = 'ditolak' WHERE id = ?");
        if (!$stmt_update_pengajuan) throw new Exception("Gagal mempersiapkan query update pengajuan (tolak): " . $conn->error);

        $stmt_update_pengajuan->bind_param("i", $pengajuan_id);
        if (!$stmt_update_pengajuan->execute()) throw new Exception("Gagal mengupdate status pengajuan (tolak): " . $stmt_update_pengajuan->error);
        $stmt_update_pengajuan->close();
        
        $_SESSION['flash_message'] = "Pengajuan absensi untuk " . htmlspecialchars($pengajuan['nama']) . " (" . htmlspecialchars($pengajuan['status_diajukan']) . ") telah ditolak.";
        $_SESSION['flash_message_type'] = "warning";

    } else {
        throw new Exception("Aksi tidak dikenal.");
    }

    $conn->commit();

} catch (Exception $e) {
    // Jika terjadi error di salah satu query, batalkan semua perubahan
    $conn->rollback();
    $_SESSION['flash_message'] = "Terjadi kesalahan saat memproses pengajuan: " . $e->getMessage();
    $_SESSION['flash_message_type'] = "danger";
}

$conn->close(); // Menutup koneksi
header("Location: ../halaman/dasbor.php"); 
exit;
?>
