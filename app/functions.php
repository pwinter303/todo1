<?php

###################################
function  getTodo($dbh, $customer_id, $todo_id){
  $query = "select todo_id, group_id, task_name, DATE_FORMAT(due_dt,'%m/%d/%Y') AS due_dt, starred, priority_cd,
                 frequency_cd, status_cd, note, done, tags, done_dt from todo where customer_id = $customer_id and todo_id = $todo_id";
  $data = execSqlSingleRow($dbh, $query);
  return $data;
}

###################################
function  getTodos($dbh, $customer_id){

  $query = "select todo_id, group_id, task_name, DATE_FORMAT(due_dt,'%m/%d/%Y') AS due_dt, starred, priority_cd,
  frequency_cd, status_cd, note, done, tags, done_dt from todo
  where customer_id = $customer_id and
  ((done_dt is NULL) or (done_dt >= CURDATE() - INTERVAL 1 DAY))
  order by priority_cd desc";
  $data = execSqlMultiRow($dbh, $query);
  #$data{0}{'done'} = true;
  #echo "my data:". $data{0}{'done'};
  $data = convertToBoolean($data);
  return $data;
}

###################################
function  getGroups($dbh, $customer_id){
  $query = "select group_id, group_name, sort_order, active from todo_group where customer_id = $customer_id order by sort_order asc";
  $data = execSqlMultiRow($dbh,$query);
  $data = convertToBoolean($data);
  return $data;
}

###################################
function  getGroup($dbh, $customer_id, $group_id){
  $query = "select group_id, group_name, sort_order, active from todo_group where customer_id = $customer_id and group_id = $group_id order by sort_order asc";
  $data = execSqlSingleRow($dbh,$query);
  $data = convertToBoolean($data);
  return $data;
}

###################################
function getFrequencies($dbh){
  $query = "select frequency_cd as cd, frequency_name as name from todo_frequency order by 1";
  $data = execSqlMultiRow($dbh, $query);
  return $data;
}

###################################
function getPriorities($dbh){
  $query = "select priority_cd as cd, priority_name as name from todo_priority order by 1";
  $data = execSqlMultiRow($dbh, $query);
  return $data;
}

###################################
function convertToBoolean($mainArray){
  foreach ($mainArray as $primaryKey => $fieldValuePairs){
    foreach ($fieldValuePairs as $fieldKey => $value){
      if (("done" == $fieldKey) or ("active" == $fieldKey)){
        if (1 == $value){
          $mainArray{$primaryKey}{$fieldKey} = true;
        } else {
          $mainArray{$primaryKey}{$fieldKey} = false;
        }
      }
    }
  }
  return $mainArray;
}

###################################
function convertFromBoolean($mainArray){
    foreach ($mainArray as $fieldKey => $value){
      if ("done" == $fieldKey){
        if (true == $value){
          $mainArray->$fieldKey = 1;
        } else {
          $mainArray->$fieldKey = 0;
        }
      }
  }
  return $mainArray;
}



###################################
function  updateTodo($dbh, $request_data, $customer_id){
  $todo_id = $request_data->todo_id;
  $priority_cd = $request_data->priority_cd;
  $frequency_cd = $request_data->frequency_cd;
  $status_cd = $request_data->status_cd;
  $task_name = mysqli_real_escape_string($dbh, $request_data->task_name);
  $tags = mysqli_real_escape_string($dbh, $request_data->tags);
  $note = $request_data->note;

  $done = $request_data->done;
  if ('1' == $done){
    $done_dt_sql = "done_dt = CURDATE()";
  } else {
    $done_dt_sql = "done_dt = NULL";
  }

  $due_dt = $request_data->due_dt;
  if (strlen($due_dt)){
    $due_dt = doDateStuff($due_dt);
  }

  // if it is still a valid date after the doDateStuff routine... then update
  if (strlen($due_dt)){
      //$due_dt_sql = "due_dt = STR_TO_DATE('$due_dt', '%Y-%m-%d'),";
      $due_dt_sql = "due_dt = STR_TO_DATE('$due_dt', '%m/%d/%Y'),";
  } else {
      $due_dt_sql = "due_dt = NULL,";
  }

  $query = "update todo set
  priority_cd = $priority_cd,    frequency_cd = $frequency_cd,    status_cd = $status_cd,        task_name = '$task_name',
  $due_dt_sql                    tags = '$tags',                  note = '$note',                done = '$done',
  $done_dt_sql
  where customer_id = $customer_id and todo_id = $todo_id";

  #####echo "$query       ENDOFQUERY";
  $rowsAffected = actionSql($dbh,$query);

  $new_todo_data = getTodo($dbh, $customer_id, $todo_id);
  return $new_todo_data;
  //return $rowsAffected;
}

###################################
function  addTodo($dbh, $request_data, $customer_id, $batch_id_parm = 0){

  if (0 == strlen($request_data->task_name)){
      $response{'err'}=1;
      $response{'errMsg'}="Todo name is blank. Nothing was added";
      return $response;
  } else {
    $group_id = $request_data->activegroup;
    $response = checkFreeTodoThresholds($dbh, $customer_id, $group_id);

    if ($response{'err'}){
      //this is a problem... can't add more todos... limit has been reached....
      return $response;
    } else {

        $request_data = explodeTodoName($request_data);

        $priority_cd = 5;
        if (isset($request_data->priority_cd)){
          $priority_cd = $request_data->priority_cd;
        }
        $frequency_cd = 1;
        if (isset($request_data->frequency_cd)){
          $frequency_cd = $request_data->frequency_cd;
        }

        $due_dt = "NULL";
        if (isset($request_data->due_dt)){
          $due_dt = $request_data->due_dt;
          $due_dt = "STR_TO_DATE('$due_dt', '%m/%d/%Y')";
        }

        $tags = '';
        if (isset($request_data->tags)){
          $tags = $request_data->tags;
        }

        $batch_id = "NULL";
        if ($batch_id_parm > 0){
          $batch_id = $batch_id_parm;
        }
        $status_cd = 1;

        $task_name = mysqli_real_escape_string($dbh, $request_data->task_name);

        $query = "INSERT INTO todo
        (  task_name,   due_dt  , starred,  group_id,   priority_cd,  frequency_cd,  status_cd,  customer_id, Note, done, done_dt, batch_id,   tags)  VALUES
        ('$task_name',  $due_dt , 0      , $group_id,  $priority_cd, $frequency_cd, $status_cd, $customer_id, '',      0, NULL   , $batch_id, '$tags')";

        $rowsAffected = actionSql($dbh,$query);
        $todo_id = mysqli_insert_id($dbh);
        $new_todo_data = getTodo($dbh, $customer_id, $todo_id);

        return $new_todo_data;
    }
  }
}

function  explodeTodoName($request){

  //var_dump($request);

  $task_name = $request->task_name;

  $daysOfWeek = array('Sunday', 'Sun', 'Monday', 'Mon', 'Tuesday', 'Tue', 'Wednesday', 'Wed', 'Thursday',
   'Thu', 'Friday', 'Fri', 'Saturday', 'Sat'
   );

  if (preg_match('/|/', $task_name)) {
      $fields = explode('|',$task_name);
      $request->task_name = $fields[0];
      unset($fields[0]);     # remove the task name
      $fields = array_values($fields);  #re-index

      foreach ($fields as $value) {
          $foundPriorityMatch = 0;
          $foundDueDtMatch = 0;

          if(preg_match('/^\d+$/',$value)){
            $request->priority_cd= $value;
            $foundPriorityMatch=1;
          }
          if(preg_match('/\//',$value)){
            $due_dt = doDateStuff($value);
            $request->due_dt = $due_dt;
            $foundDueDtMatch=1;
          }

          foreach ($daysOfWeek as $day) {
            //echo "day:$day value:$value\n";
            if(preg_match("/$day/i",$value)){
              $due_dt = doDateStuff($value);
              //echo "this is due_dt after the call $due_dt\n";
              $request->due_dt = $due_dt;
              $foundDueDtMatch=1;
            }
          }

          if ((0 == $foundPriorityMatch) and (0 == $foundDueDtMatch)){
            $request->tags = $value;
          }

      }
  }

  //var_dump($request);
  return $request;
}


function  isPremiumAccount($dbh, $customer_id){
    $query = "select count(*) as TrueInd from account_period where customer_id = $customer_id and
    begin_dt <= CURDATE() and end_dt >= CURDATE() and account_type_cd in (1,3)";
    $data = execSqlSingleRow($dbh, $query);
    return $data{'TrueInd'};
}

function checkFreeTodoThresholds($dbh, $customer_id, $group_id){

    $response{'err'} = 0;

    if (isPremiumAccount($dbh, $customer_id)){
        // customer is premium... no need to check anything else...
    } else {
        $response = checkFreeTodoThreshold($dbh, $customer_id);

        if (!$response{'err'}){
            $response = checkFreeTodoWithinGroupThreshold($dbh, $customer_id, $group_id);
        }
    }
    return $response;
}



function  checkFreeTodoThreshold($dbh, $customer_id){
    $query = "select count(*) as todo_count from todo where customer_id = $customer_id and done = 0";
    $data = execSqlSingleRow($dbh, $query);
    $response{'err'}=0;
    //echo "this is the todo count:" . $data{'todo_count'};
    if ($data{'todo_count'} > 50){
        $response{'err'}=1;
        $response{'errMsg'}="You've reached the maximum todos (50) across all groups for a free account. Please upgrade by going to Settings, Account/Profile, or delete existing todos";
    }
    return $response;
}

function  checkFreeGroupsThreshold($dbh, $customer_id){
    $response{'err'} = 0;

    if (isPremiumAccount($dbh, $customer_id)){
        // customer is premium... no need to check anything else...
    } else {
        $query = "select count(*) as group_count from todo_group where customer_id = $customer_id";
        $data = execSqlSingleRow($dbh, $query);
        if ($data{'group_count'} > 2){
            $response{'err'}=1;
            $response{'errMsg'}="You've reached the maximum groups (2) for a free account. Please upgrade by going to Settings, Account/Profile, or delete an existing group.";
        }
    }
    return $response;
}

function  checkFreeTodoWithinGroupThreshold($dbh, $customer_id,$group_id){
    $query = "select count(*) as todo_count from todo where customer_id = $customer_id and done = 0 and group_id = $group_id";
    $data = execSqlSingleRow($dbh, $query);
    $response{'err'}=0;
    if ($data{'todo_count'} > 15){
        $response{'err'}=1;
        $response{'errMsg'}="You've reached the maximum todos (15) within a group for a free account. Please upgrade by going to Settings, Account/Profile, or delete existing todos";
    }
    return $response;
}


###################################
function deleteTodo($dbh, $request, $customer_id){
  $todo_id = $request->todo_id;
  $query = "delete from todo where customer_id = $customer_id and todo_id = $todo_id";
  $rowsAffected = actionSql($dbh,$query);
  $response{'RowsDeleted'} = $rowsAffected;
  return $response;
}

###################################
function  moveTodos($dbh, $request_data, $customer_id){
  $from_group_id = $request_data->fromGroup;
  $to_group_id = $request_data->toGroup;

  $query = "update todo todo set group_id = $to_group_id where customer_id = $customer_id and group_id = $from_group_id";

  $rowsAffected = actionSql($dbh,$query);
  if ($rowsAffected) {
    $response{'msg'} = "$rowsAffected todo(s) moved!";
  } else {
    $response{'error'} = "no todos moved";
  }

  return $response;
}

###################################
function  updateGroup($dbh, $request_data, $customer_id){
  $group_name = mysqli_real_escape_string($dbh, $request_data->group_name);
  $group_id = $request_data->group_id;
  $query = "update todo_group set group_name = '$group_name' where customer_id = $customer_id
            and group_id = $group_id";
  $rowsAffected = actionSql($dbh,$query);
  $response{'RowsUpdated'} = $rowsAffected;
  return $response;
}

###################################
function  addGroup($dbh, $request_data, $customer_id){

  if (strlen($request_data->name)){

    $response{'err'}=0;
    $response = checkFreeGroupsThreshold($dbh, $customer_id);

    if ($response{'err'}){
      //this is a problem... can't add more todos... limit has been reached....
      return $response;

    } else {

        #### set all groups to inactive
        $query = "update todo_group set active = 0 where customer_id = $customer_id";
        $rowsAffected = actionSql($dbh,$query);

        ### Get Max Sort_order
        $query = "select max(sort_order) as max_order from todo_group where customer_id = $customer_id";
        $data = execSqlSingleRow($dbh, $query);
        #####var_dump($data);
        $max_sort_order = $data{'max_order'};
        $max_sort_order = $max_sort_order + 1;
        #####echo "max sort order: $max_sort_order";

        #### add new group
        $groupName = mysqli_real_escape_string($dbh, $request_data->name);
        $query = "insert into todo_group (customer_id, group_name, active, sort_order) VALUES ($customer_id, '$groupName', 1, $max_sort_order)";
        $rowsAffected = actionSql($dbh,$query);
        // no need to return the group add since the controller does a full refresh of groups
        //$group_id = mysqli_insert_id($dbh);
        //$new_group = getGroup($dbh, $customer_id, $group_id);

        $response{'RowsAdded'} = $rowsAffected;
    }
  } else {
    // nothing to do
    $response{'err'} = 1;
    $response{'errMsg'} = "Group name is blank. Nothing was added.";
  }
  return $response;
}


###################################
function  setGroupToActive($dbh, $request_data, $customer_id){
  $group_id = $request_data->group_id;

  #### set all groups to inactive
  $query = "update todo_group set active = 0 where customer_id = $customer_id";
  $rowsAffected = actionSql($dbh,$query);

  #### set selected group to active
  $query = "update todo_group set active = 1 where customer_id = $customer_id and group_id = $group_id";
  $rowsAffected = actionSql($dbh,$query);

  $response{'Status'} = 1;

  return $response;

}


###################################
function  deleteGroup($dbh, $request_data, $customer_id){
  $group_id = $request_data->group_id;

  $query = "select count(*) from todo_group where customer_id = $customer_id";
  $data = execSqlSingleRow($dbh, $query);
  $count = $data{'count'};

  if (1 == $count){
      $response{'Msg'} = "Group not deleted. Must have at least one group";
      $response{'RowsDeleted'} = 0;
  } else {
      #### delete Todos associated with the group
      $query = "delete from todo where group_id = $group_id and customer_id = $customer_id";
      $rowsAffected = actionSql($dbh,$query);

      #### delete the group
      $query = "delete from todo_group where group_id = $group_id and customer_id = $customer_id";
      $rowsAffected = actionSql($dbh,$query);
      $response{'RowsDeleted'} = $rowsAffected;

      ### only try and fix actives if something was actually deleted....
      if ($rowsAffected){
          #### Count of actives
          $query = "select count(*) from todo_group where customer_id = $customer_id and active = 1";
          $data = execSqlSingleRow($dbh, $query);
          $count = $data{'count'};
          if (!$count){
            $query = "select group_id, min(sort_order) from todo_group where customer_id = $customer_id";
            $data = execSqlSingleRow($dbh, $query);
            $group_id = $data{'group_id'};

            $query = "update todo_group set active = 1 where customer_id = $customer_id and group_id = $group_id";
            $rowsAffected = actionSql($dbh,$query);
          }
      }
  } ## end of the count on on groups


  return $response;

}

###################################
function doDateStuff($date_string){

  //Replace dashes with slashes
  if (preg_match("/^([0-9]{1,2})\-([0-9]{1,2})/", $date_string)){
    $date_string = strtr($date_string, '-', '/');
  }

  //Replace missing year
  if (preg_match("/^([0-9]{1,2})\/([0-9]{1,2})$/", $date_string)) {
      $date_string = $date_string . "/" . date("Y");
  }

  if (preg_match("/([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{4})/", $date_string, $matches)) {

      //echo "this is date_string within doDateStuff:$date_string\n";

      if (!checkdate($matches[1], $matches[2], $matches[3])) {
          $date_string = "";
      }
  } else {
    // It isnt a valid date so try and convert it into a valid date
    if (($timestamp = strtotime($date_string)) === false) {
      $date_string = "";
    } else {
      $date_string = date("m/d/Y", strtotime("$date_string"));
    }
  }
  return $date_string;
}

################################################################################
function readUploadedFileIntoArray(){
    $csv = array();

    // FixMe: sanitize the data read from the file
    // check there are no errors

    //var_dump($_FILES);
    //echo "this is the error on the file:" . $_FILES['file']['error'];

    if(0 == $_FILES['file']['error']){
        $name = $_FILES['file']['name'];
        $ext = strtolower(end(explode('.', $_FILES['file']['name'])));
        $type = $_FILES['file']['type'];
        $tmpName = $_FILES['file']['tmp_name'];
        // check the file is a csv
        if($ext === 'csv'){

            if(($handle = fopen($tmpName, 'r')) !== FALSE) {
                // necessary if a large csv file
                set_time_limit(0);

                $row = 0;

                while(($data = fgetcsv($handle, 1000, ',')) !== FALSE) {
                    // number of fields in the csv
                    $num = count($data);

                    // get the values from the csv
                    $array[] = $data;

                    // inc the row
                    $row++;
                }
                fclose($handle);
            }
        }
    }
    return array($name, $array);
}

################################################################################
function addBatch($dbh, $file_name, $customer_id ){
  $query = "insert into todo_batch (file_name, upload_dt, customer_id) values ('$file_name', CURTIME(), $customer_id)";
  $rowsAffected = actionSql($dbh,$query);
  $batch_id = mysqli_insert_id($dbh);
  $response{'RowsAdded'} = $rowsAffected;
  $response{'batch_id'} = $batch_id;
  return $response;
}

################################################################################
function updateBatchStats($dbh, $customer_id, $batch_id, $uploaded, $errored, $skipped){
  $query = "update todo_batch set  count_uploaded = $uploaded,   count_error_no_group = $errored,  count_error_above_limit = $skipped
  where customer_id = $customer_id and batch_id = $batch_id";
  $rowsAffected = actionSql($dbh,$query);
  $response{'RowsUpdated'} = $rowsAffected;
  return $response;
}

################################################################################
function deleteBatch($dbh, $request, $customer_id){
  $batch_id = $request->batch_id;
  // fixme: add delete of todos with matching batch_id... or... change table to do cascading delete
  if (!isset($batch_id)){die("cannot delete batch.. missing information in the request");}
  $query = "delete from todo_batch where customer_id = $customer_id and batch_id = $batch_id";
  $rowsAffected = actionSql($dbh,$query);
  $response{'RowsDeleted'} = $rowsAffected;
  return $response;
}

################################################################################
function getBatches($dbh, $customer_id){
  $query = "select batch_id, file_name, upload_dt, count_uploaded, count_error_no_group, count_error_above_limit from todo_batch
  where customer_id = $customer_id order by upload_dt desc";
  $data = execSqlMultiRow($dbh, $query);
  return $data;
}

################################################################################
function addEvent($dbh, $customer_id, $event_cd, $dateTime){
  $query = "INSERT INTO event (customer_id, create_dt, event_cd) VALUES ($customer_id, '$dateTime', $event_cd)";
  $rowsAffected = actionSql($dbh,$query);
  $response{'RowsUpdated'} = $rowsAffected;
  $response{'LastInsertId'} = mysqli_insert_id($dbh);
  return $response;
}

################################################################################
function addAccountPeriod($dbh, $customer_id, $begin_dt, $end_dt, $account_type_cd, $account_period_status_cd, $event_id){
  $query = "INSERT INTO account_period (customer_id, begin_dt, end_dt, account_type_cd, account_period_status_cd, event_id) VALUES (
    $customer_id, '$begin_dt', '$end_dt', $account_type_cd, $account_period_status_cd,$event_id)";
  $rowsAffected = actionSql($dbh,$query);
  $response{'RowsUpdated'} = $rowsAffected;
  $response{'LastInsertId'} = mysqli_insert_id($dbh);
  return $response;
}

################################################################################
function createGUID(){
    mt_srand((double)microtime()*10000);//optional for php 4.2.0 and up.
    $charid = strtoupper(md5(uniqid(rand(), true)));
    $hyphen = chr(45);// "-"
//    $uuid = chr(123)// "{"
//            .substr($charid, 0, 8).$hyphen
//            .substr($charid, 8, 4).$hyphen
//            .substr($charid,12, 4).$hyphen
//            .substr($charid,16, 4).$hyphen
//            .substr($charid,20,12)
//            .chr(125);// "}"

    //dont want to use curly braces
    $uuid =  substr($charid, 0, 8).$hyphen
            .substr($charid, 8, 4).$hyphen
            .substr($charid,12, 4).$hyphen
            .substr($charid,16, 4).$hyphen
            .substr($charid,20,12);

    return $uuid;
}


################################################################################
function eMailActivation($email, $guid){
  $body = "";
  $body .= "Welcome to Todo Giant!\n\r";
  $body .= "Thanks for signing up. Please click the link below to activate your account:\n\r";
  $body .= "https://todogiant.com/login.php?action=Activate&GUID=$guid\n\r";
  $body .= "Thanks Again!";

  eMailSend($email, 'Account Activation', $body, 0);  #0: No word wrap

}

################################################################################
function eMailForgotPassword($email, $password){
  $body = "";
  $body .= "Hi\n\r";
  $body .= "Here is the temporary password that can be used to login to your account:\n\r";
  $body .= "$password\n\r";
  $body .= "Please change it after you've logged into the site";

  eMailSend($email, 'Password Reset', $body, 0);  #0: No word wrap

}

################################################################################
function eMailSend($email, $subject, $body, $wordWrap = 1){
// In case any of our lines are larger than 70 characters, we should use wordwrap()
if ($wordWrap){
  $body = wordwrap($body, 70, "\r\n");
}
// Send
mail($email, $subject, $body);

}

################################################################################
function updateCustomerCredentialCd($dbh, $customer_id, $credential_cd){
  $query = "UPDATE customer set credential_cd = $credential_cd where customer_id = $customer_id";
  $rowsAffected = actionSql($dbh,$query);
  $response{'RowsUpdated'} = $rowsAffected;
  return $response;
}

################################################################################
function getMaxPremiumDt($dbh, $customer_id){
    $query = "select max(end_dt) as end_dt from account_period where customer_id = $customer_id and account_type_cd in (1,3)";  ### 3=Premium,  #1:Trial(Premium)
    $data = execSqlSingleRow($dbh,$query);
    return $data;
}

################################################################################
function getAccountPeriod($dbh, $customer_id){
    $query = "select description, begin_dt, end_dt from  account_period, account_type
    where account_type.account_type_cd = account_period.account_type_cd and account_period_status_cd = 1
    and customer_id = $customer_id
    order by begin_dt asc";   ### 1 = active
    $data = execSqlMultiRow($dbh, $query);
    return $data;
}

################################################################################
function addPayment($dbh, $customer_id, $pmt_amt, $event_id, $payment_method_cd, $pmt_dt){
  $query = "INSERT INTO payment (customer_id, payment_amt, event_id, payment_method_cd, payment_dt) VALUES
  ($customer_id, $pmt_amt, $event_id, $payment_method_cd, '$pmt_dt')";
  $rowsAffected = actionSql($dbh,$query);
  $response{'RowsUpdated'} = $rowsAffected;
  $response{'LastInsertId'} = mysqli_insert_id($dbh);
  return $response;
}

################################################################################
function setCustomerCredentialCd($dbh, $customer_id, $credential_status_cd){
  $query = "UPDATE customer set credential_status_cd = $credential_status_cd where customer_id = $customer_id";
  $rowsAffected = actionSql($dbh,$query);
  $response{'RowsUpdated'} = $rowsAffected;
  return $response;
}

################################################################################
function getCustomerIdUsingGUID($dbh, $guid){
    $query = "SELECT customer_id FROM customer where guid = '$guid' ";
    $data = execSqlSingleRow($dbh, $query);
    return $data;
}

################################################################################
function doesUserExist($dbh, $email){
    #### see if user already exists
    $query = "SELECT count(*) as theCount fROM customer where email = '$email'";
    $data = execSqlSingleRow($dbh, $query);
    $nbrOfCustomers = $data['theCount'];
    if ($nbrOfCustomers){
      return 1;
    } else {
      return 0;
    }
}

################################################################################
function getCustomerId($dbh, $email){
    $query = "SELECT customer_id fROM customer where email = '$email'";
    $data = execSqlSingleRow($dbh, $query);
    if (isset($data['customer_id'])){
        return $data['customer_id'];
    } else {
        return '';
    }
}

################################################################################
function getEmail($dbh, $customer_id){
    $query = "SELECT email fROM customer where customer_id = $customer_id     ";
    $data = execSqlSingleRow($dbh, $query);
    return $data;
}

################################################################################
function generatePassword( $length = 8 ) {
    $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_-=+;:,.?";
    $password = substr( str_shuffle( $chars ), 0, $length );
    return $password;
}

################################################################################
function setAccountPeriodToDone($dbh, $customer_id, $account_type_cd) {
    $query = "UPDATE account_period set account_period_status_cd = 2
    where customer_id = $customer_id and account_period_status_cd = 1
    and account_type_cd = $account_type_cd";
    $rowsAffected = actionSql($dbh,$query);
    $response{'RowsUpdated'} = $rowsAffected;
    return $response;
}

################################################################################
function setStripeCustomerId($dbh, $customer_id, $stripe_customer_id) {
    $query = "UPDATE customer set stripe_customer_id = '$stripe_customer_id'
    where customer_id = $customer_id";
    $rowsAffected = actionSql($dbh,$query);
    $response{'RowsUpdated'} = $rowsAffected;
    return $response;
}

################################################################################
function updateCustomerName($dbh, $customer_id, $first_name, $last_name) {
    $query = "UPDATE customer set first_name = '$first_name', last_name = '$last_name'
    where customer_id = $customer_id";
    $rowsAffected = actionSql($dbh,$query);
    $response{'RowsUpdated'} = $rowsAffected;
    return $response;
}


################################################################################
function setAcctPeriodsForPayment($dbh, $customer_id, $event_id){
    #Get Current Max Premium Date
    $response = getMaxPremiumDt($dbh, $customer_id);
    //var_dump($response);
    $maxdt = $response{'end_dt'};
    #fixme: check for null in the response and default to current date....

    #deactivate any free rows
    setAccountPeriodToDone($dbh, $customer_id, 2); #2:Free

    #add premium period
    $begin_dt = date('Y-m-d', strtotime($maxdt. ' + 1 days'));
    $end_dt =date('Y-m-d', strtotime('+1 year', strtotime($begin_dt)) );

    addAccountPeriod($dbh, $customer_id, $begin_dt, $end_dt, 3, 1, $event_id); #3:Premium;  1:Active

    #add free period
    $begin_dt = date('Y-m-d', strtotime($end_dt. ' + 1 days'));
    $end_dt =date('Y-m-d', strtotime('+1 year', strtotime($begin_dt)) );
    addAccountPeriod($dbh, $customer_id, $begin_dt, $end_dt, 2, 1,$event_id); #2:Free;  1:Active

}

function setAcctPeriodsForRegistration($dbh, $customer_id, $event_id){

    #add Trial (Premium)
    $begin_dt = $currentDate = date("Y-m-d");
    $end_dt =date('Y-m-d', strtotime('+25 day', strtotime($begin_dt)) ); #add 1 year to begin date
    addAccountPeriod($dbh, $customer_id, $begin_dt, $end_dt, 1, 1, $event_id); #1:Trial(Premium) ;  1:Active

    #add free period
    $begin_dt = date('Y-m-d', strtotime($end_dt. ' + 1 days')); #add 1 day to end_dt of Trail
    $end_dt =date('Y-m-d', strtotime('+1 year', strtotime($begin_dt)) );  #add 1 year to begin date
    addAccountPeriod($dbh, $customer_id, $begin_dt, $end_dt, 2, 1,$event_id); #2:Free;  1:Active

}


?>