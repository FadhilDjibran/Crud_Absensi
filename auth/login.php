<?php
// File: auth/login.php
require_once '../config/config.php'; // Ini seharusnya memanggil session_start()

// Jika sudah login, langsung arahkan ke dasbor
if (isset($_SESSION['user_id'])) {
    header("Location: ../halaman/dasbor.php");
    exit;
}

$message = '';
$message_type = ''; // 'success' atau 'danger'

// Proses Login
if (isset($_POST['login'])) {
    $username = $_POST['username_log'];
    $password = $_POST['password_log'];

    $stmt = $conn->prepare("SELECT id, username, password, role FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            
            header("Location: ../halaman/dasbor.php");
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
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - AbsensiCorp</title>
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <style>
        body {
            /* Gambar latar belakang dari Unsplash */
            background-image: url('../assets/img/loginBG.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
        }
        .login-card {
            /* Efek glassmorphism */
            background: rgba(255, 255, 255, 0.4);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 15px;
        }
        .login-card .card-body {
            padding: 2rem;
        }
        .login-title {
            color: #333;
        }
        /* PERBAIKAN: Style baru untuk container logo */
        .logo-container {
            background-color: rgba(255, 255, 255, 1); /* Latar belakang putih semi-transparan */
            padding: 15px 15px;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }
        .logo-text {
            text-shadow: 1px 1px 2px rgba(0,0,0,0.1); /* Bayangan teks halus sebagai outline */
        }
        .photo-credit {
            position: fixed;
            bottom: 10px;
            right: 15px;
            font-size: 12px;
            color: white;
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.7);
            background-color: rgba(0, 0, 0, 0.5);
            padding: 3px 8px;
            border-radius: 5px;
        }
        .photo-credit a {
            color: #eee;
            text-decoration: none;
        }
        .photo-credit a:hover {
            color: #fff;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-7 col-lg-5 col-xl-4">
            <!-- PERBAIKAN: Menambahkan kelas .logo-container -->
            <div class="text-center mb-4 logo-container">
                <h2 class="fw-bold text-dark display-5 logo-text"><i class="bi bi-calendar-check-fill"></i> AbsensiCorp</h2>
                <p class="text-secondary">Silakan masuk untuk melanjutkan</p>
            </div>
            
            <?php if($message): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php echo htmlspecialchars($message); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <div class="card shadow-lg border-0 login-card">
                <div class="card-body">
                    <h4 class="mb-4 text-center login-title">Login</h4>
                    <form method="POST">
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" id="username" name="username_log" placeholder="Username" required>
                            <label for="username"><i class="bi bi-person me-2"></i>Username</label>
                        </div>
                        <div class="form-floating mb-3">
                            <input type="password" class="form-control" id="password" name="password_log" placeholder="Password" required>
                            <label for="password"><i class="bi bi-lock me-2"></i>Password</label>
                        </div>
                        <div class="d-grid mt-4">
                            <button type="submit" name="login" class="btn btn-primary btn-lg">Masuk</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="photo-credit">
    Foto oleh <a href="https://unsplash.com/@sunday_digital" target="_blank">Nastuh Abootalebi di Unsplash</a>
</div>

<script src="../assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>
