<?php
session_start();
require '../config/database.php';

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

// Statistik
$totalUser = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM users WHERE role!='admin'"))[0];
$userAktif = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM users WHERE status='active' AND role!='admin'"))[0];
$userNonaktif = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM users WHERE status='inactive' AND role!='admin'"))[0];

// Dummy sementara (nanti ganti ke tabel pembayaran)
$totalPembayaran = "Rp 0";
$totalOutstanding = "Rp 0";
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin</title>
    <link rel="stylesheet" href="assets/css/admin.css">
</head>
<body>

<?php include 'sidebar.php'; ?>

<div class="main">
<?php include 'header.php'; ?>

<div class="content">
    <h2>Dashboard Admin</h2>
    <p>Selamat datang, <b><?= $_SESSION['admin']['name']; ?></b></p>

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
