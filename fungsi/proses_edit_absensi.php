<?php
// File: fungsi/proses_edit_absensi.php

require_once '../config/config.php';
require_once '../auth/auth.php';

// Fitur ini hanya untuk admin
if ($_SESSION['role'] !== 'admin') {
    $_SESSION['flash_message'] = "Anda tidak memiliki hak akses untuk halaman ini.";
    $_SESSION['flash_message_type'] = "danger";
    header("Location: ../halaman/dasbor.php");
    exit();
}

// Validasi ID
if (!isset($_GET['id']) || empty($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['flash_message'] = "ID data absensi tidak valid.";
    $_SESSION['flash_message_type'] = "warning";
    header("Location: ../halaman/absensi.php");
    exit();
}

$id = (int)$_GET['id'];
$page_title = "Edit Data Absensi";

// Ambil data absensi dari database untuk ditampilkan di form
$stmt_select = $conn->prepare("SELECT absensi.*, users.username FROM absensi LEFT JOIN users ON absensi.user_id = users.id WHERE absensi.id = ?");
$stmt_select->bind_param("i", $id);
$stmt_select->execute();
$result_select = $stmt_select->get_result();
$data_absensi = $result_select->fetch_assoc();
$stmt_select->close();

if (!$data_absensi) {
    $_SESSION['flash_message'] = "Data absensi tidak ditemukan!";
    $_SESSION['flash_message_type'] = "warning";
    header("Location: ../halaman/absensi.php");
    exit();
}

// Siapkan variabel
$nama_karyawan_db = $data_absensi['username'] ?? $data_absensi['nama']; // Fallback ke nama jika username null
$tanggal_absensi_db = $data_absensi['tanggal'];
$status_absensi_db = $data_absensi['status'];
$jam_masuk_db = $data_absensi['jam_masuk'];
$bukti_file_db = $data_absensi['bukti_file'];

// Proses Update Data jika form disubmit
if (isset($_POST['update'])) {
    $tanggal_form = $_POST['tanggal'];
    $status_form = $_POST['status'];
    $jam_masuk_form = !empty($_POST['jam_masuk']) ? $_POST['jam_masuk'] : null;
    
    $hapus_bukti_saat_ini = isset($_POST['hapus_bukti_saat_ini']);
    $file_bukti_baru_info = $_FILES['file_bukti_baru'];
    
    $kondisi_masuk_baru = null;
    $path_db_untuk_bukti = $bukti_file_db; 

    // 1. Logika Hapus File Bukti
    if ($hapus_bukti_saat_ini && !empty($bukti_file_db)) {
        if (file_exists("../" . $bukti_file_db)) {
            unlink("../" . $bukti_file_db);
        }
        $path_db_untuk_bukti = null; // Set jadi null karena dihapus
    }

    // 2. Logika Upload File Bukti Baru
    if (isset($file_bukti_baru_info) && $file_bukti_baru_info['error'] == UPLOAD_ERR_OK) {
        // Hapus file lama terlebih dahulu 
        if (!empty($path_db_untuk_bukti)) {
             if (file_exists("../" . $path_db_untuk_bukti)) {
                unlink("../" . $path_db_untuk_bukti);
            }
        }

        // Proses unggah file baru
        $upload_dir = "../uploads/bukti_absensi/"; 
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0775, true);

        $file_extension = strtolower(pathinfo($file_bukti_baru_info['name'], PATHINFO_EXTENSION));
        $unique_filename = uniqid('bukti_update_', true) . '.' . $file_extension;
        $destination_path = $upload_dir . $unique_filename;

        // Validasi file
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'pdf'];
        $max_file_size = 2 * 1024 * 1024; // 2MB

        if (in_array($file_extension, $allowed_extensions) && $file_bukti_baru_info['size'] <= $max_file_size) {
            if (move_uploaded_file($file_bukti_baru_info['tmp_name'], $destination_path)) {
                $path_db_untuk_bukti = "uploads/bukti_absensi/" . $unique_filename;
            } else {
                $_SESSION['flash_message'] = "Gagal mengunggah file bukti baru.";
                $_SESSION['flash_message_type'] = "danger";
                header("Location: ../halaman/edit_absensi.php?id=" . $id);
                exit();
            }
        } else {
             $_SESSION['flash_message'] = "Format file tidak valid atau ukuran terlalu besar (Maks 2MB).";
             $_SESSION['flash_message_type'] = "danger";
             header("Location: ../halaman/edit_absensi.php?id=" . $id);
             exit();
        }
    }

    // 3. Menentukan nilai akhir 
    if ($status_form == 'Hadir') {
        // Jika status Hadir, tentukan kondisi masuk dan hapus bukti file
        if (!empty($jam_masuk_form)) {
            $kondisi_masuk_baru = ($jam_masuk_form > JAM_MASUK_KANTOR) ? 'Terlambat' : 'Tepat Waktu';
        }
        // Jika ada bukti file dan status diubah menjadi Hadir, hapus file lama.
        if (!empty($path_db_untuk_bukti)) {
            if (file_exists("../" . $path_db_untuk_bukti)) unlink("../" . $path_db_untuk_bukti);
        }
        $path_db_untuk_bukti = null;
    } else {
        // Jika status bukan Hadir, jam_masuk dan kondisi_masuk dihapus
        $jam_masuk_form = null;
        $kondisi_masuk_baru = null;
        // Jika status diubah jadi Alpha dan ada file bukti, hapus filenya.
        if ($status_form == 'Alpha' && !empty($path_db_untuk_bukti)) {
            if (file_exists("../" . $path_db_untuk_bukti)) unlink("../" . $path_db_untuk_bukti);
            $path_db_untuk_bukti = null;
        }
    }

    // 4. Update Database
    $stmt_update = $conn->prepare("UPDATE absensi SET tanggal = ?, jam_masuk = ?, status = ?, kondisi_masuk = ?, bukti_file = ? WHERE id = ?");
    $stmt_update->bind_param("sssssi", $tanggal_form, $jam_masuk_form, $status_form, $kondisi_masuk_baru, $path_db_untuk_bukti, $id);

    if ($stmt_update->execute()) {
        $_SESSION['flash_message'] = "Data absensi untuk " . htmlspecialchars($nama_karyawan_db) . " berhasil diperbarui!";
        $_SESSION['flash_message_type'] = "success";
        header("Location: ../halaman/absensi.php");
        exit();
    } else {
        $_SESSION['flash_message'] = "Gagal memperbarui data absensi. Error: " . htmlspecialchars($stmt_update->error);
        $_SESSION['flash_message_type'] = "danger";
        header("Location: ../halaman/edit_absensi.php?id=" . $id);
        exit();
    }
    $stmt_update->close();
}

// Logika Flash Message
$flash_message_text = '';
$flash_message_type = '';
if (isset($_SESSION['flash_message'])) {
    $flash_message_text = $_SESSION['flash_message'];
    $flash_message_type = $_SESSION['flash_message_type'] ?? 'info';
    unset($_SESSION['flash_message']);
    unset($_SESSION['flash_message_type']);
}
