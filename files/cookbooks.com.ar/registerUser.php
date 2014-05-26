<?php

	/*
	 * TODO: 	Cambiar los mensajes de error a mensajes de error en la pagina de registro.
	 * 			O mejor, hacer la validación con ajax antes de enviar el formulario?
	 */
			

    include_once("database.php");
	$USERS = new Users;
	
    if ($USERS->getUserLogin()){
    	//ERROR, ya está logueado
    	header( 'Location: ./' ) ;
    }
    if (isset($_POST['username']) && $_POST['username']!='' && isset($_POST['email']) && isset($_POST['password1']) && isset($_POST['password2']) ){
    	$username = $_POST['username'];
		$email = $_POST['email'];
		$password1 = $_POST['password1'];
		$password2 = $_POST['password2'];
		
		if (!$USERS->userExists($username)){
			if (!$USERS->emailExists($email)){
				if ($password1==$password2){
					if ($newUser = $USERS->userCreate($username, $password1, $email)){
						//SUCCESS, usuario creado exitosamente, login usuario
						$USERS->saveLogin($newUser);
						header( 'Location: ./' ) ;
					}else{
						echo "<p>Error: al crear el usuario.</p>";
						//header('Refresh: 2; URL=register.php');
					}
					
				}else{
					echo "<p>Error: las contraseñas no coinciden</p>";
					header('Refresh: 2; URL=register.php');
				}
			}else{
				echo "<p>Error: el email '$email' ya está registrado</p>";
				header('Refresh: 2; URL=register.php');
			}
		}else{
			echo "<p>Error: el nombre de usuario '$username' ya está registrado</p>";
			header('Refresh: 2; URL=register.php');
		}
		
    }else{
    	echo "<p>Error: faltan parámetros o son erroneos</p>";
		header('Refresh: 2; URL=register.php');
    }
    
?>