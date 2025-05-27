<?php
// pages/dashboard.php
require_once '../config/config.php';
require_once '../auth/auth.php'; // Proteksi halaman

$page_title = "Dashboard Absensi"; 

// Kondisi WHERE Berdasarkan Role
$is_admin = ($_SESSION['role'] == 'admin');
$where_clause_karyawan_for_total_absensi = '';
$params_karyawan_for_total_absensi = []; 
$types_karyawan_for_total_absensi = '';

if (!$is_admin) {
    $where_clause_karyawan_for_total_absensi = " WHERE nama = ?";
    $username_session_val = $_SESSION['username']; 
    $params_karyawan_for_total_absensi[] = $username_session_val;
    $types_karyawan_for_total_absensi .= 's';
}

//Query untuk Statistik
//Total Pengguna (Karyawan)
$total_karyawan = 0;
if ($is_admin) {
    $total_karyawan_res = $conn->query("SELECT COUNT(id) as total FROM users WHERE role = 'karyawan'"); // Count only karyawan
    if ($total_karyawan_res) {
        $total_karyawan_data = $total_karyawan_res->fetch_assoc();
        $total_karyawan = $total_karyawan_data['total'] ?? 0;
    }
}

//Total Absensi Tercatat
$total_absensi_sql = "SELECT COUNT(id) as total FROM absensi" . $where_clause_karyawan_for_total_absensi;
$stmt_total_absensi = $conn->prepare($total_absensi_sql);
if (!$is_admin && !empty($params_karyawan_for_total_absensi)) { 
    $stmt_total_absensi->bind_param($types_karyawan_for_total_absensi, ...$params_karyawan_for_total_absensi);
}
$stmt_total_absensi->execute();
$total_absensi_data = $stmt_total_absensi->get_result()->fetch_assoc();
$total_absensi = $total_absensi_data['total'] ?? 0;
$stmt_total_absensi->close();

//Statistik Hari Ini
$hari_ini = date('Y-m-d');
$stats_today_sql = "SELECT status, COUNT(id) as jumlah FROM absensi" . ($is_admin ? " WHERE tanggal = ?" : " WHERE tanggal = ? AND nama = ?") . " GROUP BY status";
$stmt_today = $conn->prepare($stats_today_sql);
if ($is_admin) {
    $stmt_today->bind_param("s", $hari_ini);
} else {
    $username_session_val_today = $_SESSION['username'];
    $stmt_today->bind_param("ss", $hari_ini, $username_session_val_today);
}
$stmt_today->execute();
$result_today = $stmt_today->get_result();
$stats_today = ['Hadir' => 0, 'Izin' => 0, 'Sakit' => 0, 'Alpha' => 0];
while($row = $result_today->fetch_assoc()) {
    if (array_key_exists($row['status'], $stats_today)) {
        $stats_today[$row['status']] = $row['jumlah'];
    }
}
$stmt_today->close();

//Statistik Bulan Ini
$bulan_ini_num = date('m'); 
$tahun_ini_num = date('Y'); 
$stats_month_sql = "SELECT status, COUNT(id) as jumlah FROM absensi" . ($is_admin ? " WHERE MONTH(tanggal) = ? AND YEAR(tanggal) = ?" : " WHERE MONTH(tanggal) = ? AND YEAR(tanggal) = ? AND nama = ?") . " GROUP BY status";
$stmt_month = $conn->prepare($stats_month_sql);
if ($is_admin) {
    $stmt_month->bind_param("ss", $bulan_ini_num, $tahun_ini_num);
} else {
    $username_session_val_month = $_SESSION['username'];
    $stmt_month->bind_param("sss", $bulan_ini_num, $tahun_ini_num, $username_session_val_month);
}
$stmt_month->execute();
$result_month = $stmt_month->get_result();
$stats_month = ['Hadir' => 0, 'Izin' => 0, 'Sakit' => 0, 'Alpha' => 0];
while($row = $result_month->fetch_assoc()) {
     if (array_key_exists($row['status'], $stats_month)) {
        $stats_month[$row['status']] = $row['jumlah'];
    }
}
$stmt_month->close();

//Data untuk Grafik Hadir per Hari (Current Month)
$labels_hadir_per_hari = []; 
$data_hadir_per_hari = [];   

$days_in_current_month = (int)date('t'); 
for ($i = 1; $i <= $days_in_current_month; $i++) {
    $labels_hadir_per_hari[] = $i; 
    $data_hadir_per_hari[] = 0;    
}

$hadir_per_hari_sql = "SELECT DAY(tanggal) as hari, COUNT(id) as jumlah_hadir 
                       FROM absensi";
$hadir_per_hari_sql .= ($is_admin ? " WHERE status = 'Hadir' AND MONTH(tanggal) = ? AND YEAR(tanggal) = ?" 
                                  : " WHERE status = 'Hadir' AND MONTH(tanggal) = ? AND YEAR(tanggal) = ? AND nama = ?");
$hadir_per_hari_sql .= " GROUP BY DAY(tanggal) ORDER BY DAY(tanggal) ASC";

$stmt_hadir_harian = $conn->prepare($hadir_per_hari_sql);
if ($is_admin) {
    $stmt_hadir_harian->bind_param("ss", $bulan_ini_num, $tahun_ini_num);
} else {
    $username_session_val_graph = $_SESSION['username'];
    $stmt_hadir_harian->bind_param("sss", $bulan_ini_num, $tahun_ini_num, $username_session_val_graph);
}
$stmt_hadir_harian->execute();
$result_hadir_harian = $stmt_hadir_harian->get_result();

while($row = $result_hadir_harian->fetch_assoc()) {
    $day_of_month = (int)$row['hari'];
    if (isset($data_hadir_per_hari[$day_of_month - 1])) {
        $data_hadir_per_hari[$day_of_month - 1] = (int)$row['jumlah_hadir'];
    }
}
$stmt_hadir_harian->close();

include '../includes/header.php';
?>

<!-- Bagian Atas -->
<div class="mb-4">
    <h2><i class="bi bi-speedometer2"></i> Dashboard</h2>
    <p class="text-muted">
        <?php if ($is_admin): ?>
            Ringkasan data absensi seluruh karyawan.
        <?php else: ?>
            Ringkasan data absensi Anda, <?php echo htmlspecialchars($_SESSION['username']); ?>.
        <?php endif; ?>
    </p>
</div>

<div class="row g-4">
    <?php if ($is_admin): ?>
    <div class="col-md-6 col-lg-3">
        <div class="card text-white bg-primary shadow-sm">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <h3 class="card-title mb-0"><?php echo $total_karyawan; ?></h3>
                    <p class="card-text">Total Karyawan</p>
                </div>
                <i class="bi bi-people-fill" style="font-size: 3rem; opacity: 0.5;"></i>
            </div>
        </div>
    </div>
    <?php endif; ?>
     <div class="col-md-6 <?php echo $is_admin ? 'col-lg-3' : 'col-lg-4'; ?>">
        <div class="card text-white bg-info shadow-sm">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <h3 class="card-title mb-0"><?php echo $total_absensi; ?></h3>
                    <p class="card-text">Total Absensi</p>
                </div>
                <i class="bi bi-clipboard-data-fill" style="font-size: 3rem; opacity: 0.5;"></i>
            </div>
        </div>
    </div>
    <div class="col-md-6 <?php echo $is_admin ? 'col-lg-3' : 'col-lg-4'; ?>">
        <div class="card text-white bg-success shadow-sm">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <h3 class="card-title mb-0"><?php echo $stats_today['Hadir']; ?></h3>
                    <p class="card-text">Hadir Hari Ini</p>
                </div>
                <i class="bi bi-person-check-fill" style="font-size: 3rem; opacity: 0.5;"></i>
            </div>
        </div>
    </div>
    <div class="col-md-6 <?php echo $is_admin ? 'col-lg-3' : 'col-lg-4'; ?>">
        <div class="card text-white bg-warning shadow-sm">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <h3 class="card-title mb-0"><?php echo $stats_today['Izin'] + $stats_today['Sakit']; ?></h3>
                    <p class="card-text">Izin/Sakit Hari Ini</p>
                </div>
                <i class="bi bi-bandaid-fill" style="font-size: 3rem; opacity: 0.5;"></i>
            </div>
        </div>
    </div>
</div>

<!-- Rekapitulasi dan Grafik -->

<div class="mt-5">
    <div class="row d-flex align-items-stretch"> 
        
        <div class="col-lg-4 mb-4 mb-lg-0">
            <div class="card shadow-sm h-100"> 
                <div class="card-body d-flex flex-column"> 
                    <h4 class="card-title"><i class="bi bi-calendar-month"></i> Rekapitulasi Bulan <?php echo date('F Y'); ?></h4>
                    <hr>
                    <div class="list-group list-group-flush flex-grow-1"> 
                        <a href="#" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center" style="font-size: 1.05em; padding-top: 0.85rem; padding-bottom: 0.85rem;">
                            <span>Total Hadir</span>
                            <span class="badge bg-success rounded-pill" style="font-size: 0.95em; padding: 0.5em 0.7em;"><?php echo $stats_month['Hadir']; ?></span>
                        </a>
                        <a href="#" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center" style="font-size: 1.05em; padding-top: 0.85rem; padding-bottom: 0.85rem;">
                            <span>Total Izin</span>
                            <span class="badge bg-warning text-dark rounded-pill" style="font-size: 0.95em; padding: 0.5em 0.7em;"><?php echo $stats_month['Izin']; ?></span>
                        </a>
                        <a href="#" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center" style="font-size: 1.05em; padding-top: 0.85rem; padding-bottom: 0.85rem;">
                            <span>Total Sakit</span>
                            <span class="badge bg-info text-dark rounded-pill" style="font-size: 0.95em; padding: 0.5em 0.7em;"><?php echo $stats_month['Sakit']; ?></span>
                        </a>
                        <a href="#" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center" style="font-size: 1.05em; padding-top: 0.85rem; padding-bottom: 0.85rem;">
                            <span>Total Alpha</span>
                            <span class="badge bg-danger rounded-pill" style="font-size: 0.95em; padding: 0.5em 0.7em;"><?php echo $stats_month['Alpha']; ?></span>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card shadow-sm h-100"> 
                <div class="card-body d-flex flex-column">
                    <h4 class="card-title"><i class="bi bi-bar-chart-line-fill"></i> Kehadiran Harian (<?php echo date('F Y'); ?>)</h4>
                    <hr>
                    <div style="position: relative; flex-grow: 1; min-height: 280px;"> 
                        <canvas id="hadirPerHariChart" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>

<!-- Pemanggilan chart.js untuk grafik -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script> 
<script>
document.addEventListener('DOMContentLoaded', function () {
    const ctxHadirPerHari = document.getElementById('hadirPerHariChart');

    if (ctxHadirPerHari) {
        new Chart(ctxHadirPerHari, {
            type: 'bar', // Tipe grafik bisa diganti ke tipe 'line'
            data: {
                labels: <?php echo json_encode($labels_hadir_per_hari); ?>,
                datasets: [{
                    label: 'Jumlah Hadir',
                    data: <?php echo json_encode($data_hadir_per_hari); ?>,
                    backgroundColor: '#0d6efd', 
                    borderColor: '#0d6efd',
                    borderWidth: 1,
                    hoverBackgroundColor: '#0d6efd'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false, // Pengaturan penting agar tinggi grafik bisa dikustomisasi
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Jumlah Kehadiran'
                        },
                        ticks: {
                           stepSize: 1, // Set presisi
                           precision: 0  
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Tanggal (Hari ke-)'
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: true, 
                        position: 'top',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed.y !== null) {
                                    label += context.parsed.y;
                                }
                                return label;
                            }
                        }
                    }
                }
            }
        });
    } else {
        console.error("Canvas element 'hadirPerHariChart' not found!");
    }
});
</script>