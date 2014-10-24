<?php

include 'db.php';
include 'functions.php';


// Include the phpass library
require_once('bower_components/phpass-0.3/PasswordHash.php');

$dbh = createDatabaseConnection();

if (defined('STDIN')) {
  $patch = $argv[1];  #### assumes your running:  php dbRefreshTables.php DB20140727
} else {
  $patch = $_GET['patch'];
}

if (!isset($patch)){
    die('patch needs to be passed via query string for http requests... or passed on command line \n valid values:DB.20140727 or BASE or ALL');
}


switch ($patch) {
    case 'ALL':
        process_all($dbh);
        break;
    case 'DB.20140727':
        process_patchDB_20140727($dbh);
        break;
    case 'DB.20141006':
        process_patchDB_20141006($dbh);
        break;
    case 'DB.20141007':
        process_patchDB_20141007($dbh);
        break;
    case 'BASE':
        process_base($dbh);
        break;
    default:
        die("patch value of -->$patch<-- is not valid");
        break;
}


function process_all($dbh){

emptyTable($dbh,'todo');
emptyTable($dbh,'todo_batch');
emptyTable($dbh,'todo_group');
emptyTable($dbh,'todo_status');
emptyTable($dbh,'todo_priority');
emptyTable($dbh,'payment');
emptyTable($dbh,'todo_frequency');

emptyTable($dbh,'payment');
emptyTable($dbh,'payment_method');

emptyTable($dbh,'account_period');
emptyTable($dbh,'account_period_status');

emptyTable($dbh,'event');
emptyTable($dbh,'event_description');

emptyTable($dbh,'account_type');

emptyTable($dbh,'customer');

# must be after customer since customer has FK to this:
emptyTable($dbh,'credential_status');

insert_credential_status($dbh);
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

insert_account_period($dbh);


}

function process_base($dbh){

insertCustomer($dbh);
insertFrequencies($dbh);
insertStatuses($dbh);
insertPriorities($dbh);
insertTodoGroups($dbh);
insertTodos($dbh);


}


function process_patchDB_20140727($dbh){

echo "processing....process_patchDB_20140727";

insert_credential_status($dbh);
insert_payment_method($dbh);
insert_event_description($dbh);
insert_account_period_status($dbh);
insert_account_type($dbh);

insert_event($dbh);
insert_payment($dbh);

insert_account_period($dbh);


}


function process_patchDB_20141006($dbh){

echo "processing....process_patchDB_20141006";
fix_frequency($dbh);
}




function emptyTable($dbh, $table){

  $query = "delete from $table"  ;

  //$query = "TRUNCATE TABLE $table";
  $rowsDeleted = deleteData($dbh, $query);
  echo "$table deleted:" . $rowsDeleted . "<br>";


}


function insert_credential_status($dbh){
    $query = "INSERT INTO credential_status (credential_cd, description) VALUES
    (1, 'Awaiting Confirmation eMail Return'),
    (2, 'Temporary Password Issued'),
    (0, 'Legitimate')
    ";

    $rowsInserted = insertData($dbh, $query);
    echo "credential_status: rows inserted:" . $rowsInserted . "<br>";

}



function insertCustomer($dbh){


  $guid1 = createGUID();
  $guid2 = createGUID();

  // Initialize the hasher without portable hashes (this is more secure)
  $hasher = new PasswordHash(8, false);

  // Hash the password.  $hashedPassword will be a 60-character string.
  $hashedPassword1 = $hasher->HashPassword('fakepassword');
  $hashedPassword2 = $hasher->HashPassword('fakepassword2');

  $query = "INSERT INTO customer (customer_id, email, password, guid) VALUES
    ( 1, 'fakeuser@yahoo.com', '$hashedPassword1', '$guid1'),
    ( 2, 'fakeuser2@yahoo.com', '$hashedPassword2','$guid2')";

  $rowsInserted = insertData($dbh, $query);
  echo "Customer inserted:" . $rowsInserted . "<br>";

}
function insertFrequencies($dbh){

  $query = "INSERT INTO todo_frequency (frequency_cd ,frequency_name) VALUES
    ( '10','Once'),
    ( '20','Weekly'),
    ( '30','BiWeekly'),
    ( '40','Monthly'),
    ( '50','Quarterly'),
    ( '60','Yearly')"  ;

  $rowsInserted = insertData($dbh, $query);
  echo "Frequencies inserted:" . $rowsInserted . "<br>";

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
  echo "priorities inserted:" . $rowsInserted . "<br>";

}

function insertStatuses($dbh){

  $query = "INSERT INTO todo_status (status_cd ,status_name) VALUES
    ( 0,'Inactive'),
    ( 1,'Active'),
    ( 2,'Completed'),
    ( 3,'Placeholder for Reocurring')"
  ;

  $rowsInserted = insertData($dbh, $query);
  echo "statuses inserted:" . $rowsInserted . "<br>";

}

function insertTodos($dbh){

  $query = "INSERT INTO todo (
      task_name,                                      due_dt, starred, group_id, priority_cd,  frequency_cd, status_cd, customer_id, Note, done, tags, todo_id)  VALUES
    ('Buy Milk',                       '2014-10-05 00:00:00',     '1',      '1',         '1',           '10',       '1',    1,  'my note',    0,  '',   1),
    ('Fertilize the lawn',             '2014-10-06 00:00:00',     '0',      '1',         '2',           '20',       '2',    1,  'my note',    0,  '',   2),
    ('Plan Vacation',                  '2014-10-07 00:00:00',     '1',      '1',         '3',           '30',       '1',    1,  'my note',    0,  '',   3),
    ('Study for math exam',            '2014-11-02 00:00:00',     '0',      '1',         '1',           '20',       '2',    1,  'my note',    0,  '',   4),
    ('Plant Fall flowers',             '2014-12-03 00:00:00',     '1',      '1',         '2',           '30',       '1',    1,  'my note',    0,  '',   5),
    ('Get To Do Giant!',               '2014-12-09 00:00:00',     '0',      '1',         '3',           '20',       '2',    1,  'my note',    0,  '',   6),
    ('Presentation for the Boss',      '2014-12-09 00:00:00',     '0',      '1',         '3',           '20',       '2',    1,  'my note',    0,  'Boss',   7),
    ('Buy Gift for Dad',               '2014-12-09 00:00:00',     '0',      '1',         '3',           '20',       '2',    1,  'my note',    0,  '',   8),
    ('Flowers for Anniversary ',       '2014-12-09 00:00:00',     '0',      '1',         '3',           '20',       '2',    1,  'my note',    0,  '',   9),
    ('Exercise Training for Marathon', '2014-12-09 00:00:00',     '0',      '1',         '3',           '20',       '2',    1,  'my note',    0,  '',   10),
    ('Ideas for starting a business',  '2014-12-09 00:00:00',     '0',      '1',         '3',           '20',       '2',    1,  'my note',    0,  '',   11),
    ('Create Will',                    '2014-12-09 00:00:00',     '0',      '1',         '3',           '20',       '2',    1,  'my note',    0,  '',   12),
    ('Create Financial Budget',        '2014-12-09 00:00:00',     '0',      '1',         '3',           '20',       '2',    1,  'my note',    0,  '',   13),
    ('Do Taxes',                       '2015-03-14 00:00:00',     '0',      '1',         '3',           '20',       '2',    1,  'my note',    0,  '',   14),
    ('Make Vet Appointment',           '2015-03-14 00:00:00',     '0',      '1',         '3',           '20',       '2',    1,  'my note',    0,  '',   15),
    ('Take out Trash',                 '2015-03-14 00:00:00',     '0',      '1',         '3',           '20',       '2',    1,  'my note',    0,  '',   16),
    ('Buy Milk',                       '2014-10-05 00:00:00',     '1',      '3',         '1',           '10',       '1',    2,  'my note',    0,  '',   17),
    ('Fertilize the lawn',             '2014-10-06 00:00:00',     '0',      '3',         '2',           '20',       '2',    2,  'my note',    0,  '',   18),
    ('Plan Vacation',                  '2014-10-07 00:00:00',     '1',      '3',         '3',           '30',       '1',    2,  'my note',    0,  '',   19),
    ('Study for math exam',            '2014-11-02 00:00:00',     '0',      '3',         '1',           '20',       '2',    2,  'my note',    0,  '',   20),
    ('Plant Fall flowers',             '2014-12-03 00:00:00',     '1',      '3',         '2',           '30',       '1',    2,  'my note',    0,  '',   21),
    ('Get To Do Giant!',               '2014-12-09 00:00:00',     '0',      '3',         '3',           '20',       '2',    2,  'my note',    0,  '',   22),
    ('Presentation for the Boss',      '2014-12-09 00:00:00',     '0',      '3',         '3',           '20',       '2',    2,  'my note',    0,  'Boss',   23),
    ('Buy Gift for Dad',               '2014-12-09 00:00:00',     '0',      '3',         '3',           '20',       '2',    2,  'my note',    0,  '',   24),
    ('Flowers for Anniversary ',       '2014-12-09 00:00:00',     '0',      '3',         '3',           '20',       '2',    2,  'my note',    0,  '',   25),
    ('Exercise Training for Marathon', '2014-12-09 00:00:00',     '0',      '3',         '3',           '20',       '2',    2,  'my note',    0,  '',   26),
    ('Ideas for starting a business',  '2014-12-09 00:00:00',     '0',      '3',         '3',           '20',       '2',    2,  'my note',    0,  '',   27),
    ('Create Will',                    '2014-12-09 00:00:00',     '0',      '3',         '3',           '20',       '2',    2,  'my note',    0,  '',   28),
    ('Create Financial Budget',        '2014-12-09 00:00:00',     '0',      '3',         '3',           '20',       '2',    2,  'my note',    0,  '',   29),
    ('Do Taxes',                       '2015-03-14 00:00:00',     '0',      '3',         '3',           '20',       '2',    2,  'my note',    0,  '',   30),
    ('Make Vet Appointment',           '2015-03-14 00:00:00',     '0',      '3',         '3',           '20',       '2',    2,  'my note',    0,  '',   31),
    ('Take out Trash',                 '2015-03-14 00:00:00',     '0',      '3',         '3',           '20',       '2',    2,  'my note',    0,  '',   32)
    ";

  $rowsInserted = insertData($dbh, $query);
  echo "todos inserted:" . $rowsInserted . "<br>";

}

function insertTodoGroups($dbh){

  $query = "INSERT INTO todo_group (group_id, group_name, Sort_Order,customer_id, active) VALUES
    (1, 'Home',1,1,1),
    (2, 'Work',2,1,0),
    (3, 'My To Dos',1,2,1)
    "
  ;

  $rowsInserted = insertData($dbh, $query);
  echo "todoGroups inserted:" . $rowsInserted . "<br>";

}

function insert_payment_method($dbh){
    $query = "INSERT INTO payment_method (payment_method_cd, description) VALUES
    (1, 'CreditCard'),
    (2, 'PayPal')
    ";

    $rowsInserted = insertData($dbh, $query);
    echo "payment_method inserted:" . $rowsInserted . "<br>";

}




function insert_event_description($dbh){
    $query = "INSERT INTO event_description (event_cd, description) VALUES
    (1, 'Registration'),
    (2, 'Login'),
    (3, 'Facebook Like'),
    (4, 'Tweet'),
    (5, 'Payment'),
    (6, 'Password Change'),
    (7, 'Referral'),
    (8, 'Temporary Password Created')
    ";

    $rowsInserted = insertData($dbh, $query);
    echo "event_description inserted:" . $rowsInserted . "<br>";
};



function insert_account_period_status($dbh){
    $query = "INSERT INTO account_period_status (account_period_status_cd, description) VALUES
    (1, 'Active'),
    (2, 'Done')
    ";

    $rowsInserted = insertData($dbh, $query);
    echo "account_period_status inserted:" . $rowsInserted . "<br>";
};



function insert_account_type($dbh){
    $query = "INSERT INTO account_type (account_type_cd, description) VALUES
    (1, 'Trial (Premium)'),
    (2, 'Free'),
    (3, 'Premium')
    ";

    $rowsInserted = insertData($dbh, $query);
    echo "account_type inserted:" . $rowsInserted . "<br>";
};


function insert_event($dbh){
    $query = "INSERT INTO event (customer_id, create_dt, event_cd, event_id) VALUES
    (1, CURDATE(), 1, 1),
    (2, CURDATE(), 1, 2)
    ";

    $rowsInserted = insertData($dbh, $query);
    echo "event inserted:" . $rowsInserted . "<br>";
};



function insert_payment($dbh){

};

function insert_account_period($dbh){
    $query = "INSERT INTO account_period (customer_id, begin_dt, end_dt, account_type_cd, account_period_status_cd, account_period_id, event_id) VALUES
    (1, '2014-07-29', '2014-08-29', 1, 1, 1, 1),
    (1, '2014-08-30', '2015-08-29', 2, 1, 2, 1),
    (2, '2014-07-29', '2014-08-29', 1, 1, 3, 2),
    (2, '2014-08-30', '2015-08-29', 2, 1, 4, 2)
    ";

    //(1, CURDATE(), CURDATE() + INTERVAL 31 DAY, 1, 1, 1,1),

    $rowsInserted = insertData($dbh, $query);
    echo "account_period inserted:" . $rowsInserted . "<br>";

}


function fix_frequency($dbh){
  $query = "INSERT INTO todo_frequency (frequency_cd ,frequency_name) VALUES
    ( '10','Once'),
    ( '20','Weekly'),
    ( '30','BiWeekly'),
    ( '40','Monthly'),
    ( '50','Quarterly'),
    ( '60','Yearly')"  ;

  $rowsInserted = insertData($dbh, $query);
  echo "Frequencies inserted:" . $rowsInserted . "<br>";

  $query = "UPDATE todo set frequency_cd = 10 where frequency_cd = 1";
  $rowsUpdated = insertData($dbh, $query);
  echo "Updated 1 to 10. This many rows changed :" . $rowsUpdated . "<br>";

  $query = "UPDATE todo set frequency_cd = 20 where frequency_cd = 2";
  $rowsUpdated = insertData($dbh, $query);
  echo "Updated 2 to 20. This many rows changed :" . $rowsUpdated . "<br>";

  $query = "UPDATE todo set frequency_cd = 40 where frequency_cd = 3";
  $rowsUpdated = insertData($dbh, $query);
  echo "Updated 3 to 40. This many rows changed :" . $rowsUpdated . "<br>";

  $query = "UPDATE todo set frequency_cd = 50 where frequency_cd = 4";
  $rowsUpdated = insertData($dbh, $query);
  echo "Updated 4 to 50. This many rows changed :" . $rowsUpdated . "<br>";

  $query = "UPDATE todo set frequency_cd = 60 where frequency_cd = 5";
  $rowsUpdated = insertData($dbh, $query);
  echo "Updated 5 to 60. This many rows changed :" . $rowsUpdated . "<br>";

  $query = "delete from todo_frequency where frequency_cd in (1, 2, 3, 4, 5)";
  $rowsUpdated = insertData($dbh, $query);
  echo "Deleted old values. This many rows deleted:" . $rowsUpdated . "<br>";

}
