<?php


$baseURL = 'http://localhost/todo/app/';

## SESSION
### TO SET THIS.... Get into Chrome/FireFox... login.. then inspect headers and grab this value....
define("SESSIONid", '7HDDDDDDDu4oa3itc0jgq0lllqikj4');


###################
##   THIS ESTABLISHES THE SESSION  :-)
$url = $baseURL . 'login.php';
$data = array("action" => "loginUser", "email" => "fakeuser@yahoo.com", "password" => "fakepassword");
$expected = '{"login":1}';
testDriver('auth', 'login', $url, 0, $data, $expected);


###################
$url = $baseURL . 'login.php';
$data = "?action=getloginStatus";
$expected = '{"login":1}';
testDriver('auth', 'getloginStatus', $url, 1, $data, $expected);


###################
$url = $baseURL . 'todo.php';
$data = "?action=getfrequencies";
$expected = '[{"cd":"1","name":"Once"},{"cd":"2","name":"Weekly"},{"cd":"3","name":"Monthly"},{"cd":"4","name":"Quarterly"},{"cd":"5","name":"Yearly"}]';
testDriver('ref', 'getfrequencies', $url, 1, $data, $expected);


###################
$url = $baseURL . 'todo.php';
$data = "?action=getpriorities";
$expected = '[{"cd":"1","name":"1-Max"},{"cd":"2","name":"2"},{"cd":"3","name":"3"},{"cd":"4","name":"4"},{"cd":"5","name":"5"},{"cd":"6","name":"6"},{"cd":"7","name":"7"},{"cd":"8","name":"8"},{"cd":"9","name":"9-Low"}]';
testDriver('ref', 'getpriorities', $url, 1, $data, $expected);


###################
$url = $baseURL . 'todo.php';
$data = "?action=gettodos";
$expected = '[{"todo_id":"3","group_id":"1","task_name":"Plan Vacation","due_dt":"07\/07\/2014","starred":"1","priority_cd":"3","frequency_cd":"3","status_cd":"1","note":"my note","done":false,"tags":"","done_dt":null},{"todo_id":"6","group_id":"1","task_name":"Get Todo Giant","due_dt":"09\/09\/2014","starred":"0","priority_cd":"3","frequency_cd":"2","status_cd":"2","note":"my note","done":false,"tags":"","done_dt":null},{"todo_id":"2","group_id":"1","task_name":"Fertilize the lawn","due_dt":"07\/06\/2014","starred":"0","priority_cd":"2","frequency_cd":"2","status_cd":"2","note":"my note","done":false,"tags":"","done_dt":null},{"todo_id":"5","group_id":"1","task_name":"Plant Fall flowers","due_dt":"09\/03\/2014","starred":"1","priority_cd":"2","frequency_cd":"3","status_cd":"1","note":"my note","done":false,"tags":"","done_dt":null},{"todo_id":"1","group_id":"1","task_name":"Buy Milk","due_dt":"07\/05\/2014","starred":"1","priority_cd":"1","frequency_cd":"1","status_cd":"1","note":"my note","done":false,"tags":"","done_dt":null},{"todo_id":"4","group_id":"1","task_name":"Study for exam","due_dt":"08\/02\/2014","starred":"0","priority_cd":"1","frequency_cd":"2","status_cd":"2","note":"my note","done":false,"tags":"","done_dt":null}]';
testDriver('todo', 'gettodos', $url, 1, $data, $expected);


###################
$url = $baseURL . 'todo.php';
$data = "?action=gettodogroups";
$expected = '[{"group_id":"1","group_name":"Home","sort_order":"1","active":true},{"group_id":"2","group_name":"Work","sort_order":"2","active":false}]';
testDriver('todo', 'gettodogroups', $url, 1, $data, $expected);


###################
$url = $baseURL . 'testing_php_wrapper.php';
$data = "?action=getMaxPremiumDt";
$expected = '[{"end_dt":"2014-08-29"}]';
testDriver('acct', 'getMaxPremiumDt', $url, 1, $data, $expected);


###################
$url = $baseURL . 'testing_php_wrapper.php';
$data = "?action=getAccountPeriod";
$expected = '[{"description":"Trial (Premium)","begin_dt":"2014-07-29","end_dt":"2014-08-29"},{"description":"Free","begin_dt":"2014-08-30","end_dt":"2015-08-29"}]';
testDriver('acct', 'getAccountPeriod', $url, 1, $data, $expected);



###################
##   THIS BLOWS AWAY THE SESSION  :-)
$url = $baseURL . 'login.php';
$data = array("action" => "logOutUser");
$expected = '{"login":0}';
testDriver('auth', 'logOutUser', $url, 0, $data, $expected);


###########################
###########################
###########################
###########################
#### this section calls the functions directly.. no HTTP calls...
include 'db.php';
include 'functions.php';

$dbh = createDatabaseConnection();



###################
$expected = 1;
$result = getCustomerId($dbh, 'fakeuser@yahoo.com');
testDriverDirect('cust', 'getCustomerId', $result, $expected);


###################
$expected = '[{"end_dt":"2014-08-29"}]';
$result = getMaxPremiumDt($dbh, 1);
$result = json_encode($result);
testDriverDirect('acct', 'getMaxPremiumDt', $result, $expected);


###################
$expected = '[{"description":"Trial (Premium)","begin_dt":"2014-07-29","end_dt":"2014-08-29"},{"description":"Free","begin_dt":"2014-08-30","end_dt":"2015-08-29"}]';
$result = getAccountPeriod($dbh, 1);
$result = json_encode($result);
testDriverDirect('acct', 'getAccountPeriod', $result, $expected);


###################
$expected = 1;
$result = doesUserExist($dbh, 'fakeuser@yahoo.com');
testDriverDirect('cust', 'doesUserExist', $result, $expected);



//$result = addEvent($dbh, 1, 2, '2014-08-02');
//      $payment_method_cd = 1; #1:credit card
//      $pmt_amt = 1000;
//      $customer_id=1;
//      $event_id = 53;
//      $pmt_amt = $pmt_amt/100; ##divide by 100 since 1000 is $10.00 for stripe.
//      addPayment($dbh, $customer_id, $pmt_amt, $event_id, $payment_method_cd, date('Y-m-d'));

$customer_id=1;
setExtendPremiumOneYear($dbh, $customer_id, 53);


#########################################################################
function testDriver($cat, $testName, $url, $get, $data, $expected){
if ($get){
    $result = getIT($url, $data);
} else {
    $result = postIT($url, $data);
}
if ($result == $expected){echo "$cat\t$testName PASSED\n";} else {echo "$cat\t$testName FAILED.. \nGOT:    \t$result \nEXPECTED:\t$expected\n\n\n\n\n\n";}

}



#########################################################################
function testDriverDirect($cat, $testName, $result, $expected){
if ($result == $expected){echo "$cat\t$testName PASSED\n";} else {echo "$cat\t$testName FAILED.. \nGOT:    \t$result \nEXPECTED:\t$expected\n\n\n\n\n\n";}

}




#### POST ##########
function postIT($url, $data){
        $data_string = json_encode($data);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_COOKIE, 'PHPSESSID='.SESSIONid);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($data_string))
        );
        $result = curl_exec($ch);
        $result = preg_replace("/\n/", "", $result);
        return $result;
        //echo "\n\n$result\n\n";
}


#### GET ##########
function getIT($url, $query_string){
        //echo "url:$url qs:$query_string";
        $fullURL = $url.$query_string;
        //echo "this is fullURL: $fullURL";
        $ch = curl_init($fullURL);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_COOKIE, 'PHPSESSID='.SESSIONid);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json')
        );
        $result = curl_exec($ch);
        $result = preg_replace("/\n/", "", $result);
        return $result;
        //echo "\n\n$result\n\n";
}



?>