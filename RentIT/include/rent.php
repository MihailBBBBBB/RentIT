<?php
session_start();
require_once 'dbh.inc.php';

// Проверка метода запроса и авторизации
if ($_SERVER["REQUEST_METHOD"] !== "POST" || !isset($_SESSION['user_id'])) {
    $place_id = (int)($_GET['id'] ?? 0);
    header("Location: ../HTML/aboutOffer.php?id=$place_id&error=" . urlencode("Please log in to book a table"));
    exit();
}

// Проверка CSRF
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    $place_id = (int)($_POST['place_id'] ?? 0);
    header("Location: ../HTML/aboutOffer.php?id=$place_id&error=" . urlencode("Invalid CSRF token"));
    exit();
}

// Получаем данные бронирования
$place_id = (int)($_POST['place_id'] ?? 0);
$user_id = (int)$_SESSION['user_id'];
$date = $_POST['date'] ?? '';
$time = $_POST['time'] ?? '';
$duration = (int)($_POST['duration'] ?? 0);

try {
    // Получаем цену за час и баланс пользователя
    $stmt = $pdo->prepare("SELECT Price FROM place WHERE Place_id = ?");
    $stmt->execute([$place_id]);
    $place = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$place) throw new Exception("Venue not found");

    $price_per_hour = (float)$place['Price'];

    $stmt = $pdo->prepare("SELECT Balance FROM users WHERE User_id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$user) throw new Exception("User not found");

    $total_price = $price_per_hour * $duration;
    $balance = (float)$user['Balance'];

    if ($balance < $total_price) {
        header("Location: ../HTML/aboutOffer.php?id=$place_id&error=" . urlencode("Insufficient balance"));
        exit();
    }

    // Проверяем конфликтующие брони
    $res_start = new DateTime("$date $time");
    $res_finish = clone $res_start;
    $res_finish->modify("+$duration hours");

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
    if ($stmt->fetchColumn() > 0) {
        header("Location: ../HTML/aboutOffer.php?id=$place_id&error=" . urlencode("This time slot is already booked"));
        exit();
    }

    // Всё ок — списываем баланс и создаем бронь в транзакции
    $pdo->beginTransaction();

    // Списание
    $stmt = $pdo->prepare("UPDATE users SET Balance = Balance - ? WHERE User_id = ?");
    $stmt->execute([$total_price, $user_id]);

    // Создание брони
    $stmt = $pdo->prepare("INSERT INTO reservation (Place_id, User_id, Res_start, Res_finish) VALUES (?, ?, ?, ?)");
    $stmt->execute([
        $place_id,
        $user_id,
        $res_start->format('Y-m-d H:i:s'),
        $res_finish->format('Y-m-d H:i:s')
    ]);

    $pdo->commit();

    header("Location: ../HTML/aboutOffer.php?id=$place_id&success=" . urlencode("Table booked successfully! Total: €" . number_format($total_price, 2)));
    exit();

} catch (Exception $e) {
    $pdo->rollBack();
    header("Location: ../HTML/aboutOffer.php?id=$place_id&error=" . urlencode("Error: " . $e->getMessage()));
    exit();
}
?>
