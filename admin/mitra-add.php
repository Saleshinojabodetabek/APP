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

/* ================= SIMPAN DATA ================= */
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $nama_mitra     = trim($_POST['nama_mitra']);
    $email          = trim($_POST['email']);
    $no_telepon     = trim($_POST['no_telepon']);
    $alamat_lengkap = trim($_POST['alamat_lengkap']);
    $jenis_kelamin  = $_POST['jenis_kelamin'];
    $tanggal_lahir  = $_POST['tanggal_lahir'];
    $kendaraanId    = (int) $_POST['kendaraan_id'];
    $password       = trim($_POST['password']);

    /* VALIDASI */
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Format email tidak valid");
    }

    if (strlen($password) < 6) {
        die("Password minimal 6 karakter");
    }

    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    /* CEK KENDARAAN */
    $stmt = $pdo->prepare("
        SELECT tipe_mobil, plat_nomor
        FROM kendaraan
        WHERE id = ? AND status = 'available'
        LIMIT 1
    ");
    $stmt->execute([$kendaraanId]);
    $kendaraan = $stmt->fetch();

    if (!$kendaraan) {
        die("Kendaraan tidak tersedia.");
    }

    /* ================= UPLOAD FOTO ================= */
    $uploadDir = __DIR__ . "/upload/datamitra/";
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    function uploadFoto($file, $prefix, $uploadDir) {
        if ($file['error'] !== 0) {
            die("Gagal upload file");
        }

        if ($file['size'] > 2 * 1024 * 1024) {
            die("Ukuran foto maksimal 2MB");
        }

        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png'];

        if (!in_array($ext, $allowed)) {
            die("Format foto harus JPG atau PNG");
        }

        $fileName = $prefix . '_' . time() . '_' . rand(100,999) . '.' . $ext;
        move_uploaded_file($file['tmp_name'], $uploadDir . $fileName);

        return $fileName;
    }

    $foto_identitas = uploadFoto($_FILES['foto_identitas'], 'identitas', $uploadDir);
    $foto_mitra     = uploadFoto($_FILES['foto_mitra'], 'mitra', $uploadDir);

    /* ================= SIMPAN MITRA ================= */
    $stmt = $pdo->prepare("
        INSERT INTO mitra
        (nama_mitra, email, no_telepon, alamat_lengkap, jenis_kelamin, tanggal_lahir,
         foto_identitas, foto_mitra, tipe_mobil, plat_mobil, password)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

    $stmt->execute([
        $nama_mitra,
        $email,
        $no_telepon,
        $alamat_lengkap,
        $jenis_kelamin,
        $tanggal_lahir,
        $foto_identitas,
        $foto_mitra,
        $kendaraan['tipe_mobil'],
        $kendaraan['plat_nomor'],
        $passwordHash
    ]);

    /* UPDATE STATUS KENDARAAN */
    $stmt = $pdo->prepare("
        UPDATE kendaraan SET status = 'unavailable' WHERE id = ?
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
        <form method="POST" enctype="multipart/form-data">

            <label>Nama Mitra</label>
            <input type="text" name="nama_mitra" required>

            <label>Email</label>
            <input type="email" name="email" required>

            <label>No Telepon</label>
            <input type="text" name="no_telepon" required>

            <label>Alamat Lengkap</label>
            <input type="text" name="alamat_lengkap" required>

            <label>Jenis Kelamin</label>
            <select name="jenis_kelamin" required>
                <option value="">-- Pilih --</option>
                <option value="Laki-laki">Laki-laki</option>
                <option value="Perempuan">Perempuan</option>
            </select>

            <label>Tanggal Lahir</label>
            <input type="date" name="tanggal_lahir" required>

            <label>Foto Identitas (KTP/SIM)</label>
            <input type="file" name="foto_identitas" accept="image/*" required>

            <label>Foto Mitra</label>
            <input type="file" name="foto_mitra" accept="image/*" required>

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
