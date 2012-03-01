<?php

include("xmpp.php");
include("connect.php");
//ini_set("max_execution_time",200); 
$conn = new XMPPHP_XMPP('talk.google.com', 5222, $username, $password, 'xmpphp', 'gmail.com', $printlog = False, $loglevel = LOGGING_INFO);
$conn->connect();
$conn->processUntil('session_start');
$conn->presence($status = "Live Support Team 1", "chat");
$conn->message($email, $_REQUEST["msg"]);
?>