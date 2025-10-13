<?php
session_start();
require_once 'dbh.inc.php';

// Verify the request method and user session
if ($_SERVER["REQUEST_METHOD"] != "POST" || !isset($_SESSION['user_id'])) {
    header("Location: login.php?error=" . urlencode("Please log in to submit a review"));
    exit();
}

// Validate CSRF token
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    $place_id = (int)($_POST['place_id'] ?? 0);
    header("Location: aboutOffer.php?id=$place_id&error=" . urlencode("Invalid CSRF token"));
    exit();
}

// Collect and sanitize input
$place_id = (int)($_POST['place_id'] ?? 0);
$user_id = (int)$_SESSION['user_id'];
$rating = (int)($_POST['rating'] ?? 0);
$comment = filter_input(INPUT_POST, 'comment', FILTER_SANITIZE_STRING) ?? '';
$date = date('Y-m-d');

// Validate input
if ($rating < 1 || $rating > 5 || empty($comment) || strlen($comment) < 10) {
    header("Location: aboutOffer.php?id=$place_id&error=" . urlencode("Invalid rating or comment must be at least 10 characters"));
    exit();
}

try {
    // Prepare and execute the insert query
    $stmt = $pdo->prepare("INSERT INTO reviews (Place_id, User_id, Stars, Comment, Date) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$place_id, $user_id, $rating, $comment, $date]);

    // Update the average rating in the places table (optional, depending on your schema)
    $stmt = $pdo->prepare("UPDATE places SET Stars = (SELECT AVG(Stars) FROM reviews WHERE Place_id = ?) WHERE Place_id = ?");
    $stmt->execute([$place_id, $place_id]);

    header("Location: aboutOffer.php?id=$place_id&success=" . urlencode("Review submitted successfully"));
    exit();
} catch (PDOException $e) {
     header("Location: ../HTML/aboutOffer.php?id=$place_id");
     exit();
}
