<?php
// File: fungsi/proses_ekspor_laporan.php
// Skrip ini bertanggung jawab untuk menghasilkan dan mengunduh laporan absensi dalam format CSV.

require_once '../config/config.php';
require_once '../auth/auth.php';

// Fitur ini hanya untuk admin
if ($_SESSION['role'] !== 'admin') {
    // Admin tidak perlu pesan flash, cukup hentikan eksekusi
    http_response_code(403); // Forbidden
    echo "Akses ditolak.";
    exit;
}

// Logika ini adalah salinan dari proses_laporan_absensi.php untuk memastikan
// data yang diekspor konsisten dengan apa yang ditampilkan di halaman laporan.
$user_id = $_GET['user_id'] ?? '';
$tanggal_mulai = $_GET['tanggal_mulai'] ?? '';
$tanggal_selesai = $_GET['tanggal_selesai'] ?? '';

// Membangun query dinamis berdasarkan filter
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

// Query untuk mengambil data laporan.
// Memilih kolom yang relevan untuk ekspor.
$sql_laporan = "SELECT absensi.id AS id_absensi, users.username, absensi.tanggal, absensi.status 
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
    // Jika query gagal, hentikan eksekusi
    http_response_code(500); // Internal Server Error
    echo "Gagal mempersiapkan query laporan.";
    exit;
}


// --- Proses Pembuatan dan Pengiriman File CSV ---

// 1. Menentukan nama file dan mengatur Header HTTP untuk download
$nama_file = "laporan_absensi_" . date('Y-m-d') . ".csv";
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $nama_file . '"');

// 2. Membuka 'output stream' PHP untuk menulis file CSV
$output = fopen('php://output', 'w');

// 3. Menulis baris header untuk file CSV
fputcsv($output, ['ID Absensi', 'Nama Karyawan', 'Tanggal', 'Status']);

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
