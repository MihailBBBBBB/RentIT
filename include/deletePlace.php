<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../HTML/login.php");
    exit();
}
require_once '../include/dbh.inc.php';

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../HTML/offers.php");
    exit();
}

$place_id = $_POST['place_id'] ?? 0;
$csrf_token = $_POST['csrf_token'] ?? '';
if ($csrf_token !== $_SESSION['csrf_token']) {
    header("Location: ../HTML/offers.php?error=invalidcsrf");
    exit();
}

try {
    $stmt = $pdo->prepare("DELETE FROM place WHERE Place_id = ?");
    $stmt->execute([$place_id]);

    if ($stmt->rowCount() === 0) {
        header("Location: ../HTML/offers.php?error=notfound");
        exit();
    }

    // Optional: Delete related reservations if needed
    $stmt = $pdo->prepare("DELETE FROM reservation WHERE Place_id = ?");
    $stmt->execute([$place_id]);

    header("Location: ../HTML/offers.php?success=deleted");
    exit();
} catch (PDOException $e) {
    error_log("Deletion failed: " . $e->getMessage());
    header("Location: ../HTML/offers.php?error=deletionfailed");
    exit();
}