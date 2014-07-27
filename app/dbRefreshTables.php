<?php

include 'db.php';
// Include the phpass library
require_once('bower_components/phpass-0.3/PasswordHash.php');


$dbh = createDatabaseConnection();

#echo var_dump($dbh);

emptyTable($dbh,'todo');
emptyTable($dbh,'todo_batch');
emptyTable($dbh,'todo_group');
//emptyTable($dbh,'todo_tag_xref');
//emptyTable($dbh,'tag');
emptyTable($dbh,'todo_status');
emptyTable($dbh,'todo_priority');
emptyTable($dbh,'payment');
emptyTable($dbh,'todo_frequency');

emptyTable($dbh,'payment');
emptyTable($dbh,'payment_method');

emptyTable($dbh,'event');
emptyTable($dbh,'event_description');
emptyTable($dbh,'account_period');
emptyTable($dbh,'account_period_status');
emptyTable($dbh,'account_type');

emptyTable($dbh,'customer');



insertCustomer($dbh);
insertFrequencies($dbh);
insertStatuses($dbh);
insertPriorities($dbh);
insertTodoGroups($dbh);
insertTodos($dbh);

insert_payment_method($dbh);
insert_event_description($dbh);
insert_account_period_status($dbh);
insert_account_type($dbh);


insert_event($dbh);
insert_payment($dbh);


function emptyTable($dbh, $table){

  $query = "delete from $table"  ;

  $rowsDeleted = deleteData($dbh, $query);
  echo "$table deleted:" . $rowsDeleted . "</br>";

}
function insertCustomer($dbh){

  // Initialize the hasher without portable hashes (this is more secure)
  $hasher = new PasswordHash(8, false);

  // Hash the password.  $hashedPassword will be a 60-character string.
  $hashedPassword1 = $hasher->HashPassword('fakepassword');
  $hashedPassword2 = $hasher->HashPassword('fakepassword2');

  $query = "INSERT INTO customer (customer_id, user_name, password) VALUES
    ( 1, 'fakeuser@yahoo.com', '$hashedPassword1'),
    ( 2, 'fakeuser2@yahoo.com', '$hashedPassword2')";

  $rowsInserted = insertData($dbh, $query);
  echo "Customer inserted:" . $rowsInserted . "</br>";

}
function insertFrequencies($dbh){

  $query = "INSERT INTO todo_frequency (frequency_cd ,frequency_name) VALUES
    ( '1','Once'),
    ( '2','Weekly'),
    ( '3','Monthly'),
    ( '4','Quarterly'),
    ( '5','Yearly')"  ;

  $rowsInserted = insertData($dbh, $query);
  echo "Frequencies inserted:" . $rowsInserted . "</br>";

}

function insertPriorities($dbh){

  $query = "INSERT INTO todo_priority (priority_cd ,priority_name) VALUES
    ( 1,'1-Max'),
    ( 2,'2'),
    ( 3,'3'),
    ( 4,'4'),
    ( 5,'5'),
    ( 6,'6'),
    ( 7,'7'),
    ( 8,'8'),
    ( 9,'9-Low')"
  ;

  $rowsInserted = insertData($dbh, $query);
  echo "priorities inserted:" . $rowsInserted . "</br>";

}

function insertStatuses($dbh){

  $query = "INSERT INTO todo_status (status_cd ,status_name) VALUES
    ( 0,'Inactive'),
    ( 1,'Active'),
    ( 2,'Completed'),
    ( 3,'Placeholder for Reocurring')"
  ;

  $rowsInserted = insertData($dbh, $query);
  echo "statuses inserted:" . $rowsInserted . "</br>";

}

function insertTodos($dbh){

  $query = "INSERT INTO todo (task_name, due_dt, starred, group_id, priority_cd,
  frequency_cd, status_cd, customer_id, Note, done, tags)  VALUES
    ('Buy Milk', '2014-07-05 00:00:00', '1', '1', '1', '1', '1',1, 'my note', 0, ''),
    ('Fertilize the lawn', '2014-07-06 00:00:00', '0', '1', '2', '2', '2',1, 'my note', 0, ''),
    ('Plan Vacation', '2014-07-07 00:00:00', '1', '1', '3', '3', '1',1, 'my note', 0, ''),
    ('Study for exam', '2014-08-02 00:00:00', '0', '1', '1', '2', '2',1, 'my note', 0, ''),
    ('Plant Fall flowers', '2014-09-03 00:00:00', '1', '1', '2', '3', '1',1, 'my note', 0, ''),
    ('Get Todo Giant', '2014-9-09 00:00:00', '0', '1', '3', '2', '2',1, 'my note', 0, '')";

  $rowsInserted = insertData($dbh, $query);
  echo "todos inserted:" . $rowsInserted . "</br>";

}

function insertTodoGroups($dbh){

  $query = "INSERT INTO todo_group (group_id, group_name, Sort_Order,customer_id, active) VALUES
    (1, 'Home',1,1,1),
    (2, 'Work',2,1,0),
    (3, 'Home',1,2,1),
    (4, 'Work',2,2,0)
    "
  ;

  $rowsInserted = insertData($dbh, $query);
  echo "todoGroups inserted:" . $rowsInserted . "</br>";

}

function insert_payment_method($dbh){
    $query = "INSERT INTO payment_method (payment_method_cd, description) VALUES
    (1, 'CreditCard'),
    (2, 'PayPal')
    ";

    $rowsInserted = insertData($dbh, $query);
    echo "insert_payment_method inserted:" . $rowsInserted . "</br>";

}




function insert_event_description($dbh){
    $query = "INSERT INTO event_description (event_cd, description) VALUES
    (1, 'Registration'),
    (2, 'Login'),
    (3, 'Facebook Like'),
    (4, 'Tweet'),
    (5, 'Payment'),
    (6, 'Password Change'),
    (7, 'Referral')
    ";

    $rowsInserted = insertData($dbh, $query);
    echo "insert_event_description inserted:" . $rowsInserted . "</br>";
};



function insert_account_period_status($dbh){
    $query = "INSERT INTO account_period_status (account_period_status_cd, description) VALUES
    (1, 'Active'),
    (2, 'Done')
    ";

    $rowsInserted = insertData($dbh, $query);
    echo "insert_account_period_status inserted:" . $rowsInserted . "</br>";
};



function insert_account_type($dbh){
    $query = "INSERT INTO account_type (account_type_cd, description) VALUES
    (1, 'Trial'),
    (2, 'Free'),
    (3,'Premium')
    ";

    $rowsInserted = insertData($dbh, $query);
    echo "insert_account_type inserted:" . $rowsInserted . "</br>";
};


function insert_event($dbh){
    $query = "INSERT INTO event (customer_id, create_dt, event_cd) VALUES
    (1, CURDATE(), 1),
    (2, CURDATE(), 1)
    ";

    $rowsInserted = insertData($dbh, $query);
    echo "insert_event inserted:" . $rowsInserted . "</br>";
};



function insert_payment($dbh){

};