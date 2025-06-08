<?php
// File: fungsi/proses_hapus_pengguna.php
// Skrip ini menangani logika untuk menghapus pengguna (karyawan).

require_once '../config/config.php';
require_once '../auth/auth.php'; // Pastikan sesi dimulai dan pengguna terotentikasi

// Hanya admin yang bisa mengakses skrip ini
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    $_SESSION['flash_message'] = "Anda tidak memiliki hak akses untuk tindakan ini.";
    $_SESSION['flash_message_type'] = "danger";
    header("Location: ../halaman/dasbor.php");
    exit;
}

// Validasi ID pengguna dari parameter GET
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['flash_message'] = "ID pengguna tidak valid atau tidak disediakan.";
    $_SESSION['flash_message_type'] = "danger";
    header("Location: ../halaman/manajemen_pengguna.php");
    exit;
}

$user_id_to_delete = (int)$_GET['id'];
$current_admin_id = (int)$_SESSION['user_id'];

// Mencegah admin menghapus akunnya sendiri
if ($user_id_to_delete === $current_admin_id) {
    $_SESSION['flash_message'] = "Anda tidak dapat menghapus akun Anda sendiri.";
    $_SESSION['flash_message_type'] = "danger";
    header("Location: ../halaman/manajemen_pengguna.php");
    exit;
}

// Ambil role dari akun yang akan dihapus untuk validasi tambahan
$stmt_fetch_role = $conn->prepare("SELECT role, username FROM users WHERE id = ?");
$stmt_fetch_role->bind_param("i", $user_id_to_delete);
$stmt_fetch_role->execute();
$result_role = $stmt_fetch_role->get_result();

if ($user_to_delete = $result_role->fetch_assoc()) {
    $target_user_role = $user_to_delete['role'];
    $target_user_username = $user_to_delete['username'];

    // Mencegah admin menghapus admin lainnya
    if ($target_user_role === 'admin') {
        $_SESSION['flash_message'] = "Anda tidak dapat menghapus sesama akun admin ('" . htmlspecialchars($target_user_username) . "').";
        $_SESSION['flash_message_type'] = "danger";
        header("Location: ../halaman/manajemen_pengguna.php");
        exit;
    }

    // Lanjutkan proses penghapusan jika target bukan admin
    $stmt_delete = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt_delete->bind_param("i", $user_id_to_delete);

    if ($stmt_delete->execute()) {
        if ($stmt_delete->affected_rows > 0) {
            $_SESSION['flash_message'] = "Pengguna '" . htmlspecialchars($target_user_username) . "' berhasil dihapus.";
            $_SESSION['flash_message_type'] = "success";
        } else {
            // Ini terjadi jika ID valid tapi pengguna sudah dihapus oleh proses lain
            $_SESSION['flash_message'] = "Pengguna tidak ditemukan atau sudah dihapus sebelumnya.";
            $_SESSION['flash_message_type'] = "warning";
        }
    } else {
        $_SESSION['flash_message'] = "Gagal menghapus pengguna. Error: " . htmlspecialchars($stmt_delete->error);
        $_SESSION['flash_message_type'] = "danger";
    }
    $stmt_delete->close();

} else {
    // Pengguna dengan ID yang diberikan tidak ada di database
    $_SESSION['flash_message'] = "Pengguna dengan ID " . htmlspecialchars($user_id_to_delete) . " tidak ditemukan.";
    $_SESSION['flash_message_type'] = "danger";
}
$stmt_fetch_role->close();
$conn->close();

// Arahkan kembali ke halaman manajemen pengguna
header("Location: ../halaman/manajemen_pengguna.php");
exit;
?>
