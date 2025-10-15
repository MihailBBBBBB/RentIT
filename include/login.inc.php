<?php

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    $password = $_POST["password"];

    try {
        require_once "dbh.inc.php";

        $query = "SELECT * FROM users WHERE mail = ?;";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$email]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // Check if password matches
            if ($password === $user['Password']) {  
                session_start();
                $_SESSION['user_id'] = $user['User_id']; 
                $_SESSION['user_email'] = $user['Mail'];
                $_SESSION['is_admin'] = $user['Is_admin'];

                header("Location: ../HTML/index.php"); 
                exit();
            } else {
                header("Location: ../HTML/Login.php?error=Wrong password");
                exit();
            }
        } else {
            // User not found
            header("Location: ../HTML/Login.php?error=usernotfound");
            exit();
        }

    } catch (PDOException $e) {
        die("Query failed: " . $e->getMessage());
    }
} else {
    header("Location: ../HTML/Login.php");
    exit();
}

