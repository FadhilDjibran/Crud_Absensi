<?php
// File: pages/dashboard.php (Versi dengan Sistem Pengajuan & Persetujuan)
require_once '../config/config.php'; // Memuat konfigurasi dan memulai session
require_once '../auth/auth.php';     // Memastikan hanya pengguna yang terautentikasi

$page_title = "Dashboard Absensi"; // Judul halaman untuk header

// --- BAGIAN FILTER BULAN/TAHUN (Untuk Statistik & Grafik Admin) ---
$filter_bulan = $_GET['bulan'] ?? date('m');
$filter_tahun = $_GET['tahun'] ?? date('Y');
$nama_bulan_filter = DateTime::createFromFormat('!m', $filter_bulan)->format('F');

$is_admin = ($_SESSION['role'] == 'admin');
$current_user_id = $_SESSION['user_id'];
$current_username = $_SESSION['username'];

// --- Logika Status Absensi Hari Ini untuk Karyawan & Daftar Pengajuan untuk Admin ---
$status_absensi_hari_ini_karyawan = "Belum Absen"; // Default untuk karyawan
$pengajuan_absensi_pending_admin = []; // Untuk admin

$hari_ini_tanggal = date('Y-m-d');

if (!$is_admin) {
    // 1. Cek tabel pengajuanAbsensi untuk status 'pending' atau 'ditolak' hari ini
    $stmt_cek_pengajuan = $conn->prepare(
        "SELECT status_diajukan, status_review FROM pengajuanAbsensi 
         WHERE user_id = ? AND tanggal = ? ORDER BY created_at DESC LIMIT 1"
    );
    $stmt_cek_pengajuan->bind_param("is", $current_user_id, $hari_ini_tanggal);
    $stmt_cek_pengajuan->execute();
    $result_pengajuan = $stmt_cek_pengajuan->get_result();
    if ($pengajuan = $result_pengajuan->fetch_assoc()) {
        if ($pengajuan['status_review'] == 'pending') {
            $status_absensi_hari_ini_karyawan = "Menunggu Konfirmasi";
        } elseif ($pengajuan['status_review'] == 'ditolak') {
            $status_absensi_hari_ini_karyawan = "Pengajuan Ditolak";
        }
    }
    $stmt_cek_pengajuan->close();

    // 2. Jika status masih "Belum Absen" atau "Pengajuan Ditolak", cek tabel absensi (untuk yang sudah disetujui)
    if ($status_absensi_hari_ini_karyawan == "Belum Absen" || $status_absensi_hari_ini_karyawan == "Pengajuan Ditolak") {
        $stmt_cek_absensi_final = $conn->prepare(
            "SELECT status FROM absensi WHERE user_id = ? AND tanggal = ? LIMIT 1"
        );
        $stmt_cek_absensi_final->bind_param("is", $current_user_id, $hari_ini_tanggal);
        $stmt_cek_absensi_final->execute();
        $result_absensi_final = $stmt_cek_absensi_final->get_result();
        if ($absensi_final = $result_absensi_final->fetch_assoc()) {
            $status_absensi_hari_ini_karyawan = ucfirst($absensi_final['status']);
        }
        $stmt_cek_absensi_final->close();
    }
} else { // Jika Admin, ambil data pengajuan yang masih pending
    $sql_pengajuan_pending = "SELECT pa.id, pa.user_id, pa.nama AS nama_pengaju, pa.tanggal, pa.status_diajukan, pa.bukti_file, u.username 
                              FROM pengajuanAbsensi pa
                              JOIN users u ON pa.user_id = u.id
                              WHERE pa.status_review = 'pending' ORDER BY pa.created_at ASC";
    $result_pengajuan_pending = $conn->query($sql_pengajuan_pending);
    if ($result_pengajuan_pending) {
        $pengajuan_absensi_pending_admin = $result_pengajuan_pending->fetch_all(MYSQLI_ASSOC);
    }
}

// --- Query Statistik (Membaca dari tabel 'absensi' yang sudah disetujui) ---

// 1. Total Pengguna Karyawan (Hanya untuk Admin)
$total_karyawan = 0;
if ($is_admin) {
    $total_karyawan_res = $conn->query("SELECT COUNT(id) as total FROM users"); // Dimodifikasi untuk menghitung 'karyawan' saja
    if ($total_karyawan_res) $total_karyawan = $total_karyawan_res->fetch_assoc()['total'] ?? 0;
}

// 2. Total Absensi Tercatat KESELURUHAN (dari tabel absensi, disesuaikan dengan role)
$where_clause_total_absensi_keseluruhan = $is_admin ? '' : ' WHERE user_id = ?';
$total_absensi_keseluruhan_sql = "SELECT COUNT(id) as total FROM absensi" . $where_clause_total_absensi_keseluruhan;
$stmt_total_absensi_keseluruhan = $conn->prepare($total_absensi_keseluruhan_sql);
if (!$is_admin) {
    $stmt_total_absensi_keseluruhan->bind_param('i', $current_user_id);
}
$stmt_total_absensi_keseluruhan->execute();
$total_absensi_keseluruhan_data = $stmt_total_absensi_keseluruhan->get_result()->fetch_assoc();
$total_absensi_keseluruhan = $total_absensi_keseluruhan_data['total'] ?? 0;
$stmt_total_absensi_keseluruhan->close();

// 3. Statistik Bulanan (dari tabel absensi, menggunakan filter bulan & tahun)
$stats_month_sql = "SELECT status, COUNT(id) as jumlah FROM absensi" . ($is_admin ? " WHERE MONTH(tanggal) = ? AND YEAR(tanggal) = ?" : " WHERE MONTH(tanggal) = ? AND YEAR(tanggal) = ? AND user_id = ?") . " GROUP BY status";
$stmt_month = $conn->prepare($stats_month_sql);
if ($is_admin) {
    $stmt_month->bind_param("ss", $filter_bulan, $filter_tahun);
} else {
    $stmt_month->bind_param("ssi", $filter_bulan, $filter_tahun, $current_user_id);
}
$stmt_month->execute();
$result_month = $stmt_month->get_result();
$stats_month = ['Hadir' => 0, 'Izin' => 0, 'Sakit' => 0, 'Alpha' => 0];
while ($row = $result_month->fetch_assoc()) {
    if (isset($stats_month[$row['status']])) $stats_month[$row['status']] = $row['jumlah'];
}
$stmt_month->close();

// 4. Data untuk Grafik Hadir Harian (dari tabel absensi, menggunakan filter bulan & tahun)
$labels_hadir_per_hari = [];
$data_hadir_per_hari = [];
$days_in_month = cal_days_in_month(CAL_GREGORIAN, (int)$filter_bulan, (int)$filter_tahun);
for ($i = 1; $i <= $days_in_month; $i++) {
    $labels_hadir_per_hari[] = $i;
    $data_hadir_per_hari[] = 0;
}

$hadir_per_hari_sql = "SELECT DAY(tanggal) as hari, COUNT(id) as jumlah_hadir FROM absensi" . ($is_admin ? " WHERE status = 'Hadir' AND MONTH(tanggal) = ? AND YEAR(tanggal) = ?" : " WHERE status = 'Hadir' AND MONTH(tanggal) = ? AND YEAR(tanggal) = ? AND user_id = ?") . " GROUP BY DAY(tanggal) ORDER BY hari ASC";
$stmt_hadir_harian = $conn->prepare($hadir_per_hari_sql);
if ($is_admin) {
    $stmt_hadir_harian->bind_param("ss", $filter_bulan, $filter_tahun);
} else {
    $stmt_hadir_harian->bind_param("ssi", $filter_bulan, $filter_tahun, $current_user_id);
}
$stmt_hadir_harian->execute();
$result_hadir_harian = $stmt_hadir_harian->get_result();
while ($row = $result_hadir_harian->fetch_assoc()) {
    $day_of_month = (int)$row['hari'];
    if (isset($data_hadir_per_hari[$day_of_month - 1])) $data_hadir_per_hari[$day_of_month - 1] = (int)$row['jumlah_hadir'];
}
$stmt_hadir_harian->close();

// Logika untuk Notifikasi Flash Message
$flash_message_text = '';
$flash_message_type = '';
if (isset($_SESSION['flash_message'])) {
    $flash_message_text = $_SESSION['flash_message'];
    $flash_message_type = $_SESSION['flash_message_type'] ?? 'info';
    unset($_SESSION['flash_message']);
    unset($_SESSION['flash_message_type']);
}

include '../includes/header.php'; // Memuat header HTML
?>

<div class="mb-4 d-flex justify-content-between align-items-center">
    <div>
        <h2><i class="bi bi-speedometer2"></i> Dashboard</h2>
        <p class="text-muted">
            <?php if ($is_admin): ?>
                Ringkasan data absensi dan manajemen pengajuan.
            <?php else: ?>
                Selamat datang, <?php echo htmlspecialchars($current_username); ?>!
            <?php endif; ?>
        </p>
    </div>
    <form method="GET" class="d-flex align-items-center">
        <select name="bulan" class="form-select form-select-sm me-2" onchange="this.form.submit()" aria-label="Pilih Bulan">
            <?php for ($m = 1; $m <= 12; $m++):
                $nama_bulan_loop = DateTime::createFromFormat('!m', $m)->format('F'); ?>
                <option value="<?php echo str_pad($m, 2, '0', STR_PAD_LEFT); ?>" <?php echo ($filter_bulan == str_pad($m, 2, '0', STR_PAD_LEFT)) ? 'selected' : ''; ?>>
                    <?php echo $nama_bulan_loop; ?>
                </option>
            <?php endfor; ?>
        </select>
        <select name="tahun" class="form-select form-select-sm me-2" onchange="this.form.submit()" aria-label="Pilih Tahun">
            <?php for ($y = date('Y'); $y >= date('Y') - 5; $y--): ?>
                <option value="<?php echo $y; ?>" <?php echo ($filter_tahun == $y) ? 'selected' : ''; ?>><?php echo $y; ?></option>
            <?php endfor; ?>
        </select>
        <noscript><button type="submit" class="btn btn-sm btn-secondary">Filter</button></noscript>
    </form>
</div>

<?php if (!empty($flash_message_text)): ?>
    <div class="alert alert-<?php echo htmlspecialchars($flash_message_type); ?> alert-dismissible fade show" role="alert">
        <?php echo htmlspecialchars($flash_message_text); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>


<?php if (!$is_admin): ?>
    <div class="card shadow-sm mb-4 border-start border-primary border-4">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-7">
                    <h4 class="card-title mb-2">Absensi Hari Ini - <?php echo date('l, d F Y'); ?></h4>
                    <p class="card-text mb-0 fs-5">
                        Status Anda:
                        <?php if ($status_absensi_hari_ini_karyawan == "Belum Absen"): ?>
                            <span class="fw-bold text-danger">Belum Melakukan Absensi</span>
                        <?php elseif ($status_absensi_hari_ini_karyawan == "Menunggu Konfirmasi"): ?>
                            <span class="fw-bold text-warning"><i class="bi bi-hourglass-split"></i> Menunggu Konfirmasi Admin</span>
                        <?php elseif ($status_absensi_hari_ini_karyawan == "Pengajuan Ditolak"): ?>
                            <span class="fw-bold text-danger"><i class="bi bi-x-circle-fill"></i> Pengajuan Ditolak</span>. Silakan ajukan ulang atau hubungi admin.
                        <?php else: ?>
                            <span class="fw-bold text-success">
                                <i class="bi bi-check-circle-fill"></i> Sudah Absen (<?php echo htmlspecialchars($status_absensi_hari_ini_karyawan); ?>)
                            </span>
                        <?php endif; ?>
                    </p>
                </div>
                <div class="col-md-5 text-md-end mt-3 mt-md-0">
                    <?php if ($status_absensi_hari_ini_karyawan == "Belum Absen" || $status_absensi_hari_ini_karyawan == "Pengajuan Ditolak"): ?>
                        <div class="btn-group" role="group" aria-label="Aksi Absensi">
                            <a href="proses_clock_in.php" class="btn btn-primary btn-lg">
                                <i class="bi bi-clock-history me-1"></i> Clock In (Hadir)
                            </a>
                            <a href="ajukan_izin_sakit.php" class="btn btn-info btn-lg">
                                <i class="bi bi-journal-medical me-1"></i> Ajukan Izin/Sakit
                            </a>
                        </div>
                    <?php elseif ($status_absensi_hari_ini_karyawan == "Menunggu Konfirmasi"): ?>
                        <button class="btn btn-secondary btn-lg" disabled><i class="bi bi-hourglass-split me-1"></i> Pengajuan Diproses</button>
                    <?php else: ?>
                        <button class="btn btn-success btn-lg" disabled><i class="bi bi-check-all me-1"></i> Absensi Tercatat</button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>


<?php if ($is_admin && !empty($pengajuan_absensi_pending_admin)): ?>
    <div class="card shadow-sm mb-5">
        <div class="card-header bg-warning text-dark">
            <h4 class="mb-0"><i class="bi bi-hourglass-top"></i> Pengajuan Absensi Menunggu Persetujuan (<?php echo count($pengajuan_absensi_pending_admin); ?>)</h4>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-striped align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Nama Karyawan</th>
                            <th>Tanggal Diajukan</th>
                            <th>Status Diajukan</th>
                            <th class="text-center">Bukti</th>
                            <th class="text-center" style="min-width: 180px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pengajuan_absensi_pending_admin as $index => $pengajuan): ?>
                            <tr>
                                <td><?php echo $index + 1; ?></td>
                                <td><?php echo htmlspecialchars($pengajuan['username']); ?> <small class="text-muted">(ID: <?php echo $pengajuan['user_id']; ?>)</small></td>
                                <td><?php echo date('d M Y', strtotime($pengajuan['tanggal'])); ?></td>
                                <td>
                                    <?php
                                    $status_badge_class = 'bg-secondary'; // Default
                                    if ($pengajuan['status_diajukan'] == 'Hadir') $status_badge_class = 'bg-success';
                                    else if ($pengajuan['status_diajukan'] == 'Izin') $status_badge_class = 'bg-info text-dark';
                                    else if ($pengajuan['status_diajukan'] == 'Sakit') $status_badge_class = 'bg-warning text-dark';
                                    ?>
                                    <span class="badge <?php echo $status_badge_class; ?> fs-6"><?php echo htmlspecialchars($pengajuan['status_diajukan']); ?></span>
                                </td>
                                <td class="text-center">
                                    <?php if (!empty($pengajuan['bukti_file'])): ?>
                                        <a href="../<?php echo htmlspecialchars($pengajuan['bukti_file']); ?>" target="_blank" class="btn btn-sm btn-outline-primary" title="Lihat Bukti">
                                            <i class="bi bi-eye-fill"></i>
                                        </a>
                                    <?php else: ?>
                                        -
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <a href="proses_approval_absensi.php?id=<?php echo $pengajuan['id']; ?>&aksi=setujui" class="btn btn-sm btn-success me-1" onclick="return confirm('Anda yakin ingin MENYETUJUI pengajuan absensi untuk <?php echo htmlspecialchars(addslashes($pengajuan['username'])); ?>?')">
                                        <i class="bi bi-check-lg"></i> Setujui
                                    </a>
                                    <a href="proses_approval_absensi.php?id=<?php echo $pengajuan['id']; ?>&aksi=tolak" class="btn btn-sm btn-danger" onclick="return confirm('Anda yakin ingin MENOLAK pengajuan absensi untuk <?php echo htmlspecialchars(addslashes($pengajuan['username'])); ?>?')">
                                        <i class="bi bi-x-lg"></i> Tolak
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php elseif ($is_admin && empty($pengajuan_absensi_pending_admin)): ?>
    <div class="alert alert-info shadow-sm"><i class="bi bi-info-circle-fill"></i> Tidak ada pengajuan absensi yang menunggu persetujuan saat ini.</div>
<?php endif; ?>


<div class="mb-4 <?php echo ($is_admin && !empty($pengajuan_absensi_pending_admin)) ? 'mt-4' : 'mt-5'; ?>">
    <?php if ($is_admin): ?>
        <div class="row g-4">
            <div class="col-md-6 col-lg-6">
                <div class="card text-black bg-indigo shadow-sm h-100">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="card-title mb-0"><?php echo $total_karyawan; ?></h3>
                            <p class="card-text">Total Karyawan Aktif</p>
                        </div>
                        <i class="bi bi-people-fill" style="font-size: 3rem; opacity: 0.6;"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-6">
                <div class="card text-black bg-purple shadow-sm h-100">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="card-title mb-0"><?php echo $total_absensi_keseluruhan; ?></h3>
                            <p class="card-text">Total Semua Absensi (Disetujui)</p>
                        </div>
                        <i class="bi bi-server" style="font-size: 3rem; opacity: 0.6;"></i>
                    </div>
                </div>
            </div>
        </div>
        <h4 class="mt-4 mb-3">Statistik Bulan <?php echo $nama_bulan_filter . ' ' . $filter_tahun; ?> (Disetujui)</h4>
    <?php else: // Untuk Karyawan 
    ?>
        <h4 class="mt-2 mb-3">Statistik Absensi Anda Bulan <?php echo $nama_bulan_filter . ' ' . $filter_tahun; ?> (Disetujui)</h4>
    <?php endif; ?>

    <div class="row g-4">
        <div class="col-lg-3 col-md-6">
            <div class="card text-white bg-success shadow-sm h-100">
                <div class="card-body text-center">
                    <i class="bi bi-check-circle-fill fs-1 mb-2 d-block" style="opacity:0.7;"></i>
                    <h3 class="card-title mb-1"><?php echo $stats_month['Hadir']; ?></h3>
                    <p class="card-text">Total Hadir</p>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card text-dark bg-warning shadow-sm h-100">
                <div class="card-body text-center">
                    <i class="bi bi-journal-medical fs-1 mb-2 d-block" style="opacity:0.7;"></i>
                    <h3 class="card-title mb-1"><?php echo $stats_month['Izin']; ?></h3>
                    <p class="card-text">Total Izin</p>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card text-dark bg-info shadow-sm h-100">
                <div class="card-body text-center">
                    <i class="bi bi-bandaid-fill fs-1 mb-2 d-block" style="opacity:0.7;"></i>
                    <h3 class="card-title mb-1"><?php echo $stats_month['Sakit']; ?></h3>
                    <p class="card-text">Total Sakit</p>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card text-white bg-danger shadow-sm h-100">
                <div class="card-body text-center">
                    <i class="bi bi-x-octagon-fill fs-1 mb-2 d-block" style="opacity:0.7;"></i>
                    <h3 class="card-title mb-1"><?php echo $stats_month['Alpha']; ?></h3>
                    <p class="card-text">Total Alpha</p>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="mt-5">
    <h4>
        <i class="bi bi-calendar3"></i> Rekapitulasi Absensi Bulan
        <?php echo $nama_bulan_filter . ' ' . $filter_tahun; ?> (Disetujui)
    </h4>
    <hr>
    <div class="row g-4">
        <div class="col-lg-8 mb-4 mb-lg-0">
            <div class="card shadow-sm h-100">
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title mb-3"><i class="bi bi-bar-chart-line-fill"></i> Tren Kehadiran Harian (Disetujui)</h5>
                    <div style="position: relative; flex-grow: 1; min-height: 300px;">
                        <canvas id="hadirPerHariChart" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card shadow-sm h-100">
                <div class="card-body d-flex flex-column text-center">
                    <h5 class="card-title mb-3"><i class="bi bi-pie-chart-fill"></i> Proporsi Status Absensi (Disetujui)</h5>
                    <div style="position: relative; flex-grow: 1; min-height: 300px;">
                        <canvas id="rekapBulananChart" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$conn->close(); // Menutup koneksi di akhir
include '../includes/footer.php';
?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // ... (JavaScript untuk Chart.js tetap sama) ...
    document.addEventListener('DOMContentLoaded', function() {
        // Data dari PHP untuk grafik (dari tabel absensi yang disetujui)
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
                        label: 'Jumlah Hadir (Disetujui)',
                        data: dataHarian,
                        backgroundColor: 'rgba(25, 135, 84, 0.7)',
                        borderColor: 'rgba(25, 135, 84, 1)',
                        borderWidth: 1,
                        hoverBackgroundColor: 'rgba(25, 135, 84, 0.9)'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0,
                                stepSize: 1
                            },
                            title: {
                                display: true,
                                text: 'Jumlah Kehadiran'
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
                            position: 'top'
                        }
                    }
                }
            });
        }

        // Grafik 2: Doughnut Chart Rekapitulasi Bulanan
        const ctxRekapBulanan = document.getElementById('rekapBulananChart');
        if (ctxRekapBulanan) {
            new Chart(ctxRekapBulanan, {
                type: 'doughnut',
                data: {
                    labels: labelsBulanan,
                    datasets: [{
                        label: 'Jumlah (Disetujui)',
                        data: dataBulanan,
                        backgroundColor: [
                            'rgba(25, 135, 84, 0.8)',
                            'rgba(255, 193, 7, 0.8)',
                            'rgba(13, 202, 240, 0.8)',
                            'rgba(220, 53, 69, 0.8)'
                        ],
                        borderColor: '#fff',
                        borderWidth: 2,
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 15
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    if (context.parsed !== null) {
                                        label += context.parsed;
                                    }
                                    return label;
                                }
                            }
                        }
                    }
                }
            });
        }
    });
</script>