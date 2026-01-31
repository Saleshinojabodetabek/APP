<?php
session_start();
require '../config/database.php';

/* CEK LOGIN ADMIN */
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

/* VALIDASI PARAMETER */
if (
    !isset($_GET['id']) || !is_numeric($_GET['id']) ||
    !isset($_GET['status']) || !in_array($_GET['status'], ['active','suspend'])
) {
    header("Location: mitra.php");
    exit;
}

$mitraId = (int) $_GET['id'];
$status  = $_GET['status'];

/* UPDATE STATUS */
$stmt = $pdo->prepare("
    UPDATE mitra
    SET status = ?
    WHERE id = ?
");
$stmt->execute([$status, $mitraId]);

header("Location: mitra.php");
exit;
