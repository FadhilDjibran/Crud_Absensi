<?php
// File: fungsi/proses_absensi.php
// Berisi logika untuk menampilkan halaman daftar absensi (data yang sudah disetujui).

require_once '../config/config.php';
require_once '../auth/auth.php'; 

// Menyiapkan variabel untuk halaman tampilan
$page_title = "Data Absensi";

// Logika untuk Notifikasi (Flash Message)
$flash_message_text = '';
$flash_message_type = '';
if (isset($_SESSION['flash_message'])) {
    $flash_message_text = $_SESSION['flash_message'];
    $flash_message_type = $_SESSION['flash_message_type'] ?? 'success'; 
    unset($_SESSION['flash_message']);
    unset($_SESSION['flash_message_type']);
}

// Konfigurasi Paginasi
$batas = 10; // Jumlah data per halaman
$halaman = isset($_GET['halaman']) && is_numeric($_GET['halaman']) ? (int)$_GET['halaman'] : 1;
$mulai = ($halaman > 0) ? (($halaman - 1) * $batas) : 0; // Pastikan 'mulai' tidak bernilai negatif

// Logika Pencarian & Peran Pengguna (User Role)
$cari = isset($_GET['cari']) ? trim($_GET['cari']) : '';
$where_parts = [];
$params = [];
$types = '';

// Filter berdasarkan user_id jika yang login adalah karyawan
if ($_SESSION['role'] == 'karyawan') {
    $where_parts[] = "absensi.user_id = ?";
    $params[] = $_SESSION['user_id']; 
    $types .= 'i'; // Tipe data integer
}

// Filter berdasarkan pencarian nama
if (!empty($cari)) {
    // Pencarian dilakukan pada kolom username di tabel users karena lebih konsisten
    $where_parts[] = "users.username LIKE ?";
    $search_term = "%" . $cari . "%";
    $params[] = $search_term;
    $types .= 's';
}

$where_clause = '';
if (!empty($where_parts)) {
    $where_clause = ' WHERE ' . implode(' AND ', $where_parts);
}

// PERBAIKAN: Query utama sekarang juga mengambil 'jam_masuk' dan 'kondisi_masuk' dari tabel absensi
$sql_data = "SELECT absensi.id, absensi.user_id, absensi.nama, absensi.tanggal, absensi.jam_masuk, absensi.status, absensi.kondisi_masuk, users.username 
             FROM absensi 
             LEFT JOIN users ON absensi.user_id = users.id" 
            . $where_clause . " ORDER BY absensi.tanggal DESC, absensi.id DESC LIMIT ?, ?";
            
$params_for_data_query = $params; 
$params_for_data_query[] = $mulai;
$params_for_data_query[] = $batas;
$types_for_data_query = $types . 'ii';

$stmt = $conn->prepare($sql_data);
if ($stmt) {
    if (!empty($types_for_data_query)) {
        // Menggunakan call_user_func_array untuk bind_param dinamis yang lebih aman
        $bind_args = array_merge([$types_for_data_query], $params_for_data_query);
        $refs = [];
        foreach ($bind_args as $key => $value) {
            $refs[$key] = &$bind_args[$key];
        }
        call_user_func_array([$stmt, 'bind_param'], $refs);
    }
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    // Menangani error jika prepare statement gagal
    error_log("Gagal mempersiapkan statement utama: " . $conn->error);
    // Inisialisasi $result sebagai objek kosong agar tidak error di view
    $result = new stdClass();
    $result->num_rows = 0;
}

// Query untuk menghitung total data (untuk paginasi)
// Perlu JOIN juga agar filter pencarian berdasarkan username berfungsi
$sql_hitung = "SELECT COUNT(absensi.id) AS total FROM absensi LEFT JOIN users ON absensi.user_id = users.id" . $where_clause;
$stmt_hitung = $conn->prepare($sql_hitung);
$total = 0;
$jumlahHalaman = 0;
if ($stmt_hitung) {
    if(!empty($types)) { 
        $stmt_hitung->bind_param($types, ...$params);
    }
    $stmt_hitung->execute();
    $total_result = $stmt_hitung->get_result()->fetch_assoc();
    $total = $total_result['total'] ?? 0;
    $jumlahHalaman = ceil($total / $batas);
    $stmt_hitung->close();
} else {
    // Menangani error jika prepare statement gagal
    error_log("Gagal mempersiapkan statement hitung: " . $conn->error);
}

// Semua variabel yang dibutuhkan oleh view sudah siap.
?>
