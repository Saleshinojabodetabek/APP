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
    header("Location: kendaraan.php");
    exit;
}

$id = (int) $_GET['id'];

/* AMBIL DATA KENDARAAN */
$stmt = $pdo->prepare("
    SELECT *
    FROM kendaraan
    WHERE id = ?
    LIMIT 1
");
$stmt->execute([$id]);
$kendaraan = $stmt->fetch();

if (!$kendaraan) {
    header("Location: kendaraan.php");
    exit;
}

/* AMBIL FOTO KENDARAAN */
$stmt = $pdo->prepare("
    SELECT posisi, foto
    FROM kendaraan_foto
    WHERE kendaraan_id = ?
");
$stmt->execute([$id]);
$fotos = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Detail Kendaraan</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="../assets/css/kendaraan-detail.css">
</head>
<body>

<?php include 'sidebar.php'; ?>

<div class="main">
<div class="content">

    <div class="page-header">
        <h2>Detail Kendaraan</h2>
        <a href="kendaraan.php" class="btn-primary">Kembali</a>
    </div>

    <div class="detail-card">

        <div class="detail-info">
            <div class="info-row">
                <span>Tipe Mobil</span>
                <strong><?= htmlspecialchars($kendaraan['tipe_mobil']) ?></strong>
            </div>

            <div class="info-row">
                <span>Tahun Mobil</span>
                <strong><?= $kendaraan['tahun_mobil'] ?></strong>
            </div>

            <div class="info-row">
                <span>Plat Nomor</span>
                <strong><?= htmlspecialchars($kendaraan['plat_nomor']) ?></strong>
            </div>

            <div class="info-row">
                <span>Status</span>
                <?php if ($kendaraan['status'] === 'available'): ?>
                    <span class="badge-success">Available</span>
                <?php else: ?>
                    <span class="badge-warning">Unavailable</span>
                <?php endif; ?>
            </div>

            <div class="info-row">
                <span>Dibuat Pada</span>
                <strong><?= $kendaraan['created_at'] ?></strong>
            </div>
        </div>

        <hr>

        <h3>Foto Kendaraan</h3>

        <div class="photo-grid">

            <?php
            $label = [
                'depan'    => 'Depan',
                'kiri'     => 'Samping Kiri',
                'kanan'    => 'Samping Kanan',
                'belakang' => 'Belakang'
            ];
            ?>

            <?php foreach ($label as $posisi => $text): ?>
                <div class="photo-box">
                    <span><?= $text ?></span>

                    <?php if (!empty($fotos[$posisi])): ?>
                        <img src="upload/datakendaraan/<?= htmlspecialchars($fotos[$posisi]) ?>">
                    <?php else: ?>
                        <div class="no-photo">Tidak ada foto</div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>

        </div>

    </div>

</div>
</div>

</body>
</html>
