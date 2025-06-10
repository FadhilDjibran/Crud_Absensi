<?php
// File: halaman/laporan_absensi.php

// Memuat file proses
require_once '../fungsi/proses_laporan_absensi.php';

// Memuat header HTML
include '../includes/header.php';
?>

<div class="mb-4">
    <h2><i class="bi bi-file-earmark-text-fill"></i> <?php echo htmlspecialchars($page_title); ?></h2>
    <p class="text-muted">Gunakan filter di bawah ini untuk menghasilkan laporan absensi yang spesifik.</p>
</div>

<!-- Form Filter Laporan -->
<div class="card shadow-sm mb-4">
    <div class="card-header">
        <strong>Filter Laporan</strong>
    </div>
    <div class="card-body">
        <form method="GET" action="laporan_absensi.php" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label for="user_id" class="form-label">Pilih Pengguna</label>
                <select name="user_id" id="user_id" class="form-select">
                    <option value="">Semua Pengguna</option>
                    
                    <?php if (!empty($users_list['admin'])): ?>
                    <optgroup label="Admin">
                        <?php foreach ($users_list['admin'] as $user): ?>
                            <option value="<?php echo $user['id']; ?>" <?php echo ($filter_user_id == $user['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($user['username']); ?>
                            </option>
                        <?php endforeach; ?>
                    </optgroup>
                    <?php endif; ?>

                    <?php if (!empty($users_list['karyawan'])): ?>
                    <optgroup label="Karyawan">
                        <?php foreach ($users_list['karyawan'] as $user): ?>
                            <option value="<?php echo $user['id']; ?>" <?php echo ($filter_user_id == $user['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($user['username']); ?>
                            </option>
                        <?php endforeach; ?>
                    </optgroup>
                    <?php endif; ?>

                </select>
            </div>
            <div class="col-md-3">
                <label for="tanggal_mulai" class="form-label">Dari Tanggal</label>
                <input type="date" name="tanggal_mulai" id="tanggal_mulai" class="form-control" value="<?php echo htmlspecialchars($filter_tanggal_mulai); ?>">
            </div>
            <div class="col-md-3">
                <label for="tanggal_selesai" class="form-label">Sampai Tanggal</label>
                <input type="date" name="tanggal_selesai" id="tanggal_selesai" class="form-control" value="<?php echo htmlspecialchars($filter_tanggal_selesai); ?>">
            </div>
            <div class="col-md-2">
                <button type="submit" name="tampilkan" value="1" class="btn btn-primary w-100"><i class="bi bi-funnel-fill"></i> Tampilkan</button>
            </div>
        </form>
    </div>
</div>

<!-- Menampilkan hasil laporan -->
<?php if ($filter_aktif): ?>
<div class="card shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center">
        <strong>Hasil Laporan</strong>
        <?php if (!empty($laporan_data)): // Hanya tampilkan tombol ekspor jika ada data ?>
        <a href="../fungsi/proses_ekspor_laporan.php?<?= http_build_query($_GET) ?>" class="btn btn-sm btn-success">
            <i class="bi bi-file-earmark-excel-fill"></i> Ekspor ke CSV
        </a>
        <?php endif; ?>
    </div>
    <div class="card-body">
        <!-- Ringkasan Statistik -->
        <div class="row g-3 mb-4 text-center">
            <div class="col"><div class="p-3 bg-success text-white rounded"><h5>Hadir<br><?= $stats_laporan['Hadir'] ?></h5></div></div>
            <div class="col"><div class="p-3 bg-warning text-dark rounded"><h5>Izin<br><?= $stats_laporan['Izin'] ?></h5></div></div>
            <div class="col"><div class="p-3 bg-info text-dark rounded"><h5>Sakit<br><?= $stats_laporan['Sakit'] ?></h5></div></div>
            <div class="col"><div class="p-3 bg-danger text-white rounded"><h5>Alpha<br><?= $stats_laporan['Alpha'] ?></h5></div></div>
        </div>

        <!-- Tabel Detail Laporan -->
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>No</th>
                        <th>Nama Karyawan</th>
                        <th class="text-center">Tanggal</th>
                        <th class="text-center">Jam Masuk</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Kondisi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($laporan_data)): ?>
                        <?php foreach($laporan_data as $index => $item): ?>
                            <tr>
                                <td><?= $mulai + $index + 1 ?></td>
                                <td><?= htmlspecialchars($item['username'] ?? $item['nama']) ?></td>
                                <td class="text-center"><?= date('d F Y', strtotime($item['tanggal'])) ?></td>
                                <td class="text-center"><?= $item['jam_masuk'] ? date('H:i', strtotime($item['jam_masuk'])) : '-' ?></td>
                                <td class="text-center">
                                    <?php
                                    $status_color = 'secondary';
                                    if ($item['status'] == 'Hadir') $status_color = 'success';
                                    else if ($item['status'] == 'Izin') $status_color = 'warning text-dark';
                                    else if ($item['status'] == 'Sakit') $status_color = 'info text-dark';
                                    else if ($item['status'] == 'Alpha') $status_color = 'danger';
                                    ?>
                                    <span class="badge bg-<?= $status_color ?>"><?= htmlspecialchars($item['status']) ?></span>
                                </td>
                                <td class="text-center">
                                    <?php
                                    $kondisi_badge = '-';
                                    if ($item['status'] == 'Hadir' && !empty($item['kondisi_masuk'])) {
                                        if ($item['kondisi_masuk'] == 'Tepat Waktu') {
                                            $kondisi_badge = "<span class='badge bg-success'>Tepat Waktu</span>";
                                        } elseif ($item['kondisi_masuk'] == 'Terlambat') {
                                            $kondisi_badge = "<span class='badge bg-danger'>Terlambat</span>";
                                        }
                                    }
                                    echo $kondisi_badge;
                                    ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="6" class="text-center">Tidak ada data yang cocok dengan filter Anda.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <!-- Paginasi -->
    <div class="card-footer bg-white">
        <?php
        if ($jumlahHalaman > 1) {
            $parameter_lainnya = [
                'user_id' => $filter_user_id,
                'tanggal_mulai' => $filter_tanggal_mulai,
                'tanggal_selesai' => $filter_tanggal_selesai,
                'tampilkan' => 1
            ];
            $parameter_query = http_build_query($parameter_lainnya);
            $url_awal = 'laporan_absensi.php?' . $parameter_query . '&halaman=';

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
<?php endif; ?>

<?php 
$conn->close(); // Menutup koneksi
include '../includes/footer.php'; 
?>
