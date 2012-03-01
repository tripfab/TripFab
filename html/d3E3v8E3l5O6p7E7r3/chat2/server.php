<?php

error_reporting(0);
session_start();

$user = "";
if (!isset($_SESSION["chatuser"]) || $_SESSION["chatuser"] == "") {
    $_SESSION["chatuser"] = $_SERVER["REMOTE_ADDR"] . "$" . date("Y-m-d H:i:s");
    $user = $_SESSION["chatuser"];
} else {
    $user = $_SESSION["chatuser"];
}

//error_reporting(E_ERROR);
//set_time_limit(0);
include("xmpp.php");
include("connect.php");

//$c=mysql_connect("127.0.0.1","root","");
//mysql_select_db("pop");

$conn = new XMPPHP_XMPP('talk.google.com', 5222, $username, $password, 'xmpphp', 'gmail.com', $printlog = False, $loglevel = LOGGING_INFO);
$conn->connect();
$conn->processUntil('session_start');
$conn->presence($status = "Live Support Team 1");
$conn->message($email, $_REQUEST["msg"]);
while (!$conn->disconnected) {

    $conn->presence($status = "Live Support Team 1");



    $payloads = $conn->processUntil(array('message', 'presence', 'end_stream', 'session_start'));
    foreach ($payloads as $event) {
        $pl = $event[1];
        switch ($event[0]) {
            case 'message':
                if ($pl['subject'])
                    print "Subject: {$pl['subject']}\n";

                $conn->message($pl['from'], $body = "Thanks for sending me \"{$pl['body']}\".", $type = $pl['type']);
                mysql_query("insert into msg_bkp(msg, stamp, user) values('" . $pl['body'] . "',now(),'" . $user . "')", $c);
                mysql_query("insert into msg(msg,sent, stamp, user) values('" . $pl['body'] . "','',now(),'" . $user . "')", $c);


                echo '<script>  document.getElementById("display1").innerHTML += "<font color=green>Operator:ppp </font>"; </script>';


                if ($pl['body'] == 'quit')
                    $conn->disconnect();
                if ($pl['body'] == 'break')
                    $conn->send("</end>");
                break;
            case 'presence':
                //            print "Presence: {$pl['from']} [{$pl['show']}] {$pl['status']}\n";
                break;
            case 'session_start':
                $conn->presence($status = "Live Support Team 1");
                break;
        }
    }
}
?>