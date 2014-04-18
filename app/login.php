<?php

session_start();

include 'db.php';

$json = processRequest();
echo $json;

function processRequest(){

    switch ($_SERVER['REQUEST_METHOD']) {
       case 'POST':
             $result = processPost();
             break;
       case 'GET':
             $result = processGet();
             break;
       default:
             echo "Error:Invalid Request";
             break;
    }

    $json = json_encode($result);
    ######echo $json;
    return $json;
}


function processPost(){
    $postdata = file_get_contents("php://input");
    $request = json_decode($postdata);

    $dbh = createDatabaseConnection();

    $action = $request->action;
    switch ($action) {
       case 'registerUser':
            $email = $request->email;
            $pass = $request->password;
            $pass2 = $request->repassword;
            $result = registerUser($dbh, $email, $pass, $pass2);
            break;
       case 'validateUser':
            $email = $request->email;
            $pass = $request->password;
            $result = validateUser($dbh, $email, $pass);
            break;
       case 'changePassword':
            $passOld = $request->old;
            $pass1 = $request->new1;
            $pass2 = $request->new2;
            $result = changePassword($dbh, $passOld, $pass1, $pass2);
            break;
       case 'logOutUser':
             logOutUser();
             break;
       default:
             echo "Error:Invalid Request:Action not set properly";
             break;
    }

    return $result;
}

#####
function registerUser($dbh, $userName, $password, $password2){
    if ($password <> $password2){
      $response{'error'} = "ERROR - Passwords do not match";
    } else {
      $exists = doesUserExist($dbh, $userName);
      if ($exists){
              $response{'error'} = "ERROR - customer already exists";
      } else {
        $return_status = addUser($dbh, $userName, $password);
        if ($return_status){
          $response{'msg'} = "Successful Registration";
          $call_response = validateUser($userName, $password);
          $response{'login'} = $call_response{'login'};
          if ($call_response{'login'}){
            ##### Add Todo_Group.....
            if (isset($_SESSION['customer_id'])) {
              $customer_id = $_SESSION['customer_id'];
              addTodoGroup($dbh, $customer_id);
            }
          }
        } else {
          $response{'error'} = "ERROR - Could not register you";
        }
      }
    }
    return $response;
}

function doesUserExist($dbh, $userName){
    #### see if user already exists
    $query = "SELECT count(*) as theCount fROM customer where user_name = '$userName'";
    $data = execSqlSingleRow($dbh, $query);
    $nbrOfCustomers = $data['theCount'];
    if ($nbrOfCustomers){
      return 1;
    } else {
      return 0;
    }
}

function addUser($dbh, $userName, $password){
    $query = "Insert into customer (user_name, password) VALUES ('$userName', '$password')";
    $rowsAffected = actionSql($dbh,$query);
    return $rowsAffected;
}

function  addTodoGroup($dbh, $customer_id){
  $query = "INSERT INTO todo_group (group_name, Sort_Order,customer_id, active) VALUES
    ('Home',1,$customer_id,1),
    ('Work',2,$customer_id,0)
    "
  ;
  ##echo "$query";
  #### add new group
  $rowsInserted = insertData($dbh, $query);

}


##
function validateUser($dbh, $userName, $password){
    $query = "SELECT customer_id fROM customer where user_name = '$userName' and password = '$password' ";
    $data = execSqlSingleRow($dbh, $query);
    $customer_id = $data['customer_id'];

    if ($customer_id){
      $response{'login'} = 1;
      $_SESSION['authenticated'] = 1;
      $_SESSION['customer_id'] = $customer_id;
    } else {
      $response{'login'} = 0;
    }
    closeDatabaseConnection($dbh);

    return $response;
}

function changePassword($dbh, $oldPassword, $password, $password2){
    if ($password <> $password2){
      $response{'error'} = "ERROR - Re-entered password does not match";
    } else {
        if ($oldPassword === $password2){
          $response{'error'} = "ERROR - New password cannot equal old";
        } else {
            if (isset($_SESSION['customer_id'])) {
                $customer_id = $_SESSION['customer_id'];
                $valid = validatePassword($customer_id, $oldPassword);
                if ($valid){
                    $query = "update customer set password = '$password' where customer_id = $customer_id";
                    $rowsAffected = actionSql($dbh,$query);

                    if ($rowsAffected){
                      $response{'msg'} = "Password Changed";
                    } else {
                      $response{'error'} = "ERROR - Password could not be updated.";
                    }
                } else {
                    $response{'error'} = "ERROR - Current password is invalid";
                }
            } else {
              $response{'error'} = "ERROR - Password could not be updated. CustomerID Unavailable.";
            }
        }
    }
    return $response;
}

function validatePassword($dbh, $customer_id, $password){
    $query = "SELECT customer_id fROM customer where customer_id = $customer_id and password = '$password' ";
    $data = execSqlSingleRow($dbh, $query);
    $customer_id = $data['customer_id'];
    if ($customer_id){
      $response = 1;
    } else {
      $response = 0;
    }
    return $response;
}


function processGet(){
    if (isset($_SESSION['authenticated'])) {
        $response{'login'} = 1;
    } else {
        $response{'login'} = 0;
    }
    return $response;
}

function logOutUser(){
  session_unset();
  session_destroy();
}


?>