<?php

include('connect.php');


$conn = new XMPPHP_XMPP('talk.google.com', 5222, $username, $password, 'xmpphp', 'gmail.com', $printlog = False, $loglevel = LOGGING_INFO);
$conn->connect();

while (!$conn->disconnected) {

    //$conn->presence($status="Live Support Team 1");



    $payloads = $conn->processUntil(array('message', 'presence', 'end_stream', 'session_start'));
    foreach ($payloads as $event) {
        $pl = $event[1];
        switch ($event[0]) {
            case 'message':
                if ($pl['subject'])
                    print "Subject: {$pl['subject']}\n";

                $conn->message($pl['from'], $body = "Thanks for sending me \"{$pl['body']}\".", $type = $pl['type']);
                mysql_query("insert into msg_bkp(msg, stamp, user) values('" . $pl['body'] . "',now(),'" . $user . "')", $c);
                mysql_query("insert into msg(msg, stamp, user) values('" . $pl['body'] . "',now(),'" . $user . "')", $c);


                //         if($pl['body'] == 'quit') $conn->disconnect();
                if ($pl['body'] == 'break')
                    $conn->send("</end>");
                break;
            case 'presence':
                //            print "Presence: {$pl['from']} [{$pl['show']}] {$pl['status']}\n";
                break;
            case 'session_start':
//     $conn->presence($status="Live Support Team 1");
                break;
        }
    }
}
?>