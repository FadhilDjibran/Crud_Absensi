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
    $jam_masuk = !empty($_POST['jam_masuk']) ? $_POST['jam_masuk'] : null;
    $bukti_file_info = $_FILES['bukti_file'];
    
    // Inisialisasi variabel untuk disimpan ke DB
    $kondisi_masuk = null;
    $path_bukti_file = null;
    
    // Ambil username berdasarkan user_id untuk disimpan di kolom 'nama'
    $stmt_get_user = $conn->prepare("SELECT username FROM users WHERE id = ?");
    $stmt_get_user->bind_param("i", $user_id);
    $stmt_get_user->execute();
    $user_data = $stmt_get_user->get_result()->fetch_assoc();
    $nama_pengguna = $user_data['username'] ?? 'User Tidak Dikenal';

    // Logika berdasarkan status yang dipilih
    if ($status === 'Hadir' && !empty($jam_masuk)) {
        $kondisi_masuk = ($jam_masuk > JAM_MASUK_KANTOR) ? 'Terlambat' : 'Tepat Waktu';
    } elseif (in_array($status, ['Izin', 'Sakit']) && !empty($bukti_file_info['name'])) {
        // Proses upload file bukti
        $upload_dir = "../uploads/bukti_absensi/";
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0775, true);

        $file_extension = strtolower(pathinfo($bukti_file_info['name'], PATHINFO_EXTENSION));
        $unique_filename = uniqid('bukti_admin_', true) . '.' . $file_extension;
        $destination_path = $upload_dir . $unique_filename;

        if (move_uploaded_file($bukti_file_info['tmp_name'], $destination_path)) {
            $path_bukti_file = "uploads/bukti_absensi/" . $unique_filename;
        } else {
            $flash_message_text = "Gagal mengunggah file bukti.";
            $flash_message_type = "danger";
        }
    }

    if (empty($flash_message_text)) { // Lanjutkan jika tidak ada error upload
        $stmt_insert = $conn->prepare(
            "INSERT INTO absensi (user_id, nama, tanggal, jam_masuk, status, kondisi_masuk, bukti_file) 
             VALUES (?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt_insert->bind_param("issssss", $user_id, $nama_pengguna, $tanggal, $jam_masuk, $status, $kondisi_masuk, $path_bukti_file);

        if ($stmt_insert->execute()) {
            $_SESSION['flash_message'] = "Data absensi manual untuk " . htmlspecialchars($nama_pengguna) . " berhasil ditambahkan!";
            $_SESSION['flash_message_type'] = "success";
            header("Location: ../halaman/absensi.php");
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
