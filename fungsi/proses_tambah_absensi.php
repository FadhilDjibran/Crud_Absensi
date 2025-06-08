<?php
// File: fungsi/proses_tambah_absensi.php
// Berisi logika untuk halaman tambah absensi manual oleh admin.

require_once '../config/config.php'; 
require_once '../auth/auth.php'; 

// Hanya admin yang bisa menambah absensi
if ($_SESSION['role'] !== 'admin') {
    $_SESSION['flash_message'] = "Anda tidak memiliki hak akses untuk halaman ini.";
    $_SESSION['flash_message_type'] = "danger";
    header("Location: ../halaman/dasbor.php");
    exit();
}

$page_title = "Tambah Absensi Manual"; 

// PERBAIKAN: Ambil semua pengguna (admin dan karyawan) untuk dropdown
$users_list = [];
$users_result = $conn->query("SELECT id, username, role FROM users ORDER BY role ASC, username ASC");
if ($users_result) {
    // Mengelompokkan pengguna berdasarkan peran
    while ($user = $users_result->fetch_assoc()) {
        $users_list[$user['role']][] = $user;
    }
}

// Inisialisasi variabel pesan
$flash_message_text = '';
$flash_message_type = '';

// Proses form jika disubmit
if (isset($_POST['tambah'])) {
    $user_id = $_POST['user_id']; 
    $tanggal = $_POST['tanggal'];
    $status = $_POST['status'];
    
    // Ambil username berdasarkan user_id untuk disimpan di kolom 'nama'
    $stmt_get_user = $conn->prepare("SELECT username FROM users WHERE id = ?");
    $stmt_get_user->bind_param("i", $user_id);
    $stmt_get_user->execute();
    $user_data = $stmt_get_user->get_result()->fetch_assoc();
    $nama_pengguna = $user_data['username'] ?? 'User Tidak Dikenal';

    if (empty($user_id) || empty($nama_pengguna) || empty($tanggal) || empty($status)) {
        // Jika validasi gagal, siapkan pesan untuk ditampilkan di halaman form
        $flash_message_text = "Semua kolom wajib diisi.";
        $flash_message_type = "danger";
    } else {
        // Query INSERT sekarang menyertakan user_id dan nama
        $stmt_insert = $conn->prepare("INSERT INTO absensi (user_id, nama, tanggal, status) VALUES (?, ?, ?, ?)");
        $stmt_insert->bind_param("isss", $user_id, $nama_pengguna, $tanggal, $status);

        if ($stmt_insert->execute()) {
            $_SESSION['flash_message'] = "Data absensi manual untuk " . htmlspecialchars($nama_pengguna) . " berhasil ditambahkan!";
            $_SESSION['flash_message_type'] = "success";
            header("Location: ../halaman/absensi.php"); // Redirect ke halaman daftar absensi baru
            exit();
        } else {
            $flash_message_text = "Gagal menambahkan data: " . $stmt_insert->error;
            $flash_message_type = "danger";
        }
        $stmt_insert->close();
    }
}

// Tangani flash message dari redirect lain (jika ada)
if (isset($_SESSION['flash_message']) && !isset($_POST['tambah'])) {
    $flash_message_text = $_SESSION['flash_message'];
    $flash_message_type = $_SESSION['flash_message_type'] ?? 'info';
    unset($_SESSION['flash_message']);
    unset($_SESSION['flash_message_type']);
}

// Variabel yang sudah disiapkan di sini akan tersedia untuk file tampilan.
