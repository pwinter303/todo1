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

  //FixMe: Get the customer information... eg eMail...

  $customer = Stripe_Customer::create(array(
      'email' => $email,
      'card'  => $token
  ));

  $charge = Stripe_Charge::create(array(
      'customer' => $customer->id,
      'amount'   => 1000,
      'currency' => 'usd'
  ));


  // FixMe: add logic to handle processing payment
  ### Add Payment Record
  ### Add Event
  ### Add Account Period
  ### Extend Free Account Period

  $response{'msg'} = 'Successfully charged $10.00. Thank you!';

  return $response;



}


?>