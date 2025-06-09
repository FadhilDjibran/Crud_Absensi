<?php
// File: halaman/tentang.php
// Menampilkan halaman statis "Tentang Kami".

require_once '../config/config.php';
require_once '../auth/auth.php'; // Memastikan hanya pengguna yang login yang bisa lihat

$page_title = "Tentang Kami"; // Judul untuk tab browser

// Memuat header HTML
include '../includes/header.php';
?>

<div class="container mt-4">
    <!-- Judul dan Deskripsi Utama -->
    <div class="text-center mb-5">
        <h2 class="display-5"><i class="bi bi-info-circle-fill"></i> Tentang Aplikasi Ini</h2>
        <p class="lead text-muted">Website ini dikerjakan oleh <strong>Kelompok 4</strong> Pemrograman Web D081 UPN Veteran Jawa Timur.</p>
        <hr class="w-50 mx-auto">
    </div>

    <!-- Bagian Profil Anggota Kelompok -->
    <h3 class="text-center mb-4">Tim Kami</h3>
    <div class="row row-cols-1 row-cols-md-3 row-cols-lg-5 g-4 justify-content-center">
        
        <!-- Kartu Profil 2 -->
         <div class="col">
            <div class="card h-100 text-center shadow-sm">
                <!-- PERBAIKAN: Path ke gambar lokal -->
                <img src="../assets/img/Fadhil.jpg" class="card-img-top" alt="Foto Fadhil Djibran">
                <div class="card-body">
                    <h5 class="card-title">Fadhil Djibran</h5>
                    <p class="card-text text-muted">NPM 23081010030</p>
                </div>
                <div class="card-footer">
                    <a href="https://github.com/FadhilDjibran" class="btn btn-dark btn-sm w-100">
                        <i class="bi bi-github"></i> GitHub
                    </a>
                </div>
            </div>
        </div>

        <!-- Kartu Profil 2 -->
        <div class="col">
            <div class="card h-100 text-center shadow-sm">
                <img src="../assets/img/Rafi.jpg" class="card-img-top" alt="Foto Anggota 2">
                <div class="card-body">
                    <h5 class="card-title">Muhammad Rafi Walidain</h5>
                    <p class="card-text text-muted">NPM 23081010005</p>
                </div>
                <div class="card-footer">
                    <a href="https://github.com/rafiwalidain" class="btn btn-dark btn-sm w-100">
                        <i class="bi bi-github"></i> GitHub
                    </a>
                </div>
            </div>
        </div>

        <!-- Kartu Profil 3 -->
        <div class="col">
            <div class="card h-100 text-center shadow-sm">
                <img src="../assets/img/Afandi.png" class="card-img-top" alt="Foto Anggota 3">
                <div class="card-body">
                    <h5 class="card-title">Muhammad Syaifudin Afandi</h5>
                    <p class="card-text text-muted">NPM 23081010001</p>
                </div>
                <div class="card-footer">
                    <a href="https://github.com/Syaifudinafandi21" class="btn btn-dark btn-sm w-100">
                        <i class="bi bi-github"></i> GitHub
                    </a>
                </div>
            </div>
        </div>

        <!-- Kartu Profil 4 -->
        <div class="col">
            <div class="card h-100 text-center shadow-sm">
                <img src="../assets/img/Nasikh.jpg" class="card-img-top" alt="Foto Anggota 4">
                <div class="card-body">
                    <h5 class="card-title">Moch Nasikh Andhyka Pratama</h5>
                    <p class="card-text text-muted">NPM 23081010037</p>
                </div>
                <div class="card-footer">
                     <a href="https://github.com/nasikhand" class="btn btn-dark btn-sm w-100">
                        <i class="bi bi-github"></i> GitHub
                    </a>
                </div>
            </div>
        </div>

        <!-- Kartu Profil 5 -->
        <div class="col">
            <div class="card h-100 text-center shadow-sm">
                <img src="../assets/img/Reno.jpg" class="card-img-top" alt="Foto Anggota 5">
                <div class="card-body">
                    <h5 class="card-title">Reno Naufal Maulidyan</h5>
                    <p class="card-text text-muted">NPM 23081010026</p>
                </div>
                <div class="card-footer">
                     <a href="https://github.com/renonaufal" class="btn btn-dark btn-sm w-100">
                        <i class="bi bi-github"></i> GitHub
                    </a>
                </div>
            </div>
        </div>

    </div>
</div>

<?php 
// Menutup koneksi dan memuat footer
$conn->close();
include '../includes/footer.php'; 
?>
