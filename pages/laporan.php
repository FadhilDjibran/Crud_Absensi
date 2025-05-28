<?php
// File: pages/laporan.php
require_once '../config/config.php';
require_once '../auth/auth.php';

// Fitur ini hanya untuk admin
if ($_SESSION['role'] !== 'admin') {
    $_SESSION['flash_message'] = "Anda tidak memiliki hak akses untuk halaman ini.";
    $_SESSION['flash_message_type'] = "danger";
    header("Location: dashboard.php");
    exit;
}

$page_title = "Laporan Absensi";

// Ambil daftar karyawan untuk dropdown filter
$users_list = [];
$users_result = $conn->query("SELECT id, username FROM users ORDER BY username ASC");
if ($users_result) {
    $users_list = $users_result->fetch_all(MYSQLI_ASSOC);
}

// Inisialisasi variabel
$laporan_data = [];
$stats_laporan = ['Hadir' => 0, 'Izin' => 0, 'Sakit' => 0, 'Alpha' => 0];
$filter_aktif = false;

// Cek jika form filter disubmit
if (isset($_GET['tampilkan'])) {
    $filter_aktif = true;
    
    // Ambil nilai filter dari form
    $user_id = $_GET['user_id'];
    $tanggal_mulai = $_GET['tanggal_mulai'];
    $tanggal_selesai = $_GET['tanggal_selesai'];

    // Bangun query dinamis berdasarkan filter
    $where_parts = [];
    $params = [];
    $types = '';

    if (!empty($user_id)) {
        $where_parts[] = "absensi.user_id = ?";
        $params[] = &$user_id;
        $types .= 'i';
    }
    if (!empty($tanggal_mulai)) {
        $where_parts[] = "absensi.tanggal >= ?";
        $params[] = &$tanggal_mulai;
        $types .= 's';
    }
    if (!empty($tanggal_selesai)) {
        $where_parts[] = "absensi.tanggal <= ?";
        $params[] = &$tanggal_selesai;
        $types .= 's';
    }

    $where_clause = '';
    if (!empty($where_parts)) {
        $where_clause = ' WHERE ' . implode(' AND ', $where_parts);
    }

    // Query untuk mengambil data detail laporan
    $sql_laporan = "SELECT absensi.*, users.username FROM absensi LEFT JOIN users ON absensi.user_id = users.id" . $where_clause . " ORDER BY absensi.tanggal DESC, absensi.id DESC";
    $stmt_laporan = $conn->prepare($sql_laporan);
    if (!empty($params)) {
        $stmt_laporan->bind_param($types, ...$params);
    }
    $stmt_laporan->execute();
    $laporan_data = $stmt_laporan->get_result()->fetch_all(MYSQLI_ASSOC);

    // Query untuk mengambil statistik ringkasan
    $sql_stats = "SELECT status, COUNT(id) as jumlah FROM absensi" . $where_clause . " GROUP BY status";
    $stmt_stats = $conn->prepare($sql_stats);
    if (!empty($params)) {
        $stmt_stats->bind_param($types, ...$params);
    }
    $stmt_stats->execute();
    $result_stats = $stmt_stats->get_result();
    while($row = $result_stats->fetch_assoc()) {
        $stats_laporan[$row['status']] = $row['jumlah'];
    }
}

include '../includes/header.php';
?>

<div class="mb-4">
    <h2><i class="bi bi-file-earmark-text-fill"></i> <?php echo htmlspecialchars($page_title); ?></h2>
    <p class="text-muted">Gunakan filter di bawah ini untuk menghasilkan laporan absensi yang spesifik.</p>
</div>

<div class="card shadow-sm mb-4">
    <div class="card-header">
        <strong>Filter Laporan</strong>
    </div>
    <div class="card-body">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label for="user_id" class="form-label">Pilih Karyawan</label>
                <select name="user_id" id="user_id" class="form-select">
                    <option value="">Semua Karyawan</option>
                    <?php foreach($users_list as $user): ?>
                        <option value="<?php echo $user['id']; ?>" <?php echo (isset($_GET['user_id']) && $_GET['user_id'] == $user['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($user['username']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label for="tanggal_mulai" class="form-label">Dari Tanggal</label>
                <input type="date" name="tanggal_mulai" id="tanggal_mulai" class="form-control" value="<?php echo isset($_GET['tanggal_mulai']) ? htmlspecialchars($_GET['tanggal_mulai']) : ''; ?>">
            </div>
            <div class="col-md-3">
                <label for="tanggal_selesai" class="form-label">Sampai Tanggal</label>
                <input type="date" name="tanggal_selesai" id="tanggal_selesai" class="form-control" value="<?php echo isset($_GET['tanggal_selesai']) ? htmlspecialchars($_GET['tanggal_selesai']) : ''; ?>">
            </div>
            <div class="col-md-2">
                <button type="submit" name="tampilkan" value="1" class="btn btn-primary w-100">Tampilkan</button>
            </div>
        </form>
    </div>
</div>

<?php if ($filter_aktif): ?>
<div class="card shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center">
        <strong>Hasil Laporan</strong>
        <a href="export.php?<?= http_build_query($_GET) ?>" class="btn btn-sm btn-success">
            <i class="bi bi-file-earmark-excel-fill"></i> Ekspor ke CSV
        </a>
    </div>
    <div class="card-body">
        <div class="row g-3 mb-4">
            <div class="col"><div class="p-3 bg-success text-white rounded text-center"><h5>Hadir<br><?= $stats_laporan['Hadir'] ?></h5></div></div>
            <div class="col"><div class="p-3 bg-warning text-dark rounded text-center"><h5>Izin<br><?= $stats_laporan['Izin'] ?></h5></div></div>
            <div class="col"><div class="p-3 bg-info text-dark rounded text-center"><h5>Sakit<br><?= $stats_laporan['Sakit'] ?></h5></div></div>
            <div class="col"><div class="p-3 bg-danger text-white rounded text-center"><h5>Alpha<br><?= $stats_laporan['Alpha'] ?></h5></div></div>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead class="table-dark">
                    <tr><th>No</th><th>Nama</th><th>Tanggal</th><th>Status</th></tr>
                </thead>
                <tbody>
                    <?php if (!empty($laporan_data)): ?>
                        <?php foreach($laporan_data as $index => $item): ?>
                            <tr>
                                <td><?= $index + 1 ?></td>
                                <td><?= htmlspecialchars($item['username'] ?? $item['nama']) ?></td>
                                <td><?= date('d F Y', strtotime($item['tanggal'])) ?></td>
                                <td><?= htmlspecialchars($item['status']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="4" class="text-center">Tidak ada data yang cocok dengan filter Anda.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php endif; ?>


<?php include '../includes/footer.php'; ?>