<?php

include("xmpp.php");
include("connect.php");

$conn = new XMPPHP_XMPP('talk.google.com', 5222, $username, $password, 'xmpphp', 'gmail.com', $printlog = False, $loglevel = 'LOGGING_INFO');

//Notiying...

$conn->connect();
$conn->processUntil('session_start');

$conn->message($email, "");
while (!$conn->disconnected) {
    $conn->presence($status = "Live Support Team 1");
}
echo "<script> Notify(); </script>";



//	 echo "kjkjkjk ".$p;
//	 echo "Notifying to ".$email;
// $conn->message($email,"*System: ".$username." is online.*");
//End...
?>