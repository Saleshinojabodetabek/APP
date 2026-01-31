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
    SELECT id, nama_mitra, email, no_telepon, tipe_mobil, plat_mobil, status
    FROM mitra
    ORDER BY created_at DESC
");
$mitra = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Mitra</title>
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
        <h2>Kelola Mitra</h2>
        <a href="mitra-add.php" class="btn-primary">+ Tambah Mitra</a>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Mitra</th>
                <th>Email</th>
                <th>No Telepon</th>
                <th>Tipe Mobil</th>
                <th>Plat Mobil</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>

        <?php if (count($mitra) === 0): ?>
            <tr>
                <td colspan="8" style="text-align:center;">Belum ada data mitra</td>
            </tr>
        <?php endif; ?>

        <?php $no = 1; foreach ($mitra as $m): ?>
            <tr>
                <td><?= $no++ ?></td>
                <td><?= htmlspecialchars($m['nama_mitra']) ?></td>
                <td><?= htmlspecialchars($m['email']) ?></td>
                <td><?= htmlspecialchars($m['no_telepon']) ?></td>
                <td><?= htmlspecialchars($m['tipe_mobil']) ?></td>
                <td><?= htmlspecialchars($m['plat_mobil']) ?></td>

                <td>
                    <?php if ($m['status'] === 'active'): ?>
                        <span class="badge-success">Active</span>
                    <?php else: ?>
                        <span class="badge-warning">Suspend</span>
                    <?php endif; ?>
                </td>

                <td class="action-icons">

                    <!-- DETAIL -->
                    <a href="mitra-detail.php?id=<?= $m['id'] ?>" title="Detail">
                        <i class="fa-solid fa-eye"></i>
                    </a>

                    <!-- EDIT -->
                    <a href="mitra-edit.php?id=<?= $m['id'] ?>" title="Edit">
                        <i class="fa-solid fa-pen-to-square"></i>
                    </a>

                    <!-- HAPUS -->
                    <a href="mitra-delete.php?id=<?= $m['id'] ?>"
                       onclick="return confirm('Yakin hapus mitra ini?')"
                       title="Hapus">
                        <i class="fa-solid fa-trash"></i>
                    </a>

                    <!-- STATUS OPTION -->
                    <form action="mitra-status.php" method="GET" class="status-form">
                        <input type="hidden" name="id" value="<?= $m['id'] ?>">
                        <select name="status" onchange="this.form.submit()">
                            <option value="active" <?= $m['status']=='active'?'selected':'' ?>>
                                Active
                            </option>
                            <option value="suspend" <?= $m['status']=='suspend'?'selected':'' ?>>
                                Suspend
                            </option>
                        </select>
                    </form>

                </td>
            </tr>
        <?php endforeach; ?>

        </tbody>
    </table>

</div>
</div>

</body>
</html>
