<?php
include 'config.php';
include 'auth.php';

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $conn->query("DELETE FROM absensi WHERE id=$id");
}
header("Location: index.php");
exit();
?>
