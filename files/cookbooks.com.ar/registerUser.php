<?php

	/*
	 * TODO: 	Cambiar los mensajes de error a mensajes de error en la pagina de registro.
	 * 			O mejor, hacer la validación con ajax antes de enviar el formulario?
	 */
			

    include_once("database.php");
	
    if (Users::getUserLogin()){
    	//ERROR, ya está logueado
    	header( 'Location: ./' ) ;
    }
    if (isset($_POST['username']) && $_POST['username']!='' && isset($_POST['email']) && isset($_POST['password1']) && isset($_POST['password2']) ){
    	$username = $_POST['username'];
		$email = $_POST['email'];
		$password1 = $_POST['password1'];
		$password2 = $_POST['password2'];
		
		if (!Users::userExists($username)){
			if (!Users::emailExists($email)){
				if ($password1==$password2){
					if ($newUser = Users::userCreate($username, $password1, $email)){
						//SUCCESS, usuario creado exitosamente, login usuario
						Users::saveLogin($newUser);
						header( 'Location: ./' ) ;
					}else{
						Errors::error("Error de registro", "Error: al crear el usuario.");
					}
					
				}else{
					Errors::error("Error de registro", "Error: las contraseñas no coinciden");
					header('Refresh: 2; URL=register.php');
				}
			}else{
				Errors::error("Error de registro", "Error: el email '$email' ya está registrado");
				header('Refresh: 2; URL=register.php');
			}
		}else{
			Errors::error("Error de registro", "Error: el nombre de usuario '$username' ya está registrado");
			header('Refresh: 2; URL=register.php');
		}
		
    }else{
    	Errors::error("Error de registro", "Error: faltan parámetros o son erroneos");
		header('Refresh: 2; URL=register.php');
    }
    
?>