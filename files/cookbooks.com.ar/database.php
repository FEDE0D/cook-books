<?php
 

/**
 * Clase Conexion (usada por otras clases para comunicarse con la base de datos)
 */
class Conexion {
	
	private $hostname = 'localhost';
	private $port = '8080';
	private $username = 'root';
	private $password = 'F3Dericcio';
	private $dbname = 'CookBooks';
	// private $hostname = 'sql3.freemysqlhosting.net';
	// private $port = '3306';
	// private $username = 'sql336432';
	// private $password = 'eM2!lM4%';
	// private $dbname = 'sql336432';
	
	private $dbh;

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
	
	/*
	 * CONSULTAS SELECT
	 */
	
	/** Convierte una consulta SELECT a un formato legible */
	function resultToString($queryResult){
		$string = "";
	    $result = $queryResult->fetchAll(PDO::FETCH_NAMED);
		foreach ($result as $row => $columns) {
			foreach ($columns as $columnName => $columnValue) {
				$string .= $columnName." - ".$columnValue."<br>";
			}
			$string .= "<br>";
		}
		return $string;
	}
	
	/** Convierte una consulta SELECT a una tabla */
	function resultToTable($queryResult, $tableHTMLAttributes){
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
	function queryToTable($statement){
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
	
	 
		 
	/*
	 * CONSULTAS UPDATE
	 */
	
	
}

/**
 * Clase que usamos para hacer las cosas mas comunes
 */
class DataBase{
	
	private $conexion;
	
	/** Constructor */
	function __construct(){
		$this->conexion = new Conexion;
		if (!$this->conexion->conectar())	die ("Error al crear objeto conexion");
	}
	
	/*
	 * LIBROS
	 */
	
	/** Retorna la tabla de los libros completa */
	function tableBooks(){
		return $this->conexion->queryToTable("SELECT * FROM libros");
	}
	
	/** Retorna la tabla de los libros mejores vendidos*/
	function tableBooksBestSeller($cant){
		return $this->conexion->queryToTable("SELECT * FROM libros");
	}
	
	/** Retorna la tabla de los libros más nuevos, limitando la tabla a $cantidad resultados*/
	function tableBooksNewest($cantidad){
		return $this->conexion->queryToTable("SELECT * FROM libros ORDER BY fecha LIMIT $cantidad");
	}
	
	/** Devuelve la tabla de libros lista para mostrar en la pagina del catalogo (books.php) */
	function tableCatalogo(){
		$result = $this->conexion->query('SELECT ISBN, titulo, autor, paginas, precio, idioma, fecha, etiquetas FROM libros ORDER BY ISBN');
		return $this->conexion->resultToTable($result, 'id="bookstable" style="color: #000000"');
	}
	
	/** Devuelve como Array la información del libro con ISBN $isbn */
	function bookInfo($isbn){
		return $this->conexion->query("SELECT * FROM libros WHERE ISBN=$isbn")->fetchAll(PDO::FETCH_NAMED);;
	}
	
	
	/*
	 * USUARIOS
	 */
	 
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
	
	function userExists2($username, $password){
		return $this->conexion->queryExist("SELECT * FROM usuarios WHERE username='$username' AND password='$password' ");
	}
	
	/** Devuelve un PDOStatement con la información del usuario */
	function userGet($username){
		return $this->conexion->query("SELECT * FROM usuarios WHERE username='$username' ");
	}
	
	/** Agrega un nuevo usuario a la base de datos */
	function userCreate($username, $password, $_){
		
	}
	
	/** Modifica los datos de un usuario de la base de datos */
	function userUpdate($username, $password, $_){
		
	}
	
	
	
	//Sesiones de usuario
	
	
	/** Checkea si las credenciales locales corresponden a una sesión de usuario.
	 * En caso de que así sea retorna los datos del usuario (Array), caso contrario devuelve NULL. 
	 */
	function userGetLogin(){
		if (session_status() == PHP_SESSION_NONE) session_start();
		if (isset($_SESSION['username'])){
			$info = $this->userGet($_SESSION['username']);
			if ($info->rowCount() == 1)	return $info->fetch(PDO::FETCH_ASSOC);
			else	return;
		}else{
			return;
		}
	}
	
	/** Guarda la información de la sesión del usuario*/
	function userSaveLogin($username){
		if (session_status() == PHP_SESSION_NONE) session_start();
        $_SESSION['username'] = $username;
	}
	
	/** Elimina la información de la sesión del usuario */
	function userRemoveLogin(){
		if (session_status() == PHP_SESSION_NONE) session_start();
		session_destroy();
	}
	
	
	//OTROS
	
	/** Hace una consulta libre y devuelve el resultado */
	function rawQuery($statement){
		return $this->conexion->query($statement);
	}
	
}

?>



