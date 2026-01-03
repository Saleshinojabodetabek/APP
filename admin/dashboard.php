<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}
$admin = $_SESSION['admin'];
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
        <h2>Dashboard</h2>

        <div class="cards">
            <div class="card">
                <h3>Total User</h3>
                <p>120</p>
            </div>
            <div class="card">
                <h3>Total Kendaraan</h3>
                <p>45</p>
            </div>
            <div class="card">
                <h3>Pembayaran Pending</h3>
                <p>Rp 3.600.000</p>
            </div>
            <div class="card">
                <h3>Pembayaran Lunas</h3>
                <p>Rp 18.400.000</p>
            </div>
        </div>

        <div class="section">
            <h3>Menu Cepat</h3>
            <div class="quick-menu">
                <a href="#">Kelola User</a>
                <a href="#">Kelola Kendaraan</a>
                <a href="#">Verifikasi Pembayaran</a>
                <a href="#">Izin Keluar Geofence</a>
                <a href="#">Broadcast Info</a>
            </div>
        </div>

    </div>
</div>

</body>
</html>
