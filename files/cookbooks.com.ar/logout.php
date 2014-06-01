<?php
    include_once('database.php');
	
	Users::removeLogin();
	
	header( 'Location: ./' ) ;
?>