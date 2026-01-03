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
    SELECT id, nama_mitra, no_telepon, tipe_mobil, plat_mobil, status
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
    <style>
        /* tambahan khusus tabel */
        table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            margin-top: 20px;
            border-radius: 12px;
            overflow: hidden;
        }
        th, td {
            padding: 12px 14px;
            text-align: left;
            border-bottom: 1px solid #eee;
            font-size: 14px;
        }
        th {
            background: #f5f7fb;
            font-weight: 600;
        }
        tr:hover {
            background: #fafafa;
        }
        .badge-active {
            color: #198754;
            font-weight: 600;
        }
        .badge-inactive {
            color: #dc3545;
            font-weight: 600;
        }
        .btn {
            padding: 6px 10px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 13px;
            margin-right: 5px;
        }
        .btn-edit {
            background: #0d6efd;
            color: #fff;
        }
        .btn-delete {
            background: #dc3545;
            color: #fff;
        }
        .top-action {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
    </style>
</head>
<body>

<?php include 'sidebar.php'; ?>

<div class="main">
<div class="content">

<div class="top-action">
    <h2>Kelola Mitra</h2>
    <a href="mitra-add.php" class="menu-card">+ Tambah Mitra</a>
</div>

<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Nama Mitra</th>
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
                <td colspan="7">Belum ada data mitra</td>
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
                <span class="<?= $m['status'] === 'active' ? 'badge-active' : 'badge-inactive' ?>">
                    <?= $m['status'] ?>
                </span>
            </td>
            <td>
                <a href="mitra-edit.php?id=<?= $m['id'] ?>" class="btn btn-edit">Edit</a>
                <a href="mitra-delete.php?id=<?= $m['id'] ?>"
                   class="btn btn-delete"
                   onclick="return confirm('Yakin hapus mitra ini?')">
                   Hapus
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
