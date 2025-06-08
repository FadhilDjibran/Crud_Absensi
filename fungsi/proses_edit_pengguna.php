<?php
// File: fungsi/proses_edit_pengguna.php
// Berisi logika untuk mengambil dan memperbarui data pengguna oleh admin.

require_once '../config/config.php';
require_once '../auth/auth.php';

// Fitur ini hanya untuk admin
if ($_SESSION['role'] !== 'admin') {
    $_SESSION['flash_message'] = "Anda tidak memiliki hak akses untuk halaman ini.";
    $_SESSION['flash_message_type'] = "danger";
    header("Location: ../halaman/dasbor.php");
    exit();
}

// Inisialisasi variabel untuk view
$page_title = "Edit Pengguna";
$user_id_to_edit = null;
$form_username = ''; 
$form_role = '';     
$original_username = ''; 
$original_role = '';     
$message = '';
$message_type = '';

// Ambil user ID dari URL dan validasi
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $user_id_to_edit = (int)$_GET['id'];
} else {
    $_SESSION['flash_message'] = "ID pengguna tidak valid atau tidak disediakan.";
    $_SESSION['flash_message_type'] = "danger";
    header("Location: ../halaman/manajemen_pengguna.php");
    exit;
}

// Ambil data pengguna saat ini dari database untuk pre-fill form
$stmt_fetch_user = $conn->prepare("SELECT username, role FROM users WHERE id = ?");
$stmt_fetch_user->bind_param("i", $user_id_to_edit);
$stmt_fetch_user->execute();
$result_fetch_user = $stmt_fetch_user->get_result();

if ($user_to_edit = $result_fetch_user->fetch_assoc()) {
    $form_username = $user_to_edit['username'];
    $original_username = $user_to_edit['username'];
    $form_role = $user_to_edit['role'];
    $original_role = $user_to_edit['role'];
} else {
    $_SESSION['flash_message'] = "Pengguna dengan ID " . htmlspecialchars($user_id_to_edit) . " tidak ditemukan.";
    $_SESSION['flash_message_type'] = "danger";
    header("Location: ../halaman/manajemen_pengguna.php");
    exit;
}
$stmt_fetch_user->close();

// Proses Update Data jika form disubmit
if (isset($_POST['update_user_submit'])) {
    $new_username = trim($_POST['username']);
    $new_password = $_POST['password']; 
    $new_role = $_POST['role'];

    // Update nilai form untuk sticky form jika terjadi error
    $form_username = $new_username;
    $form_role = $new_role;

    // Validasi input
    if (empty($new_username) || empty($new_role)) {
        $message = "Username dan Role tidak boleh kosong.";
        $message_type = 'danger';
    } elseif (!in_array($new_role, ['admin', 'karyawan'])) {
        $message = "Role yang dipilih tidak valid.";
        $message_type = 'danger';
    } else {
        // Mencegah admin mengubah role dari 'admin' menjadi 'karyawan'
        if ($original_role === 'admin' && $new_role === 'karyawan') {
            $message = "Perubahan role dari 'Admin' menjadi 'Karyawan' tidak diizinkan.";
            $message_type = 'danger';
            $form_role = $original_role; 
            $new_role = $original_role;  
        }

        $update_clauses = [];
        $params_types = "";
        $params_values = [];

        // Logika ganti username
        if (empty($message) && $new_username !== $original_username) {
            $stmt_check_username = $conn->prepare("SELECT id FROM users WHERE username = ? AND id != ?");
            $stmt_check_username->bind_param("si", $new_username, $user_id_to_edit);
            $stmt_check_username->execute();
            if ($stmt_check_username->get_result()->num_rows > 0) {
                $message = "Username '" . htmlspecialchars($new_username) . "' sudah digunakan.";
                $message_type = 'danger';
            } else {
                $update_clauses[] = "username = ?";
                $params_types .= "s";
                $params_values[] = $new_username;
            }
            $stmt_check_username->close();
        }

        // Logika ganti password
        if (empty($message) && !empty($new_password)) {
            $new_password_hash = password_hash($new_password, PASSWORD_DEFAULT);
            $update_clauses[] = "password = ?";
            $params_types .= "s";
            $params_values[] = $new_password_hash;
        }

        // Logika ganti role
        if (empty($message) && $new_role !== $original_role) {
            $update_clauses[] = "role = ?";
            $params_types .= "s";
            $params_values[] = $new_role;
        }

        // Lakukan update ke database jika ada perubahan dan tidak ada error
        if (!empty($update_clauses) && empty($message)) {
            $params_values[] = $user_id_to_edit;
            $params_types .= "i";

            $sql_update = "UPDATE users SET " . implode(", ", $update_clauses) . " WHERE id = ?";
            $stmt_update = $conn->prepare($sql_update);
            $stmt_update->bind_param($params_types, ...$params_values);

            if ($stmt_update->execute()) {
                // Update session jika admin mengedit data mereka sendiri
                if ($user_id_to_edit === $_SESSION['user_id']) {
                    if (in_array("username = ?", $update_clauses)) {
                        $username_key = array_search("username = ?", $update_clauses);
                        $_SESSION['username'] = $params_values[$username_key];
                    }
                    if (in_array("role = ?", $update_clauses)) {
                        $role_key = array_search("role = ?", $update_clauses);
                        $_SESSION['role'] = $params_values[$role_key];
                    }
                }
                $_SESSION['flash_message'] = "Data pengguna '" . htmlspecialchars($new_username) . "' berhasil diperbarui.";
                $_SESSION['flash_message_type'] = 'success';
                header("Location: ../halaman/manajemen_pengguna.php");
                exit;
            } else {
                $message = "Gagal memperbarui data pengguna. Error: " . $stmt_update->error;
                $message_type = 'danger';
            }
            $stmt_update->close();
        } elseif (empty($update_clauses) && empty($message)) {
            $message = "Tidak ada perubahan data yang dilakukan.";
            $message_type = 'info';
        }
    }
}

// Mengambil pesan flash jika ada (misal dari redirect sebelumnya)
if (isset($_SESSION['flash_message'])) {
    $message = $_SESSION['flash_message'];
    $message_type = $_SESSION['flash_message_type'] ?? 'info';
    unset($_SESSION['flash_message']);
    unset($_SESSION['flash_message_type']);
}

// Variabel yang sudah disiapkan akan digunakan oleh file tampilan.
