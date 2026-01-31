<?php
session_start();
require '../config/database.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: kendaraan.php");
    exit;
}

$id = (int) $_GET['id'];

/* ================= AMBIL DATA KENDARAAN ================= */
$stmt = $pdo->prepare("SELECT * FROM kendaraan WHERE id = ?");
$stmt->execute([$id]);
$kendaraan = $stmt->fetch();

if (!$kendaraan) {
    header("Location: kendaraan.php");
    exit;
}

/* ================= AMBIL FOTO KENDARAAN ================= */
$stmt = $pdo->prepare("
    SELECT posisi, foto
    FROM kendaraan_foto
    WHERE kendaraan_id = ?
");
$stmt->execute([$id]);
$fotos = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

/* ================= UPDATE DATA ================= */
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    /* UPDATE DATA KENDARAAN */
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
            continue; // tidak diganti
        }

        if ($_FILES[$input]['size'] > 2 * 1024 * 1024) {
            die("Ukuran foto maksimal 2MB");
        }

        $ext = strtolower(pathinfo($_FILES[$input]['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg','jpeg','png'];

        if (!in_array($ext, $allowed)) {
            die("Format foto harus JPG atau PNG");
        }

        /* hapus foto lama */
        if (!empty($fotos[$posisi])) {
            $oldFile = $uploadDir . $fotos[$posisi];
            if (file_exists($oldFile)) {
                unlink($oldFile);
            }

            $stmt = $pdo->prepare("
                DELETE FROM kendaraan_foto
                WHERE kendaraan_id = ? AND posisi = ?
            ");
            $stmt->execute([$id, $posisi]);
        }

        $fileName = 'kendaraan_' . $posisi . '_' . time() . '_' . rand(100,999) . '.' . $ext;
        move_uploaded_file($_FILES[$input]['tmp_name'], $uploadDir . $fileName);

        /* simpan foto baru */
        $stmt = $pdo->prepare("
            INSERT INTO kendaraan_foto (kendaraan_id, posisi, foto)
            VALUES (?, ?, ?)
        ");
        $stmt->execute([$id, $posisi, $fileName]);
    }

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
        <form method="POST" enctype="multipart/form-data">

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
                <option value="available" <?= $kendaraan['status']=='available'?'selected':'' ?>>
                    Available
                </option>
                <option value="unavailable" <?= $kendaraan['status']=='unavailable'?'selected':'' ?>>
                    Unavailable
                </option>
            </select>

            <hr>

            <h4>Foto Kendaraan</h4>

            <?php
            $labels = [
                'depan'    => 'Foto Depan',
                'kiri'     => 'Foto Samping Kiri',
                'kanan'    => 'Foto Samping Kanan',
                'belakang' => 'Foto Belakang'
            ];
            ?>

            <?php foreach ($labels as $posisi => $label): ?>
                <label><?= $label ?></label>

                <?php if (!empty($fotos[$posisi])): ?>
                    <div style="margin-bottom:8px;">
                        <img src="upload/datakendaraan/<?= htmlspecialchars($fotos[$posisi]) ?>"
                             width="160"
                             style="border-radius:8px;border:1px solid #ddd;">
                    </div>
                <?php endif; ?>

                <input type="file" name="foto_<?= $posisi ?>" accept="image/*">
            <?php endforeach; ?>

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
