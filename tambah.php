<?php
include 'config.php';
include 'auth.php';

if (isset($_POST['submit'])) {
    $nama = $conn->real_escape_string($_POST['nama']);
    $tanggal = $_POST['tanggal'];
    $status = $_POST['status'];

    $conn->query("INSERT INTO absensi (nama, tanggal, status) VALUES ('$nama', '$tanggal', '$status')");
    header("Location: index.php");
    exit();
}
?>

<h2>Tambah Absensi</h2>
<a href="index.php">Kembali</a>
<form method="POST">
    Nama: <input type="text" name="nama" required><br><br>
    Tanggal: <input type="date" name="tanggal" required><br><br>
    Status:
    <select name="status" required>
        <option value="Hadir">Hadir</option>
        <option value="Izin">Izin</option>
        <option value="Sakit">Sakit</option>
        <option value="Alpha">Alpha</option>
    </select><br><br>
    <button type="submit" name="submit">Simpan</button>
</form>
