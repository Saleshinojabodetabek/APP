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

/* AMBIL KENDARAAN */
$stmt = $pdo->prepare("
    SELECT id, tipe_mobil, plat_nomor, status
    FROM kendaraan
    WHERE status = 'available'
       OR plat_nomor = ?
    ORDER BY tipe_mobil ASC
");
$stmt->execute([$mitra['plat_mobil']]);
$kendaraanList = $stmt->fetchAll();

/* ================= SIMPAN UPDATE ================= */
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $nama_mitra     = trim($_POST['nama_mitra']);
    $email          = trim($_POST['email']);
    $no_telepon     = trim($_POST['no_telepon']);
    $alamat_lengkap = trim($_POST['alamat_lengkap']);
    $jenis_kelamin  = $_POST['jenis_kelamin'];
    $tanggal_lahir  = $_POST['tanggal_lahir'];
    $kendaraanId    = (int) $_POST['kendaraan_id'];
    $passwordBaru   = trim($_POST['password']);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Format email tidak valid");
    }

    /* AMBIL KENDARAAN BARU */
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

    /* JIKA GANTI KENDARAAN */
    if ($kendaraanBaru['plat_nomor'] !== $mitra['plat_mobil']) {

        $stmt = $pdo->prepare("
            UPDATE kendaraan SET status = 'available'
            WHERE plat_nomor = ?
        ");
        $stmt->execute([$mitra['plat_mobil']]);

        $stmt = $pdo->prepare("
            UPDATE kendaraan SET status = 'unavailable'
            WHERE id = ?
        ");
        $stmt->execute([$kendaraanId]);
    }

    /* ================= UPLOAD FOTO ================= */
    $uploadDir = __DIR__ . "/upload/datamitra/";
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    function uploadFotoEdit($file, $prefix, $uploadDir, $fotoLama) {
        if ($file['error'] !== 0) return $fotoLama;

        if ($file['size'] > 2 * 1024 * 1024) {
            die("Ukuran foto maksimal 2MB");
        }

        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg','jpeg','png'];
        if (!in_array($ext, $allowed)) {
            die("Format foto harus JPG / PNG");
        }

        if ($fotoLama && file_exists($uploadDir . $fotoLama)) {
            unlink($uploadDir . $fotoLama);
        }

        $fileName = $prefix.'_'.time().'_'.rand(100,999).'.'.$ext;
        move_uploaded_file($file['tmp_name'], $uploadDir.$fileName);

        return $fileName;
    }

    $foto_identitas = uploadFotoEdit(
        $_FILES['foto_identitas'], 'identitas', $uploadDir, $mitra['foto_identitas']
    );

    $foto_mitra = uploadFotoEdit(
        $_FILES['foto_mitra'], 'mitra', $uploadDir, $mitra['foto_mitra']
    );

    /* ================= UPDATE MITRA ================= */
    if ($passwordBaru !== '') {

        $stmt = $pdo->prepare("
            UPDATE mitra SET
            nama_mitra=?, email=?, no_telepon=?, alamat_lengkap=?,
            jenis_kelamin=?, tanggal_lahir=?, foto_identitas=?, foto_mitra=?,
            tipe_mobil=?, plat_mobil=?, password=?
            WHERE id=?
        ");

        $stmt->execute([
            $nama_mitra, $email, $no_telepon, $alamat_lengkap,
            $jenis_kelamin, $tanggal_lahir, $foto_identitas, $foto_mitra,
            $kendaraanBaru['tipe_mobil'], $kendaraanBaru['plat_nomor'],
            password_hash($passwordBaru, PASSWORD_DEFAULT),
            $mitraId
        ]);

    } else {

        $stmt = $pdo->prepare("
            UPDATE mitra SET
            nama_mitra=?, email=?, no_telepon=?, alamat_lengkap=?,
            jenis_kelamin=?, tanggal_lahir=?, foto_identitas=?, foto_mitra=?,
            tipe_mobil=?, plat_mobil=?
            WHERE id=?
        ");

        $stmt->execute([
            $nama_mitra, $email, $no_telepon, $alamat_lengkap,
            $jenis_kelamin, $tanggal_lahir, $foto_identitas, $foto_mitra,
            $kendaraanBaru['tipe_mobil'], $kendaraanBaru['plat_nomor'],
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
        <form method="POST" enctype="multipart/form-data">

            <label>Nama Mitra</label>
            <input type="text" name="nama_mitra" value="<?= htmlspecialchars($mitra['nama_mitra']) ?>" required>

            <label>Email</label>
            <input type="email" name="email" value="<?= htmlspecialchars($mitra['email']) ?>" required>

            <label>No Telepon</label>
            <input type="text" name="no_telepon" value="<?= htmlspecialchars($mitra['no_telepon']) ?>" required>

            <label>Alamat Lengkap</label>
            <input type="text" name="alamat_lengkap" value="<?= htmlspecialchars($mitra['alamat_lengkap']) ?>" required>

            <label>Jenis Kelamin</label>
            <select name="jenis_kelamin" required>
                <option value="Laki-laki" <?= $mitra['jenis_kelamin']=='Laki-laki'?'selected':'' ?>>Laki-laki</option>
                <option value="Perempuan" <?= $mitra['jenis_kelamin']=='Perempuan'?'selected':'' ?>>Perempuan</option>
            </select>

            <label>Tanggal Lahir</label>
            <input type="date" name="tanggal_lahir" value="<?= $mitra['tanggal_lahir'] ?>" required>

            <label>Foto Identitas (kosongkan jika tidak ganti)</label>
            <input type="file" name="foto_identitas" accept="image/*">

            <label>Foto Mitra (kosongkan jika tidak ganti)</label>
            <input type="file" name="foto_mitra" accept="image/*">

            <label>Password Baru (opsional)</label>
            <input type="password" name="password">

            <label>Pilih Kendaraan</label>
            <select name="kendaraan_id" required>
                <?php foreach ($kendaraanList as $k): ?>
                    <option value="<?= $k['id'] ?>"
                        <?= $k['plat_nomor'] === $mitra['plat_mobil'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($k['tipe_mobil']) ?> —
                        <?= htmlspecialchars($k['plat_nomor']) ?>
                        <?= $k['status']=='unavailable'?'(Dipakai)':'' ?>
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
