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
<title>asdasdkjh</title><!--
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
</script>-->
<style>
	.hidden {
		display: none;
	}
</style>
</head>
<body id="toma2">
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
<style>
	div.traop {
		width: 200px;
		height: 200px;
		background-color: red;
	}
	.normal {
		}
	}
</style>
<div class="form">
	<form method="post" action="" id="form">
		<label>Name: </label><input type="text" name="" value="" /></label>
		<label>Email: </label><input id="email" type="text" name="" value="" /></label>
		<div id="errorEmail"></div>
		<label>Phone: </label><input type="text" name="" value="" /></label>
		<label>asdasd: </label><input type="text" name="" value="" /></label>
		<select>
			<option>Option1</option>
			<option>Option2</option>
		</select>
		<input type="submit" value="Submit" name="" tabindex="300" />
	</form>
	
	<script type="text/javascript">
	function evenHandler() {
	
		document.getElementById("form").onsubmit = function() {
			if (document.getElementById("email").value == "") {
				document.getElementById("errorEmail").innerHTML = "Please Insert a valid eMail Address";
				
				return false;
			} else {
				document.getElementById("errorEmail").innerHTML = "";
				return true;
			}
		};
	
	}
	
	
	
	window.onload = function() {
		evenHandler();
	}
	</script>
</div>
<ul id="list1">
	<li>item1</li>
	<li>item2</li>
	<li>item3</li>
	<li>item4</li>
</ul>
<button id="fadein">Fade In</button>
<button id="fadeout">fade Out</button>
<button id="fadeTo">fadeTo</button>
<button id="fadeTo2">fadeTo</button>
<div class="traop"></div>

<p id="para2">lorealkdioaio fdoae siof uioasfoi saio fiosafd 11111</p>
<p id="para1">lorealkdioaio fdoae siof uioasfoi saio fiosafd 22222</p>

<p id="para3">lorealkdioaio fdoae siof uioasfoi saio fiosafd 33333</p>

<div id="trivia">


</div>

<div class="slider">

	<div>
		<img src="https://static.tripfab.com/trips/papagayo/main.jpg" width="690" height="285" alt="" />
	</div>
	<div>
		<img src="https://static.tripfab.com/images6/land4-3.png" width="690" height="366" alt="" />
	</div>
	<div>
		<img src="https://static.tripfab.com/images6/land4-3.png" width="690" height="366" alt="" />
	</div>
</div><!--SLIDER-->
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.18/jquery-ui.min.js"></script>
<script type="text/javascript">
//	var mainContent = document.getElementById("para2");
//	mainContent.setAttribute("class", "Hello");
//	var newHeading = document.createElement("h1");
//	var newParagra = document.createElement("p");
//	
//	newHeading.innerHTML  = "Did you Know??";
//	newParagra.innerHTML  = "asldfjadfkj dskfkjh sdkjfh kjdhfkh dsfhf sdhfkjsdjjdjfjsadhfjf";
//	
//	document.getElementById("trivia").appendChild(newHeading);
//	document.getElementById("trivia").appendChild(newParagra);

	var newH1 = document.createElement("h1");
	var newPa = document.createElement("p");
	
	var textNode1 = document.createTextNode("This is the new Heading");
	var textNode2 = document.createTextNode("as dhafdjhj kdhf kjsdhfkjh dskjfhkdshfkjhsdfjdshfjk ");
	
	newH1.appendChild(textNode1);
	newPa.appendChild(textNode2);
	
	document.getElementById("trivia").appendChild(newH1);
	document.getElementById("trivia").appendChild(newPa);
	
	
	var mainContent = document.getElementById("trivia");
	mainContent.setAttribute("align", "right");
	
	

//	$(document).ready(function() {
//		$("#list1").html("<li>cacaca</li>");
//		var newItem = $("<p>This new Paragraph</p>");
//		$("para2").html(newItem.html());
//		$("p:last").text("Hello this is jmasdasdsad")
//		$("p").append(" hello this is apend content");
//		$("p").prepend("Hello");
//		$("p").wrapAll("<div style='border:1px solid red' />");
//		$("ul").empty();
//		$("#para2").html( $("div.traop").offset().top + ", " + $("div.traop").offset().left );
//		$("#para3").html( $("div.traop").po	sition().top + ", " + $("div.traop").position().left );		
//		$("#list1 li").text("HEllo is it me your loolimg");
//		$(function () {
//			$("div.traop").one("click", function() { 
//				$(this).css({ 
//					background: "blue", 
//					cursor: "pointer"
//				}); 
//			}, 
//			helloFn);
//		});
//		function helloFn() {
//			alert("I did it");
//		}
//		$(function() {
//			$("#show").click(function() {
//				$("div.traop").show("normal", Fncolor);
//			});
//			$("#hide").click(function() {
//				$("div.traop").hide("normal");
//			});
//			function Fncolor() {
//				$("div.traop").css({
//					background: "green",
//					border: "3px solid red"
//				})
//			}
//		})



//		$(function() {
//			$("#fadein").click(function() {
//				$("div.traop").slideDown(1000);
//			});
//			var min = 20;
//			var div = $("div.traop").height();
//			


//			$(function() {
//				function slideUp() {
//					$("div.traop").animate({height: "40px",}, 200, "swing");
//					$("div.traop").animate({height: "10px",}, 600, "linear");
//					$("div.traop").animate({height: "0px",}, 50, "linear");
//				}
//				function slideDown() {
//					$("div.traop").animate({
//						height: "200px",
//						border: "1px solid black"
//					});	
//				}
//				$("#fadeout").click(slideUp);
//				$("#fadein").click(slideDown);
//				
//			})
		
</script>











</body>
</html>