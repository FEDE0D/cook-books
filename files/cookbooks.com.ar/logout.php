<?php
    include_once('database.php');
	$USERS = new Users;
	
	$USERS->removeLogin();
	
	header( 'Location: ./' ) ;
?>