<?php
// File: fungsi/proses_laporan_absensi.php
require_once '../config/config.php';
require_once '../auth/auth.php';

// Fitur ini hanya untuk admin
if ($_SESSION['role'] !== 'admin') {
    $_SESSION['flash_message'] = "Anda tidak memiliki hak akses untuk halaman ini.";
    $_SESSION['flash_message_type'] = "danger";
    header("Location: ../halaman/dasbor.php");
    exit;
}

$page_title = "Laporan Absensi";

// Ambil daftar karyawan untuk dropdown
$users_list = [];
$users_result = $conn->query("SELECT id, username, role FROM users ORDER BY role ASC, username ASC");
if ($users_result) {
    while ($user = $users_result->fetch_assoc()) {
        $users_list[$user['role']][] = $user;
    }
}

// Inisialisasi variabel untuk view
$laporan_data = [];
$stats_laporan = ['Hadir' => 0, 'Izin' => 0, 'Sakit' => 0, 'Alpha' => 0];
$filter_aktif = false;

// Inisialisasi variabel paginasi
$halaman = isset($_GET['halaman']) && is_numeric($_GET['halaman']) ? (int)$_GET['halaman'] : 1;
$jumlahHalaman = 0;
$total = 0;
$batas = 15; // Batas data per halaman

// Menyimpan nilai filter untuk sticky form
$filter_user_id = $_GET['user_id'] ?? '';
$filter_tanggal_mulai = $_GET['tanggal_mulai'] ?? '';
$filter_tanggal_selesai = $_GET['tanggal_selesai'] ?? '';

// Cek jika form filter disubmit
if (isset($_GET['tampilkan'])) {
    $filter_aktif = true;
    
    // Membangun query berdasarkan filter
    $where_parts = []; 
    $params = [];      
    $types = '';       

    if (!empty($filter_user_id)) {
        $where_parts[] = "absensi.user_id = ?";
        $params[] = $filter_user_id;
        $types .= 'i';
    }
    if (!empty($filter_tanggal_mulai)) {
        $where_parts[] = "absensi.tanggal >= ?";
        $params[] = $filter_tanggal_mulai;
        $types .= 's';
    }
    if (!empty($filter_tanggal_selesai)) {
        $where_parts[] = "absensi.tanggal <= ?";
        $params[] = $filter_tanggal_selesai;
        $types .= 's';
    }

    $where_clause = ''; 
    if (!empty($where_parts)) {
        $where_clause = ' WHERE ' . implode(' AND ', $where_parts);
    }

    // Langkah 1 - Hitung total data yang cocok dengan filter untuk paginasi
    $sql_hitung = "SELECT COUNT(absensi.id) AS total FROM absensi LEFT JOIN users ON absensi.user_id = users.id" . $where_clause;
    $stmt_hitung = $conn->prepare($sql_hitung);
    if ($stmt_hitung) {
        if(!empty($types)) { $stmt_hitung->bind_param($types, ...$params); }
        $stmt_hitung->execute();
        $total_result = $stmt_hitung->get_result()->fetch_assoc();
        $total = $total_result['total'] ?? 0;
        $jumlahHalaman = ceil($total / $batas);
        $stmt_hitung->close();
    }

    // Langkah 2 - Ambil data untuk halaman saat ini dengan LIMIT
    $mulai = ($halaman > 0) ? (($halaman - 1) * $batas) : 0;
    
    $sql_laporan = "SELECT absensi.id, absensi.nama, absensi.tanggal, absensi.jam_masuk, absensi.status, absensi.kondisi_masuk, users.username 
                    FROM absensi 
                    LEFT JOIN users ON absensi.user_id = users.id" 
                   . $where_clause . " ORDER BY absensi.tanggal DESC, absensi.id DESC LIMIT ?, ?";
    
    // Tambahkan parameter untuk LIMIT ke query
    $params_for_data_query = $params;
    $params_for_data_query[] = $mulai;
    $params_for_data_query[] = $batas;
    $types_for_data_query = $types . 'ii';

    $stmt_laporan = $conn->prepare($sql_laporan);
    if ($stmt_laporan) {
        if (!empty($types_for_data_query)) {
            $stmt_laporan->bind_param($types_for_data_query, ...$params_for_data_query);
        }
        $stmt_laporan->execute();
        $laporan_data = $stmt_laporan->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt_laporan->close();
    }

    // Query untuk mengambil statistik ringkasan
    $sql_stats = "SELECT status, COUNT(absensi.id) as jumlah FROM absensi LEFT JOIN users ON absensi.user_id = users.id" . $where_clause . " GROUP BY absensi.status";
    $stmt_stats = $conn->prepare($sql_stats);
    if ($stmt_stats) {
        if (!empty($params)) { $stmt_stats->bind_param($types, ...$params); }
        $stmt_stats->execute();
        $result_stats = $stmt_stats->get_result();
        while($row = $result_stats->fetch_assoc()) {
            if (isset($stats_laporan[$row['status']])) {
                $stats_laporan[$row['status']] = $row['jumlah'];
            }
        }
        $stmt_stats->close();
    }
}
?>
