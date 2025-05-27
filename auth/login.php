<?php
// File: auth/login.php
require_once '../config/config.php';

// Jika sudah login, langsung arahkan ke dashboard
if (isset($_SESSION['user_id'])) {
    header("Location: ../pages/dashboard.php");
    exit;
}

$message = '';
$message_type = ''; // 'success' atau 'danger'

// Proses Registrasi
if (isset($_POST['register'])) {
    $username = $_POST['username_reg'];
    $password = $_POST['password_reg'];

    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $message = "Username sudah ada. Silakan pilih yang lain.";
        $message_type = 'danger';
    } else {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        $insert_stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
        $insert_stmt->bind_param("ss", $username, $password_hash);
        if ($insert_stmt->execute()) {
            $message = "Registrasi berhasil! Silakan login.";
            $message_type = 'success';
        }
        $insert_stmt->close();
    }
    $stmt->close();
}

// Proses Login
if (isset($_POST['login'])) {
    $username = $_POST['username_log'];
    $password = $_POST['password_log'];

    // PENTING: Pastikan query mengambil kolom 'role'
    $stmt = $conn->prepare("SELECT id, username, password, role FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            // PENTING: Simpan semua data yang dibutuhkan ke session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            
            // Arahkan ke dashboard setelah berhasil login
            header("Location: ../pages/dashboard.php");
            exit;
        } else {
            $message = "Kombinasi username dan password salah.";
            $message_type = 'danger';
        }
    } else {
        $message = "Kombinasi username dan password salah.";
        $message_type = 'danger';
    }
    $stmt->close();
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login & Registrasi - AbsensiCorp</title>
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style> body { background-color: #e9ecef; } </style>
</head>
<body>
<div class="container">
    <div class="row justify-content-center align-items-center" style="min-height: 100vh;">
        <div class="col-md-8 col-lg-7">
            <div class="text-center mb-4">
                <h2><i class="bi bi-calendar-check-fill"></i> Selamat Datang di AbsensiCorp</h2>
                <p class="text-muted">Silakan masuk untuk melanjutkan</p>
            </div>
            
            <?php if($message): ?>
                <div class="alert alert-<?= $message_type ?> alert-dismissible fade show" role="alert">
                    <?= $message ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <div class="card shadow-lg border-0">
                <div class="card-body p-4">
                    <div class="row">
                        <div class="col-md-6 border-end pe-md-4">
                            <h4 class="mb-3">Login</h4>
                            <form method="POST">
                                <div class="input-group mb-3">
                                    <span class="input-group-text"><i class="bi bi-person"></i></span>
                                    <input type="text" class="form-control" name="username_log" placeholder="Username" required>
                                </div>
                                <div class="input-group mb-3">
                                    <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                    <input type="password" class="form-control" name="password_log" placeholder="Password" required>
                                </div>
                                <div class="d-grid">
                                    <button type="submit" name="login" class="btn btn-primary">Masuk</button>
                                </div>
                            </form>
                        </div>
                        <div class="col-md-6 ps-md-4 mt-4 mt-md-0">
                            <h4 class="mb-3">Belum punya akun?</h4>
                            <form method="POST">
                                <div class="input-group mb-3">
                                    <span class="input-group-text"><i class="bi bi-person-plus"></i></span>
                                    <input type="text" class="form-control" name="username_reg" placeholder="Username Baru" required>
                                </div>
                                <div class="input-group mb-3">
                                    <span class="input-group-text"><i class="bi bi-shield-lock"></i></span>
                                    <input type="password" class="form-control" name="password_reg" placeholder="Password Baru" required>
                                </div>
                                <div class="d-grid">
                                    <button type="submit" name="register" class="btn btn-success">Daftar Sekarang</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="../assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>