<?php
// File: pages/manage_users.php (Versi dengan Modal Hapus)
require_once '../config/config.php';
require_once '../auth/auth.php';

// Hanya admin yang bisa akses
if ($_SESSION['role'] !== 'admin') {
    header("Location: dashboard.php");
    exit;
}

$page_title = "Manajemen Pengguna";
$current_user_id = $_SESSION['user_id'];
$message = '';
$message_type = '';

if (isset($_SESSION['flash_message'])) {
    $message = $_SESSION['flash_message'];
    $message_type = $_SESSION['flash_message_type'] ?? 'info';
    unset($_SESSION['flash_message']);
    unset($_SESSION['flash_message_type']);
}

include '../includes/header.php';
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="m-0"><i class="bi bi-people-fill"></i> <?php echo htmlspecialchars($page_title); ?></h2>
        <a href="add_user.php" class="btn btn-success"><i class="bi bi-plus-circle-fill"></i> Tambah Pengguna Baru</a>
    </div>

    <?php if ($message): ?>
        <div class="alert alert-<?php echo htmlspecialchars($message_type); ?> alert-dismissible fade show" role="alert">
            <?php echo htmlspecialchars($message); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Username</th>
                            <th>Role</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $stmt_users = $conn->prepare("SELECT id, username, role FROM users ORDER BY role ASC, username ASC");
                        $stmt_users->execute();
                        $result_users = $stmt_users->get_result();

                        if ($result_users->num_rows > 0):
                            while ($user = $result_users->fetch_assoc()):
                        ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($user['id']); ?></td>
                                    <td><?php echo htmlspecialchars($user['username']); ?></td>
                                    <td><span class="badge <?php echo $user['role'] == 'admin' ? 'bg-primary' : 'bg-secondary'; ?>"><?php echo htmlspecialchars(ucfirst($user['role'])); ?></span></td>
                                    <td class="text-center">
                                        <a href="edit_user.php?id=<?php echo $user['id']; ?>" class="btn btn-sm btn-warning me-1" title="Edit"><i class="bi bi-pencil-square"></i></a>
                                        <?php if ($user['id'] !== $current_user_id): ?>
                                            <button type="button" class="btn btn-sm btn-danger delete-user-btn"
                                                data-bs-toggle="modal"
                                                data-bs-target="#userDeleteModal"
                                                data-user-id="<?php echo $user['id']; ?>"
                                                data-user-name="<?php echo htmlspecialchars($user['username']); ?>">
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
                            <tr>
                                <td colspan="4" class="text-center">Tidak ada pengguna yang terdaftar.</td>
                            </tr>
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

<div class="modal fade" id="userDeleteModal" tabindex="-1" aria-labelledby="userDeleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="userDeleteModalLabel"><i class="bi bi-exclamation-triangle-fill"></i> Konfirmasi Hapus Pengguna</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Apakah Anda yakin ingin menghapus pengguna <strong id="usernameToDelete"></strong>?
                <p class="text-danger small mt-2">Peringatan: Tindakan ini tidak dapat dibatalkan!</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <a href="#" id="confirmUserDeleteBtn" class="btn btn-danger">Ya, Hapus Pengguna</a>
            </div>
        </div>
    </div>
</div>


<?php include '../includes/footer.php'; ?>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var userDeleteModal = document.getElementById('userDeleteModal');
        if (userDeleteModal) {
            userDeleteModal.addEventListener('show.bs.modal', function(event) {
                var button = event.relatedTarget;
                var userId = button.getAttribute('data-user-id');
                var userName = button.getAttribute('data-user-name');

                var modalUsername = userDeleteModal.querySelector('#usernameToDelete');
                var confirmButton = userDeleteModal.querySelector('#confirmUserDeleteBtn');

                modalUsername.textContent = userName;
                confirmButton.href = 'delete_user.php?id=' + userId;
            });
        }
    });
</script>