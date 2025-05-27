<?php
// pages/edit.php
require_once '../config/config.php'; // Memuat konfigurasi dan memulai session
require_once '../auth/auth.php';     // Memastikan hanya pengguna yang terautentikasi yang dapat mengakses

// Memastikan hanya admin yang bisa mengakses halaman edit absensi ini.
if ($_SESSION['role'] !== 'admin') {
    $_SESSION['flash_message'] = "Anda tidak memiliki hak akses untuk halaman ini.";
    $_SESSION['flash_message_type'] = "danger";
    header("Location: index.php"); // Mengarahkan kembali ke halaman data absensi
    exit();
}

// Mengambil ID dari parameter GET 
if (!isset($_GET['id']) || empty($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['flash_message'] = "ID data absensi tidak valid atau tidak disediakan.";
    $_SESSION['flash_message_type'] = "warning";
    header("Location: index.php");
    exit();
}

$id = (int)$_GET['id'];
$page_title = "Edit Data Absensi"; 


$flash_message_text = '';
$flash_message_type = '';
$nama_karyawan = ''; 
$tanggal_absensi = ''; 
$status_absensi = '';  

// Proses Update Data Absensi
if (isset($_POST['update'])) {
    $nama_karyawan = trim($_POST['nama']); 
    $tanggal_absensi = $_POST['tanggal'];
    $status_absensi = $_POST['status'];

    // Validasi dasar 
    if (empty($nama_karyawan) || empty($tanggal_absensi) || empty($status_absensi)) {
        $_SESSION['flash_message'] = "Semua kolom wajib diisi.";
        $_SESSION['flash_message_type'] = "danger";
        header("Location: edit.php?id=" . $id); // Redirect kembali ke halaman edit dengan ID
        exit();
    }

    $stmt = $conn->prepare("UPDATE absensi SET nama = ?, tanggal = ?, status = ? WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param("sssi", $nama_karyawan, $tanggal_absensi, $status_absensi, $id);
        if ($stmt->execute()) {
            $_SESSION['flash_message'] = "Data absensi untuk " . htmlspecialchars($nama_karyawan) . " berhasil diperbarui!";
            $_SESSION['flash_message_type'] = "success";
            header("Location: index.php"); // Mengarahkan kembali ke halaman daftar absensi
            exit();
        } else {
            $_SESSION['flash_message'] = "Gagal memperbarui data absensi. Error: " . htmlspecialchars($stmt->error);
            $_SESSION['flash_message_type'] = "danger";
            header("Location: edit.php?id=" . $id);
            exit();
        }
        $stmt->close();
    } else {
        $_SESSION['flash_message'] = "Terjadi kesalahan dalam sistem. Gagal mempersiapkan query pembaruan.";
        $_SESSION['flash_message_type'] = "danger";
        header("Location: edit.php?id=" . $id);
        exit();
    }
}

// Ambil data yang akan diedit dari database untuk ditampilkan di form
$stmt_select = $conn->prepare("SELECT * FROM absensi WHERE id = ?");
if ($stmt_select) {
    $stmt_select->bind_param("i", $id);
    $stmt_select->execute();
    $result = $stmt_select->get_result();
    $data = $result->fetch_assoc();
    $stmt_select->close();

    if (!$data) {
        $_SESSION['flash_message'] = "Data absensi tidak ditemukan!";
        $_SESSION['flash_message_type'] = "warning";
        header("Location: index.php");
        exit();
    }
    // Mengisi variabel untuk pre-fill form
    $nama_karyawan = $data['nama'];
    $tanggal_absensi = $data['tanggal'];
    $status_absensi = $data['status'];
} else {
    $_SESSION['flash_message'] = "Gagal mengambil data untuk diedit.";
    $_SESSION['flash_message_type'] = "danger";
    header("Location: index.php");
    exit();
}

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
            <h2 class="m-0"><i class="bi bi-pencil-square"></i> <?php echo htmlspecialchars($page_title); ?></h2>
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
                <form method="POST" action="edit.php?id=<?php echo $id; ?>"> 
                    <div class="mb-3">
                        <label for="nama" class="form-label">Nama Karyawan</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-person"></i></span>
                            <input type="text" class="form-control" id="nama" name="nama" value="<?php echo htmlspecialchars($nama_karyawan); ?>" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="tanggal" class="form-label">Tanggal</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-calendar-event"></i></span>
                            <input type="date" class="form-control" id="tanggal" name="tanggal" value="<?php echo htmlspecialchars($tanggal_absensi); ?>" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="status" class="form-label">Status Kehadiran</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-check-circle"></i></span>
                            <select name="status" id="status" class="form-select" required>
                                <option value="Hadir" <?php echo ($status_absensi == 'Hadir') ? 'selected' : ''; ?>>Hadir</option>
                                <option value="Izin" <?php echo ($status_absensi == 'Izin') ? 'selected' : ''; ?>>Izin</option>
                                <option value="Sakit" <?php echo ($status_absensi == 'Sakit') ? 'selected' : ''; ?>>Sakit</option>
                                <option value="Alpha" <?php echo ($status_absensi == 'Alpha') ? 'selected' : ''; ?>>Alpha</option>
                            </select>
                        </div>
                    </div>
                    <div class="text-end">
                        <button type="submit" name="update" class="btn btn-primary"><i class="bi bi-save-fill me-2"></i>Update Data</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php 
$conn->close(); 
include '../includes/footer.php'; // Memuat footer HTML
?>
