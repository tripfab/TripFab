<?php
	//set variable and assign the value from the form
	$email = $_POST['email'];
	
	$phpdate = date('Y-m-d H:i:s');
	
	// make connection
	mysql_connect('localhost', 'costar_dbuser', 'yFfGVUTCT]y#') or die ('Error:' .mysql_error());
	mysql_select_db('costar_landing');
	
	//Insert Query
	$query = 'INSERT INTO emails (email, date) VALUES ("'.$email.'","'.$phpdate.'")';
	
	mysql_query($query) or die ('Error updating the database');
	
	mail('cristian@tripfab.com','New Signup','We have a new sign up email: '. $email,'From: TripFab <hello@tripfab.com>');
?>