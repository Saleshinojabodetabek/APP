<?php
session_start();
require '../config/database.php';

/* CEK LOGIN ADMIN */
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

/* AMBIL DATA KENDARAAN */
$stmt = $pdo->query("SELECT * FROM kendaraan ORDER BY created_at DESC");
$data = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Kendaraan</title>
    <link rel="stylesheet" href="../assets/css/admin.css">

    <!-- ICON -->
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>

<?php include 'sidebar.php'; ?>

<div class="main">
<div class="content">

    <div class="page-header">
        <h2>Kelola Kendaraan</h2>
        <a href="kendaraan-add.php" class="btn-primary">+ Tambah Kendaraan</a>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>No</th>
                <th>Tipe Mobil</th>
                <th>Tahun</th>
                <th>Plat Nomor</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>

        <?php if (count($data) === 0): ?>
            <tr>
                <td colspan="6" style="text-align:center;">Belum ada data kendaraan</td>
            </tr>
        <?php endif; ?>

        <?php $no = 1; foreach ($data as $k): ?>
            <tr>
                <td><?= $no++ ?></td>
                <td><?= htmlspecialchars($k['tipe_mobil']) ?></td>
                <td><?= $k['tahun_mobil'] ?></td>
                <td><?= htmlspecialchars($k['plat_nomor']) ?></td>
                <td>
                    <?php if ($k['status'] === 'available'): ?>
                        <span class="badge-success">Available</span>
                    <?php else: ?>
                        <span class="badge-warning">Unavailable</span>
                    <?php endif; ?>
                </td>

                <!-- AKSI ICON -->
                <td class="action-icons">

                    <!-- DETAIL -->
                    <a href="kendaraan-detail.php?id=<?= $k['id'] ?>" title="Detail Kendaraan">
                        <i class="fa-solid fa-eye"></i>
                    </a>

                    <!-- EDIT -->
                    <a href="kendaraan-edit.php?id=<?= $k['id'] ?>" title="Edit Kendaraan">
                        <i class="fa-solid fa-pen-to-square"></i>
                    </a>

                    <!-- HAPUS -->
                    <a href="kendaraan-delete.php?id=<?= $k['id'] ?>"
                       onclick="return confirm('Yakin hapus kendaraan ini?')"
                       title="Hapus Kendaraan">
                        <i class="fa-solid fa-trash"></i>
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
