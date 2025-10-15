<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../HTML/login.php");
    exit();
}
require_once '../include/dbh.inc.php';

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../HTML/myReservations.php");
    exit();
}

$res_id = $_POST['res_id'] ?? 0;
$csrf_token = $_POST['csrf_token'] ?? '';
if ($csrf_token !== $_SESSION['csrf_token']) {
    header("Location: ../HTML/myReservations.php?error=invalidcsrf");
    exit();
}

try {
    $user_id = $_SESSION['user_id'];
    $stmt = $pdo->prepare("DELETE FROM reservation WHERE Res_id = ? AND User_id = ?");
    $stmt->execute([$res_id, $user_id]);

    if ($stmt->rowCount() === 0) {
        header("Location: ../HTML/myReservations.php?error=notfound");
        exit();
    }

    header("Location: ../HTML/myReservations.php?success=deleted");
    exit();
} catch (PDOException $e) {
    error_log("Deletion failed: " . $e->getMessage());
    header("Location: ../HTML/myReservations.php?error=deletionfailed");
    exit();
}