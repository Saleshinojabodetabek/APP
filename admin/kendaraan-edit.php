<?php
session_start();
require '../config/database.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: kendaraan.php");
    exit;
}

$id = (int) $_GET['id'];

/* Ambil data kendaraan */
$stmt = $pdo->prepare("SELECT * FROM kendaraan WHERE id = ?");
$stmt->execute([$id]);
$kendaraan = $stmt->fetch();

if (!$kendaraan) {
    header("Location: kendaraan.php");
    exit;
}

/* Update data */
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $stmt = $pdo->prepare("
        UPDATE kendaraan
        SET tipe_mobil = ?, tahun_mobil = ?, plat_nomor = ?, status = ?
        WHERE id = ?
    ");
    $stmt->execute([
        trim($_POST['tipe_mobil']),
        (int) $_POST['tahun_mobil'],
        trim($_POST['plat_nomor']),
        $_POST['status'],
        $id
    ]);

    header("Location: kendaraan.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Kendaraan</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>

<?php include 'sidebar.php'; ?>

<div class="main">
<div class="content">

    <div class="page-header">
        <h2>Edit Kendaraan</h2>
    </div>

    <div class="form-box">
        <form method="POST">

            <label>Tipe Mobil</label>
            <input type="text" name="tipe_mobil"
                   value="<?= htmlspecialchars($kendaraan['tipe_mobil']) ?>" required>

            <label>Tahun Mobil</label>
            <input type="number" name="tahun_mobil"
                   min="1990" max="<?= date('Y') ?>"
                   value="<?= $kendaraan['tahun_mobil'] ?>" required>

            <label>Plat Nomor</label>
            <input type="text" name="plat_nomor"
                   value="<?= htmlspecialchars($kendaraan['plat_nomor']) ?>" required>

            <label>Status</label>
            <select name="status">
                <option value="available"
                    <?= $kendaraan['status'] === 'available' ? 'selected' : '' ?>>
                    Available
                </option>
                <option value="unavailable"
                    <?= $kendaraan['status'] === 'unavailable' ? 'selected' : '' ?>>
                    Unavailable
                </option>
            </select>

            <div class="form-actions">
                <button type="submit">Update</button>
                <a href="kendaraan.php">Batal</a>
            </div>

        </form>
    </div>

</div>
</div>

</body>
</html>
