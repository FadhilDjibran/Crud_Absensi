<?php
// pages/tambah.php
require_once '../config/config.php';
require_once '../auth/auth.php';

if (isset($_POST['tambah'])) {
    $nama = $_POST['nama'];
    $tanggal = $_POST['tanggal'];
    $status = $_POST['status'];

    $stmt = $conn->prepare("INSERT INTO absensi (nama, tanggal, status) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $nama, $tanggal, $status);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Data absensi untuk " . htmlspecialchars($nama) . " berhasil ditambahkan!";
        header("Location: index.php");
        exit();
    }
    $stmt->close();
}

include '../includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="m-0">Form Tambah Absensi</h2>
            <a href="index.php" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Kembali</a>
        </div>
        <div class="card shadow-sm">
            <div class="card-body">
                <form method="POST">
                    <div class="mb-3">
                        <label for="nama" class="form-label">Nama Karyawan</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-person"></i></span>
                            <input type="text" class="form-control" id="nama" name="nama" placeholder="Masukkan nama lengkap" required>
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
                                <option value="Hadir" selected>Hadir</option>
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