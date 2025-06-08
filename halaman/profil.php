<?php
// File: halaman/profil.php
// Menampilkan halaman profil pengguna.

// Memuat file proses yang akan menyiapkan semua variabel
require_once '../fungsi/proses_profil.php';

// Memuat header HTML
include '../includes/header.php';
?>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-7">
            <div class="d-flex justify-content-between align-items-center mb-3">
                 <h2 class="m-0"><i class="bi bi-person-lines-fill"></i> <?php echo htmlspecialchars($page_title); ?></h2>
                <a href="dasbor.php" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Kembali ke Dasbor</a>
            </div>

            <?php if (!empty($message)): ?>
                <div class="alert alert-<?php echo htmlspecialchars($message_type); ?> alert-dismissible fade show" role="alert">
                    <?php echo htmlspecialchars($message); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <div class="card shadow-sm">
                <div class="card-body">
                    <form method="POST" action="profil.php">
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($current_username_display); ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Role</label>
                            <input type="text" class="form-control" value="<?php echo htmlspecialchars(ucfirst($current_role_display)); ?>" disabled readonly>
                            <small class="form-text text-muted">Role tidak dapat diubah melalui halaman ini.</small>
                        </div>
                        
                        <hr>
                        <h5 class="mt-4 mb-3">Ubah Password (Opsional)</h5>
                        <p class="text-muted"><small>Kosongkan bagian ini jika Anda tidak ingin mengubah password.</small></p>

                        <div class="mb-3">
                            <label for="current_password" class="form-label">Password Saat Ini</label>
                            <input type="password" class="form-control" id="current_password" name="current_password" placeholder="Masukkan password Anda saat ini">
                        </div>

                        <div class="mb-3">
                            <label for="new_password" class="form-label">Password Baru</label>
                            <input type="password" class="form-control" id="new_password" name="new_password" placeholder="Masukkan password baru">
                        </div>

                        <div class="mb-3">
                            <label for="confirm_new_password" class="form-label">Konfirmasi Password Baru</label>
                            <input type="password" class="form-control" id="confirm_new_password" name="confirm_new_password" placeholder="Ketik ulang password baru">
                        </div>

                        <div class="text-end">
                            <button type="submit" name="update_profile" class="btn btn-primary"><i class="bi bi-save-fill me-2"></i>Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Menutup koneksi database dan memuat footer
$conn->close();
include '../includes/footer.php';
?>
