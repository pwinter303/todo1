<?php

if (defined('STDIN')) {
        include '../config/config.php';
} else {
        $basepath = dirname(dirname($_SERVER['SCRIPT_FILENAME']));
        require($basepath.'/config/config.php');
}

function closeDatabaseConnection($dbh){
      mysqli_close($dbh);
}

function createDatabaseConnection(){
    // Create connection - Using Configuration Data...
    $dbh = mysqli_connect(DBSRVR,DBUSER,DBPWRD,DBNAME);

    // Check connection
    if (mysqli_connect_errno()){
      echo "Failed to connect to Database: " . mysqli_connect_error();
    } else {
      return $dbh;
    }
}


########################################################################
function  execSqlMultiRowPREPARED($dbh, $query, $types, $params){

  if (!($stmt = $dbh->prepare($query))) {
       echo "Prepare failed: (" . $dbh->errno . ") " . $dbh->error;
  }

  // http://stackoverflow.com/questions/5100046/how-to-bind-mysqli-bind-param-arguments-dynamically-in-php
  // credit for the code below goes to the post above...
  if($types&&$params){
      $bind_names[] = $types;
      for ($i=0; $i<count($params);$i++)
      {
          $bind_name = 'bind' . $i;
          $$bind_name = $params[$i];
          $bind_names[] = &$$bind_name;
      }
      $return = call_user_func_array(array($stmt,'bind_param'),$bind_names);
  }

  /* execute query */
  if (!$stmt->execute()) {
      echo "Execute failed: (" . $dbh->errno . ") " . $dbh->error;
  }

  # these lines of code below return one dimensional array, similar to mysqli::fetch_assoc()
  $meta = $stmt->result_metadata();

  while ($field = $meta->fetch_field()) {
      $var = $field->name;
      $$var = null;
      $parameters[$field->name] = &$$var;
  }

  call_user_func_array(array($stmt, 'bind_result'), $parameters);

  $data = array();
  while($stmt->fetch() ){
    foreach( $parameters as $key=>$value ){
        $row_tmb[ $key ] = $value;
    }
    $data[] = $row_tmb;
  }

  # close statement
  $stmt->close();

  return $data;

}

########################################################################
function  execSqlSingleRowPREPARED($dbh, $query, $types, $params){

  if (!($stmt = $dbh->prepare($query))) {
       echo "Prepare failed: (" . $dbh->errno . ") " . $dbh->error;
  }

  // http://stackoverflow.com/questions/5100046/how-to-bind-mysqli-bind-param-arguments-dynamically-in-php
  // credit for the code below goes to the post above...
  if($types&&$params){
      $bind_names[] = $types;
      for ($i=0; $i<count($params);$i++)
      {
          $bind_name = 'bind' . $i;
          $$bind_name = $params[$i];
          $bind_names[] = &$$bind_name;
      }
      $return = call_user_func_array(array($stmt,'bind_param'),$bind_names);

      if (!($return)){
      }
  }

  /* execute query */
  if (!$stmt->execute()) {
      echo "Execute failed: (" . $dbh->errno . ") " . $dbh->error;
  }

  # these lines of code below return one dimensional array, similar to mysqli::fetch_assoc()
  $meta = $stmt->result_metadata();

  while ($field = $meta->fetch_field()) {
      $var = $field->name;
      $$var = null;
      $parameters[$field->name] = &$$var;
  }

  call_user_func_array(array($stmt, 'bind_result'), $parameters);

  //fixme: Only difference between ExecSQLSingle & Multi... Can they be combined??
  $stmt->fetch(); ## Get the single row returned
  foreach( $parameters as $key=>$value ){
      $row_tmb[ $key ] = $value;
  }
  $data = $row_tmb;

  # close statement
  $stmt->close();

  return $data;

}

########################################################################
function  execSqlActionPREPARED($dbh, $query, $types, $params){

  if (!($stmt = $dbh->prepare($query))) {
       echo "Prepare failed: (" . $dbh->errno . ") " . $dbh->error;
  }

  if($types&&$params){
      $bind_names[] = $types;
      for ($i=0; $i<count($params);$i++)
      {
          $bind_name = 'bind' . $i;
          $$bind_name = $params[$i];
          $bind_names[] = &$$bind_name;
      }
      $return = call_user_func_array(array($stmt,'bind_param'),$bind_names);

      if (!($return)){
      }
  }

  /* execute query */
  if (!$stmt->execute()) {
      echo "Execute failed: (" . $dbh->errno . ") " . $dbh->error;
  }

  $rows_affected = $stmt->affected_rows;

  # close statement
  $stmt->close();

  return $rows_affected;

}

//this is used in dbRefreshTables.php
function deleteData($dbh,$query){
       mysqli_query($dbh,$query) or die('Query failed: '
                   . mysqli_error($dbh));

       //dont think this is needed
       //mysqli_free_result($result);

       return mysqli_affected_rows($dbh);
}

//this is used in dbRefreshTables.php
function insertData($dbh,$query){
       mysqli_query($dbh,$query) or die('Query failed: ' . mysqli_error($dbh));

       //dont think this is needed
       //mysqli_free_result($result);

       return mysqli_affected_rows($dbh);
}

?>
