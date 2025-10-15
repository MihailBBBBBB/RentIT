<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['is_admin'] != 1) {
    header("Location: ../HTML/offers.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../HTML/offers.php");
    exit();
}

require_once 'dbh.inc.php';

$place_id = $_POST['place_id'] ?? 0;
$csrf_token = $_POST['csrf_token'] ?? '';
if ($csrf_token !== $_SESSION['csrf_token']) {
    header("Location: offers.php?error=invalidcsrf");
    exit();
}

$name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING) ?? '';
$address = filter_input(INPUT_POST, 'address', FILTER_SANITIZE_STRING) ?? '';
$description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING) ?? '';
$price = filter_input(INPUT_POST, 'price', FILTER_VALIDATE_FLOAT) ?? 0.00;

try {
    // Handle file upload if present
    $foto = $_FILES['foto']['name'] ?? '';
    $foto_tmp = $_FILES['foto']['tmp_name'] ?? '';
    $foto_error = $_FILES['foto']['error'] ?? 0;
    $current_foto = $pdo->prepare("SELECT Foto FROM place WHERE Place_id = ?");
    $current_foto->execute([$place_id]);
    $current_foto = $current_foto->fetchColumn();

    if ($foto_error === UPLOAD_ERR_OK && $foto_tmp) {
        $allowed = ['image/jpeg', 'image/png', 'image/gif'];
        $mime = mime_content_type($foto_tmp);
        if (!in_array($mime, $allowed)) {
            header("Location: ../HTML/editPlace.php?id=$place_id&error=invalidfiletype");
            exit();
        }
        if ($_FILES['foto']['size'] > 5 * 1024 * 1024) {
            header("Location: ../HTML/editPlace.php?id=$place_id&error=filesizetoolarge");
            exit();
        }
        $foto = uniqid() . '_' . basename($foto);
        move_uploaded_file($foto_tmp, "../img/$foto");
        if ($current_foto && file_exists("../img/$current_foto")) {
            unlink("../img/$current_foto"); // Delete old image
        }
    } else {
        $foto = $current_foto; // Keep existing photo if no new upload
    }

    // Update database
    $stmt = $pdo->prepare("UPDATE place SET Name = ?, Adress = ?, Description = ?, Price = ?, Foto = ? WHERE Place_id = ?");
    $stmt->execute([$name, $address, $description, $price, $foto, $place_id]);

    header("Location: ../HTML/offers.php?success=placeupdated");
    exit();
} catch (PDOException $e) {
    error_log("Update failed: " . $e->getMessage());
    header("Location: ../HTML/editPlace.php?id=$place_id&error=updatefailed");
    exit();
}