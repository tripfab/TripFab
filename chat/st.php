<?php

ini_set("max_execution_time", 10);
include("xmpp.php");
include("connect.php");
$conn = new XMPPHP_XMPP('talk.google.com', 5222, $username, $password, 'xmpphp', 'gmail.com', $printlog = False, $loglevel = LOGGING_INFO);
$conn->connect();

$conn->processUntil('session_start');
$conn->presence($status = "Live Support Team 1");
$conn->message($email, $_REQUEST["msg"]);
while (!$conn->disconnected) {
    $payloads = $conn->processUntil(array('presence'));
    foreach ($payloads as $event) {
        $pl = $event[1];


        if (stristr($pl['from'], $email)) {
            echo "yes";
            break;
            return;
        }
    }
}
?>