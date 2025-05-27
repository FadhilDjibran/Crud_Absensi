<?php
// File: pages/edit_user.php
require_once '../config/config.php'; 

// otentikasi
if (!isset($_SESSION['user_id'])) {
    $_SESSION['flash_message'] = "Silakan login untuk melanjutkan.";
    $_SESSION['flash_message_type'] = "warning";
    header("Location: ../auth/login.php");
    exit;
}
if ($_SESSION['role'] !== 'admin') {
    $_SESSION['flash_message'] = "Anda tidak memiliki hak akses untuk halaman ini.";
    $_SESSION['flash_message_type'] = "danger";
    header("Location: dashboard.php"); 
    exit;
}

$page_title = "Edit Pengguna";
$user_id_to_edit = null;
$form_username = ''; 
$form_role = '';     
$original_username = ''; 
$original_role = '';     

$message = '';
$message_type = '';

if (isset($_SESSION['flash_message'])) {
    $message = $_SESSION['flash_message'];
    $message_type = $_SESSION['flash_message_type'] ?? 'info';
    unset($_SESSION['flash_message']);
    unset($_SESSION['flash_message_type']);
}

// ambil user ID
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $user_id_to_edit = (int)$_GET['id'];
} else {
    $_SESSION['flash_message'] = "ID pengguna tidak valid atau tidak disediakan.";
    $_SESSION['flash_message_type'] = "danger";
    header("Location: manage_users.php");
    exit;
}

// lakukan pre-fill form
$stmt_fetch_user = $conn->prepare("SELECT username, role FROM users WHERE id = ?");
$stmt_fetch_user->bind_param("i", $user_id_to_edit);
$stmt_fetch_user->execute();
$result_fetch_user = $stmt_fetch_user->get_result();

if ($user_to_edit = $result_fetch_user->fetch_assoc()) {
    $form_username = $user_to_edit['username'];
    $original_username = $user_to_edit['username'];
    $form_role = $user_to_edit['role'];
    $original_role = $user_to_edit['role'];
} else {
    $_SESSION['flash_message'] = "Pengguna dengan ID " . htmlspecialchars($user_id_to_edit) . " tidak ditemukan.";
    $_SESSION['flash_message_type'] = "danger";
    header("Location: manage_users.php");
    exit;
}
$stmt_fetch_user->close();

// lakukan handle update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_user_submit'])) {

    $new_username = trim($_POST['username']);
    $new_password = $_POST['password']; 
    $new_role = $_POST['role'];

    $form_username = $new_username;
    $form_role = $new_role;

    // validasi
    if (empty($new_username) || empty($new_role)) {
        $message = "Username dan Role tidak boleh kosong.";
        $message_type = 'danger';
    } elseif (!in_array($new_role, ['admin', 'karyawan'])) {
        $message = "Role yang dipilih tidak valid.";
        $message_type = 'danger';
    } else {
            // cegah admin merubah rolenya menjadi karyawan
        if ($original_role === 'admin' && $new_role === 'karyawan') {
            $message = "Perubahan role dari 'Admin' menjadi 'Karyawan' tidak diizinkan.";
            $message_type = 'danger';
            $form_role = $original_role; 
            $new_role = $original_role;  
        }

        $update_clauses = [];
        $params_types = "";
        $params_values = [];

        // logika ganti nama
        if (empty($message) && $new_username !== $original_username) {
            $stmt_check_username = $conn->prepare("SELECT id FROM users WHERE username = ? AND id != ?");
            $stmt_check_username->bind_param("si", $new_username, $user_id_to_edit);
            $stmt_check_username->execute();
            $stmt_check_username->store_result();
            if ($stmt_check_username->num_rows > 0) {
                $message = "Username '" . htmlspecialchars($new_username) . "' sudah digunakan oleh pengguna lain.";
                $message_type = 'danger';
            } else {
                $update_clauses[] = "username = ?";
                $params_types .= "s";
                $params_values[] = $new_username;
            }
            $stmt_check_username->close();
        }

        // logika ganti password
        if (empty($message) && !empty($new_password)) {
            $new_password_hash = password_hash($new_password, PASSWORD_DEFAULT);
            $update_clauses[] = "password = ?";
            $params_types .= "s";
            $params_values[] = $new_password_hash;
        }

        if (empty($message) && $new_role !== $original_role) {
            $update_clauses[] = "role = ?";
            $params_types .= "s";
            $params_values[] = $new_role;
        }

        if (!empty($update_clauses) && empty($message)) {
            $params_values[] = $user_id_to_edit;
            $params_types .= "i";

            $sql_update = "UPDATE users SET " . implode(", ", $update_clauses) . " WHERE id = ?";
            $stmt_update = $conn->prepare($sql_update);
            $stmt_update->bind_param($params_types, ...$params_values);

            if ($stmt_update->execute()) {
                // update session admin jika mengganti data mereke sendere
                if ($user_id_to_edit === $_SESSION['user_id']) {
                    if (in_array("username = ?", $update_clauses)) {
                         $username_key = array_search("username = ?", $update_clauses);
                         $_SESSION['username'] = $params_values[$username_key];
                    }
                    // update role sendiri
                    if (in_array("role = ?", $update_clauses)) {
                        $role_key = array_search("role = ?", $update_clauses);
                        $_SESSION['role'] = $params_values[$role_key];
                    }
                }
                $_SESSION['flash_message'] = "Data pengguna '" . htmlspecialchars($new_username) . "' berhasil diperbarui.";
                $_SESSION['flash_message_type'] = 'success';
                header("Location: manage_users.php");
                exit;
            } else {
                $message = "Gagal memperbarui data pengguna. Error: " . $stmt_update->error;
                $message_type = 'danger';
            }
            $stmt_update->close();
        } elseif (empty($update_clauses) && empty($message) && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_user_submit'])) {
            $message = "Tidak ada perubahan data yang dilakukan.";
            $message_type = 'info';
        }
    }
}

require_once '../includes/header.php';
?>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-7">
             <div class="card">
                <div class="card-header">
                    <h4><i class="bi bi-pencil-fill"></i> <?php echo htmlspecialchars($page_title); ?>: <?php echo htmlspecialchars($original_username); ?></h4>
                </div>
                <div class="card-body">
                    <?php if ($message): ?>
                        <div class="alert alert-<?php echo htmlspecialchars($message_type); ?> alert-dismissible fade show" role="alert">
                            <?php echo htmlspecialchars($message); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="edit_user.php?id=<?php echo htmlspecialchars($user_id_to_edit); ?>">
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($form_username); ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Password Baru (Opsional)</label>
                            <input type="password" class="form-control" id="password" name="password" placeholder="Kosongkan jika tidak ingin mengubah">
                            <small class="form-text text-muted">Jika diisi, password pengguna akan diubah.</small>
                        </div>

                        <div class="mb-3">
                            <label for="role" class="form-label">Role</label>
                            <select class="form-select" id="role" name="role" required>
                                <option value="karyawan" <?php echo ($form_role === 'karyawan') ? 'selected' : ''; ?>>Karyawan</option>
                                <option value="admin" <?php echo ($form_role === 'admin') ? 'selected' : ''; ?>>Admin</option>
                            </select>
                            <?php if ($original_role === 'admin'): ?>
                                <small class="form-text text-info">Pengguna ini adalah Admin. Role tidak dapat diubah menjadi Karyawan.</small>
                            <?php endif; ?>
                        </div>
                        
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="manage_users.php" class="btn btn-secondary me-md-2">Batal & Kembali</a>
                            <button type="submit" name="update_user_submit" class="btn btn-primary">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
require_once '../includes/footer.php';
?>