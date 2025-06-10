<?php
// File: halaman/tambah_pengguna.php

// Memuat file proses
require_once '../fungsi/proses_tambah_pengguna.php';

// Memuat header HTML
include '../includes/header.php';
?>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-7">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="m-0"><i class="bi bi-person-plus-fill"></i> <?php echo htmlspecialchars($page_title); ?></h2>
                <a href="manajemen_pengguna.php" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Kembali ke Manajemen</a>
            </div>

            <!-- Menampilkan pesan flash -->
            <?php if (!empty($message)): ?>
                <div class="alert alert-<?php echo htmlspecialchars($message_type); ?> alert-dismissible fade show" role="alert">
                    <?php echo htmlspecialchars($message); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <div class="card shadow-sm">
                <div class="card-body">
                    <form method="POST" action="tambah_pengguna.php">
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($form_username); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                            <small class="form-text text-muted">Password akan di-hash demi keamanan.</small>
                        </div>
                        <div class="mb-3">
                            <label for="role" class="form-label">Role</label>
                            <select class="form-select" id="role" name="role" required>
                                <option value="" disabled <?php echo empty($form_role) ? 'selected' : ''; ?>>Pilih Role...</option>
                                <option value="karyawan" <?php echo ($form_role === 'karyawan' ? 'selected' : ''); ?>>Karyawan</option>
                                <option value="admin" <?php echo ($form_role === 'admin' ? 'selected' : ''); ?>>Admin</option>
                            </select>
                        </div>
                        <div class="text-end">
                            <button type="submit" name="add_user_submit" class="btn btn-primary"><i class="bi bi-person-plus-fill me-2"></i>Tambah Pengguna</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php 
$conn->close(); // Menutup koneksi
include '../includes/footer.php';
?>
