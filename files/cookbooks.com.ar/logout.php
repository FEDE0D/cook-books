<?php
    include_once('database.php');
	$usos = new DataBase;
	
	$usos->userRemoveLogin();
	
	header( 'Location: index.php' ) ;
?>