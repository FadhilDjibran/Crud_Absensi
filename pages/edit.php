<?php
// File: pages/edit.php (Versi dengan Manajemen File Bukti)
require_once '../config/config.php';
require_once '../auth/auth.php';

if ($_SESSION['role'] !== 'admin') {
    $_SESSION['flash_message'] = "Anda tidak memiliki hak akses untuk halaman ini.";
    $_SESSION['flash_message_type'] = "danger";
    header("Location: index.php");
    exit();
}

if (!isset($_GET['id']) || empty($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['flash_message'] = "ID data absensi tidak valid.";
    $_SESSION['flash_message_type'] = "warning";
    header("Location: index.php");
    exit();
}

$id = (int)$_GET['id'];
$page_title = "Edit Data Absensi";

$nama_karyawan_db = '';
$tanggal_absensi_db = '';
$status_absensi_db = '';
$file_bukti_db = null; // Nama file bukti yang ada di database

// Ambil data absensi saat ini dari database
$stmt_select = $conn->prepare("SELECT absensi.*, users.username FROM absensi LEFT JOIN users ON absensi.user_id = users.id WHERE absensi.id = ?");
$stmt_select->bind_param("i", $id);
$stmt_select->execute();
$result_select = $stmt_select->get_result();
$data_absensi = $result_select->fetch_assoc();
$stmt_select->close();

if (!$data_absensi) {
    $_SESSION['flash_message'] = "Data absensi tidak ditemukan!";
    $_SESSION['flash_message_type'] = "warning";
    header("Location: index.php");
    exit();
}

$user_id_db = $data_absensi['user_id'];
$nama_karyawan_db = $data_absensi['username'] ?? $data_absensi['nama']; // Fallback ke nama jika username null
$tanggal_absensi_db = $data_absensi['tanggal'];
$status_absensi_db = $data_absensi['status'];
$file_bukti_db = $data_absensi['file_bukti'];


// Proses Update Data
if (isset($_POST['update'])) {
    $nama_karyawan_form = $nama_karyawan_db; // Nama tidak diubah dari form ini, tapi diambil dari user_id
    $tanggal_form = $_POST['tanggal'];
    $status_form = $_POST['status'];
    $hapus_bukti_saat_ini = isset($_POST['hapus_bukti_saat_ini']) ? true : false;
    $file_bukti_baru_path_db = $file_bukti_db; // Defaultnya pakai yang lama

    // Validasi dasar
    if (empty($tanggal_form) || empty($status_form)) {
        $_SESSION['flash_message'] = "Kolom Tanggal dan Status wajib diisi.";
        $_SESSION['flash_message_type'] = "danger";
        header("Location: edit.php?id=" . $id);
        exit();
    }

    // 1. Logika Hapus File Bukti yang Ada Jika Diminta
    if ($hapus_bukti_saat_ini && !empty($file_bukti_db)) {
        $folder_bukti_lama = ($status_absensi_db == 'Hadir') ? "../uploads/kehadiran/" : "../uploads/dokumen_izin_sakit/";
        $path_file_lama = $folder_bukti_lama . $file_bukti_db;
        if (file_exists($path_file_lama)) {
            unlink($path_file_lama);
        }
        $file_bukti_baru_path_db = null; // Set jadi null karena dihapus
    }

    // 2. Logika Upload File Bukti Baru
    if (isset($_FILES['file_bukti_baru']) && $_FILES['file_bukti_baru']['error'] == UPLOAD_ERR_OK) {
        // Tentukan direktori berdasarkan status BARU yang dipilih di form
        $target_dir = '';
        if ($status_form == 'Hadir') {
            $target_dir = "../uploads/kehadiran/";
        } elseif (in_array($status_form, ['Izin', 'Sakit'])) {
            $target_dir = "../uploads/dokumen_izin_sakit/";
        }

        if (!empty($target_dir)) {
            $file_extension = strtolower(pathinfo($_FILES["file_bukti_baru"]["name"], PATHINFO_EXTENSION));
            $safe_username = preg_replace("/[^a-zA-Z0-9_]/", "_", $nama_karyawan_db);
            $new_file_name = $user_id_db . "_" . $safe_username . "_" . $tanggal_form . "_updated." . $file_extension;
            $target_file = $target_dir . $new_file_name;
            
            $allowed_types = ($status_form == 'Hadir') ? ['jpg', 'jpeg', 'png', 'gif'] : ['jpg', 'jpeg', 'png', 'pdf'];
            $max_file_size = 2 * 1024 * 1024; // 2MB

            if (!in_array($file_extension, $allowed_types)) {
                $_SESSION['flash_message'] = "Format file bukti baru tidak diizinkan.";
                $_SESSION['flash_message_type'] = "danger";
            } elseif ($_FILES["file_bukti_baru"]["size"] > $max_file_size) {
                $_SESSION['flash_message'] = "Ukuran file bukti baru terlalu besar (Maks 2MB).";
                $_SESSION['flash_message_type'] = "danger";
            } else {
                // Hapus file lama jika ada dan file baru berhasil diupload
                if (!empty($file_bukti_db) && !$hapus_bukti_saat_ini) { // Jangan hapus jika sudah dihapus oleh checkbox
                    $folder_bukti_lama = ($status_absensi_db == 'Hadir') ? "../uploads/kehadiran/" : "../uploads/dokumen_izin_sakit/";
                    $path_file_lama = $folder_bukti_lama . $file_bukti_db;
                    if (file_exists($path_file_lama)) {
                        unlink($path_file_lama);
                    }
                }
                if (move_uploaded_file($_FILES["file_bukti_baru"]["tmp_name"], $target_file)) {
                    $file_bukti_baru_path_db = $new_file_name;
                } else {
                    $_SESSION['flash_message'] = "Gagal mengupload file bukti baru.";
                    $_SESSION['flash_message_type'] = "danger";
                }
            }
        } else {
             // Jika status baru tidak memerlukan bukti (misal Alpha), dan ada file lama, hapus file lama.
            if (!empty($file_bukti_db) && !$hapus_bukti_saat_ini) {
                $folder_bukti_lama = ($status_absensi_db == 'Hadir') ? "../uploads/kehadiran/" : "../uploads/dokumen_izin_sakit/";
                $path_file_lama = $folder_bukti_lama . $file_bukti_db;
                if (file_exists($path_file_lama)) {
                    unlink($path_file_lama);
                }
            }
            $file_bukti_baru_path_db = null; // Set jadi NULL jika status baru tidak butuh bukti
        }
    } elseif ($status_form == 'Alpha' || $status_form == '' && !empty($file_bukti_db) && !$hapus_bukti_saat_ini) {
        // Jika status diubah jadi Alpha atau dikosongkan (tidak valid tapi antisipasi)
        // dan ada file lama, hapus file lama.
        $folder_bukti_lama = ($status_absensi_db == 'Hadir') ? "../uploads/kehadiran/" : "../uploads/dokumen_izin_sakit/";
        $path_file_lama = $folder_bukti_lama . $file_bukti_db;
        if (file_exists($path_file_lama)) {
            unlink($path_file_lama);
        }
        $file_bukti_baru_path_db = null;
    }


    // 3. Update Database jika tidak ada error dari proses file
    if (empty($_SESSION['flash_message']) || $_SESSION['flash_message_type'] !== 'danger') {
        $stmt_update = $conn->prepare("UPDATE absensi SET tanggal = ?, status = ?, file_bukti = ? WHERE id = ?");
        $stmt_update->bind_param("sssi", $tanggal_form, $status_form, $file_bukti_baru_path_db, $id);

        if ($stmt_update->execute()) {
            $_SESSION['flash_message'] = "Data absensi untuk " . htmlspecialchars($nama_karyawan_db) . " berhasil diperbarui!";
            $_SESSION['flash_message_type'] = "success";
            header("Location: index.php");
            exit();
        } else {
            $_SESSION['flash_message'] = "Gagal memperbarui data absensi. Error: " . htmlspecialchars($stmt_update->error);
            $_SESSION['flash_message_type'] = "danger";
        }
        $stmt_update->close();
    }
    // Redirect kembali ke halaman edit jika ada error file
    header("Location: edit.php?id=" . $id);
    exit();
}


// Ambil pesan flash jika ada (misal dari redirect internal setelah error)
$flash_message_text = '';
$flash_message_type = '';
if (isset($_SESSION['flash_message'])) {
    $flash_message_text = $_SESSION['flash_message'];
    $flash_message_type = $_SESSION['flash_message_type'] ?? 'info';
    unset($_SESSION['flash_message']);
    unset($_SESSION['flash_message_type']);
}


include '../includes/header.php'; 
?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="m-0"><i class="bi bi-pencil-square"></i> <?php echo htmlspecialchars($page_title); ?> untuk <?php echo htmlspecialchars($nama_karyawan_db); ?></h2>
            <a href="index.php" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Kembali</a>
        </div>

        <?php if (!empty($flash_message_text)): ?>
        <div class="alert alert-<?php echo htmlspecialchars($flash_message_type); ?> alert-dismissible fade show" role="alert">
            <?php echo htmlspecialchars($flash_message_text); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php endif; ?>

        <div class="card shadow-sm">
            <div class="card-body">
                <form method="POST" action="edit.php?id=<?php echo $id; ?>" enctype="multipart/form-data"> 
                    <div class="mb-3">
                        <label class="form-label">Nama Karyawan</label>
                        <input type="text" class="form-control" value="<?php echo htmlspecialchars($nama_karyawan_db); ?>" disabled readonly>
                    </div>
                    <div class="mb-3">
                        <label for="tanggal" class="form-label">Tanggal</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-calendar-event"></i></span>
                            <input type="date" class="form-control" id="tanggal" name="tanggal" value="<?php echo htmlspecialchars($tanggal_absensi_db); ?>" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="status" class="form-label">Status Kehadiran</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-check-circle"></i></span>
                            <select name="status" id="status" class="form-select" required>
                                <option value="Hadir" <?php echo ($status_absensi_db == 'Hadir') ? 'selected' : ''; ?>>Hadir</option>
                                <option value="Izin" <?php echo ($status_absensi_db == 'Izin') ? 'selected' : ''; ?>>Izin</option>
                                <option value="Sakit" <?php echo ($status_absensi_db == 'Sakit') ? 'selected' : ''; ?>>Sakit</option>
                                <option value="Alpha" <?php echo ($status_absensi_db == 'Alpha') ? 'selected' : ''; ?>>Alpha</option>
                            </select>
                        </div>
                    </div>

                    <hr>
                    <h5 class="mt-3">Manajemen File Bukti</h5>
                    <?php if (!empty($file_bukti_db)): ?>
                        <div class="mb-3">
                            <label class="form-label">Bukti Saat Ini:</label>
                            <?php
                                $path_bukti_saat_ini = '';
                                if ($status_absensi_db == 'Hadir') {
                                    $path_bukti_saat_ini = "../uploads/kehadiran/" . htmlspecialchars($file_bukti_db);
                                } elseif (in_array($status_absensi_db, ['Izin', 'Sakit'])) {
                                    $path_bukti_saat_ini = "../uploads/dokumen_izin_sakit/" . htmlspecialchars($file_bukti_db);
                                }
                            ?>
                            <p>
                                <a href="<?php echo $path_bukti_saat_ini; ?>" target="_blank"><?php echo htmlspecialchars($file_bukti_db); ?></a>
                            </p>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="hapus_bukti_saat_ini" value="1" id="hapusBuktiCheck">
                                <label class="form-check-label text-danger" for="hapusBuktiCheck">
                                    Hapus bukti saat ini (centang lalu simpan tanpa upload baru).
                                </label>
                            </div>
                        </div>
                    <?php else: ?>
                        <p class="text-muted">Belum ada file bukti untuk data absensi ini.</p>
                    <?php endif; ?>

                    <div class="mb-3">
                        <label for="file_bukti_baru" class="form-label">Upload Bukti Baru (Opsional)</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-file-earmark-arrow-up"></i></span>
                            <input type="file" class="form-control" id="file_bukti_baru" name="file_bukti_baru" accept=".jpg, .jpeg, .png, .pdf, image/gif">
                        </div>
                        <small class="form-text text-muted">Jika diisi, akan menggantikan bukti lama (jika ada). Format: JPG, PNG, GIF (Hadir), PDF (Izin/Sakit). Maks 2MB.</small>
                    </div>
                    <hr>
                    <div class="text-end mt-4">
                        <button type="submit" name="update" class="btn btn-primary"><i class="bi bi-save-fill me-2"></i>Update Data</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php 
$conn->close(); 
include '../includes/footer.php';
?>