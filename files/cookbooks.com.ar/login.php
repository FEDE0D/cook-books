<?php
    include_once 'database.php';
    
	$usos = new DataBase;
	$exists = $usos->userExists2($_POST['username'], $_POST['password']);
	
	if ($exists){//es un usuario real, registrar sesión
		$usos->userSaveLogin($_POST['username']);
		header( 'Location: index.php' ) ;
	}else{//no es un usuario existente
		//llevar a pagina de re-login
		header( 'Location: index.php' ) ;
	}
?>