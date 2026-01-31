<?php
session_start();
require '../config/database.php';

/* CEK LOGIN ADMIN */
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

/* AMBIL DATA MITRA */
$stmt = $pdo->query("
    SELECT 
        id,
        nama_mitra,
        no_telepon,
        tipe_mobil,
        plat_mobil,
        status_online,
        saldo
    FROM mitra
    ORDER BY nama_mitra ASC
");
$mitra = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Mitra Management</title>
    <link rel="stylesheet" href="../assets/css/admin.css">

    <!-- ICON (Font Awesome) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>

<?php include 'sidebar.php'; ?>

<div class="main">
<div class="content">

    <div class="page-header">
        <h2>Mitra Management</h2>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama</th>
                <th>No Telepon</th>
                <th>Tipe Mobil</th>
                <th>Plat Mobil</th>
                <th>Status</th>
                <th>Saldo</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>

        <?php if (count($mitra) === 0): ?>
            <tr>
                <td colspan="8" style="text-align:center;">Data mitra kosong</td>
            </tr>
        <?php endif; ?>

        <?php $no = 1; foreach ($mitra as $m): ?>
            <tr>
                <td><?= $no++ ?></td>
                <td><?= htmlspecialchars($m['nama_mitra']) ?></td>
                <td><?= htmlspecialchars($m['no_telepon']) ?></td>
                <td><?= htmlspecialchars($m['tipe_mobil']) ?></td>
                <td><?= htmlspecialchars($m['plat_mobil']) ?></td>

                <td>
                    <?php if ($m['status_online'] === 'online'): ?>
                        <span class="badge-success">Online</span>
                    <?php else: ?>
                        <span class="badge-danger">Offline</span>
                    <?php endif; ?>
                </td>

                <td>
                    Rp <?= number_format($m['saldo'], 0, ',', '.') ?>
                </td>

                <td class="action-icons">

                    <!-- Riwayat Absensi -->
                    <a href="mitra-absensi.php?id=<?= $m['id'] ?>" title="Riwayat Absensi">
                        <i class="fa-solid fa-calendar-check"></i>
                    </a>

                    <!-- Riwayat Top Up -->
                    <a href="mitra-saldo.php?id=<?= $m['id'] ?>" title="Riwayat Saldo">
                        <i class="fa-solid fa-wallet"></i>
                    </a>

                    <!-- Lokasi -->
                    <a href="mitra-lokasi.php?id=<?= $m['id'] ?>" title="Lokasi Mitra">
                        <i class="fa-solid fa-location-dot"></i>
                    </a>

                    <!-- Keadaan Mobil -->
                    <a href="mitra-kendaraan.php?id=<?= $m['id'] ?>" title="Keadaan Mobil">
                        <i class="fa-solid fa-car"></i>
                    </a>

                </td>
            </tr>
        <?php endforeach; ?>

        </tbody>
    </table>

</div>
</div>

</body>
</html>
