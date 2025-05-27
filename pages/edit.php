<?php
// pages/edit.php
require_once '../config/config.php';
require_once '../auth/auth.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$id = (int)$_GET['id'];

// Proses Update
if (isset($_POST['update'])) {
    $nama = $_POST['nama'];
    $tanggal = $_POST['tanggal'];
    $status = $_POST['status'];

    $stmt = $conn->prepare("UPDATE absensi SET nama = ?, tanggal = ?, status = ? WHERE id = ?");
    $stmt->bind_param("sssi", $nama, $tanggal, $status, $id);
    if ($stmt->execute()) {
        $_SESSION['message'] = "Data absensi untuk " . htmlspecialchars($nama) . " berhasil diperbarui!";
        header("Location: index.php");
        exit();
    }
    $stmt->close();
}

// Ambil data yang akan diedit
$stmt = $conn->prepare("SELECT * FROM absensi WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

if (!$data) {
    $_SESSION['message'] = "Data tidak ditemukan!";
    header("Location: index.php");
    exit();
}
$stmt->close();

include '../includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="m-0">Form Edit Absensi</h2>
            <a href="index.php" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Kembali</a>
        </div>
        <div class="card shadow-sm">
            <div class="card-body">
                <form method="POST">
                    <div class="mb-3">
                        <label for="nama" class="form-label">Nama Karyawan</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-person"></i></span>
                            <input type="text" class="form-control" id="nama" name="nama" value="<?= htmlspecialchars($data['nama']) ?>" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="tanggal" class="form-label">Tanggal</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-calendar-event"></i></span>
                            <input type="date" class="form-control" id="tanggal" name="tanggal" value="<?= htmlspecialchars($data['tanggal']) ?>" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="status" class="form-label">Status Kehadiran</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-check-circle"></i></span>
                            <select name="status" id="status" class="form-select" required>
                                <option value="Hadir" <?= $data['status'] == 'Hadir' ? 'selected' : '' ?>>Hadir</option>
                                <option value="Izin" <?= $data['status'] == 'Izin' ? 'selected' : '' ?>>Izin</option>
                                <option value="Sakit" <?= $data['status'] == 'Sakit' ? 'selected' : '' ?>>Sakit</option>
                                <option value="Alpha" <?= $data['status'] == 'Alpha' ? 'selected' : '' ?>>Alpha</option>
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

<?php include '../includes/footer.php'; ?>