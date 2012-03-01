<?php

//ini_set("max_execution_time",0);
session_start();
include("xmpp.php");
include("connect.php");
?>
<style>
    .cnt {
        background-color: #EEEEEE;
        height: 500px;
        width: 360px;
    }
    .head {
        background-color: blue;
        color: white;
        font-family: tahoma;
        font-size: 15px;
        font-style: normal;
        padding: 2px;
        text-align: center;
    }
    .display {
        background-color: white;
        border: 1px solid white;
        height: 350px;
        margin-left: 3px;
        padding-top: 13px;
        width: 352px;
        overflow-y: scroll;
    }
    .msg {
        background-color: white;
        border: 1px solid white;
        height: 80px;
        margin-left: 3px;
        margin-top: 6px;
        width: 303px;
    }
    .txt
    {
        height: 78px;
        width: 303px;
    }
    .send {
        margin-left: 311px;
        margin-top: -50px;
        position: absolute;
    }
</style>
<script>

</script>
<script src="npk.js" type="text/javascript"></script>
<body onload="OnLoad99()">
    <div id="response" style="display:none"></div>
    <div class="cnt">
        <div class="head">Welcome in Live Chat Support <span id="stts" style="margin-left:20px">...</span></div>
        <div class="display"  id="display1">
            <!-- The Message Display Area -->

            <!-- END -->
        </div>
        <div class="msg">
            <textarea id="msg1" class="txt" onkeydown="javascript:doRefreshK(event)"></textarea>
        </div>
        <div class="send"><img src="save.jpg" onclick="javascript:doRefresh()"></div>

    </div>
</body>
<?php

if (isset($_REQUEST["action"]) && $_REQUEST["action"] == "send") {

    $user = "";
    if (!isset($_SESSION["chatuser"]) || $_SESSION["chatuser"] == "") {
        $_SESSION["chatuser"] = $_SERVER["REMOTE_ADDR"] . "$" . date("Y-m-d H:i:s");
        $user = $_SESSION["chatuser"];
    } else {
        $user = $_SESSION["chatuser"];
    }

//error_reporting(E_ERROR);
//set_time_limit(0);
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

                    //  $conn->message($pl['from'], $body="Thanks for sending me \"{$pl['body']}\".", $type=$pl['type']);
                    mysql_query("insert into msg_bkp(msg, stamp, user) values('" . $pl['body'] . "',now(),'" . $user . "')", $c);
                    mysql_query("insert into msg(msg,sent, stamp, user) values('" . $pl['body'] . "','',now(),'" . $user . "')", $c);
                    ?>
                    <script>  ReadMsg(); </script>
                    <?php

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
}

if (isset($_REQUEST["action"]) && $_REQUEST["action"] == "notify") {

    $conn = new XMPPHP_XMPP('talk.google.com', 5222, $username, $password, 'xmpphp', 'gmail.com', $printlog = False, $loglevel = LOGGING_INFO);
    $conn->connect();

    $conn->processUntil('session_start');
    $conn->presence($status = "Live Support Team 1");
    $conn->message($email, "A visitor wants to talk to you. *Please wait until visitor sends you first message.*");
    echo "sent";
}
?>