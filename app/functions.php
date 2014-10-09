<?php

###################################
function  getTodo($dbh, $customer_id, $todo_id){
  $query = "select todo_id, group_id, task_name, DATE_FORMAT(due_dt,'%m/%d/%Y') AS due_dt, starred, priority_cd,
                 frequency_cd, status_cd, note, done, tags, done_dt from todo where customer_id = ? and todo_id = ?";

  $types = 'ii';  ## pass
  $params = array($customer_id, $todo_id);
  $data = execSqlSingleRowPREPARED($dbh, $query, $types, $params);
  $data = convertToBooleanSingle($data);
  $data = convertNullSingle($data);

  return $data;
}

###################################
function  getTodos($dbh, $customer_id){

  $days = getDisplayDaysForDone($dbh, $customer_id);

  $query = "select todo_id, group_id, task_name, DATE_FORMAT(due_dt,'%m/%d/%Y') AS due_dt, starred, priority_cd,
  frequency_cd, status_cd, note, done, tags, done_dt from todo
  where customer_id = ? and
  ((done_dt is NULL) or (done_dt >= CURDATE() - INTERVAL $days DAY))
  order by priority_cd desc";

  $types = 'i';  ## pass
  $params = array($customer_id);
  $data = execSqlMultiRowPREPARED($dbh, $query, $types, $params);
  $data = convertToBoolean($data);
  $data = convertNull($data);

  return $data;
}

###################################
function  getDisplayDaysForDone($dbh, $customer_id){

    $query = "select display_days_done_todos as theDays from customer where customer_id = ? ";
    $types = 'i';  ## pass
    $params = array($customer_id);
    $data = execSqlSingleRowPREPARED($dbh, $query, $types, $params);

    $displayDays = 1;

    if ($data{'theDays'}){
        $displayDays = $data{'theDays'};
    }
    return $displayDays;
}


###################################
function  getGroups($dbh, $customer_id){

  $query = "select group_id, group_name, sort_order, active from todo_group where customer_id = ? order by sort_order asc";

  $types = 'i';  ## pass
  $params = array($customer_id);
  $data = execSqlMultiRowPREPARED($dbh, $query, $types, $params);
  $data = convertToBoolean($data);

  return $data;

}

###################################
function  getGroup($dbh, $customer_id, $group_id){
  $query = "select group_id, group_name, sort_order, active from todo_group where customer_id = ? and group_id = ? order by sort_order asc";

  //$data = execSqlSingleRow($dbh,$query);

  $types = 'ii';  ## pass
  $params = array($customer_id, $group_id);
  $data = execSqlSingleRowPREPARED($dbh, $query, $types, $params);

  $data = convertToBoolean($data);
  return $data;
}

###################################
function getFrequencies($dbh){
  $query = "select frequency_cd as cd, frequency_name as name from todo_frequency where 1 = ? order by 1";
  //$data = execSqlMultiRow($dbh, $query);

  //NOTE: Binding to $dummy was done to use the PREPARED mssql_free_statement
  // the PREPARED statement is needed because the result passes back numeric frequency_cd
  // where the execSqlMultiRow statement was returning strings for frequency_cd and it caused
  // lookup/match within todolist to not pick up the correct frequency_cd
  $dummy=1;
  $types = 'i';  ## pass
  $params = array($dummy);
  $data = execSqlMultiRowPREPARED($dbh, $query, $types, $params);

  return $data;
}

###################################
function getPriorities($dbh){
  $query = "select priority_cd as cd, priority_name as name from todo_priority where 1 = ? order by 1";
  //$data = execSqlMultiRow($dbh, $query);

  //NOTE: See NOTE in the getFrequencies function...
  $dummy=1;
  $types = 'i';  ## pass
  $params = array($dummy);
  $data = execSqlMultiRowPREPARED($dbh, $query, $types, $params);


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
function convertToBooleanSingle($mainArray){
  foreach ($mainArray as $fieldKey => $value){

      if (("done" == $fieldKey) or ("active" == $fieldKey)){
        if (1 == $value){
          $mainArray{$fieldKey} = true;
        } else {
          $mainArray{$fieldKey} = false;
        }
      }
  }
  return $mainArray;
}

###################################
function convertNull($mainArray){
  foreach ($mainArray as $primaryKey => $fieldValuePairs){
    foreach ($fieldValuePairs as $fieldKey => $value){
      if ("due_dt" == $fieldKey) {
        if (null == $value){
          $mainArray{$primaryKey}{'due_dt_sort'} = '12/31/9999';
          $mainArray{$primaryKey}{'glyph'} = 'glyphicon-none';
        } else {
          $mainArray{$primaryKey}{'due_dt_sort'} = $value;
          $mainArray{$primaryKey}{'glyph'} = setGlyphForDueDate($value);
        }
        //Remove icons for done items
        if ($mainArray{$primaryKey}{'done'}){
          $mainArray{$primaryKey}{'glyph'} = 'glyphicon-none';
        }
      }
    }
  }
  return $mainArray;
}

###################################
function convertNullSingle($mainArray){
  foreach ($mainArray as $fieldKey => $value){

      if ("due_dt" == $fieldKey){
        if (null == $value){
          $mainArray{'due_dt_sort'} = '12/31/9999';
          $mainArray{'glyph'} = 'glyphicon-none';
        } else {
          $mainArray{'due_dt_sort'} = $value;
          $mainArray{'glyph'} = setGlyphForDueDate($value);
        }
        //Remove icons for done items
        if ($mainArray{'done'}){
          $mainArray{'glyph'} = 'glyphicon-none';
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


function setGlyphForDueDate($due_date){

date_default_timezone_set('UTC');

$today = date("Y-m-d"); //today

$result = "glyphicon-none";
$one_week_ago = date('m/d/Y', strtotime("-1 week") );
$three_days_ago = date('m/d/Y', strtotime("-3 day") );

$one_week_away = date('m/d/Y', strtotime("+1 week") );
$two_weeks_away = date('m/d/Y', strtotime("+2 week") );

if (strtotime($two_weeks_away) < strtotime($due_date) ){
  $result = "glyphicon-none";  // More than 2 weeks away
}

if (strtotime($one_week_away) < strtotime($due_date) ){
  $result = "glyphicon-none";  // More than 1 week away
}else {
  $result = "glyphicon-none";  // Less than a week away (or maybe late.. will be overridden below)
}

if (strtotime($today) > strtotime($due_date) ){
  $result = "glyphicon-exclamation-sign red1";  // late
}

if (strtotime($three_days_ago) > strtotime($due_date) ){
  $result = "glyphicon-exclamation-sign red2";  // more than 3 days late
}

if (strtotime($one_week_ago) > strtotime($due_date) ){
  $result = "glyphicon-exclamation-sign red3";  // more than a week late
}

return $result;

}

###################################
function  updateTodo($dbh, $request_data, $customer_id){
  $todo_id = $request_data->todo_id;
  $priority_cd = $request_data->priority_cd;
  $frequency_cd = $request_data->frequency_cd;
  $status_cd = $request_data->status_cd;
  $task_name = $request_data->task_name;
  $tags = $request_data->tags;
  $note = $request_data->note;


  $due_dt = $request_data->due_dt;
  if (strlen($due_dt)){
    $due_dt = doDateStuff($due_dt);
  }

  $due_dt_final = NULL;
  if (strlen($due_dt)){
    $due_dt_final = date('Y-m-d', strtotime($due_dt)  );
    //echo "due_dt:$due_dt    and    due_dt_final:$due_dt_final";
  }

  $done_dt_final = NULL;
  $done = $request_data->done;
  if ('1' == $done){
    $done_dt_final = date("Y-m-d");
  }

  $query = "update todo set
  priority_cd = ? ,    frequency_cd = ?,    status_cd =  ?,        task_name =  ?,
  due_dt = ?,         tags =  ?,           note =  ?,             done = ?,
  done_dt = ?
  where customer_id = ? and todo_id = ?";

  //$rowsAffected = actionSql($dbh,$query);

  $types = 'iiissssisii';  ## pass
  $params = array($priority_cd, $frequency_cd, $status_cd, $task_name, $due_dt_final, $tags, $note, $done, $done_dt_final, $customer_id, $todo_id);
  $rowsAffected = execSqlActionPREPARED($dbh, $query, $types, $params);

  $new_todo_data = getTodo($dbh, $customer_id, $todo_id);

  //if todo is done and frequency is something other than 10 (which is Once).. then do processing to replicate todo
  //fixme: re-evaluate this... seems like it'll cause problems to sometimes return an array... sometimes not

  ## change to frequency_cd
  //  if (('1' == $done)  and (!(1 == $frequency_cd))  ){
  if (('1' == $done)  and (!(10 == $frequency_cd))  ){
    $add_todo_data = doFrequencyProcessing($dbh, $customer_id, $new_todo_data);
    $final = array($new_todo_data, $add_todo_data);
    return $final;
  } else {
    return $new_todo_data;
  }
}



###################################
function doFrequencyProcessing($dbh, $customer_id, $new_todo_data){
  #check to see if another task already exists with a parent_todo_id equal to the todo_id.. if it does exist, no need to continue

  $data = doesTodoExistWithThisParentTodoId($dbh, $customer_id, $new_todo_data{'todo_id'});

  if ($data{'MyCount'}){
    //The row has already been created... no need to continue....
  } else {
    $done_dt = $new_todo_data{'done_dt'};
    $due_dt = $new_todo_data{'due_dt'};
    $frequency_cd = $new_todo_data{'frequency_cd'};

    switch ($frequency_cd) {
       case 20:  //Weekly
             $nbr = "+7 ";
             $period = 'day';
             break;
       case 30:  //BiWeekly
             $nbr = "+14 ";
             $period = 'day';
             break;
       case 40:  //Monthly
             $nbr = "+1 ";
             $period = 'month';
             break;
       case 50:  //Quarterly
             $nbr = "+3 ";
             $period = 'month';
             break;
       case 60:  //Yearly
             $nbr = "+1 ";
             $period = 'year';
             break;
       default:
             echo "Error:Invalid Request";
             break;
    }

    //echo "this is due_dt:$due_dt";
    if (NULL == $due_dt){
      $new_due_dt =date('m/d/Y', strtotime("$nbr $period", strtotime($done_dt)) );
    } else {
      $new_due_dt =date('m/d/Y', strtotime("$nbr $period", strtotime($due_dt)) );
    }

    #clean up fields
    ### Done should be 0
    $new_todo_data{'done'} = 0;
    ### Done_Dt should be null
    $new_todo_data{'done_dt'} = 'null';
    ### Due_Dt should be what was calculated above
    $new_todo_data{'due_dt'} = $new_due_dt;
    $new_todo_data{'activegroup'} = $new_todo_data{'group_id'};
    $new_todo_data{'parent_todo_id'} = $new_todo_data{'todo_id'};

    #call addTodo
    $result = json_encode($new_todo_data);
    $resultFinal = json_decode($result);
    $new_todo_data = addTodo($dbh, $resultFinal, $customer_id);
    return $new_todo_data;
  }
}

###################################
function doesTodoExistWithThisParentTodoId($dbh, $customer_id, $parent_todo_id){

  $query = "select count(*) as MyCount from todo where customer_id = ? and parent_todo_id = ?";

  $types = 'ii';  ## pass
  $params = array($customer_id, $parent_todo_id);
  $data = execSqlSingleRowPREPARED($dbh, $query, $types, $params);

  return $data;

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

        $request_data = explodeTodoName($request_data, $dbh);

        $priority_cd = 5;
        if (isset($request_data->priority_cd)){
          $priority_cd = $request_data->priority_cd;
        }

        ## Update frequency_cd
        //        $frequency_cd = 1;
        $frequency_cd = 10;
        if (isset($request_data->frequency_cd)){
          $frequency_cd = $request_data->frequency_cd;
        }


        $done_dt = NULL;

        //$due_dt = "NULL";
        $due_dt = NULL;
        if (isset($request_data->due_dt)){
          //$due_dt = $request_data->due_dt;
          //$due_dt = "STR_TO_DATE('$due_dt', '%m/%d/%Y')";
          $due_dt = date('Y-m-d', strtotime($request_data->due_dt));
        }

        $tags = '';
        if (isset($request_data->tags)){
          $tags = $request_data->tags;
        }

        //$batch_id = "NULL";
        $batch_id = NULL;
        if ($batch_id_parm > 0){
          $batch_id = $batch_id_parm;
        }
        $status_cd = 1;

        $parent_todo_id = NULL;
        if (isset($request_data->parent_todo_id)){
          $parent_todo_id = $request_data->parent_todo_id;
        }
        $task_name = $request_data->task_name;

        $query = "INSERT INTO todo
        (  task_name,   due_dt, starred,  group_id, priority_cd, frequency_cd, status_cd, customer_id, Note,  done, done_dt,  batch_id,   tags, parent_todo_id)  VALUES
        (          ?,        ?,       0,         ?,           ?,            ?,         ?,           ?,   '',     0,       ?,         ?,      ?,              ?)";

        //$rowsAffected = actionSql($dbh,$query);
        $types = 'ssiiiiisisi';  ## pass
        $params = array($task_name, $due_dt, $group_id, $priority_cd, $frequency_cd, $status_cd, $customer_id, $done_dt, $batch_id, $tags, $parent_todo_id);
        //var_dump($params);
        $rowsAffected = execSqlActionPREPARED($dbh, $query, $types, $params);


        $todo_id = mysqli_insert_id($dbh);
        $new_todo_data = getTodo($dbh, $customer_id, $todo_id);

        return $new_todo_data;
    }
  }
}

function  explodeTodoName($request, $dbh){

  //var_dump($request);

  $task_name = $request->task_name;


  if (preg_match('/|/', $task_name)) {

      $daysOfWeek = array('Sunday', 'Sun', 'Monday', 'Mon', 'Tuesday', 'Tue', 'Wednesday', 'Wed', 'Thursday',
       'Thu', 'Friday', 'Fri', 'Saturday', 'Sat'
       );

      //fixme: may want to get this from memory... memcache (?)
      $frequencies = getFrequencies($dbh);

      $fields = explode('|',$task_name);
      $request->task_name = $fields[0];
      unset($fields[0]);     # remove the task name
      $fields = array_values($fields);  #re-index

      foreach ($fields as $value) {
          $foundPriorityMatch = 0;
          $foundDueDtMatch = 0;
          $foundFrequencyMatch = 0;

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

          $temp_freq_cd = getFrequencyCdUsingName($value, $frequencies, 0);
          if ($temp_freq_cd){
            $request->frequency_cd = $temp_freq_cd;
            $foundFrequencyMatch = 1;
          }

          //if the field being processed didnt match ANYTHING... then treat it as a tag/category
          if ((0 == $foundPriorityMatch) and (0 == $foundDueDtMatch) and (0 == $foundFrequencyMatch)){
            $request->tags = $value;
          }

      }
  }

  //var_dump($request);
  return $request;
}

#################################################################
function getGroupIdUsingName($groupName, $groups){
    $groupId = 0;
    $groupName = trim($groupName);
    foreach ($groups as $fields){
      $groupNmFromDB = $fields{'group_name'};
      $groupNmFromDB = trim($groupNmFromDB);
      if(strtolower($groupName) == strtolower($groupNmFromDB)) {
          $groupId = $fields{'group_id'};
      }
    }
    $ok = 0;
    $err = "Group Not Found";
    if ($groupId){
      $ok = 1;
      $err = "";
    }
    return array($ok, $err, $groupId);
}

function getFrequencyCdUsingName($frequency, $frequencies, $doDefault=1){
  //there are cases when you dont want to default to 10.... eg: explode since the tag will be passed
  $frequency_cd = 0;
  if ($doDefault){
    $frequency_cd = 10;  #default to 10:Once
  }
  $frequency = trim($frequency);
  foreach ($frequencies as $fields){
    $frequencyNmFromDB = $fields{'name'};
    $frequencyNmFromDB = trim($frequencyNmFromDB);
    if(strtolower($frequency) == strtolower($frequencyNmFromDB)) {
        $frequency_cd = $fields{'cd'};
    }
  }
  return $frequency_cd;
}


function getPriorityCdUsingName($priority, $priorities){
    $priority_cd = 5;
    $priority = trim($priority);
    foreach ($priorities as $fields){
      $priorityNmFromDB = $fields{'name'};
      $pattern = "/$priority/";
      if (preg_match("$pattern",$priorityNmFromDB)){
          $priority_cd = $fields{'cd'};
      }
    }
    return $priority_cd;
}

function  isPremiumAccount($dbh, $customer_id){
    $query = "select count(*) as TrueInd from account_period where customer_id = ? and
    begin_dt <= CURDATE() and end_dt >= CURDATE() and account_type_cd in (1,3)";

    //$data = execSqlSingleRow($dbh, $query);

    $types = 'i';  ## pass
    $params = array($customer_id);
    $data = execSqlSingleRowPREPARED($dbh, $query, $types, $params);

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
    $query = "select count(*) as todo_count from todo where customer_id = ? and done = 0";

    //$data = execSqlSingleRow($dbh, $query);

    $types = 'i';  ## pass
    $params = array($customer_id);
    $data = execSqlSingleRowPREPARED($dbh, $query, $types, $params);

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
        $query = "select count(*) as group_count from todo_group where customer_id = ?";
        //$data = execSqlSingleRow($dbh, $query);

        $types = 'i';  ## pass
        $params = array($customer_id);
        $data = execSqlSingleRowPREPARED($dbh, $query, $types, $params);

        if ($data{'group_count'} > 2){
            $response{'err'}=1;
            $response{'errMsg'}="You've reached the maximum groups (2) for a free account. Please upgrade by going to Settings, Account/Profile, or delete an existing group.";
        }
    }
    return $response;
}

function  checkFreeTodoWithinGroupThreshold($dbh, $customer_id,$group_id){
    $query = "select count(*) as todo_count from todo where customer_id = ? and done = 0 and group_id = ?";
    //$data = execSqlSingleRow($dbh, $query);
    $types = 'ii';  ## pass
    $params = array($customer_id, $group_id);
    $data = execSqlSingleRowPREPARED($dbh, $query, $types, $params);

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
  $query = "delete from todo where customer_id = ? and todo_id = ?  ";

  //$rowsAffected = actionSql($dbh,$query);

  $types = 'ii';  ## pass
  $params = array($customer_id, $todo_id);
  $rowsAffected = execSqlActionPREPARED($dbh, $query, $types, $params);

  $response{'RowsDeleted'} = $rowsAffected;
  return $response;
}

###################################
function  moveTodos($dbh, $request_data, $customer_id){
  $from_group_id = $request_data->fromGroup;
  $to_group_id = $request_data->toGroup;

  $query = "update todo todo set group_id = ?  where customer_id = ? and group_id = ?    ";

  //$rowsAffected = actionSql($dbh,$query);

  $types = 'iii';  ## pass
  $params = array($to_group_id, $customer_id, $from_group_id);
  $rowsAffected = execSqlActionPREPARED($dbh, $query, $types, $params);

  if ($rowsAffected) {
    $response{'msg'} = "$rowsAffected Todo(s) moved!";
  } else {
    $response{'error'} = "No Todos moved";
  }

  return $response;
}

###################################
function  updateGroup($dbh, $request_data, $customer_id){
  $group_name = $request_data->group_name;
  $group_id = $request_data->group_id;
  $query = "update todo_group set group_name = ? where customer_id = ?
            and group_id = $group_id";

  //$rowsAffected = actionSql($dbh,$query);

  $types = 'si';  ## pass
  $params = array($group_name, $customer_id);
  $rowsAffected = execSqlActionPREPARED($dbh, $query, $types, $params);

  $response{'RowsUpdated'} = $rowsAffected;
  return $response;
}


###################################
function  addBaseGroups($dbh, $customer_id){

  $query = "INSERT INTO todo_group (group_name, sort_order, customer_id, active) VALUES (?,?,?,?)";
  #### add new group
  //$rowsInserted = insertData($dbh, $query);
  $types = 'siii';  ## pass

  $group_name = 'My To Dos';
  $sort_order = 1;
  $active = 1;
  $params = array($group_name, $sort_order, $customer_id, $active);
  $rowsAffected = execSqlActionPREPARED($dbh, $query, $types, $params);

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
        $query = "update todo_group set active = 0 where customer_id = ?";
        //$rowsAffected = actionSql($dbh,$query);
        $types = 'i';  ## pass
        $params = array($customer_id);
        $rowsAffected = execSqlActionPREPARED($dbh, $query, $types, $params);


        ### Get Max Sort_order
        $query = "select max(sort_order) as max_order from todo_group where customer_id = ?";
        //$data = execSqlSingleRow($dbh, $query);
        $types = 'i';  ## pass
        $params = array($customer_id);
        $data = execSqlSingleRowPREPARED($dbh, $query, $types, $params);

        $max_sort_order = $data{'max_order'};
        $sort_order = $max_sort_order + 1;

        #### add new group
        $groupName = $request_data->name;
        $query = "insert into todo_group (customer_id, group_name, active, sort_order) VALUES
                                                   (?,          ?,      1,          ?)";
        //$rowsAffected = actionSql($dbh,$query);
        $types = 'isi';  ## pass
        $params = array($customer_id, $groupName, $sort_order);
        $rowsAffected = execSqlActionPREPARED($dbh, $query, $types, $params);

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
  $query = "update todo_group set active = 0 where customer_id = ?";
  //$rowsAffected = actionSql($dbh,$query);
  $types = 'i';  ## pass
  $params = array($customer_id);
  $rowsAffected = execSqlActionPREPARED($dbh, $query, $types, $params);

  #### set selected group to active
  $query = "update todo_group set active = 1 where customer_id = ? and group_id = ?";
  //$rowsAffected = actionSql($dbh,$query);
  $types = 'ii';  ## pass
  $params = array($customer_id, $group_id);
  $rowsAffected = execSqlActionPREPARED($dbh, $query, $types, $params);

  $response{'Status'} = 1;

  return $response;

}

###################################
function  resetDemoCustomer($dbh, $customer_id){
  #### delete the groups
  $response = deleteAllGroups($dbh, $customer_id);
  #### delete batches
  deleteAllBatches($dbh, $customer_id);
  #### add Base Groups
  $response = addBaseGroups($dbh, $customer_id);
  return $response;
}


###################################
function  deleteAllGroups($dbh, $customer_id){
  #### delete the group
  $query = "delete from todo_group where customer_id = ?";
  //$rowsAffected = actionSql($dbh,$query);
  $types = 'i';  ## pass
  $params = array($customer_id);
  $rowsAffected = execSqlActionPREPARED($dbh, $query, $types, $params);
  $response{'RowsDeleted'} = $rowsAffected;
  return $response;
}



###################################
function  deleteGroup($dbh, $request_data, $customer_id){
  $group_id = $request_data->group_id;

  $query = "select count(*) as count from todo_group where customer_id = ?";
  //$data = execSqlSingleRow($dbh, $query);
  $types = 'i';  ## pass
  $params = array($customer_id);
  $data = execSqlSingleRowPREPARED($dbh, $query, $types, $params);
  $count = $data{'count'};

  if (1 == $count){
      $response{'Msg'} = "Group not deleted. Must have at least one group";
      $response{'RowsDeleted'} = 0;
  } else {
      #### delete Todos associated with the group
      $query = "delete from todo where group_id = ? and customer_id = ?";
      //$rowsAffected = actionSql($dbh,$query);
      $types = 'ii';  ## pass
      $params = array($group_id, $customer_id);
      $rowsAffected = execSqlActionPREPARED($dbh, $query, $types, $params);

      #### delete the group
      $query = "delete from todo_group where group_id = ? and customer_id = ?";
      //$rowsAffected = actionSql($dbh,$query);
      $types = 'ii';  ## pass
      $params = array($group_id, $customer_id);
      $rowsAffected = execSqlActionPREPARED($dbh, $query, $types, $params);
      $response{'RowsDeleted'} = $rowsAffected;

      ### only try and fix actives if something was actually deleted....
      if ($rowsAffected){
          #### Count of actives
          $query = "select count(*) as TheCount from todo_group where customer_id = ? and active = 1";
          //$data = execSqlSingleRow($dbh, $query);
          $types = 'i';  ## pass
          $params = array($customer_id);
          $data = execSqlMultiRowPREPARED($dbh, $query, $types, $params);

          $count = 0;
          if (isset($data{'TheCount'})){
            $count = $data{'TheCount'};
          }

          // if there are zero ACTIVE groups... need to fix it....
          if (!$count){
            $query = "select group_id, min(sort_order) from todo_group where customer_id = ?";
            //$data = execSqlSingleRow($dbh, $query);
            $types = 'i';  ## pass
            $params = array($customer_id);
            $data = execSqlSingleRowPREPARED($dbh, $query, $types, $params);
            $group_id = $data{'group_id'};

            $query = "update todo_group set active = 1 where customer_id = ? and group_id = ?";
            //$rowsAffected = actionSql($dbh,$query);
            $types = 'ii';  ## pass
            $params = array($customer_id, $group_id);
            $rowsAffected = execSqlActionPREPARED($dbh, $query, $types, $params);
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

  $upload_dt = date('Y-m-d H:i:s');
  $query = "insert into todo_batch (file_name, upload_dt, customer_id) values
                                           (?,         ?,           ?)";
  //$rowsAffected = actionSql($dbh,$query);
  $types = 'ssi';  ## pass
  $params = array($file_name, $upload_dt, $customer_id);
  $rowsAffected = execSqlActionPREPARED($dbh, $query, $types, $params);

  $batch_id = mysqli_insert_id($dbh);
  $response{'RowsAdded'} = $rowsAffected;
  $response{'batch_id'} = $batch_id;
  return $response;
}

################################################################################
function updateBatchStats($dbh, $customer_id, $batch_id, $uploaded, $errored, $skipped){
  $query = "update todo_batch set
          count_uploaded = ?,
          count_error_no_group = ?,
          count_error_above_limit = ?
  where customer_id = ? and batch_id = ?";
  //$rowsAffected = actionSql($dbh,$query);

  $types = 'iiiii';  ## pass
  $params = array($uploaded, $errored, $skipped, $customer_id, $batch_id);
  $rowsAffected = execSqlActionPREPARED($dbh, $query, $types, $params);

  $response{'RowsUpdated'} = $rowsAffected;
  return $response;
}

################################################################################
function deleteAllBatches($dbh, $customer_id){
  $query = "delete from todo_batch where customer_id = ? ";

  $types = 'i';  ## pass
  $params = array($customer_id);
  $rowsAffected = execSqlActionPREPARED($dbh, $query, $types, $params);

  $response{'RowsDeleted'} = $rowsAffected;
  return $response;
}

################################################################################
function deleteBatch($dbh, $request, $customer_id){
  $batch_id = $request->batch_id;
  // There is a cascading delete setup... so delete of todo_batch will delete associated todos with the same batch_id
  if (!isset($batch_id)){die("cannot delete batch.. missing information in the request");}
  $query = "delete from todo_batch where customer_id = ? and batch_id = ?";
  //$rowsAffected = actionSql($dbh,$query);

  $types = 'ii';  ## pass
  $params = array($customer_id, $batch_id);
  $rowsAffected = execSqlActionPREPARED($dbh, $query, $types, $params);

  $response{'RowsDeleted'} = $rowsAffected;
  return $response;
}

################################################################################
function getBatches($dbh, $customer_id){
  $query = "select batch_id, file_name, upload_dt, count_uploaded, count_error_no_group, count_error_above_limit from todo_batch
  where customer_id = ? order by upload_dt desc";
  //$data = execSqlMultiRow($dbh, $query);
  $types = 'i';  ## pass
  $params = array($customer_id);
  $data = execSqlMultiRowPREPARED($dbh, $query, $types, $params);

  return $data;
}

################################################################################
function addEvent($dbh, $customer_id, $event_cd, $dateTime){
  $query = "INSERT INTO event (customer_id, create_dt, event_cd) VALUES
                              (          ?,         ?,        ?)";
  //$rowsAffected = actionSql($dbh,$query);

  $types = 'isi';  ## pass
  $params = array($customer_id, $dateTime, $event_cd);
  $rowsAffected = execSqlActionPREPARED($dbh, $query, $types, $params);

  $response{'RowsUpdated'} = $rowsAffected;
  $response{'LastInsertId'} = mysqli_insert_id($dbh);
  return $response;
}

################################################################################
function addAccountPeriod($dbh, $customer_id, $begin_dt, $end_dt, $account_type_cd, $account_period_status_cd, $event_id){
  $query = "INSERT INTO account_period (customer_id, begin_dt, end_dt, account_type_cd, account_period_status_cd, event_id) VALUES (
                                                  ?,        ?,      ?,               ?,                        ?,        ?)";
  //$rowsAffected = actionSql($dbh,$query);
  $types = 'issiii';  ## pass
  $params = array($customer_id, $begin_dt, $end_dt, $account_type_cd, $account_period_status_cd, $event_id);
  $rowsAffected = execSqlActionPREPARED($dbh, $query, $types, $params);

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

$headers = 'From: paul@todogiant.com' . "\r\n" .
 'Reply-To: paul@todogiant.com' . "\r\n" .
 'X-Mailer: PHP/' . phpversion();

// Send
mail($email, $subject, $body, $headers);

}

################################################################################
function updateCustomerCredentialCd($dbh, $customer_id, $credential_cd){
  $query = "UPDATE customer set credential_cd = ? where customer_id = ?";
  //$rowsAffected = actionSql($dbh,$query);
  $types = 'ii';  ## pass
  $params = array($credential_cd, $customer_id);
  $rowsAffected = execSqlActionPREPARED($dbh, $query, $types, $params);

  $response{'RowsUpdated'} = $rowsAffected;
  return $response;
}

################################################################################
function getMaxPremiumDt($dbh, $customer_id){
    $query = "select max(end_dt) as end_dt from account_period where customer_id = ? and account_type_cd in (1,3)";  ### 3=Premium,  #1:Trial(Premium)
    //$data = execSqlSingleRow($dbh,$query);
    $types = 'i';  ## pass
    $params = array($customer_id);
    $data = execSqlSingleRowPREPARED($dbh, $query, $types, $params);

    return $data;
}

################################################################################
function getAccountPeriod($dbh, $customer_id){
    $query = "select description, DATE_FORMAT(begin_dt, '%m/%d/%Y') as begin_dt, DATE_FORMAT(end_dt, '%m/%d/%Y') as end_dt from  account_period, account_type
    where account_type.account_type_cd = account_period.account_type_cd and account_period_status_cd = 1
    and customer_id = ?
    order by begin_dt asc";   ### 1 = active
    //$data = execSqlMultiRow($dbh, $query);
    $types = 'i';  ## pass
    $params = array($customer_id);
    $data = execSqlMultiRowPREPARED($dbh, $query, $types, $params);

    return $data;
}

################################################################################
function addPayment($dbh, $customer_id, $pmt_amt, $event_id, $payment_method_cd, $pmt_dt){
  $query = "INSERT INTO payment (customer_id, payment_amt, event_id, payment_method_cd, payment_dt) VALUES
                                (          ?,           ?,        ?,                 ?,          ?)";

  //$rowsAffected = actionSql($dbh,$query);
  $types = 'iiiis';  ## pass
  $params = array($customer_id, $pmt_amt, $event_id, $payment_method_cd, $pmt_dt);
  $rowsAffected = execSqlActionPREPARED($dbh, $query, $types, $params);

  $response{'RowsUpdated'} = $rowsAffected;
  $response{'LastInsertId'} = mysqli_insert_id($dbh);
  return $response;
}

################################################################################
function setCustomerCredentialCd($dbh, $customer_id, $credential_status_cd){
  $query = "UPDATE customer set credential_status_cd = ? where customer_id = ?";
  //$rowsAffected = actionSql($dbh,$query);
  $types = 'ii';  ## pass
  $params = array($credential_status_cd, $customer_id);
  $rowsAffected = execSqlActionPREPARED($dbh, $query, $types, $params);

  $response{'RowsUpdated'} = $rowsAffected;
  return $response;
}

################################################################################
function getCustomerIdUsingGUID($dbh, $guid){
    $query = "SELECT customer_id FROM customer where guid = ? ";
    //$data = execSqlSingleRow($dbh, $query);
    $types = 's';  ## pass
    $params = array($guid);
    $data = execSqlSingleRowPREPARED($dbh, $query, $types, $params);

    return $data;
}

################################################################################
function doesUserExist($dbh, $email){
    #### see if user already exists
    $query = "SELECT count(*) as theCount fROM customer where email = ? ";
    //$data = execSqlSingleRow($dbh, $query);
    $types = 's';  ## pass
    $params = array($email);
    $data = execSqlSingleRowPREPARED($dbh, $query, $types, $params);


    $nbrOfCustomers = $data['theCount'];
    if ($nbrOfCustomers){
      return 1;
    } else {
      return 0;
    }
}

################################################################################
function getCustomerId($dbh, $email){
    $query = "SELECT customer_id fROM customer where email = ?   ";
    //$data = execSqlSingleRow($dbh, $query);
    $types = 's';  ## pass
    $params = array($email);
    $data = execSqlSingleRowPREPARED($dbh, $query, $types, $params);

    if (isset($data['customer_id'])){
        return $data['customer_id'];
    } else {
        return '';
    }
}

################################################################################
function getEmail($dbh, $customer_id){
//    $query = "SELECT email fROM customer where customer_id = $customer_id  ";
//    $data = execSqlSingleRow($dbh, $query);
    $query = "SELECT email fROM customer where customer_id = ?  ";
    $types = 'i';  ## pass
    $params = array($customer_id);
    $data = execSqlSingleRowPREPARED($dbh, $query, $types, $params);

    return $data;
}

################################################################################
function getDemoCustomer($dbh){

  $query = "select customer_id from demo_customer order by last_used_ts asc  limit 1";
  //NOTE: Binding to $dummy was done to use the PREPARED mssql_free_statement
  $dummy=1;
  $types = 'i';  ## pass
  $params = array($dummy);
  $data = execSqlSingleRowPREPARED($dbh, $query, $types, $params);
  $customer_id = $data{"customer_id"};


  $query = "update demo_customer set last_used_ts = current_timestamp where customer_id = ?";
  $types = 'i';  ## pass
  $params = array($customer_id);
  $rowsAffected = execSqlActionPREPARED($dbh, $query, $types, $params);


  $query = "select email from customer where customer_id = ?";
  $types = 'i';  ## pass
  $params = array($customer_id);  //retrieved above...
  $data = execSqlSingleRowPREPARED($dbh, $query, $types, $params);
  $email = $data{"email"};


  $request_data = new stdClass();
  $request_data->email = $email;
  $request_data->password = 'demopassword';  //all the demo passwords are the same..

  return $request_data;
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
    where customer_id = ? and account_period_status_cd = 1
    and account_type_cd = ?";
    //$rowsAffected = actionSql($dbh,$query);
    $types = 'ii';  ## pass
    $params = array($customer_id, $account_type_cd);
    $rowsAffected = execSqlActionPREPARED($dbh, $query, $types, $params);

    $response{'RowsUpdated'} = $rowsAffected;
    return $response;
}

################################################################################
function setStripeCustomerId($dbh, $customer_id, $stripe_customer_id) {
    $query = "UPDATE customer set stripe_customer_id = ?
    where customer_id = ?";
    //$rowsAffected = actionSql($dbh,$query);
    $types = 'si';  ## pass
    $params = array($stripe_customer_id, $customer_id);
    $rowsAffected = execSqlActionPREPARED($dbh, $query, $types, $params);

    $response{'RowsUpdated'} = $rowsAffected;
    return $response;
}

################################################################################
function updateCustomerName($dbh, $customer_id, $first_name, $last_name) {
    $query = "UPDATE customer set
        first_name = ?,
        last_name  = ?
    where customer_id = ?";
    //$rowsAffected = actionSql($dbh,$query);
    $types = 'ssi';  ## pass
    $params = array($first_name, $last_name , $customer_id);
    $rowsAffected = execSqlActionPREPARED($dbh, $query, $types, $params);

    $response{'RowsUpdated'} = $rowsAffected;
    return $response;
}


################################################################################
function setAcctPeriodsForPayment($dbh, $customer_id, $event_id){
    #Get Current Max Premium Date
    $response = getMaxPremiumDt($dbh, $customer_id);
    //var_dump($response);
    $maxdt = $response{'end_dt'};

    if (NULL == $maxdt){
      $maxdt = date("Y-m-d");
    }

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

################################################################################
function setAcctPeriodsForRegistration($dbh, $customer_id, $event_id){

    #add Trial (Premium)
    $begin_dt = date("Y-m-d");
    $end_dt =date('Y-m-d', strtotime('+25 day', strtotime($begin_dt)) ); #add 25 days to begin date for premium trial
    addAccountPeriod($dbh, $customer_id, $begin_dt, $end_dt, 1, 1, $event_id); #1:Trial(Premium) ;  1:Active

    #add free period
    $begin_dt = date('Y-m-d', strtotime($end_dt. ' + 1 days')); #add 1 day to end_dt of Trail
    $end_dt =date('Y-m-d', strtotime('+1 year', strtotime($begin_dt)) );  #add 1 year to begin date
    addAccountPeriod($dbh, $customer_id, $begin_dt, $end_dt, 2, 1,$event_id); #2:Free;  1:Active

}


?>