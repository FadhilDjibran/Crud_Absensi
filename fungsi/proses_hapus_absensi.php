<?php
// File: fungsi/proses_hapus_absensi.php
// Skrip ini menangani logika untuk menghapus data dari tabel absensi.

require_once '../config/config.php'; // Memuat konfigurasi dan memulai session
require_once '../auth/auth.php';     // Memastikan hanya pengguna yang terautentikasi

// Memastikan hanya admin yang bisa mengakses skrip ini
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    $_SESSION['flash_message'] = "Anda tidak memiliki hak akses untuk tindakan ini.";
    $_SESSION['flash_message_type'] = "danger";
    header("Location: ../halaman/dasbor.php"); // Arahkan ke dasbor jika bukan admin
    exit();
}

// Validasi ID dari parameter GET
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = (int)$_GET['id'];
    
    // Menggunakan prepared statement untuk keamanan
    $stmt = $conn->prepare("DELETE FROM absensi WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                // Mengatur pesan sukses jika penghapusan berhasil
                $_SESSION['flash_message'] = "Data absensi berhasil dihapus.";
                $_SESSION['flash_message_type'] = "success";
            } else {
                // Jika ID tidak ditemukan atau data sudah dihapus
                $_SESSION['flash_message'] = "Data absensi dengan ID tersebut tidak ditemukan atau sudah dihapus.";
                $_SESSION['flash_message_type'] = "warning";
            }
        } else {
            // Mengatur pesan error jika eksekusi query gagal
            $_SESSION['flash_message'] = "Gagal menghapus data absensi. Error: " . htmlspecialchars($stmt->error);
            $_SESSION['flash_message_type'] = "danger";
        }
        $stmt->close();
    } else {
        // Gagal mempersiapkan statement SQL
        $_SESSION['flash_message'] = "Terjadi kesalahan pada sistem. Gagal mempersiapkan query penghapusan.";
        $_SESSION['flash_message_type'] = "danger";
    }
} else {
    // Jika parameter ID tidak ada atau tidak valid
    $_SESSION['flash_message'] = "ID data absensi tidak valid atau tidak disediakan.";
    $_SESSION['flash_message_type'] = "danger";
}

$conn->close(); // Menutup koneksi database

// Mengarahkan pengguna kembali ke halaman daftar absensi
header("Location: ../halaman/absensi.php");
exit();
?>
