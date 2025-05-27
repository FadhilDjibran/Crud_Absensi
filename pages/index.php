<?php
// pages/index.php
require_once '../config/config.php';
require_once '../auth/auth.php'; // Proteksi halaman

// Ambil pesan notifikasi dari session (flash message)
$message = '';
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']);
}

// Konfigurasi Paginasi dan Pencarian (sama seperti sebelumnya)
$batas = 5;
$halaman = isset($_GET['halaman']) ? (int)$_GET['halaman'] : 1;
$mulai = ($halaman - 1) * $batas;

$cari = isset($_GET['cari']) ? $_GET['cari'] : '';
$base_sql = "SELECT * FROM absensi";
$count_sql = "SELECT COUNT(*) AS total FROM absensi";
$params = [];
$types = '';

if (!empty($cari)) {
    $base_sql .= " WHERE nama LIKE ?";
    $count_sql .= " WHERE nama LIKE ?";
    $search_term = "%" . $cari . "%";
    $params[] = &$search_term;
    $types .= 's';
}

$base_sql .= " ORDER BY tanggal DESC, id DESC LIMIT ?, ?";
$params[] = &$mulai;
$params[] = &$batas;
$types .= 'ii';

$stmt = $conn->prepare($base_sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

if (!empty($cari)) {
    $total_stmt = $conn->prepare($count_sql);
    $total_stmt->bind_param('s', $search_term);
    $total_stmt->execute();
    $total_result = $total_stmt->get_result();
} else {
    $total_result = $conn->query($count_sql);
}
$total = $total_result->fetch_assoc()['total'];
$jumlahHalaman = ceil($total / $batas);

include '../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="m-0">Dashboard Absensi</h2>
    <a href="tambah.php" class="btn btn-primary"><i class="bi bi-plus-circle-fill me-2"></i>Tambah Absensi</a>
</div>

<?php if ($message): ?>
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <?= htmlspecialchars($message) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
<?php endif; ?>

<div class="card shadow-sm">
    <div class="card-header">
        <div class="row">
            <div class="col-md-6">
                <strong><i class="bi bi-table me-2"></i>Daftar Kehadiran Karyawan</strong>
            </div>
            <div class="col-md-6">
                <form method="GET" class="d-flex">
                    <input type="text" name="cari" class="form-control form-control-sm me-2" placeholder="Cari berdasarkan nama..." value="<?= htmlspecialchars($cari) ?>">
                    <button type="submit" class="btn btn-sm btn-outline-secondary"><i class="bi bi-search"></i></button>
                </form>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>Tanggal</th>
                        <th>Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $no = $mulai + 1;
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            // Tentukan warna badge berdasarkan status
                            $status_color = 'secondary';
                            if ($row['status'] == 'Hadir') $status_color = 'success';
                            if ($row['status'] == 'Izin') $status_color = 'warning text-dark';
                            if ($row['status'] == 'Sakit') $status_color = 'info text-dark';
                            if ($row['status'] == 'Alpha') $status_color = 'danger';
                            
                            echo "<tr>
                                <td>{$no}</td>
                                <td>" . htmlspecialchars($row['nama']) . "</td>
                                <td>" . date('d F Y', strtotime($row['tanggal'])) . "</td>
                                <td><span class='badge bg-{$status_color}'>" . htmlspecialchars($row['status']) . "</span></td>
                                <td class='text-center'>
                                    <a href='edit.php?id={$row['id']}' class='btn btn-warning btn-sm'><i class='bi bi-pencil-square'></i></a>
                                    <button type='button' class='btn btn-danger btn-sm delete-btn' data-bs-toggle='modal' data-bs-target='#deleteModal' data-id='{$row['id']}' data-nama='" . htmlspecialchars($row['nama']) . "'> 
                                    <i class='bi bi-trash-fill'></i>
                                    </button>
                                </td>
                            </tr>";
                            $no++;
                        }
                    } else {
                        echo "<tr><td colspan='5' class='text-center text-muted'>Tidak ada data absensi yang ditemukan.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer">
        <?php if($total > $batas): ?>
        <nav>
            <ul class="pagination justify-content-center m-0">
                <?php for ($i = 1; $i <= $jumlahHalaman; $i++): ?>
                    <li class="page-item <?= ($i == $halaman) ? 'active' : '' ?>">
                        <a class="page-link" href="?halaman=<?= $i ?>&cari=<?= urlencode($cari) ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>
        <?php endif; ?>
    </div>
</div>

<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title" id="deleteModalLabel"><i class="bi bi-exclamation-triangle-fill"></i> Konfirmasi Penghapusan</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        Apakah Anda yakin ingin menghapus data absensi untuk <strong id="namaKaryawan"></strong>?
        <p class="text-muted small mt-2">Tindakan ini tidak dapat dibatalkan.</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <a href="#" id="confirmDeleteBtn" class="btn btn-danger">Ya, Hapus</a>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var deleteModal = document.getElementById('deleteModal');
    deleteModal.addEventListener('show.bs.modal', function (event) {
        // Tombol yang memicu modal
        var button = event.relatedTarget;
        
        // Ambil data-id dari tombol
        var id = button.getAttribute('data-id');
        var nama = button.getAttribute('data-nama');
        
        // Update konten modal
        var modalBodyNama = deleteModal.querySelector('#namaKaryawan');
        var confirmButton = deleteModal.querySelector('#confirmDeleteBtn');
        
        modalBodyNama.textContent = nama;
        confirmButton.href = 'hapus.php?id=' + id;
    });
});
</script>

<?php include '../includes/footer.php'; ?>