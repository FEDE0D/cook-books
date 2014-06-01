<?php
    include_once 'database.php';
	
	if ($user = Users::userExists2($_POST['username'], $_POST['password'])){//es un usuario real, registrar sesión
		Users::saveLogin($user);
		header( 'Location: ./' ) ;
	}else{//no es un usuario existente
		header( 'Location: relogin.php?err=true' ) ;
	}
?>