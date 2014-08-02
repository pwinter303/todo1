<?php

session_start();

include 'db.php';
include 'functions.php';

// Include the phpass library
require_once('bower_components/phpass-0.3/PasswordHash.php');


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
    //var_dump('x');

    $dbh = createDatabaseConnection();

    $action = $request->action;
    switch ($action) {
       case 'registerUser':
            $email = $request->email;
            $pass = $request->password;
            $pass2 = $request->repassword;
            $result = registerUser($dbh, $email, $pass, $pass2);
            break;
       case 'loginUser':
            $email = $request->email;
            $pass = $request->password;
            $result = loginUser($dbh, $email, $pass);
            break;
       case 'changePassword':
            $passOld = $request->old;
            $pass1 = $request->new1;
            $pass2 = $request->new2;
            $result = changePassword($dbh, $passOld, $pass1, $pass2);
            break;
       case 'forgotPassword':
            $email = $request->email;
            $result = changePassword($dbh,$email);
            break;
       case 'logOutUser':
             $result = logOutUser();
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
              $response{'error'} = "ERROR - eMail already registered";
      } else {
        $return_status = addUser($dbh, $userName, $password);
        if ($return_status){
          $response{'msg'} = "Successful Registration";
          $call_response = loginUser($dbh, $userName, $password);
          $response{'login'} = $call_response{'login'};
          if ($call_response{'login'}){
            ##### Add Todo_Group and Create Event, account_period, and xref.....
            if (isset($_SESSION['customer_id'])) {
              $customer_id = $_SESSION['customer_id'];
              addTodoGroup($dbh, $customer_id);
              addEvent($dbh, $customer_id, 1, date('Y-m-d') );  # 1 = Registration
            }
          }
        } else {
          $response{'error'} = "ERROR - Could not register you";
        }
      }
    }
    return $response;
}


function addUser($dbh, $email, $password){

    // Initialize the hasher without portable hashes (this is more secure)
    $hasher = new PasswordHash(8, false);

    // Hash the password.  $hashedPassword will be a 60-character string.
    $hashedPassword = $hasher->HashPassword($password);

    $query = "Insert into customer (email, password) VALUES ('$email', '$hashedPassword')";
    $rowsAffected = actionSql($dbh,$query);
    return $rowsAffected;
}

function  addTodoGroup($dbh, $customer_id){
  $query = "INSERT INTO todo_group (group_name, Sort_Order,customer_id, active) VALUES
    ('Home',1,$customer_id,1),
    ('Work',2,$customer_id,0)
    "
  ;
  #### add new group
  $rowsInserted = insertData($dbh, $query);

}


##
function loginUser($dbh, $email, $password){


    //fixme: instead of duplicate code have this call validate password (?)

    // Initialize the hasher without portable hashes (this is more secure)
    $hasher = new PasswordHash(8, false);

    $query = "SELECT customer_id, password fROM customer where email = '$email' ";
    #####echo "$query";
    $data = execSqlSingleRow($dbh, $query);
    $customer_id = $data['customer_id'];
    $hashedPassword = $data['password'];

    //echo "pwd: $hashedPassword";

    $valid = $hasher->CheckPassword($password, $hashedPassword); // true

    if (($valid) and ($customer_id)) {
    //if ($customer_id){
      $response{'login'} = 1;
      $_SESSION['authenticated'] = 1;
      $_SESSION['customer_id'] = $customer_id;
      addEvent($dbh, $customer_id, 2, date('Y-m-d') );  # 2 = Login
    } else {
      $response{'login'} = 0;
    }
    // dont close the connection... it is needed in registeruser for additional processing.....
    //closeDatabaseConnection($dbh);

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
                $valid = validatePassword($dbh, $customer_id, $oldPassword);

                if ($valid){
                    $rowsAffected = setPassword($dbh, $customer_id, $password, 6);  # 6 = Password Change
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


function setPassword($dbh, $customer_id, $password, $event_cd){
    // Initialize the hasher without portable hashes (this is more secure)
    $hasher = new PasswordHash(8, false);
    // Hash the password.  $hashedPassword will be a 60-character string.
    $hashedPassword = $hasher->HashPassword($password);

    $query = "update customer set password = '$hashedPassword' where customer_id = $customer_id";
    $rowsAffected = actionSql($dbh,$query);

    # addEvent
    addEvent($dbh, $customer_id, $event_cd, date('Y-m-d') );

    return $rowsAffected;
}

function validatePassword($dbh, $customer_id, $password){
    // Initialize the hasher without portable hashes (this is more secure)
    $hasher = new PasswordHash(8, false);

    // Hash the password.  $hashedPassword will be a 60-character string.
    //$hashedPassword = $hasher->HashPassword($password);
    //$query = "SELECT customer_id fROM customer where customer_id = $customer_id and password = '$hashedPassword' ";
    $query = "SELECT password FROM customer where customer_id = $customer_id ";
    //echo "this is password:$password  and  query: $query";
    $data = execSqlSingleRow($dbh, $query);

    $hashedPassword = $data['password'];
    $valid = $hasher->CheckPassword($password, $hashedPassword); // true

//    if ($valid){
//      $response = 1;
//    } else {
//      $response = 0;
//    }
    return $valid;
}

function forgotPassword($dbh, $email){
    #see if customer exists
    if (doesUserExist($dbh, $email)){
        # get customer_id
        $customer_id = getCustomerId($dbh, $email);

        # set credential_cd
        $response = setCustomerCredentialCd($dbh, $customer_id, 2); ## 2:Temp Password Issued

        # generate password
        $password = generatePassword();

        # email password
        //fixme: eMail password...

        # save password
        $rowsAffected = setPassword($dbh, $customer_id, $password, 8);  # 8:Temp Pwd Created
        if ($rowsAffected){
            $response{'msg'} = "Temporary Password Mailed";
        } else {
            $response{'error'} = "ERROR - Temporary Password Could not be Generated.";
        }

    } else {
        $response{'error'} = "ERROR - eMail Not Found";
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
  $response{'login'} = 0;
  return $response;
}


?>