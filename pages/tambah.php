<?php
// pages/tambah.php
require_once '../config/config.php'; 
require_once '../auth/auth.php'; 


// Ambil list semua user
$users_list = [];
$users_query = "SELECT id, username FROM users ORDER BY username ASC";
$users_result = $conn->query($users_query);
if ($users_result && $users_result->num_rows > 0) {
    while ($row = $users_result->fetch_assoc()) {
        $users_list[] = $row;
    }
}

$page_message = '';
$page_message_type = '';

if (isset($_POST['tambah'])) {
    $nama = $_POST['nama_karyawan_selected']; 
    $tanggal = $_POST['tanggal'];
    $status = $_POST['status'];

    // Validasi
    if (empty($nama) || empty($tanggal) || empty($status)) {
        $page_message = "Semua field wajib diisi.";
        $page_message_type = "danger";
    } else {
        $stmt = $conn->prepare("INSERT INTO absensi (nama, tanggal, status) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $nama, $tanggal, $status);

        if ($stmt->execute()) {
            $_SESSION['flash_message'] = "Data absensi untuk " . htmlspecialchars($nama) . " berhasil ditambahkan!";
            $_SESSION['flash_message_type'] = "success";
            header("Location: index.php"); 
            exit();
        } else {
            $page_message = "Gagal menambahkan data absensi: " . $stmt->error;
            $page_message_type = "danger";
        }
        $stmt->close();
    }
}

if (isset($_SESSION['flash_message'])) {
    $page_message = $_SESSION['flash_message'];
    $page_message_type = $_SESSION['flash_message_type'];
    unset($_SESSION['flash_message']);
    unset($_SESSION['flash_message_type']);
}


$page_title = "Tambah Absensi"; 
include '../includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="m-0"><?php echo htmlspecialchars($page_title); ?></h2>
            <a href="index.php" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Kembali</a>
        </div>

        <?php if ($page_message): ?>
            <div class="alert alert-<?php echo htmlspecialchars($page_message_type); ?> alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($page_message); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="card shadow-sm">
            <div class="card-body">
                <form method="POST" action="tambah.php"> 
                    <div class="mb-3">
                        <label for="nama_karyawan_selected" class="form-label">Nama Karyawan</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-person-check"></i></span>
                            <select class="form-select" id="nama_karyawan_selected" name="nama_karyawan_selected" required>
                                <option value="" disabled selected>Pilih Karyawan...</option>
                                <?php if (!empty($users_list)): ?>
                                    <?php foreach ($users_list as $user): ?>
                                        <option value="<?php echo htmlspecialchars($user['username']); ?>">
                                            <?php echo htmlspecialchars($user['username']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <option value="" disabled>Tidak ada pengguna ditemukan</option>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="tanggal" class="form-label">Tanggal</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-calendar-event"></i></span>
                            <input type="date" class="form-control" id="tanggal" name="tanggal" value="<?= date('Y-m-d') ?>" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="status" class="form-label">Status Kehadiran</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-check-circle"></i></span>
                            <select name="status" id="status" class="form-select" required>
                                <option value="Hadir">Hadir</option> {/* Removed 'selected' to let user explicitly choose or make "Pilih Status" default */}
                                <option value="Izin">Izin</option>
                                <option value="Sakit">Sakit</option>
                                <option value="Alpha">Alpha</option>
                            </select>
                        </div>
                    </div>
                    <div class="text-end">
                        <button type="submit" name="tambah" class="btn btn-primary"><i class="bi bi-save-fill me-2"></i>Simpan Data</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>