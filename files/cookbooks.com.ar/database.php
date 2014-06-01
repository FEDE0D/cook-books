<?php

/**
 * Clase Conexion (usada por otras clases para comunicarse con la base de datos)
 */
class Conexion {
	
	private $local = TRUE;//Indica si estoy trabajando con un servidor local.
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
	
	function getLastInsertedID(){
		return $this->dbh->lastInsertId();
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
	
	static private $conexion;
	static private $initialized = FALSE;
	
	function __construct(){}
	
	static function initialize(){
		if (self::$initialized) return;
		self::$conexion = new Conexion;
		if (!self::$conexion->conectar()) Errors::error("No se pudo conectar a la base de datos", "Error al conectarse a la base de datos. Intente nuevamente.");
		self::$initialized = TRUE;
	}
	
	/** Retorna la tabla de los usuarios completa */
	static function tableUsers(){
		self::initialize();
		return self::$conexion->queryToTable("SELECT * FROM usuarios");
	}
	
	/** Retorna la tabla de los usuarios ordenada por fecha de ingreso */
	static function tableUsersBySignUpDate(){
		self::initialize();
		return self::$conexion->queryToTable("SELECT * FROM usuarios ORDER BY fecha_alta");
	}
	
	/** Checkea si existe un usuario con estas credenciales en la base de datos */
	static function userExists($username){
		self::initialize();
		return self::$conexion->queryExist("SELECT * FROM usuarios WHERE username='$username' ");
	}
	
	/** Checkea si existe un usuario con estas credenciales en la base de datos. Retorna un objeto Usuario */
	static function userExists2($username, $password){
		self::initialize();
		$info = self::$conexion->query("SELECT * FROM usuarios WHERE username='$username' AND password='$password'");
		if ($info->rowCount() == 1){
			$array = $info->fetch(PDO::FETCH_ASSOC);
			return new User($array);
		}else return NULL;
	}
	
	/** Retorna TRUE si el email existe en la base de datos, FALSE en caso contrario*/
	static function emailExists($email){
		self::initialize();
		return self::$conexion->queryExist("SELECT * FROM usuarios WHERE mail='$email' ");
	}
	
	/** Agrega un nuevo usuario a la base de datos. 
	 * Devuelve un objeto User o NULL si no pudo agregarlo 
	 * Notar que esta funcion solo crea usuarios, NO crea admins*/
	static function userCreate($username, $password, $email){
		self::initialize();
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
		$result = self::$conexion->insert($query);
		if ($result->rowCount()==1){
			//TODO: retornar nuevo usuario sacando info de $result en lugar de pedir nuevos datos?
			return self::userExists2($username, $password);
		}else return NULL;
	}
	
	/**Retorna un objeto Usuario si existe un usuario logueado, NULL en caso contrario*/
	static function getUserLogin(){
		self::initialize();
		if (session_status() == PHP_SESSION_NONE) session_start();
		if (isset($_SESSION['username'])){
			$username = $_SESSION['username'];
			$info = self::$conexion->query("SELECT * FROM usuarios WHERE username='$username' ");
			if ($info->rowCount() == 1){
				$array = $info->fetch(PDO::FETCH_ASSOC);
				return new User($array);
			}else return NULL;
		}else{
			return NULL;
		}
	}
	
	/** Guarda la información de la sesión del usuario*/
	static function saveLogin(User $user){
		self::initialize();
		if (session_status() == PHP_SESSION_NONE) session_start();
        $_SESSION['username'] = $user->getUsername();
	}
	
	/** Elimina la información de la sesión del usuario */
	static function removeLogin(){
		self::initialize();
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
	
	static private $conexion;
	static private $initialized = false;
	
	function __construct(){}
	
	static function initialize(){
		if (self::$initialized) return;
		self::$conexion = new Conexion;
		if (!self::$conexion->conectar())	Errors::error("No se puede conectar a la base de datos", "Error al conectar a la base de datos!");
		self::$initialized = TRUE;
	}
	
	/**Devuelve un objeto Book o NULL si no existe*/
	static function getBook($bookISBN){
		self::initialize();
		$result = self::$conexion->query("SELECT * FROM libros WHERE ISBN='$bookISBN' ");
		if ($result){
			$bookInfo = $result->fetchAll(PDO::FETCH_ASSOC);
			return new Book($bookInfo[0]);
		}
		return NULL;
	}
	
	/** Devuelve un Array de objetos Book */
	static function getBestSellers($cantidad){
		self::initialize();
		$result = self::$conexion->query("
			SELECT L.ISBN, L.titulo, L.AUTOR, L.paginas, L.precio, L.IDIOMA, L.fecha, L.etiquetas, L.texto, L.tapa
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
	
	/** Devuelve la tabla de libros lista para mostrar en la pagina del catalogo (books.php)
		NOTAR: Concatenación de campos y cambio de nombre de columnas! */
	static function getCatalogo(){
		self::initialize();
		$result = self::$conexion->query("
			SELECT L.ISBN, L.titulo, concat(A.nombre,' ',A.apellido) as autor, L.paginas, L.precio, I.nombre as idioma, L.fecha, L.etiquetas
			FROM libros L
			LEFT JOIN autor A ON (L.AUTOR=A.ID)
			LEFT JOIN idioma I ON (L.IDIOMA=I.ID)
			ORDER BY ISBN
		");
		return self::$conexion->resultToTable($result, 'id="bookstable" style="color: #000000"');
	}
	
	/** Retorna un array con objetos Book de ese autor */
	static function getBooksBy($author_id){
		self::initialize();
		$result = self::$conexion->query("
			SELECT L.ISBN, L.titulo, L.AUTOR, L.IDIOMA, L.paginas, L.precio, L.fecha, L.etiquetas, L.texto, L.tapa
			FROM autor A
			INNER JOIN libros L ON (A.ID=L.AUTOR)
			WHERE (A.ID=$author_id)
		");
		$books = Array();
		if ($result){
			if ($result->rowCount()>0){
				$rows = $result->fetchAll(PDO::FETCH_ASSOC);
				foreach ($rows as $num => $row) {
					array_push($books, new Book($row));
				}
			}
		}
		return $books;
	}
	
}

class Book{
	
	private	$ISBN,
			$titulo,
			$autor_ID,
			$paginas,
			$precio,
			$idioma_ID,
			$fecha,
			$etiquetas,
			$texto,
			$tapa;
			
	private $author;
			
	function __construct($param){
		$this->ISBN = $param['ISBN'];
		$this->titulo = $param['titulo'];
		$this->autor_ID = $param['AUTOR'];
		$this->paginas = $param['paginas'];
		$this->precio = $param['precio'];
		$this->idioma_ID = $param['IDIOMA'];
		$this->fecha = $param['fecha'];
		$this->etiquetas = $param['etiquetas'];
		$this->texto = $param['texto'];
		$this->tapa = $param['tapa'];
		$this->author = NULL;
	}
	
	/**Solo DEBUG*/
	function printBook(){
		echo $this->ISBN."<br>";
		echo $this->titulo."<br>";
		echo $this->autor_ID."<br>";
		echo $this->paginas."<br>";
		echo $this->precio."<br>";
		echo $this->idioma_ID."<br>";
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
	
	function getAutor_ID(){
		return $this->autor_ID;
	}
	
	/** Retorna un objeto Author */
	function getAutor(){
		if ($this->author) return $this->author;
		else return Authors::getAuthor($this->getAutor_ID());
	}
	
	function getPaginas(){
		return $this->paginas;
	}
	
	function getPrecio(){
		return $this->precio;
	}
	
	function getIdioma_ID(){
		return $this->idioma_ID;
	}
	
	/** Retorna un objeto idioma */
	function getIdioma(){
		return Idiomas::getIdioma($this->getIdioma_ID());
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
	
	/** Retorna un objeto Author si pudo agregar a la base de datos, NULL caso contrario */
	public static function newAuthor($nombre, $apellido, $fecha_nacimiento, $lugar_nacimiento){
		self::initialize();
		$result = self::$conexion->query("
			INSERT 
			INTO autor (
				`ID`,
				`nombre`,
				`apellido`,
				`fecha_nacimiento`,
				`lugar_nacimiento`,
				`eliminado`
				)
			VALUES(
				NULL,
				'$nombre',
				'$apellido',
				'$fecha_nacimiento',
				'$lugar_nacimiento',
				'0'
			)
		");
		if ($result){
			$id = self::$conexion->getLastInsertedID();
			return Authors::getAuthor($id);
		}else{
			print_r(self::$conexion->getLastError());
			return NULL;
		}
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
		$this->nombre = $params['nombre']? $params['nombre']: "";
		$this->apellido = $params['apellido']? $params['apellido']:"";
		$this->fecha_nacimiento = $params['fecha_nacimiento']? $params['fecha_nacimiento']:"0000-00-00";
		$this->lugar_nacimiento = $params['lugar_nacimiento']? $params['lugar_nacimiento']:"";
		$this->eliminado = $params['eliminado']? $params['eliminado']:0;
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
	
	/** Retorna un array de Book */
	function getBooks(){
		return Books::getBooksBy($this->ID);
	}
	
	function setNombre($nombre){
		$this->nombre=$nombre;
	}
	
	function setApellido($apellido){
		$this->apellido=$apellido;
	}

	function setFechaNacimiento($fechaNacimiento){
		if($fechaNacimiento=='') $fechaNacimiento='0000-00-00';
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
	
	static private $articulos; //variable de sesión donde se guarda la información del carrito
	static private $initialized = FALSE;
	
	function __construct(){}
	
	static function initialize(){
		if (self::$initialized) return;
		if (session_status() == PHP_SESSION_NONE) session_start();
		
		if (!isset($_SESSION['articulos']))	$_SESSION['articulos'] = Array();
		self::$articulos = $_SESSION['articulos'];
		self::$initialized = TRUE;
	}
	
	/** Agrega un libro al carrito. Si ya está en el carrito aumenta su cantidad */
	static function addToCart($bookId){
		self::initialize();
		if (array_key_exists($bookId, self::$articulos)){
			self::$articulos[$bookId]['cantidad']++;
		}else{
			self::$articulos[$bookId] = array();
			self::$articulos[$bookId]['cantidad'] = 1;
			
			//por motivos de optimización además guardo datos sobre el libro
			include_once 'database.php';
			$book = Books::getBook($bookId);
			
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
			
			self::$articulos[$bookId] = array_merge(self::$articulos[$bookId], $arrayBook);
		}
	}
	
	/** Elimina un libro del carrito */
	static function removeFromCart($bookId){
		self::initialize();
		if (array_key_exists($bookId, self::$articulos)){
			if (self::$articulos[$bookId]['cantidad']>1) self::$articulos[$bookId]['cantidad']--;
			else unset(self::$articulos[$bookId]);
		}
	}
	
	/** Vacía el carrito */
	static function emptyCart(){
		self::initialize();
		self::$articulos = Array();
	}
	
	/** Guarda la información del carrito en la sesion */
	static function saveCart(){
		self::initialize();
		$_SESSION['articulos'] = self::$articulos;
	}
	
	/** Retorna la cantidad de libros en el carrito */
	static function sizeCart(){
		self::initialize();
		$sum = 0;
		foreach (self::$articulos as $bookid => $book) {
			$cantidad = $book['cantidad'];
			$sum += $cantidad;
		}
		return $sum;
	}
	
	/** Retorna el precio total del carrito */
	static function priceCart(){
		self::initialize();
		$sum = 0;
		foreach (self::$articulos as $bookid => $book) {
			$sum += $book['precio']*$book['cantidad'];
		}
		return $sum;
	}
	
	/** Crea la vista para el dropmenu del carrito*/
	static function printCartHTML(){
		self::initialize();
		?>
		<div class="" style="width: 500px; padding: 10px">
		<?php
		foreach (self::$articulos as $bookid => $book) {
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
			<p>Total: $<?php echo self::priceCart() ?>
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
	static function printCart(){
		self::initialize();
		print_r(self::$articulos);
	}
	
}

class Idiomas{
	
	static private $conexion;
	static private $initialized = FALSE;
	
	function __construct(){}
	
	static function initialize(){
		if (self::$initialized) return;
		self::$conexion = new Conexion;
		if (!self::$conexion->conectar())	Errors::error("No se puede conectar a la base de datos", "Error al conectar a la base de datos!");
		self::$initialized = TRUE;
	}
	
	static function getIdioma($id){
		self::initialize();
		$result = self::$conexion->query("
			SELECT *
			FROM idioma
			WHERE (ID=$id)
			LIMIT 1
		");
		if ($result){
			if ($result->rowCount()>0){
				return new Idioma($result->fetch(PDO::FETCH_ASSOC));
			}
			return NULL;
		}
		return NULL;
	}
	
}

class Idioma{
	
	private	$ID,
			$nombre;
	
	function __construct($params){
		$this->ID = $params['ID'];
		$this->nombre = $params['nombre'];
	}
	
	function getID(){
		return $this->ID;
	}
	
	function getNombre(){
		return $this->nombre;
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