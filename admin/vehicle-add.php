<?php
session_start();
require '../config/database.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $stmt = $pdo->prepare("
        INSERT INTO kendaraan (tipe_mobil, tahun_mobil, plat_nomor, status)
        VALUES (?, ?, ?, ?)
    ");
    $stmt->execute([
        $_POST['tipe_mobil'],
        $_POST['tahun_mobil'],
        $_POST['plat_nomor'],
        $_POST['status']
    ]);

    header("Location: kendaraan.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Tambah Kendaraan</title>
<link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>

<?php include 'sidebar.php'; ?>

<div class="main">
<div class="content">

<h2>Tambah Kendaraan</h2>

<form method="POST">
    <label>Tipe Mobil</label><br>
    <input name="tipe_mobil" required><br><br>

    <label>Tahun Mobil</label><br>
    <input type="number" name="tahun_mobil" min="1990" max="<?= date('Y') ?>" required><br><br>

    <label>Plat Nomor</label><br>
    <input name="plat_nomor" required><br><br>

    <label>Status</label><br>
    <select name="status">
        <option value="available">Available</option>
        <option value="unavailable">Unavailable</option>
    </select><br><br>

    <button type="submit">Simpan</button>
</form>

</div>
</div>
</body>
</html>
