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
  // TODO: Add a batch ID to keep track of groups of uploaded txn.. to allow for delete
  // TODO: Capture stats: # Uploaded by Group, # Errors, etc
  $dbh = createDatabaseConnection();
  $groups = getGroups($dbh, $customer_id);

  $header = NULL;
  foreach ($array as $fields){
      ### skip the first row since its a header
      if (!$header){
        $header = $fields;
      } else {
        $result = processUploadedTodo($dbh, $fields, $customer_id, $groups);
      }
  }
  return $result;
}
#################################################################
function processUploadedTodo($dbh, $fields, $customer_id, $groups){
    ##var_dump($fields);
    // TODO: Store Group_Name and Group_id to eliminate calls to the database
    // TODO: Should the get_group function be called from here and array created, and passed to function?
    // SAME QUESTION WITH FREQ and PRIORITY

    list($status, $error_msg, $group_id) = getGroupIdUsingName($fields[0], $customer_id, $groups);

    $request_data->activegroup = $group_id;
    $request_data->taskName = $fields[1];
    $request_data->due_dt = $fields[2];
    $request_data->tags = $fields[3];
    $request_data->frequency_cd = getFrequencyCdUsingName($dbh, $fields[4], $customer_id);
    $request_data->priority_cd = getPriorityCdUsingName($dbh, $fields[5], $customer_id);
    // TODO: Upload Frequency and Priority... Decode Both...
    #var_dump($request_data);
    $result = addTodo($dbh, $request_data, $customer_id);
    return $result;

}

#################################################################
function getGroupIdUsingName($groupName, $customer_id, $groups){

    $groupId = 0;
    $groupName = trim($groupName);
    foreach ($groups as $fields){
      $groupNmFromDB = $fields{'group_name'};
      $groupNmFromDB = trim($groupNmFromDB);
      if(strtolower($groupName) == strtolower($groupNmFromDB)) {
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

function getFrequencyCdUsingName($dbh, $name, $customer_id){
  // TODO: Make this dynamic...
  return 1;
}
function getPriorityCdUsingName($dbh, $name, $customer_id){
  // TODO: Make this dynamic...
  return 5;
}


?>