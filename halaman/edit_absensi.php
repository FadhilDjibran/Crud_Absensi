<?php
// File: halaman/edit_absensi.php
// File ini hanya bertanggung jawab untuk menampilkan halaman form edit.

// Memuat file logika yang akan menyiapkan semua variabel yang dibutuhkan
require_once '../fungsi/proses_edit_absensi.php';

// Memuat header HTML
include '../includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="m-0"><i class="bi bi-pencil-square"></i> <?php echo htmlspecialchars($page_title); ?> untuk <?php echo htmlspecialchars($nama_karyawan_db); ?></h2>
            <a href="absensi.php" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Kembali</a>
        </div>

        <?php if (!empty($flash_message_text)): ?>
        <div class="alert alert-<?php echo htmlspecialchars($flash_message_type); ?> alert-dismissible fade show" role="alert">
            <?php echo htmlspecialchars($flash_message_text); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php endif; ?>

        <div class="card shadow-sm">
            <div class="card-body">
                <form method="POST" action="edit_absensi.php?id=<?php echo $id; ?>" enctype="multipart/form-data"> 
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
                    <?php if (!empty($bukti_file_db)): ?>
                        <div class="mb-3">
                            <label class="form-label">Bukti Saat Ini:</label>
                            <p>
                                <a href="../<?php echo htmlspecialchars($bukti_file_db); ?>" target="_blank"><?php echo basename(htmlspecialchars($bukti_file_db)); ?></a>
                            </p>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="hapus_bukti_saat_ini" value="1" id="hapusBuktiCheck">
                                <label class="form-check-label text-danger" for="hapusBuktiCheck">
                                    Hapus bukti saat ini (centang lalu simpan tanpa unggah baru).
                                </label>
                            </div>
                        </div>
                    <?php else: ?>
                        <p class="text-muted">Belum ada file bukti untuk data absensi ini.</p>
                    <?php endif; ?>

                    <div class="mb-3">
                        <label for="file_bukti_baru" class="form-label">Unggah Bukti Baru (Opsional)</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-file-earmark-arrow-up"></i></span>
                            <input type="file" class="form-control" id="file_bukti_baru" name="file_bukti_baru" accept=".jpg, .jpeg, .png, .pdf, image/gif">
                        </div>
                        <small class="form-text text-muted">Jika diisi, akan menggantikan bukti lama (jika ada). Maks 2MB.</small>
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
