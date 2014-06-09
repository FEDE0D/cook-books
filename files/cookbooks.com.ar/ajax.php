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
 * Los resultados son documentos JSON.
 * 	NOMBRE	|| 	VALOR
 * 	result		true|false
 *   
 * 
 */
 
 $response = array(
	"ok" => FALSE,
	"message" => "Sin mensaje" 
 );
 
	if(isset($_REQUEST['type'])){
		include_once 'database.php';
		if ($_REQUEST['type']=='user'){//	USUARIOS
			if (isset($_REQUEST['action'])){
				if ($_REQUEST['action']=='EXISTS'){
					if (isset($_REQUEST['username'])){
						$username = $_REQUEST['username'];
						echo Users::userExists($username)?'true':'false';
						return;
					}
				}else if ($_REQUEST['action']=='NAME_AVAILABLE'){
					if (isset($_REQUEST['username'])){
						$username = $_REQUEST['username'];
						echo Users::userExists($username)?'false':'true';
						return;
					}
				}else if ($_REQUEST['action']=='EMAIL_AVAILABLE'){
					if (isset($_REQUEST['email'])){
						$email = $_REQUEST['email'];
						echo Users::emailExists($email)?'false':'true';
						return;
					}
				}
			}
		}else if ($_REQUEST['type']=='cart'){//	SHOPING CART
			if (isset($_REQUEST['action'])){
				if ($_REQUEST['action']=='ADD')			Cart::addToCart($_REQUEST['bookid']);
				else if ($_REQUEST['action']=='REMOVE')	Cart::removeFromCart($_REQUEST['bookid']);
				else if ($_REQUEST['action']=='EMPTY')	Cart::emptyCart();
				else if ($_REQUEST['action']=='PRINT')	Cart::printCart();//XXX Usar solo para debug
			}	
			Cart::saveCart();
		}else if ($_REQUEST['type']=='author'){//	AUTHOR
			$id = isset($_REQUEST['auth_id'])? $_REQUEST['auth_id']:'';
			$nombre = isset($_REQUEST['auth_nombre'])? $_REQUEST['auth_nombre']:'';
			$apellido = isset($_REQUEST['auth_apellido'])? $_REQUEST['auth_apellido']:'';
			$fecha_n = isset($_REQUEST['auth_fecha_n'])? $_REQUEST['auth_fecha_n']:''; if (empty($fecha_n)) $fecha_n ="0000-00-00";
			$lugar_n = isset($_REQUEST['auth_lugar_n'])? $_REQUEST['auth_lugar_n']:'';
			
			if (isset($_REQUEST['action'])){
				if ($_REQUEST['action']=='NEW'){//	agregar nuevo autor
					$autores = Authors::getAuthorsByName($nombre, $apellido);
					if (empty($autores)){//si no hay autores con el mismo nombre
						$autor = Authors::newAuthor($nombre, $apellido, $fecha_n, $lugar_n);
						if (!$autor){
							$response['ok'] = FALSE;
							$response['message'] = "No se pudo agregar al autor!";
						}else{
							$response['ok'] = TRUE;
							$response['id_new'] = $autor->getID();
						}
					}else{
						$response['ok'] = FALSE;
						$response['message'] = "Ya existe otro autor con el mismo nombre!";
					}
					echo json_encode($response);
				}else if ($_REQUEST['action']=='UPDATE'){//	actualizar autor
					$autor = Authors::getAuthor($id);
					if ($autor){
						//Modifico el autor y lo guardo
						$autor->setNombre($nombre);
						$autor->setApellido($apellido);
						$autor->setFechaNacimiento($fecha_n);
						$autor->setLugarNacimiento($lugar_n);
						if ($autor->save()){
							$response['ok'] = TRUE;
						}else{
							$response['ok'] = FALSE;
							$response['message'] = "Error al actualizar los datos del autor.";
						}
					}else{
						$response['ok'] = FALSE;
						$response['message'] = "El ID del autor es incorrecto.";
					}
					echo json_encode($response);
				}else if ($_REQUEST['action']=='REMOVE'){// eliminar autor
					$autor = Authors::getAuthor($id);
					if ($autor){
						if (count($autor->getBooks())==0){
							$autor->setEliminado(1);
							if ($autor->save()){
								$response['ok'] = TRUE;
							}else{
								$response['ok'] = FALSE;
								$response['message'] = "Error al guardar al eliminar autor.";
							}
						}else{
							$response['ok'] = FALSE;
							$response['message'] = "El autor tiene libros asociados!";
						}
					}else{
						$response['ok'] = FALSE;
						$response['message'] = "El ID del autor es incorrecto.";
					}
					echo json_encode($response);
				}
			}
		}else if ($_REQUEST['type']=='libro'){//	Libro
			
            $old_isbn = isset($_REQUEST['libro_old_ISBN'])? $_REQUEST['libro_old_ISBN']:'';
			$isbn = isset($_REQUEST['libro_ISBN'])? $_REQUEST['libro_ISBN']:''; $isbn = substr($isbn, 0, 10);
			$titulo = isset($_REQUEST['libro_titulo'])? $_REQUEST['libro_titulo']:'';
			$idioma = isset($_REQUEST['libro_idioma'])? $_REQUEST['libro_idioma']:'';
			$precio = isset($_REQUEST['libro_precio'])? $_REQUEST['libro_precio']:'';
			$texto = isset($_REQUEST['libro_texto'])? $_REQUEST['libro_texto']:'';
			$tags = isset($_REQUEST['libro_tags'])? $_REQUEST['libro_tags']:'';
			$fecha = isset($_REQUEST['libro_fecha'])? $_REQUEST['libro_fecha']:''; if (empty($fecha)) $fecha ="0000-00-00";
			$autores = isset($_REQUEST['libro_autor'])? $_REQUEST['libro_autor']:'';
			$paginas = isset($_REQUEST['libro_pag'])? $_REQUEST['libro_pag']:'';
			$tapa = isset($_REQUEST['libro_tapa'])? $_REQUEST['libro_tapa']:''; if (is_array($tapa)) $tapa=$tapa[0];
		
			
			if (isset($_REQUEST['action'])){
				if ($_REQUEST['action']=='NEW'){//	agregar nuevo libro
					$libro = Books::getBook($isbn);
					if ($libro){
						$response['ok'] = FALSE;
						$response['message'] = "Ya existe un libro con el mismo ISBN!";
					}else{
						$libro = Books::newBook($isbn, $titulo, $idioma, $fecha, $tags, $precio, $texto, $autores, $paginas, $tapa);
						if ($libro){
							if (is_object($libro)){
								$response['ok'] = TRUE;
								$response['id_new'] = $libro->getISBN();
							}else{
								$response['ok'] = FALSE;
								$response['message'] = "Error al agregar nuevo libro.";
							}
						}else{
							$response['ok'] = FALSE;
							$response['message'] = "Error al agregar nuevo libro.";
						}
					}
					echo json_encode($response);
				}else if ($_REQUEST['action']=='UPDATE'){//	actualizar libro
					if ($old_isbn==$isbn){
						$libro = Books::getBook($isbn);
						if ($libro){
							$libro->setTitulo($titulo);
							$libro->setAutores($autores);
							$libro->setEtiquetas($tags);
							$libro->setIdioma($idioma);
							$libro->setPrecio($precio);
							$libro->setPaginas($paginas);
							$libro->setTapa($tapa);
							$libro->setTexto($texto);
							$libro->setFecha($fecha);
							
							if ($libro->save()){
								$response['ok'] = TRUE;
							}else{
								$response['ok'] = FALSE;
								$response['message'] = "Error al actualizar el libro.";
							}
						}else{
							$response['ok'] = FALSE;
							$response['message'] = "Error no existe el libro con ISBN $isbn.";
						}
					}else{//Se modifico el ISBN del libro, Hacer una BAJA y una ALTA del libro
						$response['ok'] = FALSE;
						$response['message'] = "Falta implementar!";
					}
					echo json_encode($response);
				}else if ($_REQUEST['action']=='REMOVE'){// eliminar libro
				    $libro = Books::getBook($isbn);
                    if ($libro){
                        $libro->setEliminado(1);
                        if ($libro->save()){
                            $response['ok'] = TRUE;
                        }else{
                            $response['ok'] = FALSE;
							$response['message'] = "Error al actalizar datos del libro.";
                        }
                    }else{
                        $response['ok'] = FALSE;
						$response['message'] = "No existe el libro que se intenta modificar.";
                    }
                    echo json_encode($response);
				}
			}
		}else if ($_REQUEST['type']=="JSON"){//TEST
			$test = array(
				"datos" => "1dok asokdaosd ads",
				"cant" => 1234.56,
				"result" => FALSE
			);
			echo json_encode($test);
		}
	}
?>