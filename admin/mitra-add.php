<?php
session_start();
require '../config/database.php';

/* CEK LOGIN ADMIN */
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

/* AMBIL KENDARAAN AVAILABLE */
$stmt = $pdo->query("
    SELECT id, tipe_mobil, plat_nomor
    FROM kendaraan
    WHERE status = 'available'
    ORDER BY tipe_mobil ASC
");
$kendaraanAvailable = $stmt->fetchAll();

/* SIMPAN DATA */
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nama_mitra  = trim($_POST['nama_mitra']);
    $no_telepon  = trim($_POST['no_telepon']);
    $kendaraanId = (int) $_POST['kendaraan_id'];
    $password    = password_hash($_POST['password'], PASSWORD_DEFAULT);

    /* ambil data kendaraan */
    $stmt = $pdo->prepare("
        SELECT tipe_mobil, plat_nomor
        FROM kendaraan
        WHERE id = ? AND status = 'available'
        LIMIT 1
    ");
    $stmt->execute([$kendaraanId]);
    $kendaraan = $stmt->fetch();

    if (!$kendaraan) {
        die("Kendaraan tidak tersedia atau sudah dipakai.");
    }

    /* simpan mitra */
    $stmt = $pdo->prepare("
        INSERT INTO mitra (nama_mitra, no_telepon, tipe_mobil, plat_mobil, password)
        VALUES (?, ?, ?, ?, ?)
    ");
    $stmt->execute([
        $nama_mitra,
        $no_telepon,
        $kendaraan['tipe_mobil'],
        $kendaraan['plat_nomor'],
        $password
    ]);

    /* update kendaraan jadi unavailable */
    $stmt = $pdo->prepare("
        UPDATE kendaraan
        SET status = 'unavailable'
        WHERE id = ?
    ");
    $stmt->execute([$kendaraanId]);

    header("Location: mitra.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Mitra</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>

<?php include 'sidebar.php'; ?>

<div class="main">
<div class="content">

    <div class="page-header">
        <h2>Tambah Mitra</h2>
    </div>

    <div class="form-box">
        <form method="POST">

            <label>Nama Mitra</label>
            <input type="text" name="nama_mitra" required>

            <label>No Telepon</label>
            <input type="text" name="no_telepon" required>

            <label>Password</label>
            <input type="password" name="password" required>

            <label>Pilih Kendaraan (Available)</label>
            <select name="kendaraan_id" required>
                <option value="">-- Pilih Kendaraan --</option>
                <?php foreach ($kendaraanAvailable as $k): ?>
                    <option value="<?= $k['id'] ?>">
                        <?= htmlspecialchars($k['tipe_mobil']) ?> —
                        <?= htmlspecialchars($k['plat_nomor']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <div class="form-actions">
                <button type="submit">Simpan</button>
                <a href="mitra.php">Batal</a>
            </div>

        </form>
    </div>

</div>
</div>

</body>
</html>
