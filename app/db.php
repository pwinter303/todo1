<?php

include 'config.php';

function closeDatabaseConnection($dbh){
      mysqli_close($dbh);
}

function createDatabaseConnection(){
    // Create connection - Using Configuration Data...
    $dbh = mysqli_connect(DBSRVR,DBUSER,DBPWRD,DBNAME);
    ####$dbh = mysqli_connect('localhost',DBUSER,DBPWRD,DBNAME);

    // Check connection
    if (mysqli_connect_errno()){
      echo "Failed to connect to Database: " . mysqli_connect_error();
    } else {
      return $dbh;
    }
}

function execSqlSingleRow($dbh,$query){
      $result = mysqli_query($dbh,$query) or die('Query failed: '
                  . mysqli_error($dbh));
      $data=mysqli_fetch_array($result);
      mysqli_free_result($result);
      return $data;
}

function actionSql($dbh,$query){
### use this for insert, update, delete
      $result = mysqli_query($dbh,$query) or die('Query failed: '
                  . mysqli_error($dbh));
      $rows_affected = mysqli_affected_rows($dbh);
      return $rows_affected;
}

function execSqlMultiRow($dbh, $query){
      $data = array();

      $result = mysqli_query($dbh,$query) or die('Query failed: '
                  . mysqli_error($dbh));

      while ($row = mysqli_fetch_assoc($result)){
        array_push($data, $row);
      }
      mysqli_free_result($result);
      return $data;
}


function insertData($dbh,$query){
       mysqli_query($dbh,$query) or die('Query failed: ' . mysqli_error($dbh));

       mysqli_free_result($result);

       return mysqli_affected_rows($dbh);
 }


 function deleteData($dbh,$query){
       mysqli_query($dbh,$query) or die('Query failed: '
                   . mysqli_error($dbh));

       mysqli_free_result($result);

       return mysqli_affected_rows($dbh);
 }

?>
