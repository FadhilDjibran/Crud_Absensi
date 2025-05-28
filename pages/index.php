<?php
// File: pages/index.php (Versi Perbaikan)
require_once '../config/config.php';
require_once '../auth/auth.php'; 

$flash_message_text = '';
if (isset($_SESSION['flash_message'])) {
    $flash_message_text = $_SESSION['flash_message'];
    $flash_message_type = $_SESSION['flash_message_type'] ?? 'success'; 
    unset($_SESSION['flash_message']);
    unset($_SESSION['flash_message_type']);
}

$batas = 10;
$halaman = isset($_GET['halaman']) ? (int)$_GET['halaman'] : 1;
$mulai = ($halaman - 1) * $batas;

// Logika Pencarian & Peran Pengguna (User Role)
$cari = isset($_GET['cari']) ? $_GET['cari'] : '';
$where_parts = [];
$params = [];
$types = '';

// BARU: Filter berdasarkan user_id untuk karyawan
if ($_SESSION['role'] == 'karyawan') {
    $where_parts[] = "absensi.user_id = ?";
    $params[] = &$_SESSION['user_id']; 
    $types .= 'i'; // Tipe data integer
}

if (!empty($cari)) {
    $where_parts[] = "absensi.nama LIKE ?"; // Pencarian tetap pada kolom nama
    $search_term = "%" . $cari . "%";
    $params[] = &$search_term;
    $types .= 's';
}

$where_clause = '';
if (!empty($where_parts)) {
    $where_clause = ' WHERE ' . implode(' AND ', $where_parts);
}

// BARU: Menggunakan LEFT JOIN untuk mengambil username dari tabel users
$base_sql = "SELECT absensi.*, users.username FROM absensi LEFT JOIN users ON absensi.user_id = users.id" . $where_clause . " ORDER BY absensi.tanggal DESC, absensi.id DESC LIMIT ?, ?";
$params_for_data_query = $params; 
$params_for_data_query[] = &$mulai;
$params_for_data_query[] = &$batas;
$types_for_data_query = $types . 'ii';

$stmt = $conn->prepare($base_sql);
if (!empty($params_for_data_query)) {
    $stmt->bind_param($types_for_data_query, ...$params_for_data_query);
}
$stmt->execute();
$result = $stmt->get_result();

$count_sql = "SELECT COUNT(absensi.id) AS total FROM absensi" . $where_clause;
$count_stmt = $conn->prepare($count_sql);
if(!empty($params)) { 
     $count_stmt->bind_param($types, ...$params);
}
$count_stmt->execute();
$total = $count_stmt->get_result()->fetch_assoc()['total'];
$jumlahHalaman = ceil($total / $batas);

$page_title = "Data Absensi";
include '../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="m-0"><i class="bi bi-list-ul"></i> Data Absensi</h2>
    <?php if ($_SESSION['role'] == 'admin'): ?>
        <a href="tambah.php" class="btn btn-primary"><i class="bi bi-plus-circle-fill me-2"></i>Tambah Absensi</a>
    <?php endif; ?>
</div>
<?php if (!empty($flash_message_text)): ?>
<div class="alert alert-<?php echo htmlspecialchars($flash_message_type); ?> alert-dismissible fade show" role="alert">
    <?php echo htmlspecialchars($flash_message_text); ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
<?php endif; ?>
<div class="card shadow-sm">
    <div class="card-header bg-white">
        <div class="row align-items-center">
            <div class="col-md-6"><strong><i class="bi bi-table me-2"></i>Daftar Kehadiran</strong></div>
            <div class="col-md-6">
                <form method="GET" class="d-flex">
                    <input type="text" name="cari" class="form-control form-control-sm me-2" placeholder="Cari nama..." value="<?= htmlspecialchars($cari) ?>">
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
                                <td class='text-center'>";
                                if ($_SESSION['role'] == 'admin') {
                                    echo "<a href='edit.php?id={$row['id']}' class='btn btn-warning btn-sm me-1'><i class='bi bi-pencil-square'></i></a>
                                          <button type='button' class='btn btn-danger btn-sm delete-btn' data-bs-toggle='modal' data-bs-target='#deleteModal' data-id='{$row['id']}' data-nama='" . htmlspecialchars($row['nama']) . "'>
                                              <i class='bi bi-trash-fill'></i>
                                          </button>";
                                } else { echo "-"; }
                                echo "</td>
                            </tr>";
                            $no++;
                        }
                    } else { echo "<tr><td colspan='5' class='text-center text-muted'>Tidak ada data.</td></tr>"; }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer bg-white">
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
<div class="modal fade" id="deleteModal" tabindex="-1"><div class="modal-dialog"><div class="modal-content"><div class="modal-header bg-danger text-white"><h5 class="modal-title" id="deleteModalLabel"><i class="bi bi-exclamation-triangle-fill"></i> Konfirmasi</h5><button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button></div><div class="modal-body">Apakah Anda yakin ingin menghapus data untuk <strong id="namaKaryawanModal"></strong>?<p class="text-muted small mt-2">Tindakan ini tidak dapat dibatalkan.</p></div><div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button><a href="#" id="confirmDeleteBtnModal" class="btn btn-danger">Ya, Hapus</a></div></div></div></div>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var deleteModal = document.getElementById('deleteModal');
    if (deleteModal) {
        deleteModal.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget; var id = button.getAttribute('data-id'); var nama = button.getAttribute('data-nama');
            var modalBodyNama = deleteModal.querySelector('#namaKaryawanModal'); 
            var confirmButton = deleteModal.querySelector('#confirmDeleteBtnModal'); 
            if(modalBodyNama) modalBodyNama.textContent = nama;
            if(confirmButton) confirmButton.href = 'hapus.php?id=' + id; 
        });
    }
});
</script>

<?php include '../includes/footer.php'; ?>