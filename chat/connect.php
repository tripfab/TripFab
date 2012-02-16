<?php
session_start();
error_reporting(0);




$c=mysql_connect("localhost", "root", "root") or die("error in connection");
mysql_select_db("chat", $c) or die("error in db selection");
$email="";
$username="cristian0789"; //Which was for web chat robot
$password="caaj0789titi";

/*********** Multi user bots******** SLow performance, enable this if u have values in database/

$q=mysql_query("SELECT EMAIL,USERNAME,PASSWORD FROM operator WHERE IS_FREE=1 limit 1");
if(mysql_num_rows($q) < 1)
{
	echo "All operators are busy right now, Please try after some time.";
}
else
{
	while($a=mysql_fetch_row($q))
	{
		$email=$a[0];
		
	}
}

*/
/***********or just enter here operator email***********/

             $email="cristian@tripfab.com"; // The operator Email
?>