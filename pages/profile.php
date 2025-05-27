<?php
// File: pages/profile.php
require_once '../config/config.php'; 

// cek apakah user login
if (!isset($_SESSION['user_id'])) {
    $_SESSION['flash_message'] = "Silakan login untuk mengakses halaman ini.";
    $_SESSION['flash_message_type'] = "warning";
    header("Location: ../auth/login.php");
    exit;
}

$page_title = "Profil Saya";
$user_id = $_SESSION['user_id'];

// inisialisasi pre-fill
$message = '';
$message_type = '';
$current_username_display = $_SESSION['username']; 
$current_role_display = $_SESSION['role'];     

if (isset($_SESSION['flash_message'])) {
    $message = $_SESSION['flash_message'];
    $message_type = $_SESSION['flash_message_type'] ?? 'info';
    unset($_SESSION['flash_message']);
    unset($_SESSION['flash_message_type']);
}

// handle update profil
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $new_username = trim($_POST['username']);
    $current_password_form = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_new_password = $_POST['confirm_new_password'];

    $update_clauses = [];
    $params_types = "";
    $params_values = [];
    $requires_re_auth = false; 

    // logika update username
    if (!empty($new_username) && $new_username !== $_SESSION['username']) {
        // cek apakah username sudah diambil
        $stmt_check_username = $conn->prepare("SELECT id FROM users WHERE username = ? AND id != ?");
        $stmt_check_username->bind_param("si", $new_username, $user_id);
        $stmt_check_username->execute();
        $stmt_check_username->store_result();

        if ($stmt_check_username->num_rows > 0) {
            $message = "Username '" . htmlspecialchars($new_username) . "' sudah digunakan. Pilih username lain.";
            $message_type = 'danger';
            $new_username = $_SESSION['username'];
        } else {
            $update_clauses[] = "username = ?";
            $params_types .= "s";
            $params_values[] = $new_username;
        }
        $stmt_check_username->close();
    } else {
        $new_username = $_SESSION['username']; // simpan username yang dulu jika tidak diganti
    }


    // logika update password
    // lanjut ketika field password tidak kosong
    if (empty($message) && !empty($new_password)) {
        if (empty($current_password_form)) {
            $message = "Masukkan password Anda saat ini untuk mengubah password.";
            $message_type = 'danger';
        } elseif ($new_password !== $confirm_new_password) {
            $message = "Password baru dan konfirmasi password tidak cocok.";
            $message_type = 'danger';
        } else {
            // verifikasi password
            $stmt_verify_pass = $conn->prepare("SELECT password FROM users WHERE id = ?");
            $stmt_verify_pass->bind_param("i", $user_id);
            $stmt_verify_pass->execute();
            $result_verify_pass = $stmt_verify_pass->get_result();
            $user_db_data = $result_verify_pass->fetch_assoc();
            $stmt_verify_pass->close();

            if ($user_db_data && password_verify($current_password_form, $user_db_data['password'])) {
                // password benar, lanjutkan
                $new_password_hash = password_hash($new_password, PASSWORD_DEFAULT);
                $update_clauses[] = "password = ?";
                $params_types .= "s";
                $params_values[] = $new_password_hash;
            } else {
                $message = "Password Anda saat ini salah.";
                $message_type = 'danger';
            }
        }
    }

    // update database
    if (!empty($update_clauses) && empty($message)) {
        $params_values[] = $user_id; 
        $params_types .= "i";

        $sql_update = "UPDATE users SET " . implode(", ", $update_clauses) . " WHERE id = ?";
        $stmt_update = $conn->prepare($sql_update);

        $stmt_update->bind_param($params_types, ...$params_values);

        if ($stmt_update->execute()) {
            $_SESSION['flash_message'] = "Profil berhasil diperbarui.";
            $_SESSION['flash_message_type'] = 'success';

            // update username pada session jika berganti
            if (in_array("username = ?", $update_clauses)) {
                $username_key = array_search("username = ?", $update_clauses);
                $_SESSION['username'] = $params_values[$username_key];
            }
            
            header("Location: profile.php"); // redirect
            exit;
        } else {
            $message = "Gagal memperbarui profil. Error: " . $stmt_update->error;
            $message_type = 'danger';
        }
        $stmt_update->close();
    } elseif (empty($update_clauses) && empty($message) && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
        $message = "Tidak ada informasi yang diubah.";
        $message_type = 'info';
    }

    $current_username_display = $new_username;
}


require_once '../includes/header.php'; // header
?>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-7">
            <div class="card">
                <div class="card-header">
                    <h4><i class="bi bi-person-lines-fill"></i> <?php echo htmlspecialchars($page_title); ?></h4>
                </div>
                <div class="card-body">
                    <?php if ($message): ?>
                        <div class="alert alert-<?php echo htmlspecialchars($message_type); ?> alert-dismissible fade show" role="alert">
                            <?php echo htmlspecialchars($message); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="profile.php">
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

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="dashboard.php" class="btn btn-secondary me-md-2">Kembali</a>
                            <button type="submit" name="update_profile" class="btn btn-primary">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
require_once '../includes/footer.php'; // footer
?>