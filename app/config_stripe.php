<?php
//require_once('bower_components\stripe-php-1.16.0\lib\Stripe.php');
require_once('bower_components/stripe-php-1.16.0/lib/Stripe.php');

$stripe = array(
  "secret_key"      => "sk_test_ibW1PYW9CWgbwCApmKf1OH72",
  "publishable_key" => "pk_test_fDHNv3NjM0BYMTFjxF2sPWS4"
);

Stripe::setApiKey($stripe['secret_key']);
?>

