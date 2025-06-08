<?php
// File: fungsi/logika_dasbor.php
// Berisi semua logika backend untuk halaman dasbor.

// Memuat file konfigurasi dan memulai session.
// Path ini diasumsikan dari lokasi file logika di dalam folder 'fungsi'.
require_once '../config/config.php';
// Memastikan hanya pengguna yang terautentikasi yang dapat menjalankan logika ini.
require_once '../auth/auth.php'; 

// Variabel yang akan dikirim ke file tampilan
$page_title = "Dashboard Absensi"; // Judul halaman untuk header

// --- BAGIAN FILTER BULAN/TAHUN ---
$filter_bulan = $_GET['bulan'] ?? date('m');
$filter_tahun = $_GET['tahun'] ?? date('Y');
$nama_bulan_filter = DateTime::createFromFormat('!m', $filter_bulan)->format('F');

// Variabel sesi dan peran pengguna
$is_admin = ($_SESSION['role'] == 'admin');
$current_user_id = $_SESSION['user_id'];
$current_username = $_SESSION['username'];

// --- Logika Status Absensi Hari Ini untuk Karyawan & Daftar Pengajuan untuk Admin ---
$status_absensi_hari_ini_karyawan = "Belum Absen"; // Default untuk karyawan
$pengajuan_absensi_pending_admin = []; // Default untuk admin

$hari_ini_tanggal = date('Y-m-d');

if (!$is_admin) {
    // 1. Cek tabel pengajuanAbsensi untuk status 'pending' atau 'ditolak' hari ini
    $stmt_cek_pengajuan = $conn->prepare(
        "SELECT status_diajukan, status_review FROM pengajuanAbsensi 
         WHERE user_id = ? AND tanggal = ? ORDER BY created_at DESC LIMIT 1"
    );
    $stmt_cek_pengajuan->bind_param("is", $current_user_id, $hari_ini_tanggal);
    $stmt_cek_pengajuan->execute();
    $result_pengajuan = $stmt_cek_pengajuan->get_result();
    if ($pengajuan = $result_pengajuan->fetch_assoc()) {
        if ($pengajuan['status_review'] == 'pending') {
            $status_absensi_hari_ini_karyawan = "Menunggu Konfirmasi";
        } elseif ($pengajuan['status_review'] == 'ditolak') {
            $status_absensi_hari_ini_karyawan = "Pengajuan Ditolak";
        }
    }
    $stmt_cek_pengajuan->close();

    // 2. Jika status masih "Belum Absen" atau "Pengajuan Ditolak", cek tabel absensi (untuk yang sudah disetujui)
    if ($status_absensi_hari_ini_karyawan == "Belum Absen" || $status_absensi_hari_ini_karyawan == "Pengajuan Ditolak") {
        $stmt_cek_absensi_final = $conn->prepare(
            "SELECT status FROM absensi WHERE user_id = ? AND tanggal = ? LIMIT 1"
        );
        $stmt_cek_absensi_final->bind_param("is", $current_user_id, $hari_ini_tanggal);
        $stmt_cek_absensi_final->execute();
        $result_absensi_final = $stmt_cek_absensi_final->get_result();
        if ($absensi_final = $result_absensi_final->fetch_assoc()) {
            $status_absensi_hari_ini_karyawan = ucfirst($absensi_final['status']);
        }
        $stmt_cek_absensi_final->close();
    }
} else { // Jika Admin, ambil data pengajuan yang masih pending
    $sql_pengajuan_pending = "SELECT pa.id, pa.user_id, pa.nama AS nama_pengaju, pa.tanggal, pa.status_diajukan, pa.bukti_file, u.username 
                              FROM pengajuanAbsensi pa
                              JOIN users u ON pa.user_id = u.id
                              WHERE pa.status_review = 'pending' ORDER BY pa.created_at ASC";
    $result_pengajuan_pending = $conn->query($sql_pengajuan_pending);
    if ($result_pengajuan_pending) {
        $pengajuan_absensi_pending_admin = $result_pengajuan_pending->fetch_all(MYSQLI_ASSOC);
    }
}

// --- Query Statistik (Membaca dari tabel 'absensi' yang sudah disetujui) ---

// 1. Total Pengguna Karyawan (Hanya untuk Admin)
$total_karyawan = 0;
if ($is_admin) {
    $total_karyawan_res = $conn->query("SELECT COUNT(id) as total FROM users");
    if ($total_karyawan_res) $total_karyawan = $total_karyawan_res->fetch_assoc()['total'] ?? 0;
}

// 2. Total Absensi Tercatat KESELURUHAN (dari tabel absensi, disesuaikan dengan role)
$where_clause_total_absensi_keseluruhan = $is_admin ? '' : ' WHERE user_id = ?';
$total_absensi_keseluruhan_sql = "SELECT COUNT(id) as total FROM absensi" . $where_clause_total_absensi_keseluruhan;
$stmt_total_absensi_keseluruhan = $conn->prepare($total_absensi_keseluruhan_sql);
if (!$is_admin) {
    $stmt_total_absensi_keseluruhan->bind_param('i', $current_user_id);
}
$stmt_total_absensi_keseluruhan->execute();
$total_absensi_keseluruhan_data = $stmt_total_absensi_keseluruhan->get_result()->fetch_assoc();
$total_absensi_keseluruhan = $total_absensi_keseluruhan_data['total'] ?? 0;
$stmt_total_absensi_keseluruhan->close();

// 3. Statistik Bulanan (dari tabel absensi, menggunakan filter bulan & tahun)
$stats_month_sql = "SELECT status, COUNT(id) as jumlah FROM absensi" . ($is_admin ? " WHERE MONTH(tanggal) = ? AND YEAR(tanggal) = ?" : " WHERE MONTH(tanggal) = ? AND YEAR(tanggal) = ? AND user_id = ?") . " GROUP BY status";
$stmt_month = $conn->prepare($stats_month_sql);
if ($is_admin) {
    $stmt_month->bind_param("ss", $filter_bulan, $filter_tahun);
} else {
    $stmt_month->bind_param("ssi", $filter_bulan, $filter_tahun, $current_user_id);
}
$stmt_month->execute();
$result_month = $stmt_month->get_result();
$stats_month = ['Hadir' => 0, 'Izin' => 0, 'Sakit' => 0, 'Alpha' => 0];
while ($row = $result_month->fetch_assoc()) {
    if (isset($stats_month[$row['status']])) $stats_month[$row['status']] = $row['jumlah'];
}
$stmt_month->close();

// 4. Data untuk Grafik Hadir Harian (dari tabel absensi, menggunakan filter bulan & tahun)
$labels_hadir_per_hari = [];
$data_hadir_per_hari = [];
$days_in_month = cal_days_in_month(CAL_GREGORIAN, (int)$filter_bulan, (int)$filter_tahun);
for ($i = 1; $i <= $days_in_month; $i++) {
    $labels_hadir_per_hari[] = $i;
    $data_hadir_per_hari[] = 0;
}

$hadir_per_hari_sql = "SELECT DAY(tanggal) as hari, COUNT(id) as jumlah_hadir FROM absensi" . ($is_admin ? " WHERE status = 'Hadir' AND MONTH(tanggal) = ? AND YEAR(tanggal) = ?" : " WHERE status = 'Hadir' AND MONTH(tanggal) = ? AND YEAR(tanggal) = ? AND user_id = ?") . " GROUP BY DAY(tanggal) ORDER BY hari ASC";
$stmt_hadir_harian = $conn->prepare($hadir_per_hari_sql);
if ($is_admin) {
    $stmt_hadir_harian->bind_param("ss", $filter_bulan, $filter_tahun);
} else {
    $stmt_hadir_harian->bind_param("ssi", $filter_bulan, $filter_tahun, $current_user_id);
}
$stmt_hadir_harian->execute();
$result_hadir_harian = $stmt_hadir_harian->get_result();
while ($row = $result_hadir_harian->fetch_assoc()) {
    $day_of_month = (int)$row['hari'];
    if (isset($data_hadir_per_hari[$day_of_month - 1])) $data_hadir_per_hari[$day_of_month - 1] = (int)$row['jumlah_hadir'];
}
$stmt_hadir_harian->close();

// Logika untuk Notifikasi Flash Message
$flash_message_text = '';
$flash_message_type = '';
if (isset($_SESSION['flash_message'])) {
    $flash_message_text = $_SESSION['flash_message'];
    $flash_message_type = $_SESSION['flash_message_type'] ?? 'info';
    unset($_SESSION['flash_message']);
    unset($_SESSION['flash_message_type']);
}

// Tidak ada output HTML di sini, semua variabel akan digunakan oleh file tampilan.
// Koneksi database akan ditutup di file tampilan setelah selesai digunakan.
