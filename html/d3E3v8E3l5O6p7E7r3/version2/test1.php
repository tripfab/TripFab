<?php // Leccion 1: Strings, Arrays, For y Foreach
$resultado = array(
	array(
		'nombre' => 'Cristian',
		'edad'	 => 22,
		'idiomas' => array('Español', "Ingles"),
	),
	array(
		'nombre' => 'Dionisio',
		'edad'	 => 23,
		'idiomas' => array('Español', "Ingles",'Portuguez'),
	),
	array(
		'nombre' => 'Mike',
		'edad'	 => 22,
		'idiomas' => array("Ingles"),
	),
	array(
		'nombre' => 'Gena',
		'edad'	 => 27,
		'idiomas' => array('Español', "Ingles", "Frances","Italiano"),
	)
);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>asdasdkjh</title>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
<script type="text/javascript">
	$(document).ready(function() {
		
		$("form :text:").before("WHAT?");
		
		if ( $("div").hasClass("da") ) {
			alert("Red");
		} 
		else {
			alert("Blue");
		}
	});
</script>
<style>
	.hidden {
		display: none;
	}
</style>
</head>
<body>
<table width="100%">
<tr>
	<td>Id</td>
	<th>Nombre</th>
    <th>Edad</th>
    <th>Idiomas</th>
</tr>
<?php $limit = count($resultado) ?>
<?php for($key=0; $key < $limit; $key++): ?>
<tr>
	<?php $value = $resultado[$key] ?>
	<td><?= $key ?>
	<td><?= $value['nombre'] ?></td>
    <td><?= $value['edad'] ?></td>
    <td>
    	<ul>
			<?php foreach($value['idiomas'] as $key => $idioma): ?>
			<li><?= $idioma ?></li>                
            <?php endforeach ?>
	    </ul>
    </td>
</tr>
<?php endfor ?>
</table>






<div class="hidden">
	<p>Hello World!</p>
</div>


<form method="submit" action="">

	<input type="text" disabled="disabled" name="" value="" />

</form>
















</body>
</html>