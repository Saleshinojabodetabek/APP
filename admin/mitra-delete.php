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

try {
    /* MULAI TRANSACTION */
    $pdo->beginTransaction();

    /* AMBIL DATA MITRA */
    $stmt = $pdo->prepare("
        SELECT plat_mobil, foto_identitas, foto_mitra
        FROM mitra
        WHERE id = ?
        LIMIT 1
    ");
    $stmt->execute([$mitraId]);
    $mitra = $stmt->fetch();

    if (!$mitra) {
        throw new Exception("Data mitra tidak ditemukan.");
    }

    /* KEMBALIKAN KENDARAAN KE AVAILABLE */
    if (!empty($mitra['plat_mobil'])) {
        $stmt = $pdo->prepare("
            UPDATE kendaraan
            SET status = 'available'
            WHERE plat_nomor = ?
        ");
        $stmt->execute([$mitra['plat_mobil']]);
    }

    /* HAPUS FILE FOTO */
    $uploadDir = __DIR__ . "/upload/datamitra/";

    if (!empty($mitra['foto_identitas'])) {
        $fileIdentitas = $uploadDir . $mitra['foto_identitas'];
        if (file_exists($fileIdentitas)) {
            unlink($fileIdentitas);
        }
    }

    if (!empty($mitra['foto_mitra'])) {
        $fileMitra = $uploadDir . $mitra['foto_mitra'];
        if (file_exists($fileMitra)) {
            unlink($fileMitra);
        }
    }

    /* HAPUS DATA MITRA */
    $stmt = $pdo->prepare("
        DELETE FROM mitra
        WHERE id = ?
    ");
    $stmt->execute([$mitraId]);

    /* COMMIT */
    $pdo->commit();

    header("Location: mitra.php");
    exit;

} catch (Exception $e) {

    /* ROLLBACK JIKA ERROR */
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    die("Gagal menghapus mitra: " . $e->getMessage());
}
