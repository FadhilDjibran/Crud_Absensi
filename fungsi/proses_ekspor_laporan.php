<?php
// File: fungsi/proses_ekspor_laporan.php

require_once '../config/config.php';
require_once '../auth/auth.php';

// Fitur ini hanya untuk admin
if ($_SESSION['role'] !== 'admin') {
    http_response_code(403); // Forbidden
    echo "Akses ditolak.";
    exit;
}

// Logika ini menyalin dari proses_laporan_absensi.php untuk memastikan
// data yang diekspor konsisten dengan apa yang ditampilkan di halaman laporan.
$user_id = $_GET['user_id'] ?? '';
$tanggal_mulai = $_GET['tanggal_mulai'] ?? '';
$tanggal_selesai = $_GET['tanggal_selesai'] ?? '';

// Membangun query berdasarkan filter
$where_parts = [];
$params = [];
$types = '';

if (!empty($user_id)) {
    $where_parts[] = "absensi.user_id = ?";
    $params[] = $user_id;
    $types .= 'i';
}
if (!empty($tanggal_mulai)) {
    $where_parts[] = "absensi.tanggal >= ?";
    $params[] = $tanggal_mulai;
    $types .= 's';
}
if (!empty($tanggal_selesai)) {
    $where_parts[] = "absensi.tanggal <= ?";
    $params[] = $tanggal_selesai;
    $types .= 's';
}

$where_clause = '';
if (!empty($where_parts)) {
    $where_clause = ' WHERE ' . implode(' AND ', $where_parts);
}

$sql_laporan = "SELECT 
                    absensi.id AS id_absensi, 
                    users.username, 
                    absensi.tanggal, 
                    absensi.jam_masuk, 
                    absensi.status, 
                    absensi.kondisi_masuk
                FROM absensi 
                LEFT JOIN users ON absensi.user_id = users.id" 
               . $where_clause . " ORDER BY absensi.tanggal ASC, absensi.id ASC";

$stmt_laporan = $conn->prepare($sql_laporan);
if ($stmt_laporan) {
    if (!empty($params)) {
        $stmt_laporan->bind_param($types, ...$params);
    }
    $stmt_laporan->execute();
    $result = $stmt_laporan->get_result();
} else {
    // Jika query gagal, hentikan
    http_response_code(500); 
    echo "Gagal mempersiapkan query laporan.";
    exit;
}


// Pembuatan File CSV

// 1. Menentukan nama file dan Header HTTP untuk download
$nama_file = "laporan_absensi_" . date('Y-m-d_H-i-s') . ".csv";
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $nama_file . '"');

// 2. Membuka output stream PHP untuk menulis file CSV
$output = fopen('php://output', 'w');

// 3. Menulis baris header untuk file CSV
fputcsv($output, ['ID Absensi', 'Nama Karyawan', 'Tanggal', 'Jam Masuk', 'Status', 'Kondisi']);

// 4. Melakukan loop pada hasil query dan menulis setiap baris data ke file CSV
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        fputcsv($output, $row);
    }
}

// 5. Menutup koneksi database dan output stream
$stmt_laporan->close();
$conn->close();
fclose($output);
exit();
?>
