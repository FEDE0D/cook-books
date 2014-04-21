<?php 
	include_once('database.php');
	$usos = new DataBase;
	
	
	$login = $usos->userGetLogin();
	if (isset($login)) header("location: ./");
	
?>

<!DOCTYPE html>
<html lang="es">
    <head>
        <title>Cook-Book</title>
        <meta content="text/html"; charset="UTF-8"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="bootstrap-3.1.1-dist/css/bootstrap_Cosmo.css" rel="stylesheet" media="screen">
        <script src="http://code.jquery.com/jquery-1.11.0.js"></script>
        <script src="http://ajax.aspnetcdn.com/ajax/jquery.validate/1.12.0/jquery.validate.min.js"></script>
        <script src="http://ajax.aspnetcdn.com/ajax/jquery.validate/1.12.0/localization/messages_es.js"></script>
    </head>
    <body>
    	<?php include_once 'navigation.php'; ?>
    	<div class="container-fluid text-center">
            <div class="row">
                <div class="col-md-3">
                	
                </div>
                
                <div class="col-md-6">
                	<div class="panel panel-primary">
                		<div class="panel-heading">
                			Registrate!
                		</div>
                		<div class="panel-body">
                			
                			<form id="registerForm" class="form-horizontal" action="registerUser.php" method="post">
                				<div class="form-group">
                                    <label for="username" class="col-md-3 control-label">Usuario</label>
                                    <div class="col-md-9">
                                        <input type="username" class="form-control" id="username" name="username" placeholder="Nombre de usuario" required="required">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="password" class="col-md-3 control-label">Contraseña</label>
                                    <div class="col-md-9">
                                        <input type="password" class="form-control" id="password1" name="password1" placeholder="Contraseña" required="required">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="password_repeat" class="col-md-3 control-label">Repita la contraseña</label>
                                    <div class="col-md-9">
                                        <input type="password" class="form-control" id="password2" name="password2" placeholder="Contraseña" required="required">
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                	<button type="submit" class="btn btn-primary"><span class="glyphicon glyphicon-pencil"></span>&nbsp;Registrarse</button>
                                </div>
                			</form>
                			<script>
                				$("#registerForm").validate( 
                					{
                						lang: 'es',
                						rules: {
										    password1: "required",
										    password2: {
										    	equalTo: "#password1"
											}
										}
                					}
                				);
                			</script>
                		</div>
                	</div>
            	</div>
            	
            	<div class="col-md-3"></div>
            	
        	</div>
    	</div>
    	
    	
    	<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
        <script src="http://code.jquery.com/jquery-1.11.0.js"></script>
        <!-- Include all compiled plugins (below), or include individual files as needed -->
        <script src="bootstrap-3.1.1-dist/js/bootstrap.min.js"></script>
        <script src="datatables/js/jquery.dataTables.js"></script>
        <script src="scripts.js"></script>
    </body>
</html>