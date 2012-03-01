<?php

include("connect.php");
mysql_query("insert into msg (sent, stamp, user) values('" . $_REQUEST["sent"] . "',now(),'" . $_SESSION["chatuser"] . "')", $c);
?>