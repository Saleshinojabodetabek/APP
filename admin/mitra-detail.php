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
$stmt = $pdo->prepare("
    SELECT *
    FROM mitra
    WHERE id = ?
    LIMIT 1
");
$stmt->execute([$mitraId]);
$mitra = $stmt->fetch();

if (!$mitra) {
    header("Location: mitra.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Detail Mitra</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>

<?php include 'sidebar.php'; ?>

<div class="main">
<div class="content">

    <div class="page-header">
        <h2>Detail Mitra</h2>
    </div>

    <div class="detail-box">

        <div class="detail-row">
            <strong>Nama Mitra</strong>
            <span><?= htmlspecialchars($mitra['nama_mitra']) ?></span>
        </div>

        <div class="detail-row">
            <strong>Email</strong>
            <span><?= htmlspecialchars($mitra['email']) ?></span>
        </div>

        <div class="detail-row">
            <strong>No Telepon</strong>
            <span><?= htmlspecialchars($mitra['no_telepon']) ?></span>
        </div>

        <div class="detail-row">
            <strong>Alamat Lengkap</strong>
            <span><?= nl2br(htmlspecialchars($mitra['alamat_lengkap'])) ?></span>
        </div>

        <div class="detail-row">
            <strong>Jenis Kelamin</strong>
            <span><?= htmlspecialchars($mitra['jenis_kelamin']) ?></span>
        </div>

        <div class="detail-row">
            <strong>Tanggal Lahir</strong>
            <span><?= htmlspecialchars($mitra['tanggal_lahir']) ?></span>
        </div>

        <div class="detail-row">
            <strong>Tipe Mobil</strong>
            <span><?= htmlspecialchars($mitra['tipe_mobil']) ?></span>
        </div>

        <div class="detail-row">
            <strong>Plat Mobil</strong>
            <span><?= htmlspecialchars($mitra['plat_mobil']) ?></span>
        </div>

        <div class="detail-row">
            <strong>Status</strong>
            <span><?= htmlspecialchars($mitra['status'] ?? '-') ?></span>
        </div>

        <div class="detail-row">
            <strong>Tanggal Daftar</strong>
            <span><?= htmlspecialchars($mitra['created_at'] ?? '-') ?></span>
        </div>

        <hr>

        <h3>Dokumen & Foto</h3>

        <div class="photo-box">
            <div>
                <strong>Foto Identitas</strong><br><br>
                <?php if (!empty($mitra['foto_identitas'])): ?>
                    <img src="upload/datamitra/<?= htmlspecialchars($mitra['foto_identitas']) ?>" alt="Foto Identitas">
                <?php else: ?>
                    <em>Tidak ada foto</em>
                <?php endif; ?>
            </div>

            <div>
                <strong>Foto Mitra</strong><br><br>
                <?php if (!empty($mitra['foto_mitra'])): ?>
                    <img src="upload/datamitra/<?= htmlspecialchars($mitra['foto_mitra']) ?>" alt="Foto Mitra">
                <?php else: ?>
                    <em>Tidak ada foto</em>
                <?php endif; ?>
            </div>
        </div>

        <br>

        <div class="form-actions">
            <a href="mitra.php">⬅ Kembali</a>
            <a href="edit_mitra.php?id=<?= $mitraId ?>">✏️ Edit</a>
        </div>

    </div>

</div>
</div>

</body>
</html>
