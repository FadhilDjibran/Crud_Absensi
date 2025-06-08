<?php
// File: halaman/laporan_absensi.php
// File ini bertanggung jawab untuk menampilkan halaman laporan absensi.

// Memuat file proses yang akan menyiapkan semua variabel yang dibutuhkan
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
        <!-- Form action ke halaman ini sendiri untuk memuat ulang dengan filter -->
        <form method="GET" action="laporan_absensi.php" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label for="user_id" class="form-label">Pilih Pengguna</label>
                <select name="user_id" id="user_id" class="form-select">
                    <option value="">Semua Pengguna</option>
                    
                    <!-- PERBAIKAN: Dropdown dikelompokkan berdasarkan peran -->
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

<!-- Bagian untuk menampilkan hasil laporan jika filter aktif -->
<?php if ($filter_aktif): ?>
<div class="card shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center">
        <strong>Hasil Laporan</strong>
        <?php if (!empty($laporan_data)): // Hanya tampilkan tombol ekspor jika ada data ?>
        <!-- Mengarahkan ke file ekspor yang baru -->
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
            <table class="table table-bordered table-striped table-hover">
                <thead class="table-dark">
                    <tr><th>No</th><th>Nama Karyawan</th><th>Tanggal</th><th>Status</th></tr>
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

<?php 
$conn->close();
include '../includes/footer.php'; 
?>
