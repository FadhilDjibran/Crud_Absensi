<?php
// includes/header.php (Versi dengan Menu Aktif)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// BARU: Deteksi halaman yang sedang aktif
$current_page = basename($_SERVER['SCRIPT_NAME']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? htmlspecialchars($page_title) . ' - AbsensiCorp' : 'Aplikasi Absensi Modern'; ?></title>
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style> body { background-color: #f8f9fa; } .navbar { box-shadow: 0 2px 4px rgba(0,0,0,.1); } </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
  <div class="container">
    <a class="navbar-brand" href="dashboard.php">
        <i class="bi bi-calendar-check-fill"></i> AbsensiCorp
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item">
            <a class="nav-link <?php echo ($current_page == 'dashboard.php') ? 'active' : ''; ?>" href="dashboard.php">Dashboard</a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo ($current_page == 'index.php' || $current_page == 'tambah.php' || $current_page == 'edit.php') ? 'active' : ''; ?>" href="index.php">Data Absensi</a>
        </li>
        <?php if ($_SESSION['role'] == 'admin'): ?>
        <li class="nav-item">
            <a class="nav-link <?php echo ($current_page == 'manage_users.php' || $current_page == 'add_user.php' || $current_page == 'edit_user.php') ? 'active' : ''; ?>" href="manage_users.php">Manajemen Pengguna</a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo ($current_page == 'laporan.php') ? 'active' : ''; ?>" href="laporan.php">Laporan</a>
        </li>
        <?php endif; ?>
      </ul>
      <ul class="navbar-nav ms-auto">
        <?php if (isset($_SESSION['user_id'])): ?>
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle <?php echo ($current_page == 'profile.php') ? 'active' : ''; ?>" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-person-circle"></i> Halo, <?= htmlspecialchars($_SESSION['username']) ?>
              </a>
              <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                <li><a class="dropdown-item" href="profile.php"><i class="bi bi-person-fill-gear me-2"></i>Profil Saya</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item text-danger" href="../auth/logout.php"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
              </ul>
            </li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>

<main class="container mt-4">