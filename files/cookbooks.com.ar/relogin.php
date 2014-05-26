<?php 
	include_once('database.php');
	$USERS = new Users;
	
	
	$login = $USERS->getUserLogin();
	if ($login) header("location: ./");
	
?>
<!DOCTYPE html>
<html lang="es">
    <head>
        <title>Cook-Book</title>
        <meta content="text/html"; charset="UTF-8"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="bootstrap-3.1.1-dist/css/bootstrap_Cosmo.css" rel="stylesheet" media="screen">
        <script src="http://code.jquery.com/jquery-1.11.0.js"></script>
        <?php include_once('database.php'); ?>
    </head>
    <body>
        <?php include_once('navigation.php'); ?>
        <div class="container-fluid text-center">
            <div class="row">
                <div class="col-md-3">
                	
                </div>
                <div class="col-md-6">
                	<div class="panel panel-primary">
                		<div class="panel-heading">
                			Ingresa al sitio
                		</div>
                		<form class="form-horizontal" id="formLogin" action="login.php" method="post">
	                		<div class="panel-body">
	                			<?php if (isset($_REQUEST['err'])){?>
	                				<div class="alert alert-warning">El nombre de usuario o contraseña no es válido.<br /> Por favor ingresa nuevamente tus datos.</div>
	                			<?php } ?>
	                			<div class="form-group">
		                			<label for="username" class="col-md-3 control-label">Usuario</label>
		                			<div class="col-md-9">
		                				<input name="username" id="username" type="text" placeholder="Username" class="form-control">
		                			</div>
	                			</div>
	                			<div class="form-group">
		                			<label for="password" class="col-md-3 control-label">Contraseña</label>
		                			<div class="col-md-9"> 
					                	<input name="password" id="password" type="password" placeholder="Password" class="form-control">
					                </div>
				                </div>
	            			</div>
	            			<div class="panel-footer">
	            				<button type="submit" class="btn btn-primary">Ingresar</button><br /><br />
	            				<a href="register.php" class="btn btn-default">O registrate ahora!</a>
	            			</div>
            			</form>
        			</div>
                </div>
                <div class="col-md-3">
                	
                </div>
            </div>
        </div>
        <script src="bootstrap-3.1.1-dist/js/bootstrap.min.js"></script>
    </body>
</html>