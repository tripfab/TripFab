<?php

//session_start();
include("connect.php");
//echo "select msg from msg where user='".$_SESSION["chatuser"]."' order by stamp desc limit 1";
$q = mysql_query("select msg from msg where user='" . $_SESSION["chatuser"] . "' order by stamp desc limit 1");
while ($a = mysql_fetch_row($q)) {
    echo $a[0] . "<br>";
}
?>