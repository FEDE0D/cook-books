<?php 
	include_once('database.php');
	$USERS = new Users;
	
	
	$login = $USERS->getUserLogin();
	if ($login){
		Errors::error("Estás logueado!", "Por favor deslogueate y luego intenta registrarte de nuevo.");
	}
	
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
                                        <input type="username" class="form-control" id="username" name="username" placeholder="Nombre de usuario" autocomplete="off" required="required">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="email" class="col-md-3 control-label">E-mail</label>
                                    <div class="col-md-9">
                                        <input type="email" class="form-control" id="email" name="email" placeholder="Email" autocomplete="off" required="required">
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
                							username:{
												required: true,
								                minlength: 3,
								                remote: {
									                url: "ajax.php",
									                data:{
									                	type: 'user',
									                	action: 'NAME_AVAILABLE'
									                }
								                }
											},
											email:{
												required: true,
												remote: {
													url: "ajax.php",
													data:{
														type:'user',
														action:'EMAIL_AVAILABLE'
													}
												}
											},
										    password1: "required",
										    password2: {
										    	equalTo: "#password1"
											}
										},
										messages: {
								            username:{
								                remote: "Este nombre de usuario ya está registrado. Elige otro!"
								            },
								            email:{
								            	remote: "Este email ya está registrado. Ingresa otro!"
								            }
								        }
                					}
                				);
                				$(document).ready(
                					function(){
                						$("#registerForm").find("#username").focus();
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