<?php
session_start();
require '../config/database.php';

/* CEK LOGIN ADMIN */
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

/* STATISTIK */
$totalUser = $pdo->query("SELECT COUNT(*) FROM users WHERE role != 'admin'")->fetchColumn();
$userAktif = $pdo->query("SELECT COUNT(*) FROM users WHERE role != 'admin' AND status = 'active'")->fetchColumn();
$userNonaktif = $pdo->query("SELECT COUNT(*) FROM users WHERE role != 'admin' AND status = 'inactive'")->fetchColumn();

/* OUTSTANDING SEMENTARA */
$totalOutstandingValue = 2500000; // ganti nanti dari tabel pembayaran
$totalOutstanding = "Rp " . number_format($totalOutstandingValue, 0, ',', '.');
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>

<?php include 'sidebar.php'; ?>

<div class="main">
<div class="content">

<div class="dashboard-header">
    <div class="dashboard-title">
        <h2>Dashboard Admin</h2>
        <p>
            Selamat datang,
            <span class="admin-name">
                <?= htmlspecialchars($_SESSION['admin_name']); ?>
            </span>
        </p>
    </div>
</div>


<!-- CARD STATISTIK -->
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

<!-- DIAGRAM USER -->
<h3>Statistik User</h3>
<div class="chart-box">
    <canvas id="userChart"></canvas>
</div>

<!-- DIAGRAM OUTSTANDING -->
<h3>Statistik Outstanding</h3>
<div class="chart-box wide">
    <canvas id="outstandingChart"></canvas>
</div>

</div>
</div>

<!-- DATA UNTUK JS -->
<script>
const USER_AKTIF = <?= $userAktif ?>;
const USER_NONAKTIF = <?= $userNonaktif ?>;
const OUTSTANDING_VALUE = <?= $totalOutstandingValue ?>;
</script>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="../assets/js/script.js"></script>

</body>
</html>
