<?php
require __DIR__ . "/vendor/autoload.php";

$stripe_secret_key = "sk_test_51SEZ8zGeqy6qTxmF75YgzJwvXY44cUmBol8RmNzEg1x2zFA29IJ34tU0v4xpZdD6mQclGU9Mf0XbXNnsnbaxDVHy00HhGF4WWS";  // твой ключ
\Stripe\Stripe::setApiKey($stripe_secret_key);

// Получаем сумму от пользователя
$amount = $_POST['amount'] ?? 0;

// Преобразуем в центы (Stripe требует integer)
$amount_cents = intval($amount * 100);

// Создаём сессию Checkout
$checkout_session = \Stripe\Checkout\Session::create([
    "mode" => "payment",
    "success_url" => "http://localhost/success.php",
    "cancel_url" => "http://localhost/cancel.php",
    "locale" => "auto",
    "line_items" => [
        [
            "quantity" => 1,
            "price_data" => [
                "currency" => "usd",
                "unit_amount" => $amount_cents,
                "product_data" => [
                    "name" => "Пополнение баланса"
                ]
            ]
        ]      
    ]
]);

http_response_code(303);
header("Location: " . $checkout_session->url);
exit;
