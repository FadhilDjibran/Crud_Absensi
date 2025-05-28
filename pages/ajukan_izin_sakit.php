<?php
// File: pages/ajukan_izin_sakit.php
require_once '../config/config.php'; // Memuat konfigurasi dan memulai session
require_once '../auth/auth.php';     // Memastikan hanya pengguna yang terautentikasi

$page_title = "Form Pengajuan Izin/Sakit";

// Hanya karyawan yang bisa mengakses halaman ini
if ($_SESSION['role'] === 'admin') {
    $_SESSION['flash_message'] = "Admin tidak dapat mengajukan izin/sakit melalui form ini.";
    $_SESSION['flash_message_type'] = "warning";
    header("Location: dashboard.php");
    exit;
}

$current_user_id = $_SESSION['user_id'];
$current_username = $_SESSION['username'];
$tanggal_hari_ini = date('Y-m-d');

// Cek apakah karyawan sudah memiliki pengajuan (pending/disetujui) atau absensi final untuk hari ini
$sudah_ada_absensi_atau_pengajuan = false;
// Cek pengajuan pending
$stmt_check_pending = $conn->prepare("SELECT id FROM pengajuanAbsensi WHERE user_id = ? AND tanggal = ? AND status_review = 'pending'");
$stmt_check_pending->bind_param("is", $current_user_id, $tanggal_hari_ini);
$stmt_check_pending->execute();
if ($stmt_check_pending->get_result()->num_rows > 0) {
    $sudah_ada_absensi_atau_pengajuan = true;
    $_SESSION['flash_message'] = "Anda sudah memiliki pengajuan absensi yang sedang direview untuk hari ini.";
    $_SESSION['flash_message_type'] = "info";
}
$stmt_check_pending->close();

// Jika tidak ada pengajuan pending, cek absensi final (disetujui)
if (!$sudah_ada_absensi_atau_pengajuan) {
    $stmt_check_approved = $conn->prepare("SELECT id FROM absensi WHERE user_id = ? AND tanggal = ?");
    $stmt_check_approved->bind_param("is", $current_user_id, $tanggal_hari_ini);
    $stmt_check_approved->execute();
    if ($stmt_check_approved->get_result()->num_rows > 0) {
        $sudah_ada_absensi_atau_pengajuan = true;
        $_SESSION['flash_message'] = "Anda sudah tercatat absensinya untuk hari ini.";
        $_SESSION['flash_message_type'] = "info";
    }
    $stmt_check_approved->close();
}

if ($sudah_ada_absensi_atau_pengajuan && $_SERVER['REQUEST_METHOD'] !== 'POST') { // Jangan redirect jika ini adalah hasil POST error
    header("Location: dashboard.php");
    exit;
}


// Inisialisasi variabel untuk pesan flash dan form
$flash_message_text = '';
$flash_message_type = '';
$form_status_diajukan = $_POST['status_diajukan'] ?? ''; // Sticky form

if (isset($_SESSION['flash_message']) && !isset($_POST['ajukan_submit'])) { // Tampilkan hanya jika bukan dari POST error di halaman ini
    $flash_message_text = $_SESSION['flash_message'];
    $flash_message_type = $_SESSION['flash_message_type'] ?? 'info';
    unset($_SESSION['flash_message']);
    unset($_SESSION['flash_message_type']);
}

// Proses form jika disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajukan_submit'])) {
    $status_diajukan = $_POST['status_diajukan'];
    $bukti_file_info = $_FILES['bukti_file'];
    $path_bukti_file = null;

    // Validasi input
    if (empty($status_diajukan) || !in_array($status_diajukan, ['Izin', 'Sakit'])) {
        $flash_message_text = "Silakan pilih status (Izin atau Sakit).";
        $flash_message_type = "danger";
    } elseif (empty($bukti_file_info['name'])) {
        $flash_message_text = "File bukti wajib diunggah untuk pengajuan Izin atau Sakit.";
        $flash_message_type = "danger";
    } else {
        // Proses upload file bukti
        $upload_dir = "../uploads/bukti_absensi/";
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0775, true); // Buat direktori jika belum ada
        }

        $file_extension = strtolower(pathinfo($bukti_file_info['name'], PATHINFO_EXTENSION));
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'pdf'];
        $max_file_size = 2 * 1024 * 1024; // 2MB

        if (!in_array($file_extension, $allowed_extensions)) {
            $flash_message_text = "Format file bukti tidak valid. Hanya JPG, JPEG, PNG, atau PDF yang diizinkan.";
            $flash_message_type = "danger";
        } elseif ($bukti_file_info['size'] > $max_file_size) {
            $flash_message_text = "Ukuran file bukti terlalu besar. Maksimal 2MB.";
            $flash_message_type = "danger";
        } elseif ($bukti_file_info['error'] !== UPLOAD_ERR_OK) {
            $flash_message_text = "Terjadi kesalahan saat mengunggah file bukti. Error code: " . $bukti_file_info['error'];
            $flash_message_type = "danger";
        } else {
            // Generate nama file unik
            $unique_filename = uniqid('bukti_', true) . '.' . $file_extension;
            $destination_path = $upload_dir . $unique_filename;

            if (move_uploaded_file($bukti_file_info['tmp_name'], $destination_path)) {
                $path_bukti_file = "uploads/bukti_absensi/" . $unique_filename; // Path relatif dari root proyek

                // Simpan ke database pengajuanAbsensi
                $stmt_insert = $conn->prepare(
                    "INSERT INTO pengajuanAbsensi (user_id, nama, tanggal, status_diajukan, bukti_file, status_review) 
                     VALUES (?, ?, ?, ?, ?, 'pending')"
                );
                if ($stmt_insert) {
                    $stmt_insert->bind_param("issss", $current_user_id, $current_username, $tanggal_hari_ini, $status_diajukan, $path_bukti_file);
                    if ($stmt_insert->execute()) {
                        $_SESSION['flash_message'] = "Pengajuan absensi (" . htmlspecialchars($status_diajukan) . ") Anda untuk hari ini telah berhasil dikirim dan menunggu review.";
                        $_SESSION['flash_message_type'] = "success";
                        header("Location: dashboard.php");
                        exit;
                    } else {
                        $flash_message_text = "Gagal menyimpan pengajuan absensi ke database: " . htmlspecialchars($stmt_insert->error);
                        $flash_message_type = "danger";
                        // Hapus file yang sudah diupload jika insert DB gagal
                        if (file_exists($destination_path)) {
                            unlink($destination_path);
                        }
                    }
                    $stmt_insert->close();
                } else {
                     $flash_message_text = "Gagal mempersiapkan statement database.";
                     $flash_message_type = "danger";
                }
            } else {
                $flash_message_text = "Gagal memindahkan file bukti yang diunggah.";
                $flash_message_type = "danger";
            }
        }
    }
}

include '../includes/header.php'; // Memuat header HTML
?>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-7">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="m-0"><i class="bi bi-journal-medical"></i> <?php echo htmlspecialchars($page_title); ?></h2>
                <a href="dashboard.php" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Kembali ke Dashboard</a>
            </div>

            <?php if (!empty($flash_message_text)): ?>
            <div class="alert alert-<?php echo htmlspecialchars($flash_message_type); ?> alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($flash_message_text); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php endif; ?>
            
            <?php if (!$sudah_ada_absensi_atau_pengajuan || $_SERVER['REQUEST_METHOD'] === 'POST'): // Tampilkan form jika belum ada pengajuan/absensi ATAU jika ini adalah hasil POST dengan error ?>
            <div class="card shadow-sm">
                <div class="card-body">
                    <p class="card-text">Anda akan mengajukan absensi untuk tanggal: <strong><?php echo date('d F Y', strtotime($tanggal_hari_ini)); ?></strong>.</p>
                    <hr>
                    <form method="POST" action="ajukan_izin_sakit.php" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="status_diajukan" class="form-label">Status Pengajuan <span class="text-danger">*</span></label>
                            <select class="form-select form-select-lg" id="status_diajukan" name="status_diajukan" required>
                                <option value="" disabled <?php echo empty($form_status_diajukan) ? 'selected' : '';?>>-- Pilih Status --</option>
                                <option value="Izin" <?php echo ($form_status_diajukan === 'Izin') ? 'selected' : ''; ?>>Izin</option>
                                <option value="Sakit" <?php echo ($form_status_diajukan === 'Sakit') ? 'selected' : ''; ?>>Sakit</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="bukti_file" class="form-label">Unggah File Bukti <span class="text-danger">*</span></label>
                            <input class="form-control form-control-lg" type="file" id="bukti_file" name="bukti_file" accept=".jpg, .jpeg, .png, .pdf" required>
                            <small class="form-text text-muted">Format yang diizinkan: JPG, JPEG, PNG, PDF. Maksimal ukuran: 2MB.</small>
                        </div>
                        
                        <div class="mt-4 d-grid">
                            <button type="submit" name="ajukan_submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-send-fill me-2"></i>Kirim Pengajuan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            <?php endif; // Akhir dari if tampilkan form ?>
        </div>
    </div>
</div>

<?php 
$conn->close();
include '../includes/footer.php'; 
?>
