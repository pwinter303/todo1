<?php

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



########################################################
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

    return $result;
}

####################  GETs ################################
function  processGet($customer_id){

    $dbh = createDatabaseConnection();

    $action = htmlspecialchars($_GET["action"]);
    switch ($action) {
       case 'gettodo':
             $result = getTodo($dbh, $customer_id, $todo_id);
             break;
       case 'gettodos':
             $result = getTodos($dbh, $customer_id);
             break;
       case 'gettodogroups':
             $result = getGroups($dbh, $customer_id);
             break;
       case 'getfrequencies':
             $result = getFrequencies($dbh);
             break;
       case 'getpriorities':
             $result = getPriorities($dbh);
             break;
       case 'getbatches':
             $result = getBatches($dbh, $customer_id);
             break;
       default:
             echo "Error:Invalid Request:Action not set properly";
             break;
    }

    return $result;
}


####################  POSTs ################################
function  processPost($customer_id){

    $postdata = file_get_contents("php://input");
    $request = json_decode($postdata);
    $request = convertFromBoolean($request);

    $dbh = createDatabaseConnection();

    $action = $request->action;
    switch ($action) {
       case 'updateTodo':
             $result = updateTodo($dbh, $request, $customer_id);
             break;
       case 'addNew':
             $result = addTodo($dbh, $request, $customer_id);
             break;
       case 'addGroup':
             $result = addGroup($dbh, $request, $customer_id);
             break;
       case 'setGroupToActive':
             $result = setGroupToActive($dbh, $request, $customer_id);
             break;
       case 'moveTodos':
             $result = moveTodos($dbh, $request, $customer_id);
             break;
       case 'deleteGroup':
             $result = deleteGroup($dbh, $request, $customer_id);
             break;
       case 'deleteTodo':
             $result = deleteTodo($dbh, $request, $customer_id);
             break;
       case 'updateGroup':
             $result = updateGroup($dbh, $request, $customer_id);
             break;
       case 'deleteBatch':
             $result = deleteBatch($dbh, $request, $customer_id);
             break;
       default:
             echo "Error:Invalid Request:Action not set properly";
             break;
    }

    return $result;
}


?>