<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require __DIR__ . '/vendor/autoload.php';

\Stripe\Stripe::setApiKey('sk_test_51SEZ8zGeqy6qTxmF75YgzJwvXY44cUmBol8RmNzEg1x2zFA29IJ34tU0v4xpZdD6mQclGU9Mf0XbXNnsnbaxDVHy00HhGF4WWS');

// ID пользователя, чей баланс пополняем
// Обычно его берут из сессии
session_start();
$user_id = $_SESSION['user_id'] ?? 1; // временно для теста

// Сумма от пользователя
$amount = $_POST['amount'] ?? 0;
$amount_cents = intval($amount * 100); // Stripe требует центы

// Создаём checkout-сессию
$checkout_session = \Stripe\Checkout\Session::create([
    'mode' => 'payment',
    'success_url' => 'http://localhost/RentIT/success.php',
    'cancel_url' => 'http://localhost/RentIT/cancel.php',
    'locale' => 'auto',
    'metadata' => [
        'user_id' => $user_id, // передаём ID пользователя
    ],
    'line_items' => [[
        'quantity' => 1,
        'price_data' => [
            'currency' => 'usd',
            'unit_amount' => $amount_cents,
            'product_data' => [
                'name' => 'Пополнение баланса',
            ],
        ],
    ]],
]);

// Редиректим на страницу оплаты Stripe
http_response_code(303);
header("Location: " . $checkout_session->url);
exit;
