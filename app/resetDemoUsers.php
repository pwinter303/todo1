<?php

include 'db.php';
include 'functions.php';



$dbh = createDatabaseConnection();
resetDemoUser($dbh);


###############################################################
function resetDemoUser($dbh){

  $data = getCustomerIDsToReset($dbh);
  foreach ($data as $fieldKey => $value){
        $customer_id = $data{$fieldKey}{'customer_id'};
        #echo "customer_id:$customer_id  ";
        resetDemoCustomer($dbh, $customer_id);

        $query = "update demo_customer set last_reset_ts = current_timestamp where customer_id = ?";
        $types = 'i';  ## pass
        $params = array($customer_id);
        $rowsAffected = execSqlActionPREPARED($dbh, $query, $types, $params);


  }
}

###############################################################
function getCustomerIDsToReset($dbh){
  $query = "select customer_id from demo_customer where last_used_ts > last_reset_ts";
  //NOTE: Binding to $dummy was done to use the PREPARED mssql_free_statement
  $dummy=1;
  $types = 'i';  ## pass
  $params = array($dummy);
  $data = execSqlMultiRowPREPARED($dbh, $query, $types, $params);

  return $data;
}
