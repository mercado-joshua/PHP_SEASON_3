<?php

require_once 'init.php';

$total = $_POST["total"];
$token = $_POST["stripeToken"];

// Charge the user's card:
$charge = \Stripe\Charge::create(array(
    "amount" => $total,
    "currency" => "php",
    "card" => $token,
    "description" => "Nagbayad na!"
));

exit();