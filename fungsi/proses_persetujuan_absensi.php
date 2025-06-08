<?php
// File: fungsi/proses_persetujuan_absensi.php
// Skrip ini menangani logika backend untuk menyetujui atau menolak pengajuan absensi.

require_once '../config/config.php'; // Memuat konfigurasi dan memulai session
require_once '../auth/auth.php';     // Memastikan hanya pengguna yang terautentikasi

// Memastikan hanya admin yang bisa mengakses skrip ini
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    $_SESSION['flash_message'] = "Anda tidak memiliki hak akses untuk tindakan ini.";
    $_SESSION['flash_message_type'] = "danger";
    // Mengarahkan ke halaman login jika sesi tidak valid
    header("Location: ../auth/login.php"); 
    exit;
}

// Memastikan parameter ID pengajuan dan aksi ada di URL
if (!isset($_GET['id']) || !is_numeric($_GET['id']) || !isset($_GET['aksi'])) {
    $_SESSION['flash_message'] = "Parameter tidak valid untuk memproses pengajuan.";
    $_SESSION['flash_message_type'] = "danger";
    // Mengarahkan kembali ke dasbor jika parameter tidak lengkap
    header("Location: ../halaman/dasbor.php");
    exit;
}

$pengajuan_id = (int)$_GET['id'];
$aksi = $_GET['aksi']; // 'setujui' atau 'tolak'
$admin_user_id = $_SESSION['user_id']; // ID admin yang melakukan review (opsional jika ingin dicatat)

// Mengambil detail pengajuan dari database untuk diproses
// PERBAIKAN: Menambahkan kolom 'bukti_file' ke dalam SELECT
$stmt_get_pengajuan = $conn->prepare("SELECT user_id, nama, tanggal, status_diajukan, status_review, bukti_file FROM pengajuanAbsensi WHERE id = ?");
if (!$stmt_get_pengajuan) {
    $_SESSION['flash_message'] = "Gagal mempersiapkan query untuk mengambil data pengajuan: " . $conn->error;
    $_SESSION['flash_message_type'] = "danger";
    header("Location: ../halaman/dasbor.php");
    exit;
}

$stmt_get_pengajuan->bind_param("i", $pengajuan_id);
$stmt_get_pengajuan->execute();
$result_pengajuan = $stmt_get_pengajuan->get_result();
$pengajuan = $result_pengajuan->fetch_assoc();
$stmt_get_pengajuan->close();

if (!$pengajuan) {
    $_SESSION['flash_message'] = "Pengajuan absensi dengan ID tersebut tidak ditemukan.";
    $_SESSION['flash_message_type'] = "warning";
    header("Location: ../halaman/dasbor.php");
    exit;
}

// Memastikan pengajuan masih dalam status 'pending' sebelum diproses
if ($pengajuan['status_review'] !== 'pending') {
    $_SESSION['flash_message'] = "Pengajuan ini sudah pernah direview sebelumnya.";
    $_SESSION['flash_message_type'] = "info";
    header("Location: ../halaman/dasbor.php");
    exit;
}

// Memulai transaksi database untuk menjaga konsistensi data
$conn->begin_transaction();

try {
    if ($aksi === 'setujui') {
        // 1. Update status_review di tabel pengajuanAbsensi menjadi 'disetujui'
        $stmt_update_pengajuan = $conn->prepare("UPDATE pengajuanAbsensi SET status_review = 'disetujui' WHERE id = ?");
        if (!$stmt_update_pengajuan) throw new Exception("Gagal mempersiapkan query update pengajuan: " . $conn->error);
        
        $stmt_update_pengajuan->bind_param("i", $pengajuan_id);
        if (!$stmt_update_pengajuan->execute()) throw new Exception("Gagal mengupdate status pengajuan: " . $stmt_update_pengajuan->error);
        $stmt_update_pengajuan->close();

        // 2. Masukkan data ke tabel absensi (data final)
        // PERBAIKAN: Menambahkan kolom 'bukti_file' ke dalam INSERT
        $stmt_insert_absensi = $conn->prepare(
            "INSERT INTO absensi (user_id, nama, tanggal, status, bukti_file) VALUES (?, ?, ?, ?, ?)"
        );
        if (!$stmt_insert_absensi) throw new Exception("Gagal mempersiapkan query insert absensi: " . $conn->error);

        // Menggunakan data dari pengajuan untuk dimasukkan ke tabel absensi
        // PERBAIKAN: Menambahkan 'bukti_file' ke bind_param, mengubah tipe menjadi "issss"
        $stmt_insert_absensi->bind_param(
            "issss", 
            $pengajuan['user_id'], 
            $pengajuan['nama'], 
            $pengajuan['tanggal'], 
            $pengajuan['status_diajukan'],
            $pengajuan['bukti_file'] // Menambahkan path file bukti
        );
        if (!$stmt_insert_absensi->execute()) throw new Exception("Gagal memasukkan data ke tabel absensi: " . $stmt_insert_absensi->error);
        $stmt_insert_absensi->close();

        $_SESSION['flash_message'] = "Pengajuan absensi untuk " . htmlspecialchars($pengajuan['nama']) . " (" . htmlspecialchars($pengajuan['status_diajukan']) . ") berhasil disetujui.";
        $_SESSION['flash_message_type'] = "success";

    } elseif ($aksi === 'tolak') {
        // 1. Update status_review di tabel pengajuanAbsensi menjadi 'ditolak'
        $stmt_update_pengajuan = $conn->prepare("UPDATE pengajuanAbsensi SET status_review = 'ditolak' WHERE id = ?");
        if (!$stmt_update_pengajuan) throw new Exception("Gagal mempersiapkan query update pengajuan (tolak): " . $conn->error);

        $stmt_update_pengajuan->bind_param("i", $pengajuan_id);
        if (!$stmt_update_pengajuan->execute()) throw new Exception("Gagal mengupdate status pengajuan (tolak): " . $stmt_update_pengajuan->error);
        $stmt_update_pengajuan->close();
        
        $_SESSION['flash_message'] = "Pengajuan absensi untuk " . htmlspecialchars($pengajuan['nama']) . " (" . htmlspecialchars($pengajuan['status_diajukan']) . ") telah ditolak.";
        $_SESSION['flash_message_type'] = "warning";

    } else {
        // Jika parameter 'aksi' tidak dikenal
        throw new Exception("Aksi tidak dikenal.");
    }

    // Jika semua query berhasil, commit transaksi
    $conn->commit();

} catch (Exception $e) {
    // Jika terjadi error di salah satu query, batalkan semua perubahan
    $conn->rollback();
    $_SESSION['flash_message'] = "Terjadi kesalahan saat memproses pengajuan: " . $e->getMessage();
    $_SESSION['flash_message_type'] = "danger";
}

$conn->close(); // Menutup koneksi database
header("Location: ../halaman/dasbor.php"); // Mengarahkan kembali ke dasbor admin dengan nama file baru
exit;
?>
