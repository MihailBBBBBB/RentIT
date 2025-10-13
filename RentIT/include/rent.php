<?php
session_start();
require_once 'dbh.inc.php';

// Verify request method and user session
if ($_SERVER["REQUEST_METHOD"] !== "POST" || !isset($_SESSION['user_id'])) {
    $place_id = (int)($_GET['id'] ?? 0);
    header("Location: ../HTML/aboutOffer.php?id=$place_id&error=" . urlencode("Please log in to book a table"));
    exit();
}

// Validate CSRF token
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    $place_id = (int)($_POST['place_id'] ?? 0);
    header("Location: ../HTML/aboutOffer.php?id=$place_id&error=" . urlencode("Invalid CSRF token"));
    exit();
}

// Collect and sanitize input
$place_id = (int)($_POST['place_id'] ?? 0);
$user_id = (int)$_SESSION['user_id'];
$date = filter_input(INPUT_POST, 'date', FILTER_SANITIZE_STRING) ?? '';
$time = filter_input(INPUT_POST, 'time', FILTER_SANITIZE_STRING) ?? '';
$duration = (int)($_POST['duration'] ?? 0);
$price_per_hour = 0; // To be fetched from the place table

// Validate input
$current_datetime = new DateTime();
$selected_datetime = new DateTime("$date $time");
if ($selected_datetime < $current_datetime) {
    header("Location: ../HTML/aboutOffer.php?id=$place_id&error=" . urlencode("Cannot book a date/time in the past"));
    exit();
}
if ($duration < 1 || $duration > 4) {
    header("Location: ../HTML/aboutOffer.php?id=$place_id&error=" . urlencode("Invalid duration selection"));
    exit();
}

// Fetch place details and check availability
try {
    $stmt = $pdo->prepare("SELECT Price FROM place WHERE Place_id = ?");
    $stmt->execute([$place_id]);
    $place = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$place) {
        header("Location: ../HTML/aboutOffer.php?id=$place_id&error=" . urlencode("Venue not found"));
        exit();
    }
    $price_per_hour = (float)$place['Price'];

    // Calculate Res_finish as a datetime
    $res_start = new DateTime("$date $time");
    $res_finish = clone $res_start;
    $res_finish->modify("+$duration hours");

    // Check for conflicting reservations
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM reservation WHERE Place_id = ? AND (
        (Res_start < ? AND Res_finish > ?) OR
        (Res_start < ? AND Res_finish > ?) OR
        (Res_start >= ? AND Res_finish <= ?)
    )");
    $stmt->execute([
        $place_id,
        $res_finish->format('Y-m-d H:i:s'),
        $res_start->format('Y-m-d H:i:s'),
        $res_finish->format('Y-m-d H:i:s'),
        $res_start->format('Y-m-d H:i:s'),
        $res_start->format('Y-m-d H:i:s'),
        $res_finish->format('Y-m-d H:i:s')
    ]);
    $conflicting_reservations = $stmt->fetchColumn();

    if ($conflicting_reservations > 0) {
        header("Location: ../HTML/aboutOffer.php?id=$place_id&error=" . urlencode("This time slot is already booked"));
        exit();
    }

    // Calculate total price for display
    $total_price = $price_per_hour * $duration;

    // Insert the reservation with datetime values
    $stmt = $pdo->prepare("INSERT INTO reservation (Place_id, User_id, Res_start, Res_finish) VALUES (?, ?, ?, ?)");
    $stmt->execute([
        $place_id,
        $user_id,
        $res_start->format('Y-m-d H:i:s'),
        $res_finish->format('Y-m-d H:i:s')
    ]);

    header("Location: ../HTML/aboutOffer.php?id=$place_id&success=" . urlencode("Table booked successfully! Total: €" . number_format($total_price, 2)));
    exit();
} catch (PDOException $e) {
    header("Location: ../HTML/aboutOffer.php?id=$place_id&error=" . urlencode("Error booking table: " . $e->getMessage()));
    exit();
}
