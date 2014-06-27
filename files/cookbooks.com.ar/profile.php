<?php include_once('database.php'); ?>
<?php
	//SOLO UN ADMINISTRADOR PUEDE VER ESTA PAGINA
	$user = Users::getUserLogin();
	if (!$user ){
		Errors::error("Sin privilegios", "No tienes privilegios para ver esta pagina!");
	}
	else{
		if ($user->getIsAdministrator()){
			if (isset($_REQUEST['u'])){
			 $username = $_REQUEST['u'];
			 $USER = Users::getUser($username);
			}
			else Errors::error("El administrador no puede ver su perfil");
		}
		
	
		else{
			$USER = NULL ;
			 $USER = Users::getUserLogin();
	
		}
	}
?>
                                                                             
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
                
                <div class="col-md-8 ">
                	<div class="panel panel-info">

                		<div class="panel-heading">Perfil de Usuario: 

                		</div>
                		
                		<div class="panel panel-default">
						  
				          <div class="panel-body" style="max-height: 500px; min-height:750px;">
                			<div class="">
	                			<form id="user_form" role="form" class="">
	                				
									<div class="form-group">
	                					<label for="auth_Username" class="pull-left">Username</label>	                	
	                					<input class="form-control" id="username" type="text" placeholder="Nombre del usuario" value="<?php  echo $USER->getUsername() ?>"disabled> 
	                				</div>
	                				<div class="form-group">
	                					<label for="auth_Nombre" class="pull-left">Nombre</label>
	                					<input class="form-control" id="name" type="text" placeholder="Nombre del usuario" value="<?php  echo $USER->getNombre(); ?>"/>
	                				</div>
	                				
	                				<div class="form-group">
	                					<label for="auth_Apellido" class="pull-left">Apellido</label>
	                					<input class="form-control" id="surname" type="text" placeholder="Apellido del usuario" value="<?php  echo $USER->getApellido(); ?>"/>
	                				</div>
	                				
	                				<div class="form-group">
	                					<label for="auth_Direccion" class="pull-left">Direccion</label>
	                					<input class="form-control" id="address" type="text" placeholder="Direccion" value="<?php echo $USER->getDireccion(); ?>"/>
	                				</div>
	                				
	                				<div class="form-group">
	                					<label for="auth_birthdate" class="pull-left">Email</label>
	                					<input class="form-control" id="email" type="text" placeholder="Email" value="<?php echo $USER->getEmail(); ?>"/>
	                				</div>
	                				
	                				<div class="form-group">
	                					<label for="auth_birthplace" class="pull-left">Telefono</label>
	                					<input class="form-control" id="phone" type="text" placeholder="Telefono" value="<?php  echo $USER->getTelefono(); ?>"/>
	                				</div>
	                				
	                				<div class="form-group">
	                					<label for="auth_birthplace" class="pull-left">Fecha de Alta</label>
	                					<input class="form-control" id="Date_Added" type="date" placeholder="dd/mm/aaaa" value="<?php  echo $USER->getFechaAlta(); ?>"disabled> 
	                				</div>
	                				
	                				<div class="form-group">
	                					<label for="auth_birthplace" class="pull-left">Fecha de Nacimiento</label>
	                					<input class="form-control" id="Date_of_Birth" type="date" placeholder="dd/mm/aaaa" value="<?php echo $USER->getFechaNacimiento(); ?>"/>
	                				</div>
	                				
	                				  <script>
	                					function saveUSER(){
		                						$('#btn_save').button('loading');
		                						$('#error_alert').text(''); $('#error_alert').addClass("hidden");
		                						$.ajax({
													url:"ajax.php",
													type:"POST",
													data:{
														type: "USER",
														data:JSON.stringify({
															action: "UPDATE",
															username: $('#user_form').find('#username').val(),
															nombre: $('#user_form').find('#name').val(),
															apellido: $('#user_form').find('#surname').val(),
															direccion: $('#user_form').find('#address').val(),
															email: $('#user_form').find('#email').val(),
															telefono: $('#user_form').find('#phone').val(),
     														fecha_nac: $('#user_form').find('#Date_of_Birth').val()
														})
													},
													success:function(data){
														var resp = $.parseJSON(data);
														
														if (resp.ok){
															$('#error_alert').text('');
															$('#error_alert').addClass("hidden");
															location.reload();
														}else{
															$('#error_alert').html('Error al guardar los cambios. Por favor intente nuevamente.<br />'+resp.message);
															$('#error_alert').removeClass("hidden");
														}
														$('#btn_save').button('reset');
													}
												});
		                					}
		                					
		                					
		                			  </script>
		                					   					      
	                			</form>
	                			
	                			<p><div id="error_alert" class="alert alert-warning hidden"></div></p>
	                			
	                			<div class="row">
					                <div class="col-md-2">
				                			 <button type="button" onclick="window.location.href='password.php'" class="btn btn-default btn-xs" >
											    <span class="glyphicon glyphicon-lock" ></span>  Cambiar contraseña
											 </button>
	                			 	</div>
	                			</div>
	                			
                			</div>
                		</div>
                		
                		<div class="panel-footer">
                				<a id="btn_save" class="btn btn-sm btn-default"  onclick="saveUSER()" data-loading-text="Guardando...">Guardar</a>
			     		</div>
                </div>
            </div>
        </div>
        <script src="bootstrap-3.1.1-dist/js/bootstrap.min.js"></script>
     	
     	<style>
        	body{
        		background-image: url('website/img/azul.png');
        	}
        </style>
     		
     	

    </body>
</html>
