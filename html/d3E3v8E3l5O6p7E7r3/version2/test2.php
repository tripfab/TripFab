<?php

/**
// Conexion a la base de datos
define('SERVER','localhost');
define('DBUSER', 'root');
define('DBPASS', 'root');
define('DBNAME', 'chat');

// Conmecting to the mysql server
$conn = mysql_connect(SERVER,DBUSER,DBPASS) or die('Could not connect to the database');
// Selecting the database
mysql_select_db(DBNAME, $conn);

$resource = mysql_query('SELECT * FROM users', $conn);
$limit = mysql_num_rows($resource);
$resultado = array();
for($i=0; $i<$limit; $i++) {
	$resultado[] = mysql_fetch_array($resource);
}


echo '<pre>';
print_r($resultado);
echo '</pre>';


*/

/**
// Si $_POST tiene informacion
if(count($_POST) > 0) 
{
	$errores = array();
	if(empty($_POST['username']))
		$errores[] = 'Username cannot be empty';
	
	$numero = (int) $_POST['username'];
	if($numero === 0)
		$errores[] = 'Username must be a valid number';
	
}

*/
$string = (int) "125";
$integer = 125;
//$integer = "".$integer;
settype($integer,'string');

echo gettype($integer);

die;
if($string == $integer){
	echo 'Streing tiene el mismo valor que integer';
}

if($string === $integer){
	echo 'String tiene el mismo valor y es del mismo tipo que integer';
}

die;



?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Untitled Document</title>
</head>

<!--<body>
	<?php if(count($errores) > 0): ?>
    <div style="border:1px solid #F00; color:#F00; padding:5px;">
    	<?php foreach($errores as $key => $error): ?>
        Error #<?= $key ?>: <?= $error ?><br />
        <?php endforeach ?>
    </div>
    <?php endif ?>
	<form action="" method="post">
    	<label for="name">Ingrese su nombre</label>
        <input type="text" name="username" id="username" value="" />
        <input type="submit" value="enviar" />
    </form>
</body>-->
<body>
	
</body>
</html>