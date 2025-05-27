<?php
include 'config.php';
include 'auth.php';

// Pagination config
$batas = 5;
$halaman = isset($_GET['halaman']) ? (int)$_GET['halaman'] : 1;
$mulai = ($halaman - 1) * $batas;

// Search
$cari = isset($_GET['cari']) ? $conn->real_escape_string($_GET['cari']) : '';

$sql = "SELECT * FROM absensi WHERE nama LIKE '%$cari%' ORDER BY tanggal DESC LIMIT $mulai, $batas";
$result = $conn->query($sql);

$total = $conn->query("SELECT COUNT(*) AS total FROM absensi WHERE nama LIKE '%$cari%'")->fetch_assoc()['total'];
$jumlahHalaman = ceil($total / $batas);
?>

<h2>Data Absensi</h2>
<p>Halo, <?=htmlspecialchars($_SESSION['username'])?> | <a href="logout.php">Logout</a></p>
<a href="tambah.php">+ Tambah Absensi</a>

<form method="GET" style="margin-top:15px;">
    <input type="text" name="cari" placeholder="Cari nama..." value="<?=htmlspecialchars($cari)?>">
    <button type="submit">Cari</button>
</form>

<table border="1" cellpadding="10" cellspacing="0" style="margin-top:10px;">
    <tr>
        <th>No</th>
        <th>Nama</th>
        <th>Tanggal</th>
        <th>Status</th>
        <th>Aksi</th>
    </tr>

    <?php
$no = $mulai + 1;
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
            <td>{$no}</td>
            <td>" . htmlspecialchars($row['nama']) . "</td>
            <td>{$row['tanggal']}</td>
            <td>{$row['status']}</td>
            <td>
                <a href='edit.php?id={$row['id']}'>Edit</a> | 
                <a href='hapus.php?id={$row['id']}' onclick='return confirm(\"Yakin ingin hapus?\")'>Hapus</a>
            </td>
        </tr>";
        $no++;
    }
} else {
    echo "<tr><td colspan='5'>Data tidak ditemukan</td></tr>";
}
?>

</table>

<!-- Pagination -->
<div style="margin-top:10px;">
    <?php for ($i=1; $i<=$jumlahHalaman; $i++): ?>
        <a href="?halaman=<?= $i ?>&cari=<?= urlencode($cari) ?>" style="margin-right:5px; <?= $i == $halaman ? 'font-weight:bold;' : '' ?>"><?= $i ?></a>
    <?php endfor; ?>
</div>
