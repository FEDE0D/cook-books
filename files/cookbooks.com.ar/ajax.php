<?php

	/**
	 * REQUESTS:
	 * 	type:	USER
	 * 				action:	ENABLE
	 * 						DISABLE
	 * 						EXISTS
	 * 						EMAL_AVAILABLE
	 * 			AUTHOR
	 * 				action:	CREATE
	 * 						UPDATE
	 * 						REMOVE
	 * 			BOOK
	 * 				action: CREATE
	 * 						UPDATE
	 * 						HIDE
	 * 						REMOVE
	 * 						RECREATE
	 * 			CART
	 * 				action:	ADD
	 * 						REMOVE
	 * 						CLEAR
	 * 						PRINT_OUT
	 * 						PURCHASE
	 * 			ORDER
	 * 				action: CONFIRM
	 * 						CANCEL
	 */

	include_once('database.php');
	
	$request = NULL;
	if (isset($_REQUEST['type'])&&isset($_REQUEST['data'])){
		if ($_REQUEST['type']=="USER")			$request = new UserRequest($_REQUEST['data']);
		else if($_REQUEST['type']=="AUTHOR")	$request = new AuthorRequest($_REQUEST['data']);
		else if($_REQUEST['type']=="BOOK")		$request = new BookRequest($_REQUEST['data']);
		else if($_REQUEST['type']=="CART")		$request = new CartRequest($_REQUEST['data']);
		else if($_REQUEST['type']=="ORDER")		$request = new OrderRequest($_REQUEST['data']);
	}
	

	
class Request{
	
	protected $action;
	protected $response;
	
	public function __construct($json_data = false){
		if ($json_data){
			$this->response = new JSONResponse;
			$this->set(json_decode($json_data, TRUE));
			$this->correctData();
			$this->perform();
			$this->showResponse();
		}
	}
	
	/** Array to variables */
	public function set($data){
		foreach ($data as $key => $value) {
            $this->{$key} = $value;
        }
	}
	
	/** Se corrigen los datos de entrada */
	public function correctData(){}
	
	/** Lleva a cabo la accion requerida */
	public function perform(){
		$function = $this->action;//llamo a la function que se llama como el parametro 'action'
		$this->$function();
	}
	
	public function showResponse(){
		echo $this->response->getJSON();
	}
}

class UserRequest extends Request{
	
	protected	$username,//nunca se usa para modificar datos de usuario
				$nombre,
				$apellido,
				$direccion,
				$email,
				$telefono,
				$fecha_alta,//nunca se usa para modificar datos de usuario
				$fecha_nac;
	
	public function __construct($data){
		parent::__construct($data);
	}

	public function ENABLE(){
		$user = Users::getUser($this->username);
		if ($user){
			$user->setEnabled(1);
			if ($user->save()){
				$this->response->setProperty("ok", TRUE);
			}else{
				$this->response->setProperty("ok", FALSE);
			$this->response->setProperty("message", "No se pudo actualizar la informacion del usuario");
			}
		}else{
			$this->response->setProperty("ok", FALSE);
			$this->response->setProperty("message", "El usuario $this->username no existe");
		}
	}
	
	public function DISABLE(){
		$user = Users::getUser($this->username);
		$compras = $user->getCompras();
		foreach ($compras as $key => $compra) {
			if ($compra->getEstado()=='pendiente'){
				$this->response->setProperty("ok", FALSE);
				$this->response->setProperty("message", "No se pudo deshabilitar al usuario porque tiene pedidos pendientes.");
				return;
			}
		}
		if ($user){
			$user->setEnabled(0);
			if ($user->save()){
				$this->response->setProperty("ok", TRUE);
			}else{
				$this->response->setProperty("ok", FALSE);
				$this->response->setProperty("message", "No se pudo actualizar la informacion del usuario");
			}
		}else{
			$this->response->setProperty("ok", FALSE);
			$this->response->setProperty("message", "El usuario $this->username no existe");
		}
	}

	function NOT_EXISTS(){
		$result = Users::userExists($this->username);
		if($result){
			$this->response->setJSON("false");//JQuery Validator solo admite true/false
		}else{
			$this->response->setJSON("true");
		}
	}

	function EXISTS(){
		$result = Users::userExists($this->username);
		if($result){
			$this->response->setJSON("true");
		}else{
			$this->response->setJSON("false");
		}
	}
	
	function EMAIL_AVAILABLE(){
		$result = Users::emailExists($this->email);
		if ($result){
			$this->response->setJSON("false");
		}else{
			$this->response->setJSON("true");
		}
	}
	
	function UPDATE(){
		$result = Users::getUser($this->username);
		if ($result){
			$result->setNombre($this->nombre);
			$result->setApellido($this->apellido);
			$result->setDireccion($this->direccion);
			$result->setEmail($this->email);
			$result->setTelefono($this->telefono);
			$result->setFechaNacimiento($this->fecha_nac);
			if ($result->save()){
				$this->response->setProperty("ok", TRUE);
			}else{
				$this->response->setProperty("ok", FALSE);
				$this->response->setProperty("message", "Error al actualizar los datos del usuario.");
			}
		}else{
			$this->response->setProperty("ok", FALSE);
			$this->response->setProperty("message", "No se encuentra al usuario en la base de datos!");
		}
	}
			
}

class AuthorRequest extends Request{
	
	protected	$id,
				$nombre,
				$apellido,
				$fecha_n,
				$lugar_n;
	public function __construct($data){
		parent::__construct($data);
	}
	
	function correctData(){
		if ($this->fecha_n=="") $this->fecha_n = "0000-00-00"; 
	}
	
	function CREATE(){
		$autores = Authors::getAuthorsByName($this->nombre, $this->apellido);
		if (empty($autores)){//si no hay autores con el mismo nombre
			$autor = Authors::newAuthor($this->nombre, $this->apellido, $this->fecha_n, $this->lugar_n);
			if (!$autor){
				$this->response->setProperty("ok", FALSE);
				$this->response->setProperty("message", "No se pudo agregar al autor!");
			}else{
				$this->response->setProperty("ok", TRUE);
				$this->response->setProperty("id_new", $autor->getID());
			}
		}else{
			$this->response->setProperty("ok", FALSE);
			$this->response->setProperty("message", "Ya existe otro autor con el mismo nombre!");
		}
	}
	
	function UPDATE(){
		$autor = Authors::getAuthor($this->id);
		if ($autor){
			//Modifico el autor y lo guardo
			$autor->setNombre($this->nombre);
			$autor->setApellido($this->apellido);
			$autor->setFechaNacimiento($this->fecha_n);
			$autor->setLugarNacimiento($this->lugar_n);
			if ($autor->save()){
				$this->response->setProperty("ok", TRUE);
			}else{
				$this->response->setProperty("ok", FALSE);
				$this->response->setProperty("message", "Error al actualizar los datos del autor.");
			}
		}else{
			$this->response->setProperty("ok", FALSE);
			$this->response->setProperty("message", "El ID del autor es incorrecto.");
		}
	}
	
	function REMOVE(){
		$autor = Authors::getAuthor($this->id);
		if ($autor){
			if (count($autor->getBooks())==0){
				$autor->setEliminado(1);
				if ($autor->save()){
					$this->response->setProperty("ok", TRUE);
				}else{
					$this->response->setProperty("ok", FALSE);
					$this->response->setProperty("message", "Error al guardar al eliminar autor.");
				}
			}else{
				$this->response->setProperty("ok", FALSE);
				$this->response->setProperty("message", "El autor tiene libros asociados!");
			}
		}else{
			$this->response->setProperty("ok", FALSE);
			$this->response->setProperty("message", "El ID del autor es incorrecto.");
		}
	}
}

class BookRequest extends Request{
	
	protected	$old_ISBN,
				$ISBN,
				$titulo,
				$idioma,
				$precio,
				$texto,
				$tags,
				$fecha,
				$autores,
				$paginas,
				$tapa;
	
	public function __construct($data){
		parent::__construct($data);
	}
	
	function correctData(){
		$this->ISBN = substr($this->ISBN, 0, 10);
		if ($this->fecha=="") $this->fecha ="0000-00-00";
		if (is_array($this->tapa)) $this->tapa=$this->tapa[0];//FIXME
	}
	
	function CREATE(){
		$libro = Books::getBook($this->ISBN);
		if ($libro){
			$this->response->setProperty("ok", FALSE);
			$this->response->setProperty("message", "Ya existe un libro con el mismo ISBN!");
			if ($libro->getEliminado()) $this->response->setProperty("recreate", TRUE);
		}else{
			$libro = Books::newBook($this->ISBN, $this->titulo, $this->idioma, $this->fecha, $this->tags, $this->precio, $this->texto, $this->autores, $this->paginas, $this->tapa);
			if ($libro){
				if (is_object($libro)){
					$this->response->setProperty("ok", TRUE);
					$this->response->setProperty("id_new", $libro->getISBN());
				}else{
					$this->response->setProperty("ok", FALSE);
					$this->response->setProperty("message", "Error al agregar nuevo libro.");
				}
			}else{
				$this->response->setProperty("ok", FALSE);
				$this->response->setProperty("message", "Error al agregar nuevo libro.");
			}
		}
	}
	
	function RECREATE(){
		$libro = Books::getBook($this->ISBN);
		if ($libro){
			$libro->setEliminado(0);//ya no esta eliminado
			if ($libro->save()){
				$this->response->setProperty("ok", TRUE);
			}else{
				$this->response->setProperty("ok", FALSE);
				$this->response->setProperty("message", "Error al actualizar información.");
			}
		}else{
			$this->response->setProperty("ok", FALSE);
			$this->response->setProperty("message", "Error al encontrar libro con ISBN $this->ISBN.");
		}
	}
	
	function UPDATE(){
		$libro = Books::getBook($this->ISBN);
		if ($this->old_ISBN==$this->ISBN){
			if ($libro){
				$libro->setTitulo($this->titulo);
				$libro->setAutores($this->autores);
				$libro->setEtiquetas($this->tags);
				$libro->setIdioma($this->idioma);
				$libro->setPrecio($this->precio);
				$libro->setPaginas($this->paginas);
				$libro->setTapa($this->tapa);
				$libro->setTexto($this->texto);
				$libro->setFecha($this->fecha);
				
				if ($libro->save()){
					$this->response->setProperty("ok", TRUE);
				}else{
					$this->response->setProperty("ok", FALSE);
					$this->response->setProperty("message", "Error al actualizar el libro.");
				}
			}else{
				$this->response->setProperty("ok", FALSE);
				$this->response->setProperty("message", "Error no existe el libro con ISBN $this->ISBN.");
			}
		}else{
			if (!$libro){//Si no existe un libro con el ISBN nuevo, puedo modificar el libro y cambiar el ISBN por el nuevo
				$libro = Books::getBook($this->old_ISBN);
				if ($libro){
					$libro->setISBN($this->ISBN);
					$libro->setTitulo($this->titulo);
					$libro->setAutores($this->autores);
					$libro->setEtiquetas($this->tags);
					$libro->setIdioma($this->idioma);
					$libro->setPrecio($this->precio);
					$libro->setPaginas($this->paginas);
					$libro->setTapa($this->tapa);
					$libro->setTexto($this->texto);
					$libro->setFecha($this->fecha);
					
					if ($libro->save()){
						$this->response->setProperty("ok", TRUE);
						$this->response->setProperty("id_new", $this->ISBN);
					}else{
						$this->response->setProperty("ok", FALSE);
						$this->response->setProperty("message", "Error al actualizar la información del libro.");
					}
				}else{
					$this->response->setProperty("ok", FALSE);
					$this->response->setProperty("message", "Error al modificar los datos del libro.");
				}
			}else{
				$this->response->setProperty("ok", FALSE);
				$this->response->setProperty("message", "Error: ya existe un libro con el mismo ISBN $this->ISBN.");
			}
		}
	}

	function HIDE(){
		$libro = Books::getBook($this->ISBN);
        if ($libro){
            $libro->setOculto(1);
            if ($libro->save()){
                $this->response->setProperty("ok", TRUE);
            }else{
                $this->response->setProperty("ok", FALSE);
				$this->response->setProperty("message", "Error al actalizar datos del libro.");
            }
        }else{
            $this->response->setProperty("ok", FALSE);
			$this->response->setProperty("message", "No existe el libro que se intenta modificar.");
        }
	}
	
	function SHOW(){
		$libro = Books::getBook($this->ISBN);
        if ($libro){
            $libro->setOculto(0);
            if ($libro->save()){
                $this->response->setProperty("ok", TRUE);
            }else{
                $this->response->setProperty("ok", FALSE);
				$this->response->setProperty("message", "Error al actalizar datos del libro.");
            }
        }else{
            $this->response->setProperty("ok", FALSE);
			$this->response->setProperty("message", "No existe el libro que se intenta modificar.");
        }
	}

	function REMOVE(){
		$libro = Books::getBook($this->ISBN);
        if ($libro){
            $libro->setEliminado(1);
            if ($libro->save()){
                $this->response->setProperty("ok", TRUE);
            }else{
                $this->response->setProperty("ok", FALSE);
				$this->response->setProperty("message", "Error al actalizar datos del libro.");
            }
        }else{
            $this->response->setProperty("ok", FALSE);
			$this->response->setProperty("message", "No existe el libro que se intenta modificar.");
        }
	}
	
}

class CartRequest extends Request{
	
	protected	$bookid;
	
	public function __construct($data){
		parent::__construct($data);
		Cart::saveCart();
	}
	
	function ADD(){
		Cart::addToCart($this->bookid);
	}
	
	function REMOVE(){
		echo $this->bookid;
		Cart::removeFromCart($this->bookid);
	}
	
	function CLEAR(){
		Cart::emptyCart();
	}
	
	function PRINT_OUT(){
		Cart::printCart();
	}
	
	function PURCHASE(){
		$user = Users::getUserLogin();
		if (!$user){
			$this->response->setProperty("ok", FALSE);
			$this->response->setProperty("message", "Error al crear compra, el usuario no existe!");
			return;
		}
		$pedidos = Cart::getArticulos();
		$compra = Compras::createCompra($pedidos);
		if($compra){
			Cart::emptyCart();
			$this->response->setProperty("ok", TRUE);
			$this->response->setProperty("id_new", $compra->getId());
		}else{
			$this->response->setProperty("ok", FALSE);
			$this->response->setProperty("message", "Error al guardar la información de la compra");
		}
	}
}

class OrderRequest extends Request{
	
	protected $idCompra;
	
	public function __construct($data){
		parent::__construct($data);
	}
	
	public function CONFIRM(){
		$compra = Compras::getCompra($this->idCompra);
		if ($compra){
			$compra->setEstado('efectuado');
			if ($compra->save()){
				$this->response->setProperty("ok", TRUE);
				$this->response->setProperty("id", $compra->getId());
				
			}else{
				$this->response->setProperty("ok", FALSE);
				$this->response->setProperty("message", "No se pudo actualizar la información de la compra");
			}
		}else{
			$this->response->setProperty("ok", FALSE);
			$this->response->setProperty("message", "No existe ninguna compra con id $this->idCompra");
		}
	}
	
	public function CANCEL(){
		$compra = Compras::getCompra($this->idCompra);
		if ($compra){
			$compra->setEstado('cancelado');
			if ($compra->save()){
				$this->response->setProperty("ok", TRUE);
				$this->response->setProperty("id", $compra->getId());
			}else{
				$this->response->setProperty("ok", FALSE);
				$this->response->setProperty("message", "No se pudo actualizar la información de la compra");
			}
		}else{
			$this->response->setProperty("ok", FALSE);
			$this->response->setProperty("message", "No existe ninguna compra con id $this->idCompra");
		}
	}
	
}

class JSONResponse{
	
	private $data;//arreglo de valores
	
	public function __construct(){
		$this->data = Array(
			"ok" => FALSE,
			"message" => ""
		);
	}
	
	function setProperty($name,$value){
		$this->data[$name]=$value;
	}
	
	function getJSON(){
		return json_encode($this->data);
	}
	
	function setJSON($json_data){
		$this->data = json_decode($json_data,TRUE);
	}
	
}
	
?>