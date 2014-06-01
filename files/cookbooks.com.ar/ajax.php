<?php

/*
 * 
 * Los pedidos que reciba esta pagina deben tener el siguiente formato
 * 	type = [user | cart | author]
 * 	action = [
 *		type: user => 	EXISTS: retorna true|false si un usuario existe.
 * 						NAME_AVAILABLE: retorna true|false si ese nombre de usuario está tomado.
 * 						EMAIL_AVAILABLE: retorna true|false si ese email ya está tomado.
 * 
 *	  	type: cart => 	ADD: agrega un libro al carrito.
 *						REMOVE: saca un libro del carrito.
 *						EMPTY: vacía el carrito.
 * 
 * 		type: author =>	NEW: agrega un nuevo autor. recibe los datos y retorna ID|false si el alta fue correcta.
 * 						UPDATE: modifica un autor. recibe los datos y retorna true|false si la modificación fue correcta.
 * 						REMOVE: eliminar un autor (baja lógica). recibe un ID, retorna true|false si la baja fue correcta.
 *	]
 * 
 */
 
	if(isset($_REQUEST['type'])){
		include_once 'database.php';
		if ($_REQUEST['type']=='user'){//	USUARIOS
			$USERS = new Users;
			if (isset($_REQUEST['action'])){
				if ($_REQUEST['action']=='EXISTS'){
					if (isset($_REQUEST['username'])){
						$username = $_REQUEST['username'];
						echo $USERS->userExists($username)?'true':'false';
						return;
					}
				}else if ($_REQUEST['action']=='NAME_AVAILABLE'){
					if (isset($_REQUEST['username'])){
						$username = $_REQUEST['username'];
						echo $USERS->userExists($username)?'false':'true';
						return;
					}
				}else if ($_REQUEST['action']=='EMAIL_AVAILABLE'){
					if (isset($_REQUEST['email'])){
						$email = $_REQUEST['email'];
						echo $USERS->emailExists($email)?'false':'true';
						return;
					}
				}
			}
		}else if ($_REQUEST['type']=='cart'){//	SHOPING CART
			$CART = new Cart;
			
			if (isset($_REQUEST['action'])){
				if ($_REQUEST['action']=='ADD')			$CART->addToCart($_REQUEST['bookid']);
				else if ($_REQUEST['action']=='REMOVE')	$CART->removeFromCart($_REQUEST['bookid']);
				else if ($_REQUEST['action']=='EMPTY')	$CART->emptyCart();
				else if ($_REQUEST['action']=='PRINT')	$CART->printCart();//XXX Usar solo para debug
			}
				
			$CART->saveCart();
		}else if ($_REQUEST['type']=='author'){//	AUTHOR
			$id = isset($_REQUEST['auth_id'])? $_REQUEST['auth_id']:'';
			$nombre = isset($_REQUEST['auth_nombre'])? $_REQUEST['auth_nombre']:'';
			$apellido = isset($_REQUEST['auth_apellido'])? $_REQUEST['auth_apellido']:'';
			$fecha_n = isset($_REQUEST['auth_fecha_n'])? $_REQUEST['auth_fecha_n']:'';
			$lugar_n = isset($_REQUEST['auth_lugar_n'])? $_REQUEST['auth_lugar_n']:'';
			
			if (isset($_REQUEST['action'])){
				if ($_REQUEST['action']=='NEW'){//	agregar nuevo autor
					$autor = Authors::newAuthor($nombre, $apellido, $fecha_n, $lugar_n);
					echo $autor? $autor->getID():'false';
				}else if ($_REQUEST['action']=='UPDATE'){//	actualizar autor
					$autor = Authors::getAuthor($id);
					if ($autor){
						//Modifico el autor y lo guardo
						$autor->setNombre($nombre);
						$autor->setApellido($apellido);
						$autor->setFechaNacimiento($fecha_n);
						$autor->setLugarNacimiento($lugar_n);
						echo $autor->save()? 'true':'false';
					}else{
						echo 'false';
					}
				}else if ($_REQUEST['action']=='REMOVE'){// eliminar autor
					$autor = Authors::getAuthor($id);
					if ($autor){
						if (count($autor->getBooks())==0){
							$autor->setEliminado(1);
							if ($autor->save()){
								echo 'true';
							}else{
								echo 'false';
							}
						}else{
							echo 'false';
						}
					}else{
						echo 'false';
					}
				}
			}
			
		}
	}
?>