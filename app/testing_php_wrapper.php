<?php


#########################
#########################
###  NOTE: I think this should be ditched since the testing_php_driver can just call the functions directly and that's cleaner
#########################
#########################






session_start();

include 'db.php';
include 'functions.php';


if(isset($_SESSION['authenticated'])){
    $result = processRequest();
} else {
    ### must be logged in to use this...
    header('HTTP/1.1 401 Unauthorized');
    return;
}


$json = json_encode($result);
echo $json;


function processRequest(){



  if (isset($_SESSION['customer_id'])) {
    $customer_id = $_SESSION['customer_id'];
  }   else  {
     die ('invalid customer id');
  }

$action = htmlspecialchars($_GET["action"]);
//$customer_id = htmlspecialchars($_GET["customer_id"]);
//echo "action:$action  customer_id:$customer_id";


$dbh = createDatabaseConnection();


switch ($action) {
   case 'getMaxAccountPeriodEndDt':
         $result = getMaxAccountPeriodEndDt($dbh, $customer_id);
         break;
   case 'getAccountPeriod':
         $result = getAccountPeriod($dbh, $customer_id);
         break;
   default:
         echo "Error:Invalid Request:Action not set properly";
         break;
}

    return $result;

}



