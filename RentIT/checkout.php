<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require __DIR__ . "/vendor/autoload.php";

var_dump(class_exists('\Stripe\Stripe'));



$stripe_secret_key = "";  // твой ключ3
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
