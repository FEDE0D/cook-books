<?php

/**
 * Clase Conexion (usada por otras clases para comunicarse con la base de datos)
 */
class Conexion {
	
	private $local = true;//Indica si estoy trabajando con un servidor local.
	private $hostname = 'localhost';
	private $port = '8080';
	private $username = 'root';
	private $password = 'F3Dericcio';
	private $dbname = 'CookBooks';
	
	private $dbh;
	

	function __construct(){
		if (!$this->local){
			$this->hostname = 'db4free.net';
			$this->port = '3306';
			$this->username = 'federico';
			$this->password = 'federico';
			$this->dbname = 'cookbookg32';
		}
	}
	
	/** Conecta a la base de datos */
	function conectar(){
		try {
			$this->dbh = new PDO("mysql:host=$this->hostname;dbname=$this->dbname;port=$this->port", $this->username, $this->password, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
			
			//echo "Conectado a la bbdd";
			return true;
		} catch(PDOException $e) {
			//echo $e -> getMessage();
			return false;
		}
	}
	
	/** Desconecta de la base de datos */
	function desconectar(){
		$this->dbh = null;//close database connection
		// echo "Desconectado de la bbdd";
	}
	
	/** Realiza una consulta a la base de datos, retorna el output de la consulta*/
	function query($statement){
		return $this->dbh->query($statement);
	}
	
	/** Retorna un Array con la información asociada a la última operación realizada */
	function getLastError(){
		return $this->dbh->errorInfo();
	}
	
	/*
	 * CONSULTAS SELECT
	 */
	
	/** Convierte una consulta SELECT a una tabla */
	function resultToTable($queryResult, $tableHTMLAttributes = ""){
		$string = "";
		$result = $queryResult->fetchAll(PDO::FETCH_NAMED);
				
		$string .= "<table $tableHTMLAttributes>";
		$string .= "<thead>";
			$string .= "<tr>";
				foreach ($result as $row => $column) {
					foreach ($column as $columnName => $columnValue) {
						$string .= "<th>$columnName</th>";
					}
					break;
				}
			$string .= "</tr>";
		$string .= "</thead>";
		
		$string .= "<tbody>";
		foreach ($result as $row => $columns) {
			$string .= "<tr>";
			foreach ($columns as $columnName => $columnValue) {
				$string .= "<td>".$columnValue."</td>";
			}
			$string .= "</tr>";
		}
		$string .= "</tbody>";
		$string .= "</table>";
		
		return $string;
	}
	
	/** Convierte la consulta SELECT $statement a una tabla */
	public function queryToTable($statement){
		return $this->resultToTable($this->query($statement), "");
	}
	
	/** Retorna true si la query devuelve una o más filas, false en caso contrario */
	function queryExist($statement){
		$result = $this->query($statement);
		if ($result){
			return ($result->rowCount() > 0);
		}else{
			return false;
		}
	}
	
	/*
	 * CONSULTAS INSERT
	 */
	 
	/** Query insert */
	function insert($statement){
		return $this->query($statement);
	}
	 
		 
	/*
	 * CONSULTAS UPDATE
	 */
	
	
}

class Users{
	
	private $conexion;
	
	/** Constructor */
	function __construct(){
		$this->conexion = new Conexion;
		if (!$this->conexion->conectar())	die ('<p>Error al crear objeto conexion</p>');
	}
	
	/** Retorna la tabla de los usuarios completa */
	function tableUsers(){
		return $this->conexion->queryToTable("SELECT * FROM usuarios");
	}
	
	/** Retorna la tabla de los usuarios ordenada por fecha de ingreso */
	function tableUsersBySignUpDate(){
		return $this->conexion->queryToTable("SELECT * FROM usuarios ORDER BY fecha_alta");
	}
	
	/** Checkea si existe un usuario con estas credenciales en la base de datos */
	function userExists($username){
		return $this->conexion->queryExist("SELECT * FROM usuarios WHERE username='$username' ");
	}
	
	/** Checkea si existe un usuario con estas credenciales en la base de datos. Retorna un objeto Usuario */
	function userExists2($username, $password){
		//return $this->conexion->queryExist("SELECT * FROM usuarios WHERE username='$username' AND password='$password' ");
		$info = $this->conexion->query("SELECT * FROM usuarios WHERE username='$username' AND password='$password'");
		if ($info->rowCount() == 1){
			$array = $info->fetch(PDO::FETCH_ASSOC);
			return new User($array);
		}else return NULL;
	}
	
	/** Retorna TRUE si el email existe en la base de datos, FALSE en caso contrario*/
	function emailExists($email){
		return $this->conexion->queryExist("SELECT * FROM usuarios WHERE mail='$email' ");
	}
	
	/** Agrega un nuevo usuario a la base de datos. 
	 * Devuelve un objeto User o NULL si no pudo agregarlo 
	 * Notar que esta funcion solo crea usuarios, NO crea admins*/
	function userCreate($username, $password, $email){
		$today = date("Y-m-d");
		$query = "
			INSERT INTO  `usuarios` (
			`username` ,
			`password` ,
			`nombre` ,
			`apellido` ,
			`direccion` ,
			`mail` ,
			`telefono` ,
			`admin` ,
			`fecha_alta` ,
			`fecha_nac`
			)
			VALUES (
			'$username',  '$password',  '', '' , '' ,  '$email', '' ,  '0',  '$today', ''
			);
		";
		$result = $this->conexion->insert($query);
		if ($result->rowCount()==1){
			//TODO: retornar nuevo usuario sacando info de $result en lugar de pedir nuevos datos
			return $this->userExists2($username, $password);
		}else return NULL;
	}
	
	/**Retorna un objeto Usuario si existe un usuario logueado, NULL en caso contrario*/
	function getUserLogin(){
		if (session_status() == PHP_SESSION_NONE) session_start();
		if (isset($_SESSION['username'])){
			$username = $_SESSION['username'];
			$info = $this->conexion->query("SELECT * FROM usuarios WHERE username='$username' ");
			if ($info->rowCount() == 1){
				$array = $info->fetch(PDO::FETCH_ASSOC);
				return new User($array);
			}else return NULL;
		}else{
			return NULL;
		}
	}
	
	/** Guarda la información de la sesión del usuario*/
	function saveLogin(User $user){
		if (session_status() == PHP_SESSION_NONE) session_start();
        $_SESSION['username'] = $user->getUsername();
	}
	
	/** Elimina la información de la sesión del usuario */
	function removeLogin(){
		if (session_status() == PHP_SESSION_NONE) session_start();
		session_destroy();
	}
	
}

class User{
	
	private	$username,
			$password,
			$nombre,
			$apellido,
			$direccion,
			$email,
			$telefono,
			$fecha_nacimiento,
			$fecha_alta,
			$administrator;
			
	function __construct($params){
		$this->username = $params['username'];
		$this->password = $params['password'];
		$this->nombre = $params['nombre'];
		$this->apellido = $params['apellido'];
		$this->direccion = $params['direccion'];
		$this->email = $params['mail'];
		$this->telefono = $params['telefono'];
		$this->fecha_nacimiento = $params['fecha_nac'];
		$this->fecha_alta = $params['fecha_alta'];
		$this->administrator = $params['admin'];
	}
	
	/** Solo para DEBUG */
	function printUser(){
		echo($this->username); echo "<br>";
		echo($this->password); echo "<br>";
		echo($this->nombre); echo "<br>";
		echo($this->apellido); echo "<br>";
		echo($this->direccion); echo "<br>";
		echo($this->email); echo "<br>";
		echo($this->telefono); echo "<br>";
		echo($this->fecha_nacimiento); echo "<br>";
		echo($this->fecha_alta); echo "<br>";
		echo($this->administrator); echo "<br>";
	}
	
	function getUsername(){
		return $this->username;
	}
	
	function getPassword(){
		return $this->password;
	}
	
	function getNombre(){
		return $this->nombre;
	}
	
	function getApellido(){
		return $this->apellido;
	}
	
	function getDireccion(){
		return $this->direccion;
	}
	
	function getEmail(){
		return $this->email;
	}
	
	function getTelefono(){
		return $this->telefono;
	}
	
	function getFechaNacimiento(){
		return $this->fecha_nacimiento;
	}
	
	function getFechaAlta(){
		return $this->fecha_alta;
	}
	
	function getIsAdministrator(){
		return $this->administrator;
	}
	
}

class Books{
	
	private $conexion;
	
	/** Constructor */
	function __construct(){
		$this->conexion = new Conexion;
		if (!$this->conexion->conectar())	die ('<p>Error al crear objeto conexion</p><p>Ver variable $local</p>');
	}
	
	/**Devuelve un objeto Book o NULL si no existe*/
	function getBook($bookISBN){
		$result = $this->conexion->query("SELECT * FROM libros WHERE ISBN='$bookISBN' ");
		if ($result){
			$bookInfo = $result->fetchAll(PDO::FETCH_ASSOC);
			return new Book($bookInfo[0]);
		}
		return NULL;
	}
	
	/** Devuelve un Array de objetos Book */
	function getBestSellers($cantidad){
		$result = $this->conexion->query(" 
			SELECT L.ISBN, L.titulo, concat(A.nombre,' ',A.apellido) as autor, L.paginas, L.precio, I.nombre as idioma, L.fecha, L.etiquetas, L.texto, L.tapa
			FROM libros L
			LEFT JOIN autor A ON (L.AUTOR=A.ID)
			LEFT JOIN idioma I ON (L.IDIOMA=I.ID)
			LIMIT $cantidad
		");
		$resultado = Array();
		if ($result){
			$books = $result->fetchAll(PDO::FETCH_ASSOC);
			foreach ($books as $num => $bookInfo) {
				array_push($resultado, new Book($bookInfo));
			}
		}
		return (Array)$resultado;
	}
	
	/** Devuelve la tabla de libros lista para mostrar en la pagina del catalogo (books.php) */
	function getCatalogo(){
		$result = $this->conexion->query("
			SELECT L.ISBN, L.titulo, concat(A.nombre,' ',A.apellido) as autor, L.paginas, L.precio, I.nombre as idioma, L.fecha, L.etiquetas
			FROM libros L
			LEFT JOIN autor A ON (L.AUTOR=A.ID)
			LEFT JOIN idioma I ON (L.IDIOMA=I.ID)
			ORDER BY ISBN
		");
		return $this->conexion->resultToTable($result, 'id="bookstable" style="color: #000000"');
	}
}

class Book{
	
	private	$ISBN,
			$titulo,
			$autor,
			$paginas,
			$precio,
			$idioma,
			$fecha,
			$etiquetas,
			$texto,
			$tapa;
			
	function __construct($param){
		$this->ISBN = $param['ISBN'];
		$this->titulo = $param['titulo'];
		$this->autor = $param['autor'];
		$this->paginas = $param['paginas'];
		$this->precio = $param['precio'];
		$this->idioma = $param['idioma'];
		$this->fecha = $param['fecha'];
		$this->etiquetas = $param['etiquetas'];
		$this->texto = $param['texto'];
		$this->tapa = $param['tapa'];
	}
	
	/**Solo DEBUG*/
	function printBook(){
		echo $this->ISBN."<br>";
		echo $this->titulo."<br>";
		echo $this->autor."<br>";
		echo $this->paginas."<br>";
		echo $this->precio."<br>";
		echo $this->idioma."<br>";
		echo $this->fecha."<br>";
		echo $this->etiquetas."<br>";
		echo $this->texto."<br>";
		echo $this->tapa."<br>";
	}
	
	function getISBN(){
		return $this->ISBN;
	}
	
	function getTitulo(){
		return $this->titulo;
	}
	
	function getAutor(){
		return $this->autor;
	}
	
	function getPaginas(){
		return $this->paginas;
	}
	
	function getPrecio(){
		return $this->precio;
	}
	
	function getIdioma(){
		return $this->idioma;
	}
	
	function getFecha(){
		return $this->fecha;
	}
	
	function getEtiquetas(){
		return $this->etiquetas;
	}
	
	function getTexto(){
		return $this->texto;
	}
	
	function getTapa(){
		return $this->tapa;
	}

	
}

class Authors{
	
	static private $initialized = FALSE;
	static private $conexion;
	
	
	/** Constructor */
	private function __construct(){}
	
	private static function initialize(){
		if (self::$initialized) return;
		self::$conexion = new Conexion;
		if (!self::$conexion->conectar())	Errors::error("No se puede conectar a la base de datos", "Error al conectar a la base de datos!");
		self::$initialized = TRUE;
	}
	
	/** Retorna un array de Author con todos los autores no eliminados del sistema */
	public static function getAuthors(){
		self::initialize();
		$result =  self::$conexion->query("
			SELECT *
			FROM autor
			WHERE (eliminado=0)
			ORDER BY apellido
		");
		$authors = array();
		if ($result){
			$authorsData = $result->fetchAll(PDO::FETCH_ASSOC);
			foreach ($authorsData as $key => $info) {
				array_push($authors, new Author($info));
			}
		}
		return $authors;
	}
	
	/** Retorna un objeto Author si existe un autor en la base de dato, NULL caso contrario*/
	public static function getAuthor($ID){
		self::initialize();
		$result = self::$conexion->query("
			SELECT *
			FROM autor
			WHERE (ID=$ID)AND(eliminado=0)
			LIMIT 1
		");
		if ($result && $result->rowCount()>0) return new Author($result->fetch(PDO::FETCH_ASSOC));
		else return NULL;
	}
	
	/** Modifica los datos de un autor pasado por parametro en la base de datos. Retorna true si pudo modificar, false en caso contrario*/
	public static function updateAuthor(Author $author){
		self::initialize();
		
		$id = $author->getID();
		$nombre = $author->getNombre();
		$apellido = $author->getApellido();
		$fecha_n = $author->getFechaNacimiento();
		$lugar_n = $author->getLugarNacimiento();
		$eliminado = $author->getEliminado();
		
		$result = self::$conexion->query("
			UPDATE  autor
			SET nombre = '$nombre',
			apellido = '$apellido',
			fecha_nacimiento = '$fecha_n',
			lugar_nacimiento = '$lugar_n',
			eliminado = '$eliminado'
			WHERE (ID = $id)
		");
		if ($result){
			// if ($result->rowCount()==1){
				// return TRUE;
			// }else{
				// print_r($result->rowCount());
				// return FALSE;
			// }
			return TRUE;
		}
		return FALSE;
	}
	
}

class Author{
	
	private	$ID,
			$nombre,
			$apellido,
			$fecha_nacimiento,
			$lugar_nacimiento,
			$eliminado;
			
	function __construct($params){
		$this->ID = $params['ID'];
		$this->nombre = $params['nombre'];
		$this->apellido = $params['apellido'];
		$this->fecha_nacimiento = $params['fecha_nacimiento'];
		$this->lugar_nacimiento = $params['lugar_nacimiento'];
		$this->eliminado = $params['eliminado'];
	}
	
	/** Guarda este autor en la base de datos (sobreescribiendo los datos viejos). Retorna true si la modificación tuvo éxito, false caso contrario. */
	function save(){
		return Authors::updateAuthor($this);
	}
	
	function getID(){
		return $this->ID;
	}
	
	function getNombre(){
		return $this->nombre;
	}
	
	function getApellido(){
		return $this->apellido;
	}
	
	function getFechaNacimiento(){
		return $this->fecha_nacimiento;
	}
	
	function getLugarNacimiento(){
		return $this->lugar_nacimiento;
	}
	
	function getEliminado(){
		return $this->eliminado;
	}
	
	function getNombreApellido(){
		return $this->nombre.' '.$this->apellido;
	}
	
	function getApellidoNombre(){
		return $this->apellido.' '.$this->nombre;
	}
	
	function setNombre($nombre){
		$this->nombre=$nombre;
	}
	
	function setApellido($apellido){
		$this->apellido=$apellido;
	}

	function setFechaNacimiento($fechaNacimiento){
		$this->fecha_nacimiento=$fechaNacimiento;
	}
	
	function setLugarNacimiento($lugarNacimiento){
		$this->lugar_nacimiento=$lugarNacimiento;
	}
	
	function setEliminado($num){
		$this->eliminado = $num;
	}
	
}

class Cart{
	
	private $articulos; //variable de sesión donde se guarda la información del carrito
	
	function __construct(){
		if (session_status() == PHP_SESSION_NONE) session_start();
		
		if (!isset($_SESSION['articulos']))	$_SESSION['articulos'] = Array();
		$this->articulos = $_SESSION['articulos'];
	}
	
	/** Agrega un libro al carrito. Si ya está en el carrito aumenta su cantidad */
	function addToCart($bookId){
		if (array_key_exists($bookId, $this->articulos)){
			$this->articulos[$bookId]['cantidad']++;
		}else{
			$this->articulos[$bookId] = array();
			$this->articulos[$bookId]['cantidad'] = 1;
			
			//por motivos de optimización además guardo datos sobre el libro
			include_once 'database.php';
			$BOOKS = new Books;
			$book = $BOOKS->getBook($bookId);
			
			$arrayBook = array(
				"ISBN" => $book->getISBN(),
				"titulo" => $book->getTitulo(),
				"autor" => $book->getAutor(),
				"paginas" => $book->getPaginas(),
				"precio" => $book->getPrecio(),
				"idioma" => $book->getIdioma(),
				"fecha" => $book->getFecha(),
				"etiquetas" => $book->getEtiquetas(),
				"texto" => $book->getTexto(),
				"tapa" => $book->getTapa()
			);
			
			$this->articulos[$bookId] = array_merge($this->articulos[$bookId], $arrayBook);
		}
	}
	
	/** Elimina un libro del carrito */
	function removeFromCart($bookId){
		if (array_key_exists($bookId, $this->articulos)){
			if ($this->articulos[$bookId]['cantidad']>1) $this->articulos[$bookId]['cantidad']--;
			else unset($this->articulos[$bookId]);
		}
	}
	
	/** Vacía el carrito */
	function emptyCart(){
		$this->articulos = Array();
	}
	
	/** Guarda la información del carrito en la sesion */
	function saveCart(){
		$_SESSION['articulos'] = $this->articulos;
	}
	
	/** Retorna la cantidad de libros en el carrito */
	function sizeCart(){
		//return array_sum($this->articulos);
		$sum = 0;
		foreach ($this->articulos as $bookid => $book) {
			$cantidad = $book['cantidad'];
			$sum += $cantidad;
		}
		
		return $sum;
	}
	
	/** Retorna el precio total del carrito */
	function priceCart(){
		$sum = 0;
		foreach ($this->articulos as $bookid => $book) {
			$sum += $book['precio']*$book['cantidad'];
		}
		return $sum;
	}
	
	/** Crea la vista para el dropmenu del carrito*/
	function printCartHTML(){
		?>
		<div class="" style="width: 500px; padding: 10px">
		<?php
		foreach ($this->articulos as $bookid => $book) {
			$cantidad = $book['cantidad'];
			?>
			<li >
        		<div >
	                <a href='product.php?id=<?php echo $bookid ?>' style='text-decoration: none'>
	                	<span class='badge pull-left'><?php echo $cantidad ?></span>&nbsp;<?php echo $book['titulo'] ?>
	                </a>&nbsp;
	                <button onclick="
	                	<?php
	                		//Javascript activado con el click, pide que se remueva el libro, al finalizar recarga el navbar.
	                		echo (
	                			"
	                			var btn = $('#cartButton');
	                			btn.button('loading');
	                			$.post('ajax.php', {type:'cart',action:'REMOVE', bookid:'".$bookid."'}).done(
									function(data){
										$.post('navigation.php').done(
											function(navbar){
												$('#navigationWrapper').replaceWith(navbar);
											}
										);
									}
								);"
							);
	                	?>
	                " class="pull-right" >
	                	<span class='glyphicon glyphicon-trash'></span>
	                </button>
                </div>
        	</li><br />
        	<?php
		}
		?>
			<p>Total: $<?php echo $this->priceCart() ?>
			<button onclick="
            	<?php
            		echo (
            			"
            			var btn = $('#cartButton');
            			btn.button('loading');
            			$.post('ajax.php', {type:'cart',action:'EMPTY'}).done(
							function(data){
								$.post('navigation.php').done(
									function(navbar){
										$('#navigationWrapper').replaceWith(navbar);
									}
								);
							}
						);"
					);
            	?>
	            " class="pull-right" >
	            	<span class='glyphicon glyphicon-trash'> Vaciar</span>
            </button>
			<button class="pull-right" onclick="document.location='buy.php'">
				<span class='glyphicon glyphicon-shopping-cart'> Comprar</span>
			</button>
            </p>
		</div>
		<?php
	}
	
	/** Imprime el carrito. Solo para debug. */
	function printCart(){
		print_r($this->articulos);
	}
	
}

class Errors{
	
	public static function error($title, $body){
		if (session_status() == PHP_SESSION_NONE) session_start();
		$_SESSION['error_title'] = $title;
		$_SESSION['error_info'] = $body;
		header("Location: error.php");
	}
	
}

?>