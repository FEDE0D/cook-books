<!DOCTYPE HTML>
<html>
	<header>
		<title>Test page</title>
		<meta content="text/html" charset="UTF-8" />
		<link rel="shortcut icon" href="website/favicon/1.png"/>
		<script src="website/jquery-1.11.0.js"></script>
	</header>
	<body>
		<?php
			include_once('database.php');
		?>
		
		<button onclick="printCarrito()">CART PRINT</button><br>
		<button onclick="agregarACarrito()">CART ADD</button><br>
		<button onclick="nuevoAutor()">NEW AUTHOR</button><br>
		<button onclick="testJSON()">Test JSON</button>
		<script>
			function agregarACarrito(){
				$.ajax({
					url:"ajax.php",
					type:"POST",
					data:{
						type: "cart",
						action: "ADD",
						bookid: "882894293"
					},
					success:function(data){
						$("#result").html(data);
					}
				});
			}
			function printCarrito(){
				$.ajax({
					url:"ajax.php",
					type:"POST",
					data:{
						type: "cart",
						action: "PRINT"
					},
					success:function(data){
						$("#result").html(data);
					}
				});
			}
			function nuevoAutor(){
				$.ajax({
					url:"ajax.php",
					type:"POST",
					data:{
						type: "author",
						action: "NEW",
						auth_id: "",
						auth_nombre: "nuevo",
						auth_apellido: "nuevoApellido",
						auth_fecha_n: "",
						auth_lugar_n: ""
					},
					success:function(data){
						$("#result").html(data);
					}
				});
			}
			
			function testJSON(){
				var request = {
					action:"NAME_AVAILABLE",
					username:"fede0d",
					email:"federico@gmail.com.ar"
				};
				
				$.ajax({
					url:"ajax2.php",
					type:"POST",
					data:{
						type: "USER",
						data: JSON.stringify(request)
					},
					success:function(data){
						alert(data);
						return;
						var result = $.parseJSON(data);
						$("#result").text(result.ok);
					}
				});
			}
		</script>
		<br /><br />
		<div id="result" style="margin: auto; padding: 8px; background-color: #FFD684; width: 800px; height: 500px;"></div>
	</body>
</html>