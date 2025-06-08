<?php
// File: includes/header.php
// Header ini sudah disesuaikan untuk struktur baru dengan folder 'halaman' dan 'fungsi'.

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Deteksi halaman yang sedang aktif berdasarkan nama file yang dijalankan
$current_page = basename($_SERVER['SCRIPT_NAME']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Judul halaman dinamis, diambil dari variabel $page_title di setiap halaman -->
    <title><?php echo isset($page_title) ? htmlspecialchars($page_title) . ' - AbsensiCorp' : 'Aplikasi Absensi Modern'; ?></title>
    
    <!-- Path ke aset CSS sudah benar karena file header di-include dari dalam folder 'halaman' -->
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <style>
        body { background-color: #f8f9fa; }
        .navbar { box-shadow: 0 2px 4px rgba(0,0,0,.1); }
        .navbar-dark .navbar-nav .nav-link.active {
            font-weight: bold;
            color: #ffffff; /* Warna putih untuk teks link aktif */
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
  <div class="container">
    <!-- Tautan brand ke halaman dasbor -->
    <a class="navbar-brand" href="dasbor.php">
        <i class="bi bi-calendar-check-fill"></i> AbsensiCorp
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <!-- Menu Dasbor -->
        <li class="nav-item">
            <a class="nav-link <?php echo ($current_page == 'dasbor.php') ? 'active' : ''; ?>" href="dasbor.php">Dashboard</a>
        </li>
        <!-- Menu Data Absensi -->
        <li class="nav-item">
            <a class="nav-link <?php echo in_array($current_page, ['absensi.php', 'tambah_absensi.php', 'edit_absensi.php']) ? 'active' : ''; ?>" href="absensi.php">Data Absensi</a>
        </li>
        
        <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin'): // Menu khusus Admin ?>
        <!-- Menu Manajemen Pengguna -->
        <li class="nav-item">
            <a class="nav-link <?php echo in_array($current_page, ['manajemen_pengguna.php', 'tambah_pengguna.php', 'edit_pengguna.php']) ? 'active' : ''; ?>" href="manajemen_pengguna.php">Manajemen Pengguna</a>
        </li>
        <!-- Menu Laporan -->
        <li class="nav-item">
            <a class="nav-link <?php echo ($current_page == 'laporan_absensi.php') ? 'active' : ''; ?>" href="laporan_absensi.php">Laporan</a>
        </li>
        <?php endif; ?>
      </ul>
      <ul class="navbar-nav ms-auto">
        <?php if (isset($_SESSION['user_id'])): ?>
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle <?php echo ($current_page == 'profil.php') ? 'active' : ''; ?>" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-person-circle"></i> Halo, <?php echo htmlspecialchars($_SESSION['username']); ?>
              </a>
              <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                <li><a class="dropdown-item" href="profil.php"><i class="bi bi-person-fill-gear me-2"></i>Profil Saya</a></li>
                <li><hr class="dropdown-divider"></li>
                <!-- Tautan logout sudah benar karena keluar satu direktori ke root, lalu ke folder auth -->
                <li><a class="dropdown-item text-danger" href="../auth/logout.php"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
              </ul>
            </li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>

<!-- Konten utama akan dimulai di sini -->
<main class="container mt-4">
