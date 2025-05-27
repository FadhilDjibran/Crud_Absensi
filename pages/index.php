<?php
// File: pages/index.php

// 1. Memuat file konfigurasi dan otentikasi
// Ini adalah langkah wajib untuk setiap halaman yang terproteksi.
require_once '../config/config.php';
require_once '../auth/auth.php'; 

// 2. Logika untuk Notifikasi (Flash Message)
// Cek apakah ada pesan di session, tampilkan, lalu hapus agar tidak muncul lagi.
$message = '';
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']);
}

// 3. Konfigurasi Paginasi
$batas = 5; // Jumlah data per halaman
$halaman = isset($_GET['halaman']) ? (int)$_GET['halaman'] : 1;
$mulai = ($halaman - 1) * $batas;

// 4. Logika Pencarian & Peran Pengguna (User Role)
// Ini bagian paling dinamis, query akan disesuaikan berdasarkan siapa yang login.
$cari = isset($_GET['cari']) ? $_GET['cari'] : '';
$where_parts = [];
$params = [];
$types = '';

// Filter berdasarkan peran (role)
if ($_SESSION['role'] == 'karyawan') {
    $where_parts[] = "nama = ?";
    $params[] = &$_SESSION['username'];
    $types .= 's';
}

// Filter berdasarkan pencarian
if (!empty($cari)) {
    $where_parts[] = "nama LIKE ?";
    $search_term = "%" . $cari . "%";
    $params[] = &$search_term;
    $types .= 's';
}

// Gabungkan semua kondisi WHERE
$where_clause = '';
if (!empty($where_parts)) {
    $where_clause = ' WHERE ' . implode(' AND ', $where_parts);
}

// Query utama untuk mengambil data absensi
$base_sql = "SELECT * FROM absensi" . $where_clause . " ORDER BY tanggal DESC, id DESC LIMIT ?, ?";
$params[] = &$mulai;
$params[] = &$batas;
$types .= 'ii';

$stmt = $conn->prepare($base_sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

// Query untuk menghitung total data (untuk paginasi)
$count_sql = "SELECT COUNT(*) AS total FROM absensi" . $where_clause;
$count_stmt = $conn->prepare($count_sql);
// Re-bind params tanpa LIMIT
array_pop($params); // hapus $batas
array_pop($params); // hapus $mulai
$types = substr($types, 0, -2); // hapus 'ii'

if(!empty($params)) {
    $count_stmt->bind_param($types, ...$params);
}
$count_stmt->execute();
$total = $count_stmt->get_result()->fetch_assoc()['total'];
$jumlahHalaman = ceil($total / $batas);


// 5. Memuat Header HTML
// Ini akan menampilkan bagian atas halaman, termasuk navbar.
include '../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="m-0"><i class="bi bi-list-ul"></i> Data Absensi</h2>
    <?php if ($_SESSION['role'] == 'admin'): ?>
        <a href="tambah.php" class="btn btn-primary"><i class="bi bi-plus-circle-fill me-2"></i>Tambah Absensi</a>
    <?php endif; ?>
</div>

<?php if ($message): ?>
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <?= htmlspecialchars($message) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
<?php endif; ?>

<div class="card shadow-sm">
    <div class="card-header bg-white">
        <div class="row align-items-center">
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
                            // Logika untuk warna badge status
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

                                // Tombol Aksi (hanya untuk admin)
                                if ($_SESSION['role'] == 'admin') {
                                    echo "<a href='edit.php?id={$row['id']}' class='btn btn-warning btn-sm me-1'><i class='bi bi-pencil-square'></i></a>
                                          <button type='button' class='btn btn-danger btn-sm delete-btn' data-bs-toggle='modal' data-bs-target='#deleteModal' data-id='{$row['id']}' data-nama='" . htmlspecialchars($row['nama']) . "'>
                                              <i class='bi bi-trash-fill'></i>
                                          </button>";
                                } else {
                                    // Tampilkan strip jika bukan admin
                                    echo "-";
                                }
                                
                                echo "</td>
                            </tr>";
                            $no++;
                        }
                    } else {
                        // Pesan jika tidak ada data
                        echo "<tr><td colspan='5' class='text-center text-muted'>Tidak ada data absensi yang ditemukan.</td></tr>";
                    }
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
        var button = event.relatedTarget;
        var id = button.getAttribute('data-id');
        var nama = button.getAttribute('data-nama');
        
        var modalBodyNama = deleteModal.querySelector('#namaKaryawan');
        var confirmButton = deleteModal.querySelector('#confirmDeleteBtn');
        
        modalBodyNama.textContent = nama;
        confirmButton.href = 'hapus.php?id=' + id;
    });
});
</script>


<?php
// 9. Memuat Footer HTML
include '../includes/footer.php'; 
?>