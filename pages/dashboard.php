<?php
// pages/dashboard.php
require_once '../config/config.php';
require_once '../auth/auth.php'; // Proteksi halaman

// --- Menyiapkan Kondisi WHERE Berdasarkan Role ---
$is_admin = ($_SESSION['role'] == 'admin');
$where_clause_karyawan = '';
$params_karyawan = [];
$types_karyawan = '';

if (!$is_admin) {
    // Jika bukan admin, siapkan filter untuk query
    $where_clause_karyawan = " WHERE nama = ?";
    $params_karyawan[] = &$_SESSION['username'];
    $types_karyawan .= 's';
}


// --- Query untuk Statistik ---

// 1. Total Pengguna (Karyawan) -> Ini tetap global, hanya admin yang mungkin butuh
$total_karyawan_res = $conn->query("SELECT COUNT(id) as total FROM users");
$total_karyawan = $total_karyawan_res->fetch_assoc()['total'];

// 2. Total Absensi Tercatat (Disesuaikan dengan role)
$total_absensi_sql = "SELECT COUNT(id) as total FROM absensi" . $where_clause_karyawan;
$stmt_total_absensi = $conn->prepare($total_absensi_sql);
if (!$is_admin) {
    $stmt_total_absensi->bind_param($types_karyawan, ...$params_karyawan);
}
$stmt_total_absensi->execute();
$total_absensi = $stmt_total_absensi->get_result()->fetch_assoc()['total'];


// 3. Statistik Hari Ini (Disesuaikan dengan role)
$hari_ini = date('Y-m-d');
$stats_today_sql = "SELECT status, COUNT(id) as jumlah FROM absensi" . ($is_admin ? " WHERE tanggal = ?" : " WHERE tanggal = ? AND nama = ?") . " GROUP BY status";
$stmt_today = $conn->prepare($stats_today_sql);
if ($is_admin) {
    $stmt_today->bind_param("s", $hari_ini);
} else {
    $stmt_today->bind_param("ss", $hari_ini, $_SESSION['username']);
}
$stmt_today->execute();
$result_today = $stmt_today->get_result();
$stats_today = ['Hadir' => 0, 'Izin' => 0, 'Sakit' => 0, 'Alpha' => 0];
while($row = $result_today->fetch_assoc()) {
    $stats_today[$row['status']] = $row['jumlah'];
}


// 4. Statistik Bulan Ini (Disesuaikan dengan role)
$bulan_ini = date('m');
$tahun_ini = date('Y');
$stats_month_sql = "SELECT status, COUNT(id) as jumlah FROM absensi" . ($is_admin ? " WHERE MONTH(tanggal) = ? AND YEAR(tanggal) = ?" : " WHERE MONTH(tanggal) = ? AND YEAR(tanggal) = ? AND nama = ?") . " GROUP BY status";
$stmt_month = $conn->prepare($stats_month_sql);
if ($is_admin) {
    $stmt_month->bind_param("ss", $bulan_ini, $tahun_ini);
} else {
    $stmt_month->bind_param("sss", $bulan_ini, $tahun_ini, $_SESSION['username']);
}
$stmt_month->execute();
$result_month = $stmt_month->get_result();
$stats_month = ['Hadir' => 0, 'Izin' => 0, 'Sakit' => 0, 'Alpha' => 0];
while($row = $result_month->fetch_assoc()) {
    $stats_month[$row['status']] = $row['jumlah'];
}


include '../includes/header.php';
?>

<div class="mb-4">
    <h2><i class="bi bi-speedometer2"></i> Dashboard</h2>
    <p class="text-muted">
        <?php if ($is_admin): ?>
            Ringkasan data absensi seluruh karyawan.
        <?php else: ?>
            Ringkasan data absensi Anda, <?= htmlspecialchars($_SESSION['username']) ?>.
        <?php endif; ?>
    </p>
</div>

<div class="row g-4">
    <?php if ($is_admin): // Kartu ini hanya untuk admin ?>
    <div class="col-md-6 col-lg-3">
        <div class="card text-white bg-primary shadow-sm">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <h3 class="card-title mb-0"><?= $total_karyawan ?></h3>
                    <p class="card-text">Total Karyawan</p>
                </div>
                <i class="bi bi-people-fill" style="font-size: 3rem; opacity: 0.5;"></i>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <div class="col-md-6 col-lg-3">
        <div class="card text-white bg-info shadow-sm">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <h3 class="card-title mb-0"><?= $total_absensi ?></h3>
                    <p class="card-text">Total Absensi</p>
                </div>
                <i class="bi bi-clipboard-data-fill" style="font-size: 3rem; opacity: 0.5;"></i>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-lg-3">
        <div class="card text-white bg-success shadow-sm">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <h3 class="card-title mb-0"><?= $stats_today['Hadir'] ?></h3>
                    <p class="card-text">Hadir Hari Ini</p>
                </div>
                <i class="bi bi-person-check-fill" style="font-size: 3rem; opacity: 0.5;"></i>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-lg-3">
        <div class="card text-white bg-warning shadow-sm">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <h3 class="card-title mb-0"><?= $stats_today['Izin'] + $stats_today['Sakit'] ?></h3>
                    <p class="card-text">Izin/Sakit Hari Ini</p>
                </div>
                <i class="bi bi-bandaid-fill" style="font-size: 3rem; opacity: 0.5;"></i>
            </div>
        </div>
    </div>
</div>

<div class="mt-5">
    <h4><i class="bi bi-calendar-month"></i> Rekapitulasi Bulan <?= date('F Y') ?></h4>
    <hr>
    <div class="row">
        <div class="col-md-4">
             <div class="list-group">
                <a href="#" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">Total Hadir <span class="badge bg-success rounded-pill"><?= $stats_month['Hadir'] ?></span></a>
                <a href="#" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">Total Izin <span class="badge bg-warning text-dark rounded-pill"><?= $stats_month['Izin'] ?></span></a>
                <a href="#" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">Total Sakit <span class="badge bg-info text-dark rounded-pill"><?= $stats_month['Sakit'] ?></span></a>
                <a href="#" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">Total Alpha <span class="badge bg-danger rounded-pill"><?= $stats_month['Alpha'] ?></span></a>
            </div>
        </div>
        </div>
</div>

<?php include '../includes/footer.php'; ?>