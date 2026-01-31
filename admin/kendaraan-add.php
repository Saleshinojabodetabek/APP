<?php
session_start();
require '../config/database.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    /* ================= SIMPAN KENDARAAN ================= */
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

    $kendaraanId = $pdo->lastInsertId();

    /* ================= UPLOAD FOTO ================= */
    $uploadDir = __DIR__ . "/upload/datakendaraan/";
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $fotoList = [
        'foto_depan'    => 'depan',
        'foto_kiri'     => 'kiri',
        'foto_kanan'    => 'kanan',
        'foto_belakang' => 'belakang'
    ];

    foreach ($fotoList as $input => $posisi) {

        if (!isset($_FILES[$input]) || $_FILES[$input]['error'] !== 0) {
            continue;
        }

        if ($_FILES[$input]['size'] > 2 * 1024 * 1024) {
            die("Ukuran foto maksimal 2MB");
        }

        $ext = strtolower(pathinfo($_FILES[$input]['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg','jpeg','png'];

        if (!in_array($ext, $allowed)) {
            die("Format foto harus JPG atau PNG");
        }

        $fileName = 'kendaraan_' . $posisi . '_' . time() . '_' . rand(100,999) . '.' . $ext;
        move_uploaded_file($_FILES[$input]['tmp_name'], $uploadDir . $fileName);

        /* SIMPAN KE DATABASE */
        $stmt = $pdo->prepare("
            INSERT INTO kendaraan_foto (kendaraan_id, posisi, foto)
            VALUES (?, ?, ?)
        ");
        $stmt->execute([$kendaraanId, $posisi, $fileName]);
    }

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
        <form method="POST" enctype="multipart/form-data">

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

            <hr>

            <h4>Foto Kendaraan</h4>

            <label>Foto Depan</label>
            <input type="file" name="foto_depan" accept="image/*" required>

            <label>Foto Samping Kiri</label>
            <input type="file" name="foto_kiri" accept="image/*" required>

            <label>Foto Samping Kanan</label>
            <input type="file" name="foto_kanan" accept="image/*" required>

            <label>Foto Belakang</label>
            <input type="file" name="foto_belakang" accept="image/*" required>

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
