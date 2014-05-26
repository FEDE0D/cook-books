<?php
    include_once 'database.php';
    
	$USERS = new Users;
	
	if ($user = $USERS->userExists2($_POST['username'], $_POST['password'])){//es un usuario real, registrar sesión
		$USERS->saveLogin($user);
		header( 'Location: ./' ) ;
	}else{//no es un usuario existente
		header( 'Location: relogin.php?err=true' ) ;
	}
?>