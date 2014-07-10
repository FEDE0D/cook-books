<?php include_once('database.php'); ?>
<!DOCTYPE html>
<html lang="es">
    <head>
        <title>Cook-Book</title>
        <meta content="text/html"; charset="UTF-8"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="shortcut icon" href="website/favicon/1.png"/>
        <link href="bootstrap-3.1.1-dist/css/bootstrap_Cosmo.css" rel="stylesheet" media="screen">
        <script src="website/jquery-1.11.0.js"></script>
    </head>
    <body>
        <?php include_once('navigation.php'); ?>
        <div class="container-fluid text-center">
            <div class="row">
                <div class="col-md-2">
                	
                </div>
                <div class="col-md-8">
                	<div class="panel panel-primary">
                	<h1></h1>
                	
                	<form action="send.php" method="post">
                		
                		<input type="text" name="asunto" /> <br/> <br/>
                		
                		
                		<div class="row">
		              
                		<textarea name="mensaje"> </textarea>  <br/> <br/>
                		</div>
                		
                		
                		<input type="submit" value="Enviar Correo"/>
                	
                	</form>
                	
                	</div>
                </div>
                <div class="col-md-2">
                	
                </div>
            </div>
        </div>
        <script src="bootstrap-3.1.1-dist/js/bootstrap.min.js"></script>
        <style type="text/css">
    	body{
    		background-image: url('website/img/Cake_cake_cupcake.png');
    	}
    </style>
    </body>
</html>