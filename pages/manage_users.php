<?php
// File: pages/manage_users.php
require_once '../config/config.php'; 

// otentikasi user apakah login
if (!isset($_SESSION['user_id'])) {
    $_SESSION['flash_message'] = "Silakan login untuk mengakses halaman ini.";
    $_SESSION['flash_message_type'] = "warning";
    header("Location: ../auth/login.php");
    exit;
}

$page_title = "Manajemen Pengguna";
require_once '../includes/header.php'; // header

$current_user_id = $_SESSION['user_id'];
$current_user_role = $_SESSION['role'];

$message = '';
$message_type = '';

if (isset($_SESSION['flash_message'])) {
    $message = $_SESSION['flash_message'];
    $message_type = $_SESSION['flash_message_type'] ?? 'info';
    unset($_SESSION['flash_message']);
    unset($_SESSION['flash_message_type']);
}
?>

<div class="container mt-4">
    <div class="row mb-3">
        <div class="col">
            <h2><?php echo htmlspecialchars($page_title); ?></h2>
        </div>
    </div>

    <?php if ($message): ?>
        <div class="alert alert-<?php echo htmlspecialchars($message_type); ?> alert-dismissible fade show" role="alert">
            <?php echo htmlspecialchars($message); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if ($current_user_role === 'admin'): ?>
        <div class="mb-3">
            <a href="add_user.php" class="btn btn-success"><i class="bi bi-plus-circle-fill"></i> Tambah Pengguna Baru</a>
        </div>
        <div class="card">
            <div class="card-header">
                <h4><i class="bi bi-people-fill"></i> Daftar Semua Pengguna</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Username</th>
                                <th>Role</th>
                                <th style="width: 15%;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // ambil semua user untuk admin
                            $stmt_users = $conn->prepare("SELECT id, username, role FROM users ORDER BY username ASC");
                            $stmt_users->execute();
                            $result_users = $stmt_users->get_result();
                            
                            if ($result_users->num_rows > 0):
                                while ($user = $result_users->fetch_assoc()):
                            ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($user['id']); ?></td>
                                    <td><?php echo htmlspecialchars($user['username']); ?></td>
                                    <td><?php echo htmlspecialchars(ucfirst($user['role'])); ?></td>
                                    <td>
                                        <a href="edit_user.php?id=<?php echo htmlspecialchars($user['id']); ?>" class="btn btn-sm btn-primary me-1" title="Edit"><i class="bi bi-pencil-square"></i></a>
                                        <?php if ($user['id'] !== $current_user_id): // cegah admin menghapus user mereka lewat tombol hapus ?>
                                            <a href="delete_user.php?id=<?php echo htmlspecialchars($user['id']); ?>" class="btn btn-sm btn-danger" title="Hapus" onclick="return confirm('Apakah Anda yakin ingin menghapus pengguna \'<?php echo htmlspecialchars(addslashes($user['username'])); ?>\'? Tindakan ini tidak dapat diurungkan.');"><i class="bi bi-trash-fill"></i></a>
                                        <?php else: ?>
                                            <button class="btn btn-sm btn-secondary" title="Anda tidak dapat menghapus akun Anda sendiri di sini" disabled><i class="bi bi-trash-fill"></i></button>
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

    <?php elseif ($current_user_role === 'karyawan'): ?>
        <div class="card">
            <div class="card-header">
                <h4><i class="bi bi-person-badge-fill"></i> Profil Saya</h4>
            </div>
            <div class="card-body">
                <?php
                // ambil data karyawan
                $stmt_karyawan = $conn->prepare("SELECT id, username, role FROM users WHERE id = ?");
                $stmt_karyawan->bind_param("i", $current_user_id);
                $stmt_karyawan->execute();
                $result_karyawan = $stmt_karyawan->get_result();
                
                if ($user_data = $result_karyawan->fetch_assoc()):
                ?>
                    <dl class="row">
                        <dt class="col-sm-3">ID Pengguna</dt>
                        <dd class="col-sm-9"><?php echo htmlspecialchars($user_data['id']); ?></dd>

                        <dt class="col-sm-3">Username</dt>
                        <dd class="col-sm-9"><?php echo htmlspecialchars($user_data['username']); ?></dd>

                        <dt class="col-sm-3">Role</dt>
                        <dd class="col-sm-9"><?php echo htmlspecialchars(ucfirst($user_data['role'])); ?></dd>
                    </dl>
                    <hr>
                    <a href="profile.php" class="btn btn-primary"><i class="bi bi-pencil-square"></i> Edit Profil Saya</a>
                <?php else: ?>
                    <div class="alert alert-danger" role="alert">
                        Gagal memuat data profil Anda. Silakan coba lagi nanti.
                    </div>
                <?php
                endif;
                $stmt_karyawan->close();
                ?>
            </div>
        </div>

    <?php else: ?>
        <div class="alert alert-danger" role="alert">
            Role Anda tidak dikenali atau Anda tidak memiliki akses yang sesuai.
        </div>
    <?php endif; ?>

</div>

<?php
require_once '../includes/footer.php'; //footer
?>