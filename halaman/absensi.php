<?php
// File: halaman/absensi.php

// Memuat file proses
require_once '../fungsi/proses_absensi.php';

// Memuat header HTML
include '../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="m-0"><i class="bi bi-list-ul"></i> <?php echo htmlspecialchars($page_title); ?></h2>
    <?php if ($_SESSION['role'] == 'admin'): ?>
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
                <!-- Form pencarian -->
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
                        <th class="text-center">Tanggal</th>
                        <th class="text-center">Jam Masuk</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Kondisi</th>
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

                            $kondisi_badge = '-'; // Default
                            if ($row['status'] == 'Hadir' && !empty($row['kondisi_masuk'])) {
                                if ($row['kondisi_masuk'] == 'Tepat Waktu') {
                                    $kondisi_badge = "<span class='badge bg-success'>Tepat Waktu</span>";
                                } elseif ($row['kondisi_masuk'] == 'Terlambat') {
                                    $kondisi_badge = "<span class='badge bg-danger'>Terlambat</span>";
                                }
                            }
                            
                            echo "<tr>
                                <td>{$no}</td>
                                <td>" . htmlspecialchars($row['username'] ?? $row['nama']) . "</td>
                                <td class='text-center'>" . date('d F Y', strtotime($row['tanggal'])) . "</td>
                                <td class='text-center'>" . ($row['jam_masuk'] ? date('H:i', strtotime($row['jam_masuk'])) : '-') . "</td>
                                <td class='text-center'><span class='badge bg-{$status_color}'>" . htmlspecialchars($row['status']) . "</span></td>
                                <td class='text-center'>{$kondisi_badge}</td>
                                <td class='text-center'>";
                                if ($_SESSION['role'] == 'admin') {
                                    echo "<a href='edit_absensi.php?id={$row['id']}' class='btn btn-warning btn-sm me-1' title='Edit'><i class='bi bi-pencil-square'></i></a>
                                          <button type='button' class='btn btn-danger btn-sm delete-btn' title='Hapus' data-bs-toggle='modal' data-bs-target='#deleteModal' data-id='{$row['id']}' data-nama='" . htmlspecialchars($row['username'] ?? $row['nama']) . "'>
                                              <i class='bi bi-trash-fill'></i>
                                          </button>";
                                } else { echo "-"; }
                                echo "</td>
                            </tr>";
                            $no++;
                        }
                    } else { echo "<tr><td colspan='7' class='text-center text-muted'>Tidak ada data absensi yang ditemukan.</td></tr>"; } // colspan diubah menjadi 7
                    ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer bg-white">
        <?php
        // Paginasi
        if ($jumlahHalaman > 1) {
            $parameter_lainnya = [];
            if (!empty($cari)) {
                $parameter_lainnya['cari'] = $cari;
            }
            $parameter_query = http_build_query($parameter_lainnya);
            $url_awal = 'absensi.php?' . $parameter_query . '&halaman=';

            echo '<nav aria-label="Navigasi Halaman"><ul class="pagination justify-content-center m-0">';

            // Tombol "Sebelumnya"
            $disabled_sebelumnya = ($halaman <= 1) ? "disabled" : "";
            $halaman_sebelumnya = $halaman - 1;
            echo "<li class='page-item {$disabled_sebelumnya}'><a class='page-link' href='{$url_awal}{$halaman_sebelumnya}'>Sebelumnya</a></li>";

            // Aturan untuk menampilkan nomor halaman
            $jarak = 2;
            $mulai_loop = max(1, $halaman - $jarak);
            $selesai_loop = min($jumlahHalaman, $halaman + $jarak);

            if ($mulai_loop > 1) {
                echo "<li class='page-item'><a class='page-link' href='{$url_awal}1'>1</a></li>";
                if ($mulai_loop > 2) {
                    echo "<li class='page-item disabled'><span class='page-link'>...</span></li>";
                }
            }

            for ($i = $mulai_loop; $i <= $selesai_loop; $i++) {
                $active_class = ($i == $halaman) ? "active" : "";
                echo "<li class='page-item {$active_class}'><a class='page-link' href='{$url_awal}{$i}'>{$i}</a></li>";
            }

            if ($selesai_loop < $jumlahHalaman) {
                if ($selesai_loop < $jumlahHalaman - 1) {
                    echo "<li class='page-item disabled'><span class='page-link'>...</span></li>";
                }
                echo "<li class='page-item'><a class='page-link' href='{$url_awal}{$jumlahHalaman}'>{$jumlahHalaman}</a></li>";
            }

            // Tombol "Berikutnya"
            $disabled_berikutnya = ($halaman >= $jumlahHalaman) ? "disabled" : "";
            $halaman_berikutnya = $halaman + 1;
            echo "<li class='page-item {$disabled_berikutnya}'><a class='page-link' href='{$url_awal}{$halaman_berikutnya}'>Berikutnya</a></li>";

            echo '</ul></nav>';
        }
        ?>
    </div>
</div>

<!-- Konfirmasi Hapus -->
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
            if(confirmButton) confirmButton.href = '../fungsi/proses_hapus_absensi.php?id=' + id; 
        });
    }
});
</script>

<?php 
$conn->close(); // Menutup koneksi
include '../includes/footer.php'; 
?>
