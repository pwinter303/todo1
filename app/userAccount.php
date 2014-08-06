<?php

session_start();

include 'db.php';
include 'functions.php';

$json = processRequest();
echo $json;

function processRequest(){

  if (isset($_SESSION['customer_id'])) {
    $customer_id = $_SESSION['customer_id'];
  }   else  {
     die ('invalid customer id');
  }

    switch ($_SERVER['REQUEST_METHOD']) {
       case 'POST':
             $result = processPost($customer_id);
             break;
       case 'GET':
             $result = processGet($customer_id);
             break;
       default:
             echo "Error:Invalid Request";
             break;
    }

    $json = json_encode($result);
    return $json;
}

####################  GETs ################################
function  processGet($customer_id){

    $dbh = createDatabaseConnection();

    $action = htmlspecialchars($_GET["action"]);
    switch ($action) {
       case 'getAccountPeriod':
             $result = getAccountPeriod($dbh, $customer_id);
             break;
       case 'getEmail':
             $result = getEmail($dbh, $customer_id);
             break;
       default:
             echo "Error:Invalid Request:Action not set properly";
             break;
    }

    return $result;
}


####################  POSTs ################################
function  processPost($customer_id){

    $dbh = createDatabaseConnection();

    $postdata = file_get_contents("php://input");

    //var_dump($postdata);

    $request = json_decode($postdata);

    $action = $request->action;
    switch ($action) {
       case 'processPayment':
             $result = processPayment($dbh, $customer_id, $request);
             break;
       case 'contactSubmit':
             $result = contactSubmit($dbh, $customer_id, $request);
             break;
       default:
             echo "Error:Invalid Request:action not set properly";
             break;
    }

    return $result;
}


####################  FUNCTIONS ################################


####################  FUNCTIONS ################################
function  processPayment($dbh, $customer_id, $request){

  $basepath = dirname(dirname($_SERVER['SCRIPT_FILENAME']));
  require_once($basepath.'/config/config_stripe.php');
  //require_once('../config/config_stripe.php');

  //$token  = $_POST['stripeToken'];
  $token  = $request->id;
  // echo "this is id:$token";

  $email  = $request->email;
  //echo "this is email:$email";

  //Documentation: https://stripe.com/docs/tutorials/charges

  //This creates a customer abstraction at stripe which can be used later
  $customer = Stripe_Customer::create(array(
      'email' => $email,
      'card'  => $token
  ));

  setStripeCustomerId($dbh, $customer_id, $customer->id);

  //this charges the customer...
  $pmt_amt = 1000;
  try {
      $charge = Stripe_Charge::create(array(
          'customer' => $customer->id,
          'amount'   => $pmt_amt,
          'currency' => 'usd'
      ));
  } catch(Stripe_CardError $e) {
    //print('Message is:' . $err['message'] . "\n");
    $response{'err'} = $err['message'];
  }

  //if the return from stripe has the paid field set to true... continue processing...
  if($charge['paid']){

      ### Add Event
      $response = addEvent($dbh, $customer_id, 5, date('Y-m-d H:i:s') );  # 5 = Payment
      $event_id = $response{'LastInsertId'};

      ### Add Payment Record
      $payment_method_cd = 1; #1:credit card
      $pmt_amt = $pmt_amt/100; ##divide by 100 since 1000 is $10.00 for stripe.
      addPayment($dbh, $customer_id, $pmt_amt, $event_id, $payment_method_cd, date('Y-m-d'));

      ### Add/Adjust Account Periods
      setAcctPeriodsForPayment($dbh, $customer_id, $event_id);

      $response{'msg'} = "Successfully charged $" . $pmt_amt .". Thank you!";
  }

  return $response;
}

function contactSubmit($dbh, $customer_id, $request){

  var_dump($request);

}

?>