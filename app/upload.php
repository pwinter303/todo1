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


################################################################################
function processRequest(){
  if (isset($_SESSION['customer_id'])) {
    $customer_id = $_SESSION['customer_id'];
  }   else  {
     die ('invalid customer id');
  }
  $array = readUploadedFileIntoArray();
  ##var_dump($array);
  $dbh = createDatabaseConnection();
  $header = NULL;
  foreach ($array as $fields){
      ### skip the first row since its a header
      if (!$header){
        $header = $fields;
      } else {
        $result = processUploadedTodo($dbh, $fields, $customer_id);
      }
  }
}
#################################################################
function processUploadedTodo($dbh, $fields, $customer_id){
    ##var_dump($fields);
    list($status, $error_msg, $group_id) = getGroupIdUsingName($dbh, $fields[0], $customer_id);
    echo "$status, $error_msg, $group_id";
}

#################################################################
function getGroupIdUsingName($dbh, $groupName, $customer_id){

    $groupName = trim($groupName);
    $result = getGroups($dbh, $customer_id);
    $groupId = 0;
    foreach ($result as $fields){
      #########var_dump($fields);
      #########var_dump($fields{'group_name'});
      $groupNmFromDB = $fields{'group_name'};
      #########echo "comparing: $groupName to $groupNmFromDB";
      if(strtolower($groupName) == strtolower($groupNmFromDB)) {
          ########echo "found match!";
          $groupId = $fields{'group_id'};
      }
    }
    $status = 0;
    $err = "Group Not Found";
    if ($groupId){
      $status = 1;
      $err = "";
    }

    return array($status, $err, $groupId);

}


?>