<?php
// File: halaman/dasbor.php
// File ini hanya bertanggung jawab untuk menampilkan data yang sudah disiapkan.

// Langkah 1: Memuat file logika yang akan menyiapkan semua variabel yang dibutuhkan.
// Path ini diasumsikan dari lokasi file tampilan di dalam folder 'halaman'.
require_once '../fungsi/logika_dasbor.php';

// Langkah 2: Memuat header HTML.
// Semua variabel seperti $page_title sudah tersedia dari file logika.
include '../includes/header.php';
?>

<div class="mb-4 d-flex justify-content-between align-items-center">
    <div>
        <h2><i class="bi bi-speedometer2"></i> <?php echo htmlspecialchars($page_title); ?></h2>
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
                            <!-- Link ini perlu disesuaikan jika Anda juga mengubah nama file proses -->
                            <a href="proses_pengajuan_hadir.php" class="btn btn-primary btn-lg">
                                <i class="bi bi-clock-history me-1"></i> Clock In (Hadir)
                            </a>
                            <a href="ajukan_izin.php" class="btn btn-info btn-lg">
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
                                    $status_badge_class = 'bg-secondary';
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
                                     <!-- PERBAIKAN: Tautan sekarang mengarah ke ../fungsi/proses_persetujuan_absensi.php -->
                                    <a href="../fungsi/proses_persetujuan_absensi.php?id=<?php echo $pengajuan['id']; ?>&aksi=setujui" class="btn btn-sm btn-success me-1" onclick="return confirm('Anda yakin ingin MENYETUJUI pengajuan absensi untuk <?php echo htmlspecialchars(addslashes($pengajuan['username'])); ?>?')">
                                        <i class="bi bi-check-lg"></i> Setujui
                                    </a>
                                    <a href="../fungsi/proses_persetujuan_absensi.php?id=<?php echo $pengajuan['id']; ?>&aksi=tolak" class="btn btn-sm btn-danger" onclick="return confirm('Anda yakin ingin MENOLAK pengajuan absensi untuk <?php echo htmlspecialchars(addslashes($pengajuan['username'])); ?>?')">
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
// Langkah 3: Menutup koneksi database dan memuat footer.
$conn->close(); 
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