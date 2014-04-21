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
			$usos = new DataBase;
			
			$cart->printCart();
			
		?>
		
	</body>
</html>