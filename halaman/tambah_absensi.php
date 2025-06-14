<?php
// File: halaman/tambah_absensi.php

// Memuat file proses 
require_once '../fungsi/proses_tambah_absensi.php';

// Memuat header HTML
include '../includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="m-0"><i class="bi bi-calendar-plus"></i> <?php echo htmlspecialchars($page_title); ?></h2>
            <a href="absensi.php" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Kembali ke Daftar Absensi</a>
        </div>

        <?php if (!empty($flash_message_text)): ?>
            <div class="alert alert-<?php echo htmlspecialchars($flash_message_type); ?> alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($flash_message_text); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="card shadow-sm">
            <div class="card-body">
                <form method="POST" action="tambah_absensi.php" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="user_id" class="form-label">Nama Pengguna</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-person-check"></i></span>
                            <select class="form-select" id="user_id" name="user_id" required>
                                <option value="" disabled selected>Pilih Pengguna...</option>
                                
                                <!-- Dropdown dikelompokkan berdasarkan peran -->
                                <?php if (!empty($users_list['admin'])): ?>
                                <optgroup label="Admin">
                                    <?php foreach ($users_list['admin'] as $user): ?>
                                        <option value="<?php echo htmlspecialchars($user['id']); ?>">
                                            <?php echo htmlspecialchars($user['username']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </optgroup>
                                <?php endif; ?>

                                <?php if (!empty($users_list['karyawan'])): ?>
                                <optgroup label="Karyawan">
                                    <?php foreach ($users_list['karyawan'] as $user): ?>
                                        <option value="<?php echo htmlspecialchars($user['id']); ?>">
                                            <?php echo htmlspecialchars($user['username']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </optgroup>
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
                                <option value="" selected disabled>-- Pilih Status --</option>
                                <option value="Hadir">Hadir</option>
                                <option value="Izin">Izin</option>
                                <option value="Sakit">Sakit</option>
                                <option value="Alpha">Alpha</option>
                            </select>
                        </div>
                    </div>

                    <!-- Input Jam Masuk dan Bukti File -->
                    <div id="input_jam_masuk" class="mb-3" style="display: none;">
                        <label for="jam_masuk" class="form-label">Jam Masuk</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-clock"></i></span>
                            <input type="time" class="form-control" id="jam_masuk" name="jam_masuk" step="1">
                        </div>
                    </div>

                    <div id="input_bukti_file" class="mb-3" style="display: none;">
                        <label for="bukti_file" class="form-label">Unggah File Bukti (Opsional)</label>
                        <div class="input-group">
                             <span class="input-group-text"><i class="bi bi-file-earmark-arrow-up"></i></span>
                            <input class="form-control" type="file" id="bukti_file" name="bukti_file" accept=".jpg, .jpeg, .png, .pdf">
                        </div>
                        <small class="form-text text-muted">Format: JPG, PNG, PDF. Maks: 2MB.</small>
                    </div>

                    <div class="text-end">
                        <button type="submit" name="tambah" class="btn btn-primary"><i class="bi bi-save-fill me-2"></i>Simpan Data</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// JavaScript untuk menampilkan input tambahan ketika dipilih
document.getElementById('status').addEventListener('change', function() {
    var status = this.value;
    var divJamMasuk = document.getElementById('input_jam_masuk');
    var divBuktiFile = document.getElementById('input_bukti_file');

    if (status === 'Hadir') {
        divJamMasuk.style.display = 'block';
        divBuktiFile.style.display = 'none';
    } else if (status === 'Izin' || status === 'Sakit') {
        divJamMasuk.style.display = 'none';
        divBuktiFile.style.display = 'block';
    } else {
        divJamMasuk.style.display = 'none';
        divBuktiFile.style.display = 'none';
    }
});
</script>

<?php 
$conn->close(); // Menutup koneksi
include '../includes/footer.php'; 
?>
