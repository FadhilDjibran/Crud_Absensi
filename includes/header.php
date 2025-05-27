<?php
//header.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? htmlspecialchars($page_title) . ' - AbsensiCorp' : 'Aplikasi Absensi Modern - AbsensiCorp'; ?></title>
    
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <style>
        body {
            background-color: #f8f9fa;
        }
        .navbar {
            box-shadow: 0 2px 4px rgba(0,0,0,.1);
        }
        .nav-item .nav-link.active {
            font-weight: bold;
            color: #ffffff !important; 
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
    <div class="container">
    <a class="navbar-brand" href="../pages/dashboard.php">
        <i class="bi bi-calendar-check-fill"></i> AbsensiCorp
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav" >
        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
            <?php if (isset($_SESSION['user_id'])): // hanya tunjukkan ketika sudah log in ?>
                <li class="nav-item">
                    <a class="nav-link <?php echo ($current_page === 'dashboard.php') ? 'active' : ''; ?>" href="../pages/dashboard.php">Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo ($current_page === 'index.php') ? 'active' : ''; ?>" href="../pages/index.php">Data Absensi</a>
                </li>

                <?php // user management hanya terlihat oleh admin ?>
                <li class="nav-item">
                    <a class="nav-link <?php echo ($current_page === 'manage_users.php') ? 'active' : ''; ?>" href="../pages/manage_users.php">Manajemen User</a>
                </li>

                <?php // link hanya untuk admin (kosong, mungkin bisa ditambah) ?>
                <?php if ($_SESSION['role'] === 'admin'): ?>
                    <?php endif; ?>
            <?php endif; ?>
        </ul>
        <ul class="navbar-nav ms-auto">
        <?php if (isset($_SESSION['user_id'])): ?>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownUser" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-person-circle"></i> Halo, <?php echo htmlspecialchars($_SESSION['username']); ?>
                </a>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdownUser">
                    <li><a class="dropdown-item" href="../pages/profile.php">Profil Saya</a></li> <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item text-danger" href="../auth/logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
                </ul>
            </li>
        <?php else: ?>
            <li class="nav-item">
                <a class="btn btn-outline-primary <?php echo ($current_page === 'login.php') ? 'active' : ''; ?>" href="../auth/login.php"><i class="bi bi-box-arrow-in-right"></i> Login</a>
            </li>
        <?php endif; ?>
        </ul>
    </div>
    </div>
</nav>

<main class="container mt-4">