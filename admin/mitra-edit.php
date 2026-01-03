<?php
session_start();
require '../config/database.php';

/* CEK LOGIN ADMIN */
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

/* VALIDASI ID */
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: mitra.php");
    exit;
}

$mitraId = (int) $_GET['id'];

/* AMBIL DATA MITRA */
$stmt = $pdo->prepare("SELECT * FROM mitra WHERE id = ?");
$stmt->execute([$mitraId]);
$mitra = $stmt->fetch();

if (!$mitra) {
    header("Location: mitra.php");
    exit;
}

/* AMBIL KENDARAAN:
   - available
   - + kendaraan yang sedang dipakai mitra ini
*/
$stmt = $pdo->prepare("
    SELECT id, tipe_mobil, plat_nomor, status
    FROM kendaraan
    WHERE status = 'available'
       OR plat_nomor = ?
    ORDER BY tipe_mobil ASC
");
$stmt->execute([$mitra['plat_mobil']]);
$kendaraanList = $stmt->fetchAll();

/* SIMPAN PERUBAHAN */
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $nama_mitra  = trim($_POST['nama_mitra']);
    $email       = trim($_POST['email']);
    $no_telepon  = trim($_POST['no_telepon']);
    $kendaraanId = (int) $_POST['kendaraan_id'];
    $passwordBaru = trim($_POST['password']);

    /* validasi email */
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Format email tidak valid");
    }

    /* ambil kendaraan terpilih */
    $stmt = $pdo->prepare("
        SELECT tipe_mobil, plat_nomor
        FROM kendaraan
        WHERE id = ?
        LIMIT 1
    ");
    $stmt->execute([$kendaraanId]);
    $kendaraanBaru = $stmt->fetch();

    if (!$kendaraanBaru) {
        die("Kendaraan tidak valid.");
    }

    /* JIKA KENDARAAN DIGANTI */
    if ($kendaraanBaru['plat_nomor'] !== $mitra['plat_mobil']) {

        /* kendaraan lama -> available */
        $stmt = $pdo->prepare("
            UPDATE kendaraan
            SET status = 'available'
            WHERE plat_nomor = ?
        ");
        $stmt->execute([$mitra['plat_mobil']]);

        /* kendaraan baru -> unavailable */
        $stmt = $pdo->prepare("
            UPDATE kendaraan
            SET status = 'unavailable'
            WHERE id = ?
        ");
        $stmt->execute([$kendaraanId]);
    }

    /* UPDATE MITRA */
    if ($passwordBaru !== '') {

        $stmt = $pdo->prepare("
            UPDATE mitra
            SET nama_mitra = ?, email = ?, no_telepon = ?, tipe_mobil = ?, plat_mobil = ?, password = ?
            WHERE id = ?
        ");
        $stmt->execute([
            $nama_mitra,
            $email,
            $no_telepon,
            $kendaraanBaru['tipe_mobil'],
            $kendaraanBaru['plat_nomor'],
            password_hash($passwordBaru, PASSWORD_DEFAULT),
            $mitraId
        ]);

    } else {

        $stmt = $pdo->prepare("
            UPDATE mitra
            SET nama_mitra = ?, email = ?, no_telepon = ?, tipe_mobil = ?, plat_mobil = ?
            WHERE id = ?
        ");
        $stmt->execute([
            $nama_mitra,
            $email,
            $no_telepon,
            $kendaraanBaru['tipe_mobil'],
            $kendaraanBaru['plat_nomor'],
            $mitraId
        ]);
    }

    header("Location: mitra.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Mitra</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>

<?php include 'sidebar.php'; ?>

<div class="main">
<div class="content">

    <div class="page-header">
        <h2>Edit Mitra</h2>
    </div>

    <div class="form-box">
        <form method="POST">

            <label>Nama Mitra</label>
            <input type="text" name="nama_mitra"
                   value="<?= htmlspecialchars($mitra['nama_mitra']) ?>" required>

            <label>Email (Gmail)</label>
            <input type="email" name="email"
                   value="<?= htmlspecialchars($mitra['email']) ?>" required>

            <label>No Telepon</label>
            <input type="text" name="no_telepon"
                   value="<?= htmlspecialchars($mitra['no_telepon']) ?>" required>

            <label>Password Baru (opsional)</label>
            <input type="password" name="password"
                   placeholder="Kosongkan jika tidak ingin ganti password">

            <label>Pilih Kendaraan</label>
            <select name="kendaraan_id" required>
                <?php foreach ($kendaraanList as $k): ?>
                    <option value="<?= $k['id'] ?>"
                        <?= $k['plat_nomor'] === $mitra['plat_mobil'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($k['tipe_mobil']) ?> —
                        <?= htmlspecialchars($k['plat_nomor']) ?>
                        <?= $k['status'] === 'unavailable' ? '(Dipakai)' : '' ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <div class="form-actions">
                <button type="submit">Update</button>
                <a href="mitra.php">Batal</a>
            </div>

        </form>
    </div>

</div>
</div>

</body>
</html>
