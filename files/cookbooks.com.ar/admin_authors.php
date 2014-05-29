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
        <?php
        	//SOLO UN ADMINISTRADOR PUEDE VER ESTA PAGINA
        	$Users = new Users;
			$user = $Users->getUserLogin();
			if (!$user || !$user->getIsAdministrator()){
				Errors::error("Sin privilegios", "No tienes privilegios para ver esta pagina!");
			}
        ?>
        <?php
        	//Guardo el id del autor activo, muestro los datos de ese autor y preparo para actualizar.
        	//Guardo la opcion de autor nuevo, muestro un formulario vacio y preparo para agregar.
			$ID_ACTIVE = -1;		if (isset($_REQUEST['id'])) $ID_ACTIVE = $_REQUEST['id'];
			$NUEVO_AUTOR = false;	if (isset($_REQUEST['new'])) $NUEVO_AUTOR = $_REQUEST['new'];
        ?>
        <div class="container-fluid text-center">
            <div class="row">
                <div class="col-md-4">
                	<div class="panel panel-default">
                		<div class="panel-heading">Autores registrados</div>
                		<div class="panel-body" style="max-height: 475px; overflow-y: scroll;">
                			<div class="list-group text-left">
                				<?php
                					//Obtener autores
                					$autores = Authors::getAuthors();
									foreach ($autores as $key => $value) {
										?>
										<!-- <a href="admin_authors.php?id=xxx" class="list-group-item active?">Nombre Apellido</a> -->
										<a href="admin_authors.php?id=<?php echo $value->getID(); ?>" class="list-group-item <?php if ($ID_ACTIVE==$value->getID()) echo 'active' ?>"><?php echo $value->getApellidoNombre(); ?></a>
										<?php
									}
                				?>
                			</div>
                		</div>
                		<div class="panel-footer"><a class="btn btn-sm btn-default" onclick="alert('nuevo autor')">Nuevo autor</a></div>
                	</div>
                </div>
                <?php
                	//Obtengo el autor pasado como parametro, NULL si no existe.
                	$AUTOR = Authors::getAuthor($ID_ACTIVE);
                ?>
                <div class="col-md-8 <?php echo $AUTOR? '':'hidden'?>">
                	<div class="panel panel-default">
                		<div class="panel-heading">Autor: <strong><?php echo $AUTOR->getNombreApellido(); ?></strong></div>
                		<div class="panel-body">
                			<div class="">
	                			<form id="author_form" role="form" class="">
	                				<input id="auth_ID" type="hidden" value="<?php echo $AUTOR->getID(); ?>" />
	                				<div class="form-group">
	                					<label for="auth_name" class="pull-left">Nombre</label>
	                					<input class="form-control" id="auth_name" type="text" placeholder="Nombre del autor" value="<?php echo $AUTOR->getNombre(); ?>"/>
	                				</div>
	                				<div class="form-group">
	                					<label for="auth_lastname" class="pull-left">Apellido</label>
	                					<input class="form-control" id="auth_lastname" type="text" placeholder="Apellido del autor" value="<?php echo $AUTOR->getApellido(); ?>"/>
	                				</div>
	                				<div class="form-group">
	                					<label for="auth_birthdate" class="pull-left">Fecha de nacimiento</label>
	                					<input class="form-control" id="auth_birthdate" type="date" placeholder="dd/mm/aaaa" value="<?php echo $AUTOR->getFechaNacimiento(); ?>"/>
	                				</div>
	                				<div class="form-group">
	                					<label for="auth_birthplace" class="pull-left">Lugar de nacimiento</label>
	                					<input class="form-control" id="auth_birthplace" type="text" placeholder="Ciudad/País de nacimiento" value="<?php echo $AUTOR->getLugarNacimiento(); ?>"/>
	                				</div>
	                				<a id="btn_booksby" class="btn btn-sm btn-info pull-left" onclick="seeAuthorsBooks()" >Ver libros</a>
	                				<a id="btn_save" class="btn btn-sm btn-default" onclick="saveAuthor()" data-loading-text="Guardando...">Guardar</a>
	                				<a id="btn_delete" class="btn btn-sm btn-warning pull-right" onclick="deleteAuthor()" >Borrar</a>
	                				
	                				<?php if ($AUTOR){ ?>
		                				<script>
		                					/** Envia los datos del formulario para ser actualizados*/
		                					function saveAuthor(){
		                						$('#btn_save').button('loading');
		                						$.ajax({
													url:"ajax.php",
													type:"POST",
													data:{
														type: "author",
														action: "UPDATE",
														auth_id: $('#author_form').find('#auth_ID').val(),
														auth_nombre: $('#author_form').find('#auth_name').val(),
														auth_apellido: $('#author_form').find('#auth_lastname').val(),
														auth_fecha_n: $('#author_form').find('#auth_birthdate').val(),
														auth_lugar_n: $('#author_form').find('#auth_birthplace').val()
													},
													success:function(data){
														if (data=='false'){
															$('#error_alert').text('Error al guardar los cambios\nPor favor intente nuevamente.');
															$('#error_alert').removeClass("hidden");
														}else{
															$('#error_alert').text('');
															$('#error_alert').addClass("hidden");
															location.reload();
														}
														$('#btn_save').button('reset');
													}
												});
		                					}
		                				</script>
	                				<?php } ?>
	                			</form>
	                			<p><div id="error_alert" class="alert alert-warning hidden"></div></p>
                			</div>
                		</div>
                		<div class="panel-footer"></div>
                	</div>
                </div>
            </div>
        </div>
        <script src="bootstrap-3.1.1-dist/js/bootstrap.min.js"></script>
    </body>
</html>