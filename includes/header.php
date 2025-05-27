<?php
// includes/header.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aplikasi Absensi Modern</title>
    
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <style>
        body {
            background-color: #f8f9fa; /* Warna latar belakang sedikit abu-abu */
        }
        .navbar {
            box-shadow: 0 2px 4px rgba(0,0,0,.1);
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
      <ul class="navbar-nav ms-auto">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
            <li class="nav-item">
                <a class="nav-link" href="../pages/dashboard.php">Dashboard</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="../pages/index.php">Data Absensi</a>
            </li>
        </ul>
        <ul class="navbar-nav ms-auto">
        <?php if (isset($_SESSION['user_id'])): ?>
            <li class="nav-item">
                <span class="navbar-text me-3">
                    <i class="bi bi-person-circle"></i> Halo, <?= htmlspecialchars($_SESSION['username']) ?>
                </span>
            </li>
            <li class="nav-item">
                <a class="btn btn-outline-danger" href="../auth/logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a>
            </li>
        <?php else: ?>
            <li class="nav-item">
                <a class="btn btn-outline-primary" href="../auth/login.php"><i class="bi bi-box-arrow-in-right"></i> Login</a>
            </li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>

<main class="container mt-4">