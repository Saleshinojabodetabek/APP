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
        trim($_POST['tipe_mobil']),
        (int) $_POST['tahun_mobil'],
        trim($_POST['plat_nomor']),
        $_POST['status']
    ]);

    header("Location: kendaraan.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Kendaraan</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>

<?php include 'sidebar.php'; ?>

<div class="main">
<div class="content">

    <div class="page-header">
        <h2>Tambah Kendaraan</h2>
    </div>

    <div class="form-box">
        <form method="POST">

            <label>Tipe Mobil</label>
            <input type="text" name="tipe_mobil" required>

            <label>Tahun Mobil</label>
            <input type="number" name="tahun_mobil"
                   min="1990" max="<?= date('Y') ?>" required>

            <label>Plat Nomor</label>
            <input type="text" name="plat_nomor" required>

            <label>Status</label>
            <select name="status">
                <option value="available">Available</option>
                <option value="unavailable">Unavailable</option>
            </select>

            <div class="form-actions">
                <button type="submit">Simpan</button>
                <a href="kendaraan.php">Batal</a>
            </div>

        </form>
    </div>

</div>
</div>

</body>
</html>
