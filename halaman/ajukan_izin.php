<?php
// File: halaman/ajukan_izin.php
// File ini hanya bertanggung jawab untuk menampilkan halaman form pengajuan.

// Memuat file logika yang akan menyiapkan semua variabel yang dibutuhkan
require_once '../fungsi/proses_pengajuan_izin.php';

// Memuat header HTML
include '../includes/header.php';
?>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-7">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="m-0"><i class="bi bi-journal-medical"></i> <?php echo htmlspecialchars($page_title); ?></h2>
                <a href="dasbor.php" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Kembali ke Dasbor</a>
            </div>

            <?php if (!empty($flash_message_text)): ?>
            <div class="alert alert-<?php echo htmlspecialchars($flash_message_type); ?> alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($flash_message_text); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php endif; ?>
            
            <?php 
            // Tampilkan form jika belum ada pengajuan/absensi ATAU jika ini adalah hasil POST dengan error.
            // Variabel $sudah_ada_absensi_atau_pengajuan disiapkan oleh file logika.
            if (!$sudah_ada_absensi_atau_pengajuan || $_SERVER['REQUEST_METHOD'] === 'POST'): 
            ?>
            <div class="card shadow-sm">
                <div class="card-body">
                    <p class="card-text">Anda akan mengajukan absensi untuk tanggal: <strong><?php echo date('d F Y', strtotime($tanggal_hari_ini)); ?></strong>.</p>
                    <hr>
                    <form method="POST" action="ajukan_izin.php" enctype="multipart/form-data">
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
// Menutup koneksi database dan memuat footer
$conn->close();
include '../includes/footer.php'; 
?>
