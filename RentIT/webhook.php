<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require __DIR__ . '/vendor/autoload.php';
\Stripe\Stripe::setApiKey('sk_test_51SEZ8zGeqy6qTxmF75YgzJwvXY44cUmBol8RmNzEg1x2zFA29IJ34tU0v4xpZdD6mQclGU9Mf0XbXNnsnbaxDVHy00HhGF4WWS');

// Получаем тело запроса от Stripe
$payload = @file_get_contents('php://input');
$event = null;

try {
    $event = \Stripe\Event::constructFrom(json_decode($payload, true));
} catch (\UnexpectedValueException $e) {
    http_response_code(400);
    exit();
}

// Проверяем событие
if ($event->type === 'checkout.session.completed') {
    $session = $event->data->object;
    $user_id = $session->metadata->user_id ?? null;
    $amount = $session->amount_total / 100; // Stripe передаёт в центах

    if ($user_id) {
    try {
        $pdo = new PDO('mysql:host=localhost;dbname=RentIT;charset=utf8', 'root', '');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $stmt = $pdo->prepare("UPDATE users SET Balance = Balance + ? WHERE User_id = ?");
        $stmt->execute([$amount, $user_id]);

        error_log("Пополнение: User_id=$user_id, Amount=$amount, Rows=".$stmt->rowCount());

    } catch (PDOException $e) {
        error_log("Ошибка БД: " . $e->getMessage());
    }
} else {
    error_log("Webhook: Не передан user_id");
}

}

error_log("Webhook вызван: User_id=$user_id, Amount=$amount, Rows=".$stmt->rowCount());


http_response_code(200);
