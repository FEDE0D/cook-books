<?php

/**
 * Clase Conexion (usada por otras clases para comunicarse con la base de datos)
 */
class Conexion {
	
	private $local = FALSE;//Indica si estoy trabajando con un servidor local.
	private $hostname = 'localhost';
	private $port = '8080';
	private $username = 'root';
	private $password = 'F3Dericcio';
	private $dbname = 'cookbookg32';
	
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
		if (!$queryResult) return "";
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
		if ($result){
			if ($result->rowCount()==1){
				//TODO: retornar nuevo usuario sacando info de $result en lugar de pedir nuevos datos?
				return self::userExists2($username, $password);
			}else return NULL;
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
	/** Retorna un array de objetos Book con todos los libros no eliminados en el sistema */
	static function getLibros(){
		self::initialize();
		$result = self::$conexion->query("
			SELECT L.ID, L.ISBN, L.titulo, GROUP_CONCAT(E.id_autor) as autores, L.paginas, L.precio, L.IDIOMA, L.fecha, L.etiquetas, L.texto, L.tapa, L.eliminado
			FROM libros L
			LEFT JOIN escribe E ON (L.ID=E.id_libro)
			WHERE(L.eliminado=0)
			GROUP BY L.ID
		");
				
		$libros = array();
		if ($result){
			$librosDatos = $result->fetchAll(PDO::FETCH_ASSOC);
			foreach ($librosDatos as $key => $info) {
				array_push($libros, new Book($info));
			}
		}
		return $libros;
		
	}
	
	/**Devuelve un objeto Book o NULL si no existe. El libro puede estar eliminado. */
	static function getBook($bookID){
		self::initialize();
		$result = self::$conexion->query("
			SELECT L.ID, L.ISBN, L.titulo, GROUP_CONCAT(E.id_autor) as autores, L.paginas, L.precio, L.IDIOMA, L.fecha, L.etiquetas, L.texto, L.tapa, L.eliminado
			FROM libros L
			LEFT JOIN escribe E ON (L.ID=E.id_libro)
			GROUP BY L.ID
			HAVING (L.ID=$bookID)
		");
		if ($result && $result->rowCount()>0){
			$bookInfo = $result->fetchAll(PDO::FETCH_ASSOC);
			return new Book($bookInfo[0]);
		}
		return NULL;
	}
	
	/** Devuelve un Array de objetos Book */
	static function getBestSellers($cantidad){
		self::initialize();
		$result = self::$conexion->query("
			SELECT L.ID, L.ISBN, L.titulo, GROUP_CONCAT(E.id_autor) as autores, L.paginas, L.precio, L.IDIOMA, L.fecha, L.etiquetas, L.texto, L.tapa, L.eliminado
			FROM libros L
			LEFT JOIN escribe E ON (L.ID=E.id_libro)
			WHERE (L.eliminado=0)
			Group by L.ID
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
	 *	FIXME: Devolver nombre de autores concatenados
	 *	TODO: Crear clase Catálogo y armar Tabla Jquery con eso.
	 */
	static function getCatalogo(){
		self::initialize();
		$result = self::$conexion->query("
			SELECT L.ISBN, L.titulo, GROUP_CONCAT(A.apellido) as autor, L.paginas, L.precio, L.IDIOMA as idioma, L.fecha, L.etiquetas
			FROM libros L
			LEFT JOIN escribe E ON (L.ID=E.id_libro)
			LEFT JOIN autor A ON (E.id_autor=A.ID)
			WHERE (L.eliminado=0)
			Group by L.ISBN
		");
		return self::$conexion->resultToTable($result, 'id="bookstable" style="color: #000000"');
	}
	
	/** Retorna un array con objetos Book de ese autor. Notar que este metodo no diferencia entre libros eliminados y no eliminados */
	static function getBooksBy($author_id){
		self::initialize();
		$result = self::$conexion->query("
			SELECT L.ID, L.ISBN, L.titulo, GROUP_CONCAT(E.id_autor) as autores, L.IDIOMA, L.paginas, L.precio, L.fecha, L.etiquetas, L.texto, L.tapa, L.eliminado
			FROM autor A
			LEFT JOIN escribe E ON (A.ID=E.id_autor)
			INNER JOIN libros L ON (E.id_libro=L.ID)
			GROUP BY (L.ID)
			HAVING autores LIKE '%$author_id%'
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
	
	/** Crea un nuevo libro. $autores es un string con IDs de autores separados por comas. */
	public static function newBook($ISBN, $titulo, $idioma, $fecha, $tags, $precio, $texto, $autores, $paginas, $tapa){
		self::initialize();
		$autores = explode(",", $autores);
		
		//Inserto los datos del nuevo libro
		$result = self::$conexion->query("
			INSERT 
			INTO libros (
				`ISBN`,
				`titulo`,
				`IDIOMA`,
				`paginas`,
				`precio`,
				`fecha`,
				`etiquetas`,
				`texto`,
				`tapa`,
				`eliminado`
				)
			VALUES(
				'$ISBN',
				'$titulo',
				'$idioma',
				'$paginas',
				'$precio',
				'$fecha',
				'$tags',
				'$texto',
				'$tapa',
				'0'
			)
		");
		
		if ($result){//Inserto los datos de los autores
			//obtengo el id del libro agregado
			$id_libro = self::$conexion->getLastInsertedID();
			$query = "
                INSERT INTO `escribe`(`id`, `id_libro`, `id_autor`)
                VALUES 
            ";
            foreach ($autores as $key => $value) {
                $query .= "(NULL, $id_libro, $value),";
            }
            $queryEscribe = substr($query,0,-1);
            
			$result2 = self::$conexion->query($queryEscribe);
			if ($result2){
				return Books::getBook($id_libro);
			}else{
				return NULL;
			}
		}else{
		    //Error: No se pudo agregar, ISBN repetido o falta de parametros
			return NULL;
		}
	}
	public static function updateBook(Book $libro){
		self::initialize();
		
		$id_libro = $libro->getID();
		$isbn = $libro->getISBN();
		$titulo = $libro->getTitulo();
		$idioma = $libro->getIdioma();
		$fecha = $libro->getFecha();
		$tags = $libro->getEtiquetas();
		$precio = $libro->getPrecio();
		$texto = $libro->getTexto();
		$autores = $libro->getAutoresIDs();
		$paginas = $libro->getPaginas();
		$tapa = $libro->getTapa();
		$eliminado = $libro->getEliminado();
	
		$result = self::$conexion->query("
			UPDATE  libros
			SET
			ISBN = '$isbn', 
			titulo = '$titulo',
			IDIOMA = '$idioma',
			paginas = '$paginas',
			precio = '$precio',
			fecha = '$fecha',
			etiquetas = '$tags',
			texto = '$texto',
			tapa = '$tapa',
			eliminado = '$eliminado'
			WHERE (ID = $id_libro)
		");
		if ($result){
			//	Baja de las conexiones de autores de este libro en tabla Escritos
			$result2 = self::$conexion->query("
				DELETE FROM escribe
				WHERE (id_libro=$id_libro)
			");
			if ($result2){
				//	Dar de alta las conexiones de autores de este libro en tabla Escritos
				$autores = explode(",", $autores);
				
				$query = "
					INSERT INTO escribe(`id`, `id_libro`, `id_autor`)
					VALUES 
				";
				foreach ($autores as $key => $value) {
					$query .= "(NULL, $id_libro, $value),";
				}
				$queryEscribe = substr($query,0,-1);
				$result3 = self::$conexion->query($queryEscribe);
				if ($result3){
					return TRUE;
				}
			}
			return TRUE;
		}
		return FALSE;
	}
	
	
	
}

class Book{
	
	private	$ID,
	        $ISBN,
			$titulo,
			$paginas,
			$precio,
			$idioma,
			$fecha,
			$etiquetas,
			$texto,
			$tapa,
			$eliminado,
			$autores; //es un string de id de autor separados por comas
			
	function __construct($param){
	    $this->ID = $param['ID'];
		$this->ISBN = $param['ISBN'];
		$this->titulo = $param['titulo']? $param['titulo']:'';
		$this->paginas = $param['paginas']? $param['paginas']:'0';
		$this->precio = $param['precio']? $param['precio']:'0';
		$this->idioma= $param['IDIOMA']? $param['IDIOMA']:'';
		$this->fecha = $param['fecha']? $param['fecha']:'0000-00-00';
		$this->etiquetas = $param['etiquetas']? $param['etiquetas']:'';
		$this->texto = $param['texto']? $param['texto']:'';
		$this->tapa = $param['tapa']? $param['tapa']:'';
		$this->eliminado = $param['eliminado']? $param['eliminado']:'0';
		$this->autores = $param['autores']? $param['autores']:'';
	}
	
    function getID(){
        return $this->ID;
    }
    
	function getISBN(){
		return $this->ISBN;
	}
	
	function getTitulo(){
		return $this->titulo;
	}
	
	/** Retorna la lista de IDs de autores, separadas por coma */
	function getAutoresIDs(){
		return $this->autores;
	}
	
	/** Retorna la lista de IDs de autores en un array */
	function getAutoresIDs_arr(){
		return explode(",", $this->autores);
	}
	
	/** Retorna la lista apellidos de autores separadas por coma */
	function getAutoresString(){
		$arreglo= $this->getAutores();
		$string ="";
		foreach ($arreglo as $key => $value) {
			$inicial=$value->getNombre();
			$inicial= $inicial[0];
			$string.=$inicial.'. '.$value->getApellido().", ";
		}
		$string= substr ($string, 0, -2); //eliminar el , del final
		return $string;      //retorna lista de autores: N. Apellido, ..
	}
	
	/** Retorna un arreglo de objetos Author */
	function getAutores(){
		$autores_ids = $this->autores;
		$autores_ids = explode(",", $this->autores);
		$result= Array();
		foreach ($autores_ids as $key => $value) {
			$autor = Authors::getAuthor($value);
			if ($autor)	array_push($result, $autor);
		}
		return $result;
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
	
	function getEliminado(){
		return $this->eliminado;
	}
    
    function setISBN($isbn){
        $this->ISBN=$isbn;
    }
	
	function setTitulo($titulo){
		$this->titulo=$titulo;
	}
	
	function setPaginas($paginas){
		$this->paginas=$paginas;
	}
	
	function setPrecio($precio){
		$this->precio=$precio;
	}
	
	function setIdioma($idioma){
		$this->idioma=$idioma;
	}
	
	function setFecha($fecha){
		$this->fecha=$fecha;
	}
	
	function setEtiquetas($etiquetas){
		$this->etiquetas=$etiquetas;
	}
	
	function setTexto($texto){
		$this->texto=$texto;
	}
	
	function setTapa($tapa){
		if (is_array($tapa)) $tapa=$tapa[0];
		$this->tapa=$tapa;
	}
	
	function setEliminado($num){
		$this->eliminado=$num;
	}
	
	/** Recibe una lista de autores separada por coma */
	function setAutores($autores){
		$c = substr($autores, -1);	if ($c==',' || $c==', ') $autores = substr($autores,0,-1);
		$this->autores=$autores;
	}
	
	function save(){
		return Books::updateBook($this);
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
			if ($book){
				$arrayBook = array(
					"ISBN" => $book->getISBN(),
					"titulo" => $book->getTitulo(),
					"autores" => $book->getAutoresIDs(),
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



class Errors{
	
	public static function error($title, $body){
		if (session_status() == PHP_SESSION_NONE) session_start();
		$_SESSION['error_title'] = $title;
		$_SESSION['error_info'] = $body;
		header("Location: error.php");
	}
	
}

?>
