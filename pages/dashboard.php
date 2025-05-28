<?php
// File: pages/dashboard.php (Versi Final dengan Grafik & Filter)
require_once '../config/config.php';
require_once '../auth/auth.php';

$page_title = "Dashboard Absensi";

// --- BAGIAN BARU: Logika Filter ---
// Ambil nilai filter dari URL, jika tidak ada, gunakan bulan dan tahun saat ini.
$filter_bulan = $_GET['bulan'] ?? date('m');
$filter_tahun = $_GET['tahun'] ?? date('Y');

$is_admin = ($_SESSION['role'] == 'admin');

// --- Query Statistik disesuaikan dengan Filter ---

// 1. Total Pengguna (tidak terpengaruh filter)
$total_karyawan = 0;
if ($is_admin) {
    $total_karyawan_res = $conn->query("SELECT COUNT(id) as total FROM users");
    $total_karyawan = $total_karyawan_res->fetch_assoc()['total'];
}

// 2. Total Absensi Tercatat (disesuaikan dengan role)
$where_clause_karyawan = $is_admin ? '' : ' WHERE nama = ?';
$total_absensi_sql = "SELECT COUNT(id) as total FROM absensi" . $where_clause_karyawan;
$stmt_total_absensi = $conn->prepare($total_absensi_sql);
if (!$is_admin) {
    $stmt_total_absensi->bind_param('s', $_SESSION['username']);
}
$stmt_total_absensi->execute();
$total_absensi = $stmt_total_absensi->get_result()->fetch_assoc()['total'];

// 3. Statistik Hari Ini (tidak terpengaruh filter)
$hari_ini = date('Y-m-d');
$stats_today_sql = "SELECT status, COUNT(id) as jumlah FROM absensi" . ($is_admin ? " WHERE tanggal = ?" : " WHERE tanggal = ? AND nama = ?") . " GROUP BY status";
$stmt_today = $conn->prepare($stats_today_sql);
$is_admin ? $stmt_today->bind_param("s", $hari_ini) : $stmt_today->bind_param("ss", $hari_ini, $_SESSION['username']);
$stmt_today->execute();
$result_today = $stmt_today->get_result();
$stats_today = ['Hadir' => 0, 'Izin' => 0, 'Sakit' => 0, 'Alpha' => 0];
while ($row = $result_today->fetch_assoc()) {
    $stats_today[$row['status']] = $row['jumlah'];
}

// 4. Statistik Bulanan (kini menggunakan filter bulan & tahun)
$stats_month_sql = "SELECT status, COUNT(id) as jumlah FROM absensi" . ($is_admin ? " WHERE MONTH(tanggal) = ? AND YEAR(tanggal) = ?" : " WHERE MONTH(tanggal) = ? AND YEAR(tanggal) = ? AND nama = ?") . " GROUP BY status";
$stmt_month = $conn->prepare($stats_month_sql);
if ($is_admin) {
    $stmt_month->bind_param("ss", $filter_bulan, $filter_tahun);
} else {
    $stmt_month->bind_param("sss", $filter_bulan, $filter_tahun, $_SESSION['username']);
}
$stmt_month->execute();
$result_month = $stmt_month->get_result();
$stats_month = ['Hadir' => 0, 'Izin' => 0, 'Sakit' => 0, 'Alpha' => 0];
while ($row = $result_month->fetch_assoc()) {
    $stats_month[$row['status']] = $row['jumlah'];
}

// 5. Data untuk Grafik Hadir Harian (kini menggunakan filter bulan & tahun)
$labels_hadir_per_hari = [];
$data_hadir_per_hari = [];
$days_in_month = cal_days_in_month(CAL_GREGORIAN, $filter_bulan, $filter_tahun);
for ($i = 1; $i <= $days_in_month; $i++) {
    $labels_hadir_per_hari[] = $i;
    $data_hadir_per_hari[] = 0;
}

$hadir_per_hari_sql = "SELECT DAY(tanggal) as hari, COUNT(id) as jumlah_hadir FROM absensi" . ($is_admin ? " WHERE status = 'Hadir' AND MONTH(tanggal) = ? AND YEAR(tanggal) = ?" : " WHERE status = 'Hadir' AND MONTH(tanggal) = ? AND YEAR(tanggal) = ? AND nama = ?") . " GROUP BY DAY(tanggal)";
$stmt_hadir_harian = $conn->prepare($hadir_per_hari_sql);
if ($is_admin) {
    $stmt_hadir_harian->bind_param("ss", $filter_bulan, $filter_tahun);
} else {
    $stmt_hadir_harian->bind_param("sss", $filter_bulan, $filter_tahun, $_SESSION['username']);
}
$stmt_hadir_harian->execute();
$result_hadir_harian = $stmt_hadir_harian->get_result();
while ($row = $result_hadir_harian->fetch_assoc()) {
    $day_of_month = (int)$row['hari'];
    $data_hadir_per_hari[$day_of_month - 1] = (int)$row['jumlah_hadir'];
}

// BARU: Cek status absensi hari ini untuk karyawan yang sedang login
$sudah_absen_hari_ini = false;
if (!$is_admin) {
    $hari_ini_check = date('Y-m-d');
    $stmt_absen_check = $conn->prepare("SELECT id FROM absensi WHERE user_id = ? AND tanggal = ?");
    $stmt_absen_check->bind_param("is", $_SESSION['user_id'], $hari_ini_check);
    $stmt_absen_check->execute();
    if ($stmt_absen_check->get_result()->num_rows > 0) {
        $sudah_absen_hari_ini = true;
    }
    $stmt_absen_check->close();
}

include '../includes/header.php';
?>

<div class="mb-4 d-flex justify-content-between align-items-center">
    <div>
        <h2><i class="bi bi-speedometer2"></i> Dashboard</h2>
        <p class="text-muted">Ringkasan data absensi karyawan.</p>
    </div>
</div>

<?php if (!$is_admin): ?>
    <div class="card shadow-sm mb-4 border-start border-primary border-4">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h4 class="card-title">Absensi Hari Ini - <?= date('l, d F Y') ?></h4>
                    <?php if ($sudah_absen_hari_ini): ?>
                        <p class="card-text text-success mb-0">
                            <i class="bi bi-check-circle-fill"></i> Anda sudah berhasil melakukan absensi hari ini.
                        </p>
                    <?php else: ?>
                        <p class="card-text text-muted mb-0">Anda belum melakukan absensi hari ini. Silakan tekan tombol clock-in.</p>
                    <?php endif; ?>
                </div>
                <div class="col-md-4 text-md-end mt-3 mt-md-0">
                    <?php if ($sudah_absen_hari_ini): ?>
                        <button class="btn btn-lg btn-secondary" disabled>Telah Absen</button>
                    <?php else: ?>
                        <a href="clock_in.php" class="btn btn-lg btn-primary">
                            <i class="bi bi-clock-history me-2"></i> Clock In Sekarang
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>


<div class="row g-4">
    <?php if ($is_admin): ?>
        <div class="col-md-6 col-lg-3">
            <div class="card text-white bg-primary shadow-sm h-100">
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
    <div class="col-md-6 <?php echo $is_admin ? 'col-lg-3' : 'col-lg-4'; ?>">
        <div class="card text-white bg-info shadow-sm h-100">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <h3 class="card-title mb-0"><?= $total_absensi ?></h3>
                    <p class="card-text">Total Absensi</p>
                </div>
                <i class="bi bi-clipboard-data-fill" style="font-size: 3rem; opacity: 0.5;"></i>
            </div>
        </div>
    </div>
    <div class="col-md-6 <?php echo $is_admin ? 'col-lg-3' : 'col-lg-4'; ?>">
        <div class="card text-white bg-success shadow-sm h-100">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <h3 class="card-title mb-0"><?= $stats_today['Hadir'] ?></h3>
                    <p class="card-text">Hadir Hari Ini</p>
                </div>
                <i class="bi bi-person-check-fill" style="font-size: 3rem; opacity: 0.5;"></i>
            </div>
        </div>
    </div>
    <div class="col-md-6 <?php echo $is_admin ? 'col-lg-3' : 'col-lg-4'; ?>">
        <div class="card text-white bg-warning shadow-sm h-100">
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
    <h4><i class="bi bi-calendar-month"></i> Rekapitulasi Bulan <?= DateTime::createFromFormat('!m', $filter_bulan)->format('F') . ' ' . $filter_tahun ?></h4>
    <hr>
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <h5 class="card-title"><i class="bi bi-bar-chart-line-fill"></i> Tren Kehadiran Harian</h5>
                    <div style="height: 300px;">
                        <canvas id="hadirPerHariChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card shadow-sm h-100">
                <div class="card-body text-center">
                    <h5 class="card-title"><i class="bi bi-pie-chart-fill"></i> Proporsi Kehadiran</h5>
                    <div style="height: 300px;">
                        <canvas id="rekapBulananChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Data dari PHP
        const labelsHarian = <?php echo json_encode($labels_hadir_per_hari); ?>;
        const dataHarian = <?php echo json_encode($data_hadir_per_hari); ?>;
        const dataBulanan = <?php echo json_encode(array_values($stats_month)); ?>;
        const labelsBulanan = <?php echo json_encode(array_keys($stats_month)); ?>;

        // Grafik 1: Bar Chart Kehadiran Harian
        const ctxHadirPerHari = document.getElementById('hadirPerHariChart');
        if (ctxHadirPerHari) {
            new Chart(ctxHadirPerHari, {
                type: 'bar',
                data: {
                    labels: labelsHarian,
                    datasets: [{
                        label: 'Jumlah Hadir',
                        data: dataHarian,
                        backgroundColor: 'rgba(25, 135, 84, 0.6)',
                        borderColor: 'rgba(25, 135, 84, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0
                            }
                        }
                    }
                }
            });
        }

        // BAGIAN BARU: Grafik 2: Doughnut Chart Rekapitulasi Bulanan
        const ctxRekapBulanan = document.getElementById('rekapBulananChart');
        if (ctxRekapBulanan) {
            new Chart(ctxRekapBulanan, {
                type: 'doughnut', // Tipe grafik doughnut
                data: {
                    labels: labelsBulanan,
                    datasets: [{
                        label: 'Jumlah',
                        data: dataBulanan,
                        backgroundColor: [
                            'rgba(25, 135, 84, 0.7)', // Hadir (Success)
                            'rgba(255, 193, 7, 0.7)', // Izin (Warning)
                            'rgba(13, 202, 240, 0.7)', // Sakit (Info)
                            'rgba(220, 53, 69, 0.7)' // Alpha (Danger)
                        ],
                        borderColor: '#fff',
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                        }
                    }
                }
            });
        }
    });
</script>