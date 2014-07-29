<?php


$baseURL = 'http://localhost/todo/app/';

## SESSION
### TO SET THIS.... Get into Chrome/FireFox... login.. then inspect headers and grab this value....
$sessionID = '77jk4u4oa3itc0jgq0lllqikj4';


#####   TEST - LOGIN  ##############
$testName = 'login';
$url = $baseURL . 'login.php';
$data = array("action" => "loginUser", "email" => "fakeuser@yahoo.com", "password" => "fakepassword");
$expected = '{"login":1}';
$result = postIT($url, $data, $sessionID);
if ($result == $expected){echo "$testName PASSED\n";} else {echo "$testName FAILED.. \nGOT:    \t$result \nEXPECTED:\t$expected\n\n\n\n\n\n";}


#####   TEST - LOGIN  Status ##############
$testName = 'loginStatus';
$url = $baseURL . 'login.php';
$expected = '{"login":1}';
$result = getIT($url, '', $sessionID);
if ($result == $expected){echo "$testName PASSED\n";} else {echo "$testName FAILED.. \nGOT:    \t$result \nEXPECTED:\t$expected\n\n\n\n\n\n";}


#####   TEST - FREQUENCIES  ##############
$testName = 'Frequencies';
$url = $baseURL . 'todo.php';
$query_string = "?action=getfrequencies";
$expected = '[{"cd":"1","name":"Once"},{"cd":"2","name":"Weekly"},{"cd":"3","name":"Monthly"},{"cd":"4","name":"Quarterly"},{"cd":"5","name":"Yearly"}]';
$result = getIT($url, $query_string, $sessionID);
if ($result == $expected){echo "$testName PASSED\n";} else {echo "$testName FAILED.. \nGOT:    \t$result \nEXPECTED:\t$expected\n\n\n\n\n\n";}



#####   TEST - PRIORITIES  ##############
$testName = 'Priorities';
$url = $baseURL . 'todo.php';
$query_string = "?action=getpriorities";
$expected = '[{"cd":"1","name":"1-Max"},{"cd":"2","name":"2"},{"cd":"3","name":"3"},{"cd":"4","name":"4"},{"cd":"5","name":"5"},{"cd":"6","name":"6"},{"cd":"7","name":"7"},{"cd":"8","name":"8"},{"cd":"9","name":"9-Low"}]';
$result = getIT($url, $query_string, $sessionID);
if ($result == $expected){echo "$testName PASSED\n";} else {echo "$testName FAILED.. \nGOT:    \t$result \nEXPECTED:\t$expected\n\n\n\n\n\n";}


#####   TEST - gettodos  ##############
$testName = 'gettodos';
$url = $baseURL . 'todo.php';
$query_string = "?action=gettodos";
$expected = '[{"todo_id":"3","group_id":"1","task_name":"Plan Vacation","due_dt":"07\/07\/2014","starred":"1","priority_cd":"3","frequency_cd":"3","status_cd":"1","note":"my note","done":false,"tags":"","done_dt":null},{"todo_id":"6","group_id":"1","task_name":"Get Todo Giant","due_dt":"09\/09\/2014","starred":"0","priority_cd":"3","frequency_cd":"2","status_cd":"2","note":"my note","done":false,"tags":"","done_dt":null},{"todo_id":"2","group_id":"1","task_name":"Fertilize the lawn","due_dt":"07\/06\/2014","starred":"0","priority_cd":"2","frequency_cd":"2","status_cd":"2","note":"my note","done":false,"tags":"","done_dt":null},{"todo_id":"5","group_id":"1","task_name":"Plant Fall flowers","due_dt":"09\/03\/2014","starred":"1","priority_cd":"2","frequency_cd":"3","status_cd":"1","note":"my note","done":false,"tags":"","done_dt":null},{"todo_id":"1","group_id":"1","task_name":"Buy Milk","due_dt":"07\/05\/2014","starred":"1","priority_cd":"1","frequency_cd":"1","status_cd":"1","note":"my note","done":false,"tags":"","done_dt":null},{"todo_id":"4","group_id":"1","task_name":"Study for exam","due_dt":"08\/02\/2014","starred":"0","priority_cd":"1","frequency_cd":"2","status_cd":"2","note":"my note","done":false,"tags":"","done_dt":null}]';
$result = getIT($url, $query_string, $sessionID);
if ($result == $expected){echo "$testName PASSED\n";} else {echo "$testName FAILED.. \nGOT:    \t$result \nEXPECTED:\t$expected\n\n\n\n\n\n";}


#####   TEST - gettodogroups  ##############
$testName = 'gettodogroups';
$url = $baseURL . 'todo.php';
$query_string = "?action=gettodogroups";
$expected = '[{"group_id":"1","group_name":"Home","sort_order":"1","active":true},{"group_id":"2","group_name":"Work","sort_order":"2","active":false}]';
$result = getIT($url, $query_string, $sessionID);
if ($result == $expected){echo "$testName PASSED\n";} else {echo "$testName FAILED.. \nGOT:    \t$result \nEXPECTED:\t$expected\n\n\n\n\n\n";}


#####   TEST - getMaxAccountPeriodEndDt  ##############
$testName = 'getMaxAccountPeriodEndDt';
$url = $baseURL . 'testing_php_wrapper.php';
$query_string = "?action=getMaxAccountPeriodEndDt";
$expected = '[{"end_dt":"2014-08-29"}]';
$result = getIT($url, $query_string, $sessionID);
if ($result == $expected){echo "$testName PASSED\n";} else {echo "$testName FAILED.. \nGOT:    \t$result \nEXPECTED:\t$expected\n\n\n\n\n\n";}






#### POST ##########

function postIT($url, $data, $sessionID){
        $data_string = json_encode($data);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_COOKIE, 'PHPSESSID='.$sessionID);
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
function getIT($url, $query_string, $sessionID){
        $fullURL = $url.$query_string;
        //echo "this is fullURL: $fullURL";
        $ch = curl_init($fullURL);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_COOKIE, 'PHPSESSID='.$sessionID);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json')
        );
        $result = curl_exec($ch);
        $result = preg_replace("/\n/", "", $result);
        return $result;
        //echo "\n\n$result\n\n";
}



?>