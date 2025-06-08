<?php
// File: halaman/absensi.php
// Menampilkan halaman daftar data absensi.

// Memuat file proses yang akan menyiapkan semua variabel
require_once '../fungsi/proses_absensi.php';

// Memuat header HTML
include '../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="m-0"><i class="bi bi-list-ul"></i> <?php echo htmlspecialchars($page_title); ?></h2>
    <?php if ($_SESSION['role'] == 'admin'): ?>
        <!-- PERBAIKAN: Mengarah ke file tambah_absensi.php yang baru -->
        <a href="tambah_absensi.php" class="btn btn-primary"><i class="bi bi-plus-circle-fill me-2"></i>Tambah Absensi Manual</a>
    <?php endif; ?>
</div>

<!-- Menampilkan flash message -->
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
                <!-- Form pencarian action ke halaman ini sendiri -->
                <form method="GET" action="absensi.php" class="d-flex">
                    <input type="text" name="cari" class="form-control form-control-sm me-2" placeholder="Cari nama karyawan..." value="<?= htmlspecialchars($cari) ?>">
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
                        <th>Nama Karyawan</th>
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
                            else if ($row['status'] == 'Izin') $status_color = 'warning text-dark';
                            else if ($row['status'] == 'Sakit') $status_color = 'info text-dark';
                            else if ($row['status'] == 'Alpha') $status_color = 'danger';
                            
                            echo "<tr>
                                <td>{$no}</td>
                                <td>" . htmlspecialchars($row['username'] ?? $row['nama']) . "</td>
                                <td>" . date('d F Y', strtotime($row['tanggal'])) . "</td>
                                <td><span class='badge bg-{$status_color}'>" . htmlspecialchars($row['status']) . "</span></td>
                                <td class='text-center'>";
                                if ($_SESSION['role'] == 'admin') {
                                    // Tautan mengarah ke file edit_absensi.php baru
                                    echo "<a href='edit_absensi.php?id={$row['id']}' class='btn btn-warning btn-sm me-1' title='Edit'><i class='bi bi-pencil-square'></i></a>
                                          <button type='button' class='btn btn-danger btn-sm delete-btn' title='Hapus' data-bs-toggle='modal' data-bs-target='#deleteModal' data-id='{$row['id']}' data-nama='" . htmlspecialchars($row['username'] ?? $row['nama']) . "'>
                                              <i class='bi bi-trash-fill'></i>
                                          </button>";
                                } else { echo "-"; }
                                echo "</td>
                            </tr>";
                            $no++;
                        }
                    } else { echo "<tr><td colspan='5' class='text-center text-muted'>Tidak ada data absensi yang ditemukan.</td></tr>"; }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer bg-white">
        <?php if(isset($jumlahHalaman) && $jumlahHalaman > 1): ?>
        <nav>
            <ul class="pagination justify-content-center m-0">
                <?php for ($i = 1; $i <= $jumlahHalaman; $i++): ?>
                    <li class="page-item <?= ($i == $halaman) ? 'active' : '' ?>">
                        <!-- Tautan paginasi mengarah ke halaman ini sendiri -->
                        <a class="page-link" href="?halaman=<?= $i ?>&cari=<?= urlencode($cari) ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>
        <?php endif; ?>
    </div>
</div>

<!-- Modal Konfirmasi Hapus -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteModalLabel"><i class="bi bi-exclamation-triangle-fill"></i> Konfirmasi</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Apakah Anda yakin ingin menghapus data absensi untuk <strong id="namaKaryawanModal"></strong>?
                <p class="text-muted small mt-2">Tindakan ini tidak dapat dibatalkan.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <a href="#" id="confirmDeleteBtnModal" class="btn btn-danger">Ya, Hapus</a>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var deleteModal = document.getElementById('deleteModal');
    if (deleteModal) {
        deleteModal.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget; 
            var id = button.getAttribute('data-id'); 
            var nama = button.getAttribute('data-nama');
            var modalBodyNama = deleteModal.querySelector('#namaKaryawanModal'); 
            var confirmButton = deleteModal.querySelector('#confirmDeleteBtnModal'); 
            if(modalBodyNama) modalBodyNama.textContent = nama;
            // Tautan hapus mengarah ke file proses_hapus_absensi.php baru di folder fungsi
            if(confirmButton) confirmButton.href = '../fungsi/proses_hapus_absensi.php?id=' + id; 
        });
    }
});
</script>

<?php 
// Menutup statement dan koneksi, lalu memuat footer
if (isset($stmt)) $stmt->close();
if (isset($stmt_hitung)) $stmt_hitung->close();
$conn->close();
include '../includes/footer.php'; 
?>
