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
    header("Location: kendaraan.php");
    exit;
}

$id = (int) $_GET['id'];

/* HAPUS DATA KENDARAAN */
$stmt = $pdo->prepare("DELETE FROM kendaraan WHERE id = ?");
$stmt->execute([$id]);

/* KEMBALI KE LIST */
header("Location: kendaraan.php");
exit;
