<?php


/**
 * Clase Conexion (usada por otras clases para comunicarse con la base de datos)
 */
class Conexion {
	
	private $local = TRUE;//Indica si estoy trabajando con un servidor local.
	private $hostname = 'localhost';
	private $port = '3306';
	private $username = 'root';
	private $password = 'F3Dericcio';
	private $dbname = 'cookbookg32';
	
	private static $dbh = NULL;
	

	function __construct(){
		if (!$this->local){
			$this->hostname = 'mysql.nixiweb.com';
			$this->port = '3306';
			$this->username = 'u847065820_root';
			$this->password = 'dvVq47UfJ6';
			$this->dbname = 'u847065820_cb';
		}
	}
	
	/** Conecta a la base de datos */
	function conectar(){
		try {
			if (!self::$dbh)
			self::$dbh = new PDO("mysql:host=$this->hostname;dbname=$this->dbname;port=$this->port", $this->username, $this->password, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
			
			//echo "Conectado a la bbdd";
			return true;
		} catch(PDOException $e) {
			echo $e -> getMessage();
			return false;
		}
	}
	
	/** Desconecta de la base de datos */
	function desconectar(){
		self::$dbh = null;//close database connection
		// echo "Desconectado de la bbdd";
	}
	
	/** Realiza una consulta a la base de datos, retorna el output de la consulta*/
	function query($statement){
		return self::$dbh->query($statement);
	}
	
	/** Retorna un Array con la información asociada a la última operación realizada */
	function getLastError(){
		return self::$dbh->errorInfo();
	}
	
	function getLastInsertedID(){
		return self::$dbh->lastInsertId();
	}
	
	/*
	 * CONSULTAS SELECT
	 */
	
	/** Convierte una consulta SELECT a una tabla */
	function resultToTable($queryResult, $tableHTMLAttributes = "", $tableTag = true){
		if (!$queryResult) return "";
		$string = "";
		$result = $queryResult->fetchAll(PDO::FETCH_NAMED);
				
		if ($tableTag) $string .= "<table $tableHTMLAttributes>";
		$string .= "<thead>";
			// $string .= "<tr>";
				foreach ($result as $row => $column) {
					foreach ($column as $columnName => $columnValue) {
						$string .= "<th>$columnName</th>";
					}
					break;
				}
			// $string .= "</tr>";
		$string .= "</thead>";
		
		$string .= "<tbody>";
		foreach ($result as $row => $columns) {
			$string .= "<tr>";
			foreach ($columns as $columnName => $columnValue) {
				if ($columnName=="tapa" && !is_file("books/img/tapas/".$columnValue)) $columnValue = "_DEFAULT_.jpg"; //Si no existe la tapa, pone una por defecto
				$string .= "<td>".$columnValue."</td>";
			}
			$string .= "</tr>";
		}
		$string .= "</tbody>";
		if ($tableTag) $string .= "</table>";
		
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
	
	/** Retorna un objeto User correspondiente con el $username*/
	static function getUser($username){
		self::initialize();
		$query = self::$conexion->query("
			SELECT U.username, U.password, U.nombre, U.apellido, U.direccion, U.mail, U.telefono, U.admin, U.fecha_alta, U.fecha_nac, U.enabled
			FROM usuarios U
			WHERE (U.username = '$username')
		");
		if ($query){
			$query = $query->fetch(PDO::FETCH_ASSOC);
			return new User($query);
		}
		return NULL;
	}
	
	/** Retorna un array de objetos User con todos los usuarios del sistema*/
	static function getUsers(){
		self::initialize();
		$result = self::$conexion->query("
			SELECT U.username, U.password, U.nombre, U.apellido, U.direccion, U.mail, U.telefono, U.admin, U.fecha_alta, U.fecha_nac, U.enabled
			FROM usuarios U
			WHERE (U.admin=0)
			ORDER BY U.enabled DESC, U.username ASC
		");
		$usuarios = array();
		if ($result){
			$result = $result->fetchAll(PDO::FETCH_ASSOC);
			foreach ($result as $key => $value) {
				array_push($usuarios, new User($value));
			}
		}
		return $usuarios;
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
			`fecha_nac`,
			`enabled`
			)
			VALUES (
			'$username',  '$password',  '', '' , '' ,  '$email', '' ,  '0',  '$today', '0000-00-00','1'
			);
		";
		$result = self::$conexion->insert($query);
		if ($result){
			if ($result->rowCount()==1){
				return self::userExists2($username, $password);
			}else return NULL;
		}else return NULL;
	}
	
	/** Guarda la info del $user en la BBDD */
	static function updateUser(User $user){
		self::initialize();
		$query = self::$conexion->query("
			UPDATE usuarios
			SET
			password = '".$user->getPassword()."',
			nombre = '".$user->getNombre()."',
			apellido = '".$user->getApellido()."',
			direccion = '".$user->getDireccion()."',
			mail = '".$user->getEmail()."',
			telefono = '".$user->getTelefono()."',
			fecha_nac = '".$user->getFechaNacimiento()."',
			enabled = '".$user->getEnabled()."'
			WHERE (username='".$user->getUsername()."')
		");
		if ($query){
			return TRUE;
		}else{
			return FALSE;
		}
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
			$administrator,
			$enabled;
			
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
		$this->enabled = $params['enabled'];
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
	
	function getEnabled(){
		return $this->enabled;
	}
	
	function setPassword($newPassword){
		$this->password = $newPassword;
	}
	
	function setNombre($newNombre){
		$this->nombre = $newNombre;
	}
	
	function setApellido($newApellido){
		$this->apellido = $newApellido;
	}
	
	function setDireccion($newDireccion){
		$this->direccion = $newDireccion;
	}
	
	function setEmail($newEmail){
		$this->email = $newEmail;
	}
	
	function setTelefono($newTelefono){
		$this->telefono = $newTelefono;
	}
	
	function setFechaNacimiento($newFechaNacimiento){
		$this->fecha_nacimiento = $newFechaNacimiento;
	}
	
	function setEnabled($num){
		$this->enabled = $num;
	}
	
	function save(){
		return Users::updateUser($this);
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
	/** Retorna un array de objetos Book con todos los libros del sistema. OJO! Devuelve todos los libros (incluyendo eliminados y ocultos) */
	static function getBooks(){
		self::initialize();
		$result = self::$conexion->query("
			SELECT L.ISBN, L.titulo, GROUP_CONCAT(E.id_autor) as autores, L.paginas, L.precio, L.IDIOMA, L.fecha, L.etiquetas, L.texto, L.tapa, L.eliminado, L.hidden
			FROM libros L
			LEFT JOIN escribe E ON (L.ISBN=E.isbn)
			GROUP BY L.ISBN
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
	
	/** Retorna todos los libros del sistema que no estan eliminados (pueden estar ocultos)*/
	static function getBooksAvailable(){
		self::initialize();
		$result = self::$conexion->query("
			SELECT L.ISBN, L.titulo, GROUP_CONCAT(E.id_autor) as autores, L.paginas, L.precio, L.IDIOMA, L.fecha, L.etiquetas, L.texto, L.tapa, L.eliminado, L.hidden
			FROM libros L
			LEFT JOIN escribe E ON (L.ISBN=E.isbn)
			GROUP BY L.ISBN
			HAVING (L.eliminado=0)
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
	
	/** Devuelve un objeto Book o NULL si no existe. */
	static function getBook($bookISBN){
		self::initialize();
		$result = self::$conexion->query("
			SELECT L.ISBN, L.titulo, GROUP_CONCAT(E.id_autor) as autores, L.paginas, L.precio, L.IDIOMA, L.fecha, L.etiquetas, L.texto, L.tapa, L.eliminado, L.hidden
			FROM libros L
			LEFT JOIN escribe E ON (L.ISBN=E.isbn)
			GROUP BY L.ISBN
			HAVING (L.ISBN=$bookISBN)
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
			SELECT T1.ISBN, T1.titulo, T1.autores, T1.paginas, T1.precio, T1.IDIOMA, T1.fecha, T1.etiquetas, T1.texto, T1.tapa, T1.eliminado, T1.hidden
			FROM (
					SELECT L.ISBN, L.titulo, GROUP_CONCAT(E.id_autor) as autores, L.paginas, L.precio, L.IDIOMA, L.fecha, L.etiquetas, L.texto, L.tapa, L.eliminado, L.hidden
				    FROM libros L
				    INNER JOIN escribe E ON (L.ISBN=E.isbn)
				    WHERE (L.eliminado=0 AND L.hidden=0)
				    GROUP BY E.isbn
				) AS T1
			INNER JOIN (
					SELECT P.id, P.ISBN, sum(P.cantidad) as cantidad
			        FROM pedidos P
			        INNER JOIN compra C ON (P.id_compra=C.id)
			        WHERE (C.estado='efectuado')
			        GROUP BY (P.ISBN)
			        ORDER BY cantidad DESC
				)as T2 ON (T2.ISBN=T1.ISBN)
			ORDER BY T2.cantidad DESC
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
			SELECT L.ISBN, L.titulo, GROUP_CONCAT(A.nombre,' ',A.apellido) as autores, L.paginas, L.precio, L.IDIOMA as idioma, L.fecha, L.etiquetas, L.tapa
			FROM libros L
			LEFT JOIN escribe E ON (L.ISBN=E.isbn)
			LEFT JOIN autor A ON (E.id_autor=A.ID)
			WHERE (L.eliminado=0 AND L.hidden=0)
			Group by L.ISBN
		");
		return self::$conexion->resultToTable($result, 'id="bookstable" class="display" cellspacing="0" width="100%"', false);
	}
	
	/** Retorna un array con objetos Book de ese autor. Los libros NO estan eliminados */
	static function getBooksBy($author_id){
		self::initialize();
		$result = self::$conexion->query("
			SELECT L.ISBN, L.titulo, GROUP_CONCAT(E.id_autor) as autores, L.IDIOMA, L.paginas, L.precio, L.fecha, L.etiquetas, L.texto, L.tapa, L.eliminado, L.hidden
			FROM autor A
			LEFT JOIN escribe E ON (A.ID=E.id_autor)
			INNER JOIN libros L ON (L.ISBN=E.isbn)
			GROUP BY (L.ISBN)
			HAVING (autores LIKE '%$author_id%' AND L.eliminado=0  )
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
	
	/** Crea un nuevo libro. $autores es un string con IDs de autores separados por comas. 
	 * Retorna un objeto Book si pudo crear el libro, caso contrario un numero de error*/
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
				`eliminado`,
				`hidden`
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
				'0',
				'0'
			)
		");
		
		if ($result){//Inserto los datos de los autores
			$query = "
                INSERT INTO `escribe`(`id`, `isbn`, `id_autor`)
                VALUES 
            ";
            foreach ($autores as $key => $value) {
                $query .= "(NULL, $ISBN, $value),";
            }
            $queryEscribe = substr($query,0,-1);
            
			$result2 = self::$conexion->query($queryEscribe);
			if ($result2){
				return Books::getBook($ISBN);
			}else{
				return -1;
			}
		}else{
		    //Error: No se pudo agregar, ISBN repetido o falta de parametros
			return -2;
		}
	}

	/** Guarda los datos del Book $libro en la base de datos */
	public static function updateBook(Book $libro){
		self::initialize();
		
		$old_isbn = $libro->getOLD_ISBN();
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
		$oculto = $libro->getOculto();
	
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
			eliminado = '$eliminado',
			hidden = '$oculto'
			WHERE (ISBN = $old_isbn)
		");
		if ($result){
			//	Baja de las conexiones de autores de este libro en tabla Escritos
			$result2 = self::$conexion->query("
				DELETE FROM escribe
				WHERE (isbn=$old_isbn)
			");
			if ($result2){
				//	Dar de alta las conexiones de autores de este libro en tabla Escritos
				$autores = explode(",", $autores);
				
				$query = "
					INSERT INTO escribe(`id`, `isbn`, `id_autor`)
					VALUES 
				";
				foreach ($autores as $key => $value) {
					$query .= "(NULL, $isbn, $value),";
				}
				$queryEscribe = substr($query,0,-1);
				$result3 = self::$conexion->query($queryEscribe);
				if ($result3){
					return TRUE;
				}
			}
			return TRUE;
		}
		return self::$conexion->getLastError();
	}
	
	
	
}

class Book{
	
	private	$ISBN, $OLD_ISBN,
			$titulo,
			$paginas,
			$precio,
			$idioma,
			$fecha,
			$etiquetas,
			$texto,
			$tapa,
			$eliminado,
			$oculto,
			$autores; //es un string de id de autor separados por comas
			
	function __construct($param){
		$this->ISBN = $param['ISBN'];
		$this->OLD_ISBN = $param['ISBN'];
		$this->titulo = $param['titulo']? $param['titulo']:'';
		$this->paginas = $param['paginas']? $param['paginas']:'0';
		$this->precio = $param['precio']? $param['precio']:'0';
		$this->idioma= $param['IDIOMA']? $param['IDIOMA']:'';
		$this->fecha = $param['fecha']? $param['fecha']:'0000-00-00';
		$this->etiquetas = $param['etiquetas']? $param['etiquetas']:'';
		$this->texto = $param['texto']? $param['texto']:'';
		$this->tapa = $param['tapa']? $param['tapa']:'';
		$this->eliminado = $param['eliminado']? $param['eliminado']:'0';
		$this->oculto = $param['hidden']? $param['hidden']:'0';
		$this->autores = $param['autores']? $param['autores']:'';
	}
    
	function getISBN(){
		return $this->ISBN;
	}
	
	function getOLD_ISBN(){
		return $this->OLD_ISBN;
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
	
	/** Recibe TRUE si se quiere obtener el valor real guardado en la tapa.
	 * FALSE si se quiere obtener un valor por defecto en caso de que no exista el archivo de tapa. */
	function getTapa($crudo = false){
		if ($crudo){
			return $this->tapa;
		}else{
			if(is_file("books/img/tapas/".$this->tapa)){
				return $this->tapa;
			}else{
				return "_DEFAULT_.jpg";
			}
		}
			
	}
	
	function getEliminado(){
		return $this->eliminado;
	}
	
	function getOculto(){
		return $this->oculto;
	}
	
	function setISBN($ISBN){
		$this->OLD_ISBN = $this->ISBN;
		$this->ISBN = $ISBN;
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
	
	function setOculto($num){
		$this->oculto=$num;
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
	
	/** Retorna un array de objetos Author con el mismo $nombre y $apellido que pertenezca a la base de datos.
	 * NOTE: el autor NO esta eliminado*/
	public static function getAuthorsByName($nombre, $apellido){
		self::initialize();
		$result = self::$conexion->query("
			SELECT *
			FROM autor
			WHERE ((nombre LIKE '$nombre' AND apellido LIKE '$apellido') AND eliminado=0)
		");
		$authors = array();
		if($result){
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
	
	/** Retorna un objeto Author si pudo agregar a la base de datos, NULL caso contrario
	 * NOTA: No se pueden agregar autores con el mismo nombre */
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
			print_r($fecha_nacimiento);
			echo "<p>$fecha_nacimiento</p>";
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

class Compras{
	
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
	
	static function getCompra($idCompra){
		self::initialize();
		$data = self::$conexion->query("
			SELECT *
			FROM compra C
			WHERE (C.id='$idCompra')
		");
		if ($data){
			$data = $data->fetch(PDO::FETCH_ASSOC);
			return new Compra($data);
		}
		
		return NULL;self::$conexion->desconectar();
	}
	
	/** Retorna un array de objetos Compra con todas las compras del sistema */
	static function getCompras($username = FALSE){
		self::initialize();
		$query = "
			SELECT *
			FROM compra C
		";
		if($username) $query .= "WHERE (C.username='$username')";
		
		$data = self::$conexion->query($query);
		$compras = array();
		if ($data){
			$data = $data->fetchAll(PDO::FETCH_ASSOC);
			foreach ($data as $key => $value) {
				array_push($compras, new Compra($value));
			}
		}
		
		return $compras;self::$conexion->desconectar();
	}
	
	/** Crea una compra en la base de datos. Recibe los articulos que están en el carrito. Retorna un objeto Compra */
	static function createCompra($articulos){
		self::initialize();
		$today = $today = date("Y-m-d");
		$user = Users::getUserLogin();
		if ($user){
			$username = $user->getUsername();
			$sql = self::$conexion->query("
				INSERT INTO compra(
				`id`,
				`fecha`,
				`estado`,
				`username`
				)
				VALUES( NULL, '$today', 'pendiente', '$username')
			");
			if ($sql){
				$compraId = self::$conexion->getLastInsertedID();
				$sql2 = "
					INSERT INTO pedidos (`id`,`ISBN`,`cantidad`,`precio_unitario`,`id_compra`)
					VALUES
				";
				foreach ($articulos as $isbn => $info) {
					$sql2 .= "(NULL, '$isbn', '".$info['cantidad']."', '".$info['precio']."' , '$compraId' ),";
				}
				$sql2 = substr($sql2,0,-1);
				
				$sql2 = self::$conexion->query($sql2);
				if ($sql2){
					return Compras::getCompra($compraId);
				}else{
					return NULL;
				}
			}else{
				return NULL;
			}
		}else{
			return NULL;
		}
		
	}
	
	/** Guarda los cambios del objeto Compra en la base de datos */
	static function updateCompra(Compra $compra){
		self::initialize();
		$idCompra = $compra->getId();
		$estado = $compra->getEstado();
		$data = self::$conexion->query("
			UPDATE compra
			SET estado = '$estado'
			WHERE (id='$idCompra') 
		");
		if ($data){
			return TRUE;
		}else{
			return FALSE;
		}
	} 
	
}

class Compra{
	
	private $id,
			$fecha,
			$estado,
			$username;
			
	private $pedidos;
	
	function __construct($data){
		$this->id = $data['id'];
		$this->fecha = $data['fecha'];
		$this->estado = $data['estado'];
		$this->username = $data['username'];
		$this->pedidos = FALSE;
	}
	
	function getId(){
		return $this->id;
	}
	
	function getFecha(){
		return $this->fecha;
	}
	
	function getEstado(){
		return $this->estado;
	}
	
	function getUsername(){
		return $this->username;
	}
	
	/** Retorna un array de Pedido */
	function getPedidos(){
		if (!$this->pedidos){
			$this->pedidos = Pedidos::getPedidos($this->id);
		}
		return $this->pedidos;
	}
	
	/** Retorna la cantidad de libros en esta compra */
	function getCantidadLibros(){
		$pedidos = $this->getPedidos();
		$cant = 0;
		foreach ($pedidos as $key => $pedido) {
			$cant += $pedido->getCantidad();
		}
		return $cant;
	}
	
	/** retorna el precio total para esta compra */
	function getTotal(){
		$pedidos = $this->getPedidos();
		$total = 0;
		foreach ($pedidos as $key => $value) {
			$total += $value->getSubtotal();
		}
		return $total;
	}
	
	function setEstado($newEstado){
		$this->estado = $newEstado;
	}
	
	/** Retorna TRUE/FALSE si pudo actualizar la compra */
	function save(){
		return Compras::updateCompra($this);
	}
	
}

class Pedidos{
	
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
	
	/** Retorna un array de Pedido para una compra con id $idCompra */
	static function getPedidos($idCompra){
		self::initialize();
		$data = self::$conexion->query("
			SELECT *
			FROM pedidos P
			WHERE (P.id_compra='$idCompra')
		");
		$pedidos = array();
		if ($data){
			$data = $data->fetchAll(PDO::FETCH_ASSOC);
			foreach ($data as $key => $value) {
				array_push($pedidos, new Pedido($value));
			}
		}
		return $pedidos;
	}
	
}

class Pedido{
	
	private $id,
			$ISBN,
			$cantidad,
			$precio_unitario,
			$id_compra;
			
	private $libro;
	
	function __construct($data){
		$this->id = $data['id'];
		$this->ISBN = $data['ISBN'];
		$this->cantidad = $data['cantidad'];
		$this->precio_unitario = $data['precio_unitario'];
		$this->id_compra = $data['id_compra'];
		$this->libro = FALSE;
	}
	
	function getId(){
		return $this->id;
	}
	
	function getISBN(){
		return $this->ISBN;
	}
	
	function getCantidad(){
		return $this->cantidad;
	}
	
	function getPrecioUnitario(){
		return $this->precio_unitario;
	}
	
	/** Retorna el precio subtotal para este pedido */
	function getSubtotal(){
		return $this->cantidad*$this->precio_unitario;
	}
	
	function getBook(){
		if (!$this->libro){
			$this->libro = Books::getBook($this->ISBN);
		}
		return $this->libro;
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
			// include_once 'database.php';
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
	
	/** Retorna un array de los articulos en el carrito */
	static function getArticulos(){
		self::initialize();
		return self::$articulos;
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
	                <button value="<?php echo $bookid; ?>" onclick="removeBook(this)" class="pull-right" >
	                	<span class='glyphicon glyphicon-trash'></span>
	                </button>
                </div>
        	</li><br />
        	<?php
		}
		?>
			<p>Total: $<?php echo self::priceCart() ?>
			<button onclick="clearCart()" class="pull-right" >
	            <span class='glyphicon glyphicon-trash'> Vaciar</span>
            </button>
			<button class="pull-right" onclick="document.location='buy.php'">
				<span class='glyphicon glyphicon-shopping-cart'> Comprar</span>
			</button>
            </p>
		</div>
		<script>
			/** Elimina un libro del carrito */
			function removeBook(elem){
				$.ajax({
					url:'ajax.php',
					type:'POST',
					data:{
						type:'CART',
						data: JSON.stringify({
							action:'REMOVE',
							bookid:$(elem).val()
						})
					},
					success:function(data){
						$.post('navigation.php').done(
							function(navbar){
								$('#navigationWrapper').replaceWith(navbar);
							}
						);
					}
				});
			}
			
			function clearCart(){
				$.ajax({
					url:'ajax.php',
					type:'POST',
					data:{
						type:'CART',
						data: JSON.stringify({
							action:'CLEAR'
						})
					},
					success:function(data){
						$.post('navigation.php').done(
							function(navbar){
								$('#navigationWrapper').replaceWith(navbar);
							}
						);
					}
				});
			}
			
		</script>
		<?php
	}
	
	/** Imprime el carrito. XXX Solo para debug. */
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