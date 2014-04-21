<?php

	if(isset($_POST['type'])){
		if ($_POST['type']=='ue'){//	USUARIOS
			include_once 'database.php';
			$usos = new DataBase;
			
			if (isset($_POST['username'])){
				$username = $_POST['username'];
				echo $usos->userExists($username)?'true':'false';
				return;
			}
		}else if ($_POST['type']=='sc'){//	SHOPING CART
			include_once 'shopcart.php';
			$cart = new Cart;
			
			if (isset($_POST['action'])){
				if ($_POST['action']=='ADD')			$cart->addToCart($_POST['bookid']);
				else if ($_POST['action']=='REMOVE')	$cart->removeFromCart($_POST['bookid']);
				else if ($_POST['action']=='EMPTY')		$cart->emptyCart();
				else if ($_POST['action']=='GETVIEW')	$cart->printCartMenu();	//NO USAR ESTA, PEDIR EN CAMBIO TODO EL NAVBAR
			}
				
			$cart->saveCart();
			//header("location: ".$_SERVER['HTTP_REFERER']);
		}
	}

?>