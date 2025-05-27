<?php
include 'config.php';
include 'auth.php';

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$id = (int)$_GET['id'];
$data = $conn->query("SELECT * FROM absensi WHERE id=$id")->fetch_assoc();

if (!$data) {
    echo "Data tidak ditemukan!";
    exit();
}

if (isset($_POST['update'])) {
    $nama = $conn->real_escape_string($_POST['nama']);
    $tanggal = $_POST['tanggal'];
    $status = $_POST['status'];

    $conn->query("UPDATE absensi SET nama='$nama', tanggal='$tanggal', status='$status' WHERE id=$id");
    header("Location: index.php");
    exit();
}
?>

<h2>Edit Absensi</h2>
<a href="index.php">Kembali</a>
<form method="POST">
    Nama: <input type="text" name="nama" value="<?=htmlspecialchars($data['nama'])?>" required><br><br>
    Tanggal: <input type="date" name="tanggal" value="<?=$data['tanggal']?>" required><br><br>
    Status:
    <select name="status" required>
        <option value="Hadir" <?= $data['status'] == 'Hadir' ? 'selected' : '' ?>>Hadir</option>
        <option value="Izin" <?= $data['status'] == 'Izin' ? 'selected' : '' ?>>Izin</option>
        <option value="Sakit" <?= $data['status'] == 'Sakit' ? 'selected' : '' ?>>Sakit</option>
        <option value="Alpha" <?= $data['status'] == 'Alpha' ? 'selected' : '' ?>>Alpha</option>
    </select><br><br>
    <button type="submit" name="update">Update</button>
</form>
