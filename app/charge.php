<?php

  require_once(dirname(__FILE__) . '/config_stripe.php');

  $token  = $_POST['stripeToken'];

  $customer = Stripe_Customer::create(array(
      'email' => 'customer@example.com',
      'card'  => $token
  ));

  $charge = Stripe_Charge::create(array(
      'customer' => $customer->id,
      'amount'   => 1000,
      'currency' => 'usd'
  ));

  echo '<h1>Successfully charged $10.00. Thank you!</h1>';
?>

