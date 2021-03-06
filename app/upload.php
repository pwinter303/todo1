<?php

session_start();
include 'db.php';
include 'functions.php';

if(isset($_SESSION['authenticated'])){
    $total_added = processRequest();
    $result{'msg'} = "$total_added Todo(s) were uploaded!";
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

  list($file_name, $array) = readUploadedFileIntoArray();
  #var_dump($file_name);


  $dbh = createDatabaseConnection();
  $groups = getGroups($dbh, $customer_id);
  $frequencies = getFrequencies($dbh);
  $priorities = getPriorities($dbh);

  $response = addBatch($dbh, $file_name, $customer_id);
  $batch_id = $response{'batch_id'};

  $header = NULL;
  $total_added = 0;
  $total_skipped = 0;
  $total_errored = 0;
  foreach ($array as $fields){
      ### skip the first row since its a header
      if (!$header){
        $header = $fields;
      } else {
        // fixme: Add reject if the number of items exceeds  xxxxx

        // Note: Only process item if the Todo name is populated....
        if (strlen($fields[1])){
           list($uploaded, $errored) = processUploadedTodo($dbh, $fields, $customer_id, $batch_id, $groups, $frequencies, $priorities);
           $total_added +=  $uploaded;
           $total_errored +=  $errored;
        }
      }
  }

  $rows_updated = updateBatchStats($dbh, $customer_id, $batch_id, $total_added, $total_errored, $total_skipped);

  return $total_added;
}
#################################################################
function processUploadedTodo($dbh, $fields, $customer_id, $batch_id, $groups, $frequencies, $priorities){

    list($ok, $error_msg, $group_id) = getGroupIdUsingName($fields[0], $groups);

    // Add the item if the name exists and the group could be decoded
    $todo_added = 0;
    $todo_err = 0;
    $request_data = new stdClass();
    if (($ok) ){
      $request_data->activegroup = $group_id;
      $request_data->task_name = $fields[1];
      $request_data->due_dt = $fields[2];
      $request_data->tags = $fields[3];
      $request_data->frequency_cd = getFrequencyCdUsingName($fields[4], $frequencies);
      $request_data->priority_cd = getPriorityCdUsingName($fields[5], $priorities);
      $result = addTodo($dbh, $request_data, $customer_id, $batch_id);
      //var_dump($result);
      if ($result['todo_id']){ $todo_added = 1;}
      //echo "this is todo_added: $todo_added\n";
    } else {
      $todo_err = 1;
    }

    return array($todo_added, $todo_err);

}




?>
