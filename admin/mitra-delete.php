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
    SELECT plat_mobil
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

/* KEMBALIKAN KENDARAAN KE AVAILABLE */
$stmt = $pdo->prepare("
    UPDATE kendaraan
    SET status = 'available'
    WHERE plat_nomor = ?
");
$stmt->execute([$mitra['plat_mobil']]);

/* HAPUS DATA MITRA */
$stmt = $pdo->prepare("
    DELETE FROM mitra
    WHERE id = ?
");
$stmt->execute([$mitraId]);

/* KEMBALI KE HALAMAN MITRA */
header("Location: mitra.php");
exit;
