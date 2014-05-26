<!DOCTYPE HTML>
<html>
	<header>
		<title>Test page</title>
		<meta content="text/html" charset="UTF-8" />
		<script src="http://code.jquery.com/jquery-1.11.0.js"></script>
	</header>
	<body>
		<?php
			include_once('database.php');
			include_once 'shopcart.php';
			$cart = new Cart;
			
		?>
		
		<button onclick="printCarrito()">CART PRINT</button><br>
		<button onclick="agregarACarrito()">CART ADD</button><br>
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
		</script>
		<br /><br />
		<div id="result" style="margin: auto; padding: 8px; background-color: #FFD684; width: 800px; height: 500px;"></div>
	</body>
</html>