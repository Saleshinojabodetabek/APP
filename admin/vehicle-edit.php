<?php
session_start();
require '../config/database.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

$id = $_GET['id'];

$stmt = $pdo->prepare("SELECT * FROM kendaraan WHERE id=?");
$stmt->execute([$id]);
$k = $stmt->fetch();

if (!$k) die("Data tidak ditemukan");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $stmt = $pdo->prepare("
        UPDATE kendaraan SET
        tipe_mobil=?, tahun_mobil=?, plat_nomor=?, status=?
        WHERE id=?
    ");
    $stmt->execute([
        $_POST['tipe_mobil'],
        $_POST['tahun_mobil'],
        $_POST['plat_nomor'],
        $_POST['status'],
        $id
    ]);

    header("Location: kendaraan.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Edit Kendaraan</title>
<link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>

<?php include 'sidebar.php'; ?>

<div class="main">
<div class="content">

<h2>Edit Kendaraan</h2>

<form method="POST">
    <label>Tipe Mobil</label><br>
    <input name="tipe_mobil" value="<?= htmlspecialchars($k['tipe_mobil']) ?>"><br><br>

    <label>Tahun Mobil</label><br>
    <input type="number" name="tahun_mobil" value="<?= $k['tahun_mobil'] ?>"><br><br>

    <label>Plat Nomor</label><br>
    <input name="plat_nomor" value="<?= htmlspecialchars($k['plat_nomor']) ?>"><br><br>

    <label>Status</label><br>
    <select name="status">
        <option value="available" <?= $k['status']=='available'?'selected':'' ?>>Available</option>
        <option value="unavailable" <?= $k['status']=='unavailable'?'selected':'' ?>>Unavailable</option>
    </select><br><br>

    <button type="submit">Update</button>
</form>

</div>
</div>
</body>
</html>
