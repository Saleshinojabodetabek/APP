<?php
session_start();
require '../config/database.php';

/* CEK LOGIN ADMIN */
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

/* STATISTIK */
$totalUser = $pdo->query("
    SELECT COUNT(*) FROM users WHERE role != 'admin'
")->fetchColumn();

$userAktif = $pdo->query("
    SELECT COUNT(*) FROM users 
    WHERE role != 'admin' AND status = 'active'
")->fetchColumn();

$userNonaktif = $pdo->query("
    SELECT COUNT(*) FROM users 
    WHERE role != 'admin' AND status = 'inactive'
")->fetchColumn();

/* SEMENTARA */
$totalOutstanding = "Rp 0";
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin</title>
    <link rel="stylesheet" href="/assets/css/admin.css">
</head>
<body>

<?php include 'sidebar.php'; ?>

<div class="main">
<?php include 'header.php'; ?>

<div class="content">
    <h2>Dashboard Admin</h2>
    <p>Selamat datang, <b><?= htmlspecialchars($_SESSION['admin_name']); ?></b></p>

    <div class="stats">
        <div class="card blue">
            <h3><?= $totalUser ?></h3>
            <p>Total User</p>
        </div>

        <div class="card green">
            <h3><?= $userAktif ?></h3>
            <p>User Aktif</p>
        </div>

        <div class="card red">
            <h3><?= $userNonaktif ?></h3>
            <p>User Nonaktif</p>
        </div>

        <div class="card purple">
            <h3><?= $totalOutstanding ?></h3>
            <p>Total Outstanding</p>
        </div>
    </div>

    <h3>Menu Pengelolaan</h3>
    <div class="menu-grid">
        <a href="users.php" class="menu-card">👤 Kelola User</a>
        <a href="#" class="menu-card">🚗 Kelola Kendaraan</a>
        <a href="#" class="menu-card">💳 Pembayaran</a>
        <a href="#" class="menu-card">📍 Geofence</a>
        <a href="#" class="menu-card">📄 Laporan</a>
        <a href="logout.php" class="menu-card danger">🚪 Logout</a>
    </div>
</div>
</div>

</body>
</html>
