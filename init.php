<?php

require_once('vendor/autoload.php');

$stripe = [
    'pub_key' => 'pk_test_51IxiZAK74rE8vf3ZJ2KG5q9YhpzgdeUxky6I55II6i0mkQK6NTyfbw8C5Fi3XdQDgpVaPP2aXHX4qhxcutqDPEeK00rdUvNICe',
    'pri_key' => 'sk_test_51IxiZAK74rE8vf3ZfWKcUCjAFgvCLqS83ngibf5pnAtVPk72hF0Vkqny6ZaP40hyXMFOlmxB8GxX4Ubb9g7tSue100tOfHjdmM'
];

\Stripe\Stripe::setApiKey($stripe["pri_key"]);