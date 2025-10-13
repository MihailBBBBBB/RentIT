<?php
require_once 'dbh.inc.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // Validate session
        if (!isset($_SESSION['user_id'])) {
            throw new Exception("User not logged in.");
        }

        // Validate file upload
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $max_size = 5 * 1024 * 1024; // 5MB
        $foto = $_FILES['foto']['name'];
        $file_type = $_FILES['foto']['type'];
        $file_size = $_FILES['foto']['size'];

        if (!in_array($file_type, $allowed_types)) {
            throw new Exception("Invalid file type. Only JPEG, PNG, and GIF are allowed.");
        }
        if ($file_size > $max_size) {
            throw new Exception("File size exceeds 5MB limit.");
        }

        $target_dir = "../img/";
        if (!is_dir($target_dir) || !is_writable($target_dir)) {
            throw new Exception("Image directory is not writable.");
        }

        // Sanitize file name
        $foto = preg_replace("/[^A-Za-z0-9._-]/", "", basename($foto));
        $target_file = $target_dir . $foto;

        if (!move_uploaded_file($_FILES['foto']['tmp_name'], $target_file)) {
            throw new Exception("File upload failed.");
        }

        // Prepare and bind
        $stmt = $pdo->prepare("INSERT INTO place (Adress, Name, Owner, Description, Foto, Price, Coordinates, User_id) VALUES (:address, :name, :owner, :description, :foto, :price, :coordinates, :user_id)");
        $stmt->bindParam(':address', $_POST['address']);
        $stmt->bindParam(':name', $_POST['name']);
        $stmt->bindParam(':owner', $_POST['owner']);
        $stmt->bindParam(':description', $_POST['description']);
        $stmt->bindParam(':foto', $foto);
        $stmt->bindParam(':price', $_POST['price']);
        $stmt->bindParam(':coordinates', $_POST['coordinates']);
        $stmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);

        // Execute
        $stmt->execute();
        header("Location: ../HTML/addPlace.php?message=" . urlencode("New place added successfully!"));
        exit();
    } catch (PDOException $e) {
        header("Location: ../HTML/addPlace.php?error=" . urlencode("Database Error: " . $e->getMessage()));
        exit();
    } catch (Exception $e) {
        header("Location: ../HTML/addPlace.php?error=" . urlencode("Error: " . $e->getMessage()));
        exit();
    }
} else {
    header("Location: ../HTML/addPlace.php?error=" . urlencode("Invalid request method."));
    exit();
}