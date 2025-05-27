<?php
// pages/hapus.php
require_once '../config/config.php'; // Memuat konfigurasi dan memulai session
require_once '../auth/auth.php';     // Pastikan hanya user ter-login yang bisa menghapus

// Pastikan hanya admin yang bisa mengakses halaman ini 
if ($_SESSION['role'] !== 'admin') {
    $_SESSION['flash_message'] = "Anda tidak memiliki hak akses untuk tindakan ini.";
    $_SESSION['flash_message_type'] = "danger";
    header("Location: index.php"); // Redirect ke halaman data absensi
    exit();
}

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = (int)$_GET['id'];
    
    $stmt = $conn->prepare("DELETE FROM absensi WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                // Set pesan sukses jika penghapusan berhasil
                $_SESSION['flash_message'] = "Data absensi berhasil dihapus.";
                $_SESSION['flash_message_type'] = "success";
            } else {
                $_SESSION['flash_message'] = "Data absensi dengan ID tersebut tidak ditemukan atau sudah dihapus.";
                $_SESSION['flash_message_type'] = "warning";
            }
        } else {
            // Set pesan error jika gagal eksekusi
            $_SESSION['flash_message'] = "Gagal menghapus data absensi. Error: " . $stmt->error;
            $_SESSION['flash_message_type'] = "danger";
        }
        $stmt->close();
    } else {
        $_SESSION['flash_message'] = "Terjadi kesalahan dalam sistem. Gagal mempersiapkan query penghapusan.";
        $_SESSION['flash_message_type'] = "danger";
    }
} else {
    // Jika ID tidak ada atau tidak valid
    $_SESSION['flash_message'] = "ID data absensi tidak valid atau tidak disediakan.";
    $_SESSION['flash_message_type'] = "danger";
}

$conn->close(); 

// Redirect kembali ke halaman utama (data absensi)
header("Location: index.php");
exit();
?>