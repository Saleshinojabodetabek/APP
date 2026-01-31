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

try {
    /* ================= TRANSACTION ================= */
    $pdo->beginTransaction();

    /* AMBIL FOTO KENDARAAN */
    $stmt = $pdo->prepare("
        SELECT foto
        FROM kendaraan_foto
        WHERE kendaraan_id = ?
    ");
    $stmt->execute([$id]);
    $fotos = $stmt->fetchAll();

    /* HAPUS FILE FOTO */
    $uploadDir = __DIR__ . "/upload/datakendaraan/";

    foreach ($fotos as $f) {
        $filePath = $uploadDir . $f['foto'];
        if (file_exists($filePath)) {
            unlink($filePath);
        }
    }

    /* HAPUS DATA KENDARAAN (foto DB ikut terhapus karena CASCADE) */
    $stmt = $pdo->prepare("DELETE FROM kendaraan WHERE id = ?");
    $stmt->execute([$id]);

    /* COMMIT */
    $pdo->commit();

    header("Location: kendaraan.php");
    exit;

} catch (Exception $e) {

    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    die("Gagal menghapus kendaraan: " . $e->getMessage());
}
