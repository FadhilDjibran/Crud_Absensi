<?php
// pages/hapus.php
require_once '../config/config.php';
require_once '../auth/auth.php'; // Pastikan hanya user ter-login yang bisa menghapus

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    
    // Gunakan prepared statement untuk keamanan
    $stmt = $conn->prepare("DELETE FROM absensi WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        // Set pesan sukses jika penghapusan berhasil
        $_SESSION['message'] = "Data absensi berhasil dihapus.";
    } else {
        // Set pesan error jika gagal
        $_SESSION['message'] = "Gagal menghapus data.";
    }
    $stmt->close();
}

// Redirect kembali ke halaman utama
header("Location: index.php");
exit();
?>