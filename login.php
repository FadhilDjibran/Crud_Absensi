<?php
include 'config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$message = '';

// Proses Registrasi
if (isset($_POST['register'])) {
    $username = $conn->real_escape_string($_POST['username_reg']);
    $password = $_POST['password_reg'];

    $cek = $conn->query("SELECT * FROM users WHERE username='$username'");
    if ($cek->num_rows > 0) {
        $message = "Username sudah terdaftar!";
    } else {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        $conn->query("INSERT INTO users (username, password) VALUES ('$username', '$password_hash')");
        $message = "Registrasi berhasil, silakan login.";
    }
}

// Proses Login
if (isset($_POST['login'])) {
    $username = $conn->real_escape_string($_POST['username_log']);
    $password = $_POST['password_log'];

    $result = $conn->query("SELECT * FROM users WHERE username='$username'");
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            header("Location: index.php");
            exit;
        } else {
            $message = "Password salah!";
        }
    } else {
        $message = "Username tidak ditemukan!";
    }
}
?>

<h1>Sistem Absensi Kantor</h1>

<h2>Login User</h2>
<?php if($message) echo "<p style='color:red;'>$message</p>"; ?>

<!-- Form Login -->
<form method="POST">
    Username: <input type="text" name="username_log" required><br><br>
    Password: <input type="password" name="password_log" required><br><br>
    <button type="submit" name="login">Login</button>
</form>

<hr>

<h2>Registrasi User</h2>
<!-- Form Registrasi -->
<form method="POST">
    Username: <input type="text" name="username_reg" required><br><br>
    Password: <input type="password" name="password_reg" required><br><br>
    <button type="submit" name="register">Register</button>
</form>
