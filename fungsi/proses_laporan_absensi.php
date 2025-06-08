<?php
// File: fungsi/proses_laporan_absensi.php
// Berisi logika untuk memfilter dan menyiapkan data laporan.

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

// PERBAIKAN: Ambil semua pengguna (admin dan karyawan) dan kelompokkan berdasarkan peran
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

// Variabel untuk menyimpan nilai filter (untuk sticky form)
$filter_user_id = $_GET['user_id'] ?? '';
$filter_tanggal_mulai = $_GET['tanggal_mulai'] ?? '';
$filter_tanggal_selesai = $_GET['tanggal_selesai'] ?? '';

// Cek jika form filter disubmit
if (isset($_GET['tampilkan'])) {
    $filter_aktif = true;
    
    // Bangun query dinamis berdasarkan filter yang diterapkan
    $where_parts = []; // Array untuk menyimpan bagian dari klausa WHERE
    $params = [];      // Array untuk menyimpan parameter yang akan di-bind
    $types = '';       // String untuk menyimpan tipe data parameter

    // Menambahkan filter berdasarkan user_id jika dipilih
    if (!empty($filter_user_id)) {
        $where_parts[] = "absensi.user_id = ?";
        $params[] = $filter_user_id;
        $types .= 'i';
    }
    // Menambahkan filter berdasarkan tanggal_mulai jika diisi
    if (!empty($filter_tanggal_mulai)) {
        $where_parts[] = "absensi.tanggal >= ?";
        $params[] = $filter_tanggal_mulai;
        $types .= 's';
    }
    // Menambahkan filter berdasarkan tanggal_selesai jika diisi
    if (!empty($filter_tanggal_selesai)) {
        $where_parts[] = "absensi.tanggal <= ?";
        $params[] = $filter_tanggal_selesai;
        $types .= 's';
    }

    $where_clause = ''; // Inisialisasi klausa WHERE
    if (!empty($where_parts)) {
        // Menggabungkan semua bagian WHERE dengan 'AND'
        $where_clause = ' WHERE ' . implode(' AND ', $where_parts);
    }

    // Query untuk mengambil data detail laporan
    $sql_laporan = "SELECT absensi.*, users.username 
                    FROM absensi 
                    LEFT JOIN users ON absensi.user_id = users.id" 
                   . $where_clause . " ORDER BY absensi.tanggal DESC, absensi.id DESC";
    
    $stmt_laporan = $conn->prepare($sql_laporan);
    if ($stmt_laporan) {
        if (!empty($params)) {
            $stmt_laporan->bind_param($types, ...$params);
        }
        $stmt_laporan->execute();
        $laporan_data = $stmt_laporan->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt_laporan->close();
    }

    // Query untuk mengambil statistik ringkasan
    $sql_stats = "SELECT status, COUNT(absensi.id) as jumlah 
                  FROM absensi 
                  LEFT JOIN users ON absensi.user_id = users.id" 
                 . $where_clause . " GROUP BY absensi.status";
    
    $stmt_stats = $conn->prepare($sql_stats);
    if ($stmt_stats) {
        if (!empty($params)) {
            $stmt_stats->bind_param($types, ...$params);
        }
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

// Semua variabel yang dibutuhkan oleh view sudah siap.
