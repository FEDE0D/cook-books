<?php
    include_once 'database.php';
	
	if ($user = Users::userExists2($_POST['username'], $_POST['password'])){//es un usuario real, registrar sesión
		if ($user->getEnabled()){
			Users::saveLogin($user);
			header( 'Location: ./' ) ;
		}else{
			Errors::error("Usuario no admitido", "Has sido dado de baja en el sitio!<br />Por favor contactese con el administrador del sitio");
		}
	}else{//no es un usuario existente
		header( 'Location: relogin.php?err=true' ) ;
	}
?>