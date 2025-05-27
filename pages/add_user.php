<?php
// File: pages/add_user.php
require_once '../config/config.php'; 

// otentikasi
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}
if ($_SESSION['role'] !== 'admin') {
    // cek admin
    $_SESSION['flash_message'] = "Anda tidak memiliki hak akses untuk halaman ini.";
    $_SESSION['flash_message_type'] = "danger";
    header("Location: dashboard.php");
    exit;
}

$page_title = "Tambah Pengguna Baru"; 
$form_username = '';
$form_role = '';
$message = '';
$message_type = '';

if (isset($_SESSION['flash_message'])) {
    $message = $_SESSION['flash_message'];
    $message_type = $_SESSION['flash_message_type'] ?? 'info';
    unset($_SESSION['flash_message']);
    unset($_SESSION['flash_message_type']);
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_user_submit'])) {
    $form_username = trim($_POST['username']);
    $password = $_POST['password'];
    $form_role = $_POST['role'];

    // validasi
    if (empty($form_username) || empty($password) || empty($form_role)) {
        $message = "Semua kolom wajib diisi (Username, Password, Role).";
        $message_type = 'danger';
    } elseif (!in_array($form_role, ['admin', 'karyawan'])) {
        $message = "Role tidak valid.";
        $message_type = 'danger';
    } else {
        // cek apakah username sudah ditambah
        $stmt_check = $conn->prepare("SELECT id FROM users WHERE username = ?");
        $stmt_check->bind_param("s", $form_username);
        $stmt_check->execute();
        $stmt_check->store_result();

        if ($stmt_check->num_rows > 0) {
            $message = "Username sudah ada. Silakan pilih yang lain.";
            $message_type = 'danger';
        } else {
            // hash passwordnya
            $password_hash = password_hash($password, PASSWORD_DEFAULT);

            // masukkan user baru
            $stmt_insert = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
            $stmt_insert->bind_param("sss", $form_username, $password_hash, $form_role);

            if ($stmt_insert->execute()) {
                $_SESSION['flash_message'] = "Pengguna '" . htmlspecialchars($form_username) . "' berhasil ditambahkan dengan role '" . htmlspecialchars($form_role) . "'.";
                $_SESSION['flash_message_type'] = 'success';
                header("Location: add_user.php"); 
                exit;
            } else {
                $message = "Gagal menambahkan pengguna. Error: " . $stmt_insert->error;
                $message_type = 'danger';
            }
            $stmt_insert->close();
        }
        $stmt_check->close();
    }
}

//header
require_once '../includes/header.php';
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header">
                    <h3><?php echo htmlspecialchars($page_title); ?></h3>
                </div>
                <div class="card-body">
                    <?php if ($message): ?>
                        <div class="alert alert-<?php echo htmlspecialchars($message_type); ?> alert-dismissible fade show" role="alert">
                            <?php echo htmlspecialchars($message); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="add_user.php">
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
                                <option value="">Pilih Role...</option>
                                <option value="karyawan" <?php echo ($form_role === 'karyawan' ? 'selected' : ''); ?>>Karyawan</option>
                                <option value="admin" <?php echo ($form_role === 'admin' ? 'selected' : ''); ?>>Admin</option>
                            </select>
                        </div>
                        <button type="submit" name="add_user_submit" class="btn btn-primary">Tambah Pengguna</button>
                        <a href="dashboard.php" class="btn btn-secondary">Kembali ke Dashboard</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
require_once '../includes/footer.php'; //footer
?>

</main> <script src="../assets/js/bootstrap.bundle.min.js"></script> </body>
</html>