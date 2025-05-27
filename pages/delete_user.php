<?php
// File: pages/delete_user.php
require_once '../config/config.php'; 

if ($_SESSION['role'] !== 'admin') {
    // Jika login tetapi bukan admin
    $_SESSION['flash_message'] = "Anda tidak memiliki hak akses untuk tindakan ini.";
    $_SESSION['flash_message_type'] = "danger";
    header("Location: manage_users.php"); 
    exit;
}

// Validasi user ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['flash_message'] = "ID pengguna tidak valid atau tidak disediakan.";
    $_SESSION['flash_message_type'] = "danger";
    header("Location: manage_users.php");
    exit;
}

$user_id_to_delete = (int)$_GET['id'];
$current_admin_id = (int)$_SESSION['user_id'];

// cegah admin dari menghapus akun sendiri
if ($user_id_to_delete === $current_admin_id) {
    $_SESSION['flash_message'] = "Anda tidak dapat menghapus akun Anda sendiri.";
    $_SESSION['flash_message_type'] = "danger";
    header("Location: manage_users.php");
    exit;
}

// ambil role akun yang dihapus
$stmt_fetch_role = $conn->prepare("SELECT role, username FROM users WHERE id = ?");
$stmt_fetch_role->bind_param("i", $user_id_to_delete);
$stmt_fetch_role->execute();
$result_role = $stmt_fetch_role->get_result();

if ($user_to_delete = $result_role->fetch_assoc()) {
    $target_user_role = $user_to_delete['role'];
    $target_user_username = $user_to_delete['username'];

    // cegah admin dari menghapus admin lainnya
    if ($target_user_role === 'admin') {
        $_SESSION['flash_message'] = "Anda tidak dapat menghapus akun admin ('" . htmlspecialchars($target_user_username) . "').";
        $_SESSION['flash_message_type'] = "danger";
        header("Location: manage_users.php");
        exit;
    }

    // lanjutkan penghapusan
    $stmt_delete = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt_delete->bind_param("i", $user_id_to_delete);

    if ($stmt_delete->execute()) {
        if ($stmt_delete->affected_rows > 0) {
            $_SESSION['flash_message'] = "Pengguna '" . htmlspecialchars($target_user_username) . "' (ID: " . $user_id_to_delete . ") berhasil dihapus.";
            $_SESSION['flash_message_type'] = "success";
        } else {
            $_SESSION['flash_message'] = "Pengguna tidak ditemukan atau sudah dihapus (ID: " . $user_id_to_delete . ").";
            $_SESSION['flash_message_type'] = "warning";
        }
    } else {
        $_SESSION['flash_message'] = "Gagal menghapus pengguna. Error: " . $stmt_delete->error;
        $_SESSION['flash_message_type'] = "danger";
    }
    $stmt_delete->close();

} else {
    $_SESSION['flash_message'] = "Pengguna dengan ID " . htmlspecialchars($user_id_to_delete) . " tidak ditemukan.";
    $_SESSION['flash_message_type'] = "danger";
}
$stmt_fetch_role->close();
$conn->close();

// kembali ke halaman manage users
header("Location: manage_users.php");
exit;
?>