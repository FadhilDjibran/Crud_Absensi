<?php
// File: fungsi/proses_profil.php

require_once '../config/config.php';
require_once '../auth/auth.php';

// Memastikan pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    exit;
}

$page_title = "Profil Saya";
$user_id = $_SESSION['user_id'];

// Inisialisasi variabel untuk pesan dan pre-fill form
$message = '';
$message_type = '';
$current_username_display = $_SESSION['username']; 
$current_role_display = $_SESSION['role'];     

// Ambil Flash Mesaage
if (isset($_SESSION['flash_message'])) {
    $message = $_SESSION['flash_message'];
    $message_type = $_SESSION['flash_message_type'] ?? 'info';
    unset($_SESSION['flash_message']);
    unset($_SESSION['flash_message_type']);
}

// Menangani pembaruan profil saat form disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $new_username = trim($_POST['username']);
    $current_password_form = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_new_password = $_POST['confirm_new_password'];

    $update_clauses = [];
    $params_types = "";
    $params_values = [];

    // Logika untuk pembaruan username
    if (!empty($new_username) && $new_username !== $_SESSION['username']) {
        // Cek apakah username baru sudah digunakan oleh orang lain
        $stmt_check_username = $conn->prepare("SELECT id FROM users WHERE username = ? AND id != ?");
        $stmt_check_username->bind_param("si", $new_username, $user_id);
        $stmt_check_username->execute();
        $stmt_check_username->store_result();

        if ($stmt_check_username->num_rows > 0) {
            $message = "Username '" . htmlspecialchars($new_username) . "' sudah digunakan. Pilih username lain.";
            $message_type = 'danger';
            $new_username = $_SESSION['username']; // Kembalikan ke username lama jika error
        } else {
            $update_clauses[] = "username = ?";
            $params_types .= "s";
            $params_values[] = $new_username;
        }
        $stmt_check_username->close();
    } else {
        $new_username = $_SESSION['username']; // Simpan username yang dulu jika tidak diganti
    }

    // Logika untuk pembaruan password
    // Hanya dijalankan jika field password baru tidak kosong dan tidak ada error sebelumnya
    if (empty($message) && !empty($new_password)) {
        if (empty($current_password_form)) {
            $message = "Masukkan password Anda saat ini untuk mengubah password.";
            $message_type = 'danger';
        } elseif ($new_password !== $confirm_new_password) {
            $message = "Password baru dan konfirmasi password tidak cocok.";
            $message_type = 'danger';
        } else {
            // Verifikasi password saat ini dengan yang ada di database
            $stmt_verify_pass = $conn->prepare("SELECT password FROM users WHERE id = ?");
            $stmt_verify_pass->bind_param("i", $user_id);
            $stmt_verify_pass->execute();
            $user_db_data = $stmt_verify_pass->get_result()->fetch_assoc();
            $stmt_verify_pass->close();

            if ($user_db_data && password_verify($current_password_form, $user_db_data['password'])) {
                // Jika password benar, hash password baru dan tambahkan ke query update
                $new_password_hash = password_hash($new_password, PASSWORD_DEFAULT);
                $update_clauses[] = "password = ?";
                $params_types .= "s";
                $params_values[] = $new_password_hash;
            } else {
                $message = "Password Anda saat ini salah.";
                $message_type = 'danger';
            }
        }
    }

    // Lakukan update ke database jika ada perubahan dan tidak ada error
    if (!empty($update_clauses) && empty($message)) {
        $params_values[] = $user_id; 
        $params_types .= "i";

        $sql_update = "UPDATE users SET " . implode(", ", $update_clauses) . " WHERE id = ?";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param($params_types, ...$params_values);

        if ($stmt_update->execute()) {
            $_SESSION['flash_message'] = "Profil berhasil diperbarui.";
            $_SESSION['flash_message_type'] = 'success';

            // Update username pada session jika berhasil diubah
            if (in_array("username = ?", $update_clauses)) {
                $username_key = array_search("username = ?", $update_clauses);
                $_SESSION['username'] = $params_values[$username_key];
            }
            
            header("Location: ../halaman/profil.php"); // Redirect ke halaman profil baru
            exit;
        } else {
            $message = "Gagal memperbarui profil. Error: " . $stmt_update->error;
            $message_type = 'danger';
        }
        $stmt_update->close();
    } elseif (empty($update_clauses) && empty($message)) {
        // Jika form disubmit tapi tidak ada perubahan
        $message = "Tidak ada informasi yang diubah.";
        $message_type = 'info';
    }
    // Set ulang variabel display untuk sticky form
    $current_username_display = $new_username;
}
