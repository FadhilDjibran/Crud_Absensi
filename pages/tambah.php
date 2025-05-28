<?php
// pages/tambah.php (Versi Perbaikan)
require_once '../config/config.php'; 
require_once '../auth/auth.php'; 

// Hanya admin yang bisa menambah absensi
if ($_SESSION['role'] !== 'admin') {
    header("Location: dashboard.php");
    exit();
}

// Ambil list semua user untuk dropdown
$users_list = [];
$users_result = $conn->query("SELECT id, username FROM users ORDER BY username ASC");
if ($users_result) {
    $users_list = $users_result->fetch_all(MYSQLI_ASSOC);
}

$page_message = '';
$page_message_type = '';

if (isset($_POST['tambah'])) {
    // BARU: Ambil user_id dari form
    $user_id = $_POST['user_id']; 
    $tanggal = $_POST['tanggal'];
    $status = $_POST['status'];
    
    // Ambil username berdasarkan user_id untuk disimpan di kolom 'nama' (transisi)
    $stmt_get_user = $conn->prepare("SELECT username FROM users WHERE id = ?");
    $stmt_get_user->bind_param("i", $user_id);
    $stmt_get_user->execute();
    $user_data = $stmt_get_user->get_result()->fetch_assoc();
    $nama_karyawan = $user_data['username'];

    if (empty($user_id) || empty($nama_karyawan) || empty($tanggal) || empty($status)) {
        $page_message = "Semua field wajib diisi.";
        $page_message_type = "danger";
    } else {
        // BARU: Query INSERT kini menyertakan user_id dan nama
        $stmt = $conn->prepare("INSERT INTO absensi (user_id, nama, tanggal, status) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isss", $user_id, $nama_karyawan, $tanggal, $status);

        if ($stmt->execute()) {
            $_SESSION['flash_message'] = "Data absensi untuk " . htmlspecialchars($nama_karyawan) . " berhasil ditambahkan!";
            $_SESSION['flash_message_type'] = "success";
            header("Location: index.php"); 
            exit();
        } else {
            $page_message = "Gagal menambahkan data: " . $stmt->error;
            $page_message_type = "danger";
        }
        $stmt->close();
    }
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
            <div class="alert alert-danger"><?php echo htmlspecialchars($page_message); ?></div>
        <?php endif; ?>

        <div class="card shadow-sm">
            <div class="card-body">
                <form method="POST">
                    <div class="mb-3">
                        <label for="user_id" class="form-label">Nama Karyawan</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-person-check"></i></span>
                            <select class="form-select" id="user_id" name="user_id" required>
                                <option value="" disabled selected>Pilih Karyawan...</option>
                                <?php foreach ($users_list as $user): ?>
                                    <option value="<?php echo htmlspecialchars($user['id']); ?>">
                                        <?php echo htmlspecialchars($user['username']); ?>
                                    </option>
                                <?php endforeach; ?>
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
                                <option value="Hadir">Hadir</option>
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