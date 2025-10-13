<?php
require_once 'dbh.inc.php'; // Include the database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve and sanitize form data
    $name = trim(filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING));
    $surname = trim(filter_input(INPUT_POST, 'surname', FILTER_SANITIZE_STRING));
    $email = trim(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL));
    $password = trim(filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING));
    $per_kod = trim(filter_input(INPUT_POST, 'per_kod', FILTER_SANITIZE_STRING));

    // Validate inputs
    if (empty($name) || empty($surname) || empty($email) || empty($password) || empty($per_kod)) {
        header("Location: ../HTML/register.php?error=All fields are required.");
        exit();
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("Location: ../HTML/register.php?error=Invalid email format.");
        exit();
    }

    try {
        // Check if email or personal code already exists
        $sql = "SELECT COUNT(*) FROM users WHERE mail = :email OR per_kod = :per_kod";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':email' => $email, ':per_kod' => $per_kod]);
        if ($stmt->fetchColumn() > 0) {
            header("Location: ../HTML/register.php?error=Email or Personal Code already registered.");
            exit();
        }

        // Insert data into users table
        $sql = "INSERT INTO users (name, surname, mail, password, per_kod) VALUES (:name, :surname, :email, :password, :per_kod)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':name' => $name,
            ':surname' => $surname,
            ':email' => $email,
            ':password' => $password,
            ':per_kod' => $per_kod
        ]);

        header("Location: ../HTML/login.php?success=Registration successful. Please log in.");
        exit();
    } catch (PDOException $e) {
        header("Location: ../HTML/register.php?error=Registration failed: " . htmlspecialchars($e->getMessage()));
        exit();
    }
} else {
    header("Location: ../HTML/register.php?error=Invalid request method.");
    exit();
}