<?php
session_start();
require '../config/database.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

$stmt = $pdo->query("SELECT * FROM kendaraan ORDER BY created_at DESC");
$data = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
<title>Kelola Kendaraan</title>
<link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>

<?php include 'sidebar.php'; ?>

<div class="main">
<div class="content">

<h2>Kelola kendaraan</h2>
<a href="kendaraan-add.php" class="menu-card">+ Tambah kendaraan</a>

<table style="margin-top:20px" width="100%" cellpadding="10">
<tr>
    <th>No</th>
    <th>Tipe Mobil</th>
    <th>Tahun</th>
    <th>Plat Nomor</th>
    <th>Status</th>
    <th>Aksi</th>
</tr>

<?php if (!$data): ?>
<tr><td colspan="6">Belum ada data kendaraan</td></tr>
<?php endif; ?>

<?php $no=1; foreach ($data as $k): ?>
<tr>
    <td><?= $no++ ?></td>
    <td><?= htmlspecialchars($k['tipe_mobil']) ?></td>
    <td><?= $k['tahun_mobil'] ?></td>
    <td><?= htmlspecialchars($k['plat_nomor']) ?></td>
    <td>
        <b style="color:<?= $k['status']=='available'?'green':'red' ?>">
            <?= ucfirst($k['status']) ?>
        </b>
    </td>
    <td>
        <a href="kendaraan-edit.php?id=<?= $k['id'] ?>">Edit</a> |
        <a href="kendaraan-delete.php?id=<?= $k['id'] ?>"
           onclick="return confirm('Hapus kendaraan ini?')">Hapus</a>
    </td>
</tr>
<?php endforeach; ?>

</table>

</div>
</div>
</body>
</html>
