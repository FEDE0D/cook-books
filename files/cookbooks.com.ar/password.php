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
        	<br>
        	<br>
            <div class="row">
                <div class="col-md-2">
                	
                </div>
                
                <div class="col-md-8">
                	
                	<div class="panel panel-primary">
				                  <div class="panel-heading">Contraseña </div>
				                	<table class="table">
				
										<form class="form-inline" role="form">
											
											  <div class="form-group">
											  
											    <label class="sr-only" for="ejemplo_password_2">Contraseña</label>
											    <input type="password" class="form-control" id="ejemplo_password_1" 
											           placeholder="Actual">
											  </div>
											 <div class="form-group">
											    <label class="sr-only" for="ejemplo_password_2">Contraseña</label>
											    <input type="password" class="form-control" id="ejemplo_password_2" 
											           placeholder="Nueva ">
											  </div>
											 <div class="form-group">
											    <label class="sr-only" for="ejemplo_password_2">Contraseña</label>
											    <input type="password" class="form-control" id="ejemplo_password_2" 
											           placeholder="Repetir Nueva Contraseña ">
											  </div>
											 
											  <button type="submit" class="btn btn-default">Guardar Cambios </button>
											  <button type="submit" class="btn btn-default">Cancelar </button>
										</form>
												
									</table>
								</div>
					</div>
                <div class="col-md-2">
                	
                </div>
            </div>
        </div>
        <script src="bootstrap-3.1.1-dist/js/bootstrap.min.js"></script>
        <style type="text/css">
    	body{
    		background-image: url('website/img/azul.png');
    	}
    </style>
    </body>
</html>