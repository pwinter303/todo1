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
    if ('Activate' == ($_GET["action"])){
      //this is a hack... Activate sends a message direct to page without angular so we dont want to pass JSON
    } else {
      return $json;
    }
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
            $result = forgotPassword($dbh,$email);
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
function registerUser($dbh, $email, $password, $password2){
    if ($password <> $password2){
      $response{'error'} = "ERROR - Passwords do not match";
    } else {
      $exists = doesUserExist($dbh, $email);
      if ($exists){
              $response{'error'} = "ERROR - eMail already registered";
      } else {
        $guid = createGUID();
        $return_status = addUser($dbh, $email, $password, 1, $guid); #1:Awaiting Confirmation eMail return
        if ($return_status){
          eMailActivation($email, $guid);
          $response{'msg'} = "Successful Registration";
          $call_response = loginUser($dbh, $email, $password);
          $response{'login'} = $call_response{'login'};
          if ($call_response{'login'}){
            ##### Add Todo_Group and Create Event, account_period, and xref.....
            if (isset($_SESSION['customer_id'])) {
              $customer_id = $_SESSION['customer_id'];
              addTodoGroup($dbh, $customer_id);
              $addEventResponse = addEvent($dbh, $customer_id, 1, date('Y-m-d H:i:s') );  # 1 = Registration
              $event_id = $addEventResponse{'LastInsertId'};

              #Add Account Period - Trial (Premium)
              #Add Account Period - Free
              setAcctPeriodsForRegistration($dbh, $customer_id, $event_id);
            }
          }
        } else {
          $response{'error'} = "ERROR - Could not register you";
        }
      }
    }
    return $response;
}


function addUser($dbh, $email, $password, $credential_status_cd, $guid){

    //considered doing createGUID within this function... or even letting MySQL create it... but
    //since the guid is needed in the calling function (eg: email it).. kept the creation outside of this

    // Initialize the hasher without portable hashes (this is more secure)
    $hasher = new PasswordHash(8, false);

    // Hash the password.  $hashedPassword will be a 60-character string.
    $hashedPassword = $hasher->HashPassword($password);

    $query = "Insert into customer (email,  password, credential_status_cd,   guid) VALUES
                                 (      ?,         ?,                    ?,      ?)";
    //$rowsAffected = actionSql($dbh,$query);
    $types = 'ssis';  ## pass
    $params = array($email, $hashedPassword, $credential_status_cd, $guid);
    $rowsAffected = execSqlActionPREPARED($dbh, $query, $types, $params);

    return $rowsAffected;
}


function  addTodoGroup($dbh, $customer_id){
  $query = "INSERT INTO todo_group (group_name, sort_order, customer_id, active) VALUES (?,?,?,?)";
  #### add new group
  //$rowsInserted = insertData($dbh, $query);
  $types = 'siii';  ## pass

  $group_name = 'Home';
  $sort_order = 1;
  $active = 1;
  $params = array($group_name, $sort_order, $customer_id, $active);
  $rowsAffected = execSqlActionPREPARED($dbh, $query, $types, $params);
  $group_name = 'Work';
  $sort_order = 2;
  $active = 0;
  $params = array($group_name, $sort_order, $customer_id, $active);
    $rowsAffected = execSqlActionPREPARED($dbh, $query, $types, $params);


}


##
function loginUser($dbh, $email, $password){
    //fixme: instead of duplicate code have this call validate password (?)

    // Initialize the hasher without portable hashes (this is more secure)
    $hasher = new PasswordHash(8, false);

    $query = "SELECT customer_id, password fROM customer where email = ? ";
    //$data = execSqlSingleRow($dbh, $query);

    $types = 's';  ## pass
    $params = array($email);
    $data = execSqlSingleRowPREPARED($dbh, $query, $types, $params);

    $customer_id = $data['customer_id'];
    $hashedPassword = $data['password'];

    //echo "pwd: $hashedPassword";

    $valid = $hasher->CheckPassword($password, $hashedPassword); // true

    if (($valid) and ($customer_id)) {
    //if ($customer_id){
      $response{'login'} = 1;
      $_SESSION['authenticated'] = 1;
      $_SESSION['customer_id'] = $customer_id;
      addEvent($dbh, $customer_id, 2, date('Y-m-d H:i:s') );  # 2 = Login
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

    $query = "update customer set password = ? where customer_id = ?";
    //$rowsAffected = actionSql($dbh,$query);
    $types = 'si';  ## pass
    $params = array($hashedPassword, $customer_id);
    $rowsAffected = execSqlActionPREPARED($dbh, $query, $types, $params);

    # addEvent
    addEvent($dbh, $customer_id, $event_cd, date('Y-m-d H:i:s') );

    return $rowsAffected;
}

function validatePassword($dbh, $customer_id, $password){
    // Initialize the hasher without portable hashes (this is more secure)
    $hasher = new PasswordHash(8, false);

    // Hash the password.  $hashedPassword will be a 60-character string.
    //$hashedPassword = $hasher->HashPassword($password);
    //$query = "SELECT customer_id fROM customer where customer_id = $customer_id and password = '$hashedPassword' ";

    $query = "SELECT password FROM customer where customer_id = ? ";
    //$data = execSqlSingleRow($dbh, $query);
    $types = 'i';  ## pass
    $params = array($customer_id);
    $data = execSqlSingleRowPREPARED($dbh, $query, $types, $params);

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

        # generate password
        $password = generatePassword();

        # email password
        eMailForgotPassword($email, $password);

        # set credential_cd
        $response = setCustomerCredentialCd($dbh, $customer_id, 2); ## 2:Temp Password Issued

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

    $dbh = createDatabaseConnection();

    $action = htmlspecialchars($_GET["action"]);
    switch ($action) {
       case 'getLoginStatus':
             $result = getLoginStatus();
             break;
       case 'Activate':
             $GUID = htmlspecialchars($_GET["GUID"]);
             $result = Activate($dbh, $GUID);
             break;
       default:
             echo "action:$action<----";
             echo "Error:Invalid Request:Action not set properly";
             break;
    }

    return $result;

}


function Activate($dbh, $GUID){

    $data = getCustomerIdUsingGUID($dbh, $GUID);
    $customer_id = $data{'customer_id'};

    if ($customer_id){
      $response = setCustomerCredentialCd($dbh, $customer_id, 0); #0:Legitimate
      echo "Thank you for activating your account!";
    } else {
      echo "Hmmm... Could not find a matching record.";
    }

    //return $response;
}

function getLoginStatus(){
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