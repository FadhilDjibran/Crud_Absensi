<?php
// File: halaman/manajemen_pengguna.php

// Memuat file proses
require_once '../fungsi/proses_manajemen_pengguna.php';

// Memuat header HTML
include '../includes/header.php'; 
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="m-0"><i class="bi bi-people-fill"></i> <?php echo htmlspecialchars($page_title); ?></h2>
        <a href="tambah_pengguna.php" class="btn btn-success"><i class="bi bi-plus-circle-fill"></i> Tambah Pengguna Baru</a>
    </div>

    <!-- Menampilkan pesan flash -->
    <?php if ($message): ?>
    <div class="alert alert-<?php echo htmlspecialchars($message_type); ?> alert-dismissible fade show" role="alert">
        <?php echo htmlspecialchars($message); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php endif; ?>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th><th>Username</th><th>Role</th><th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($result_users->num_rows > 0):
                            while ($user = $result_users->fetch_assoc()):
                        ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($user['id']); ?></td>
                                    <td><?php echo htmlspecialchars($user['username']); ?></td>
                                    <td><span class="badge fs-6 <?php echo $user['role'] == 'admin' ? 'bg-primary' : 'bg-secondary'; ?>"><?php echo htmlspecialchars(ucfirst($user['role'])); ?></span></td>
                                    <td class="text-center">
                                        <a href="edit_pengguna.php?id=<?php echo $user['id']; ?>" class="btn btn-sm btn-warning me-1" title="Edit"><i class="bi bi-pencil-square"></i></a>
                                        <?php if ($user['id'] !== $current_user_id): // Admin tidak bisa menghapus diri sendiri ?>
                                            <button type="button" class="btn btn-sm btn-danger delete-user-btn" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#userDeleteModal" 
                                                    data-user-id="<?php echo $user['id']; ?>" 
                                                    data-user-name="<?php echo htmlspecialchars($user['username']); ?>"
                                                    title="Hapus">
                                                <i class="bi bi-trash-fill"></i>
                                            </button>
                                        <?php else: ?>
                                            <button class="btn btn-sm btn-secondary" title="Anda tidak dapat menghapus akun Anda sendiri" disabled><i class="bi bi-trash-fill"></i></button>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                        <?php
                            endwhile;
                        else:
                        ?>
                            <tr><td colspan="4" class="text-center">Tidak ada pengguna yang terdaftar.</td></tr>
                        <?php
                        endif;
                        $stmt_users->close();
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Konfirmasi Hapus Pengguna -->
<div class="modal fade" id="userDeleteModal" tabindex="-1" aria-labelledby="userDeleteModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title" id="userDeleteModalLabel"><i class="bi bi-exclamation-triangle-fill"></i> Konfirmasi Hapus Pengguna</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        Apakah Anda yakin ingin menghapus pengguna <strong id="usernameToDelete"></strong>?
        <p class="text-danger small mt-2">Peringatan: Semua data yang terkait dengan pengguna ini mungkin akan terpengaruh. Tindakan ini tidak dapat dibatalkan!</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <a href="#" id="confirmUserDeleteBtn" class="btn btn-danger">Ya, Hapus Pengguna</a>
      </div>
    </div>
  </div>
</div>

<?php 
$conn->close(); // Menutup koneksi
include '../includes/footer.php'; 
?>

<script>
// Script Javascript untuk konfirmasi hapus
document.addEventListener('DOMContentLoaded', function () {
    var userDeleteModal = document.getElementById('userDeleteModal');
    if (userDeleteModal) {
        userDeleteModal.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget;
            var userId = button.getAttribute('data-user-id');
            var userName = button.getAttribute('data-user-name');
            
            var modalUsername = userDeleteModal.querySelector('#usernameToDelete');
            var confirmButton = userDeleteModal.querySelector('#confirmUserDeleteBtn');
            
            modalUsername.textContent = userName;
            confirmButton.href = '../fungsi/proses_hapus_pengguna.php?id=' + userId;
        });
    }
});
</script>
