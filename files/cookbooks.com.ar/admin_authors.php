<?php include_once('database.php'); ?>
<?php
	//SOLO UN ADMINISTRADOR PUEDE VER ESTA PAGINA
	$user = Users::getUserLogin();
	if (!$user || !$user->getIsAdministrator()){
		Errors::error("Sin privilegios", "No tienes privilegios para ver esta pagina!");
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
        <?php
        	//TODO: Limpiar este codigo!
        	//Guardo el id del autor activo, muestro los datos de ese autor y preparo para actualizar.
        	//Guardo la opcion de autor nuevo, muestro un formulario vacio y preparo para agregar.
			$ID_ACTIVE = -1;		if (isset($_REQUEST['id'])) $ID_ACTIVE = $_REQUEST['id'];
			$NUEVO_AUTOR = FALSE;	if (isset($_REQUEST['new'])) $NUEVO_AUTOR = TRUE;
			if ($NUEVO_AUTOR) $ID_ACTIVE = -1;
			
	    	//Obtengo el autor pasado como parametro, NULL si no existe.
	    	$AUTOR = NULL;
	    	if ($ID_ACTIVE > 0){
	    		$AUTOR = Authors::getAuthor($ID_ACTIVE);
	    	}
	    ?>
        <div class="container-fluid text-center">
            <div class="row">
                <div class="col-md-4">
                	<div class="panel panel-default">
                		<div class="panel-heading">Autores registrados</div>
                		<div class="well well-sm">
            				<input id="filter_input" type="text" class="form-control" onkeyup="filter()" placeholder="Buscar autor..." />
            			</div>
                		<div class="panel-body" style="max-height: 475px; min-height:475px; overflow-y: scroll;">
                			<div id="authors_list" class="list-group text-left">
                				<?php
                					//Obtener autores
                					$autores = Authors::getAuthors();
									foreach ($autores as $key => $value) {
										$books = $value->getBooks();
										?>
										<!-- <a href="admin_authors.php?id=xxx" class="list-group-item active?">Nombre Apellido</a> -->
										<a id="auth_<?php echo $value->getID(); ?>" href="admin_authors.php?id=<?php echo $value->getID(); ?>" class="list-group-item <?php if ($ID_ACTIVE==$value->getID()) echo 'active' ?>">
											<?php echo $value->getApellido().', '.$value->getNombre(); ?>
											<div style="display: none;">
												<?php
													echo strtolower($value->getNombreApellido());
												?>
											</div>
											<span class="badge" title="Cantidad de libros"><?php echo count($books); ?></span>
										</a>
										<?php
									}
                				?>
                			</div>
                			<script type="text/javascript">
                				var autores = $("#authors_list");
                				var filter_input = $("#filter_input");
                				
                				function filter(){
                					var text = filter_input.val().toLowerCase();
                					
                					if ($.trim(text)==""){
                						autores.children().each(function(i){
                							$(this).removeClass("hidden");
                						});
                					}else{
                						autores.children().each(function(i){
                							if (!$(this).hasClass("active"))
                								$(this).addClass("hidden");
                						});
                						autores.find(":contains("+text+")").each(function(i){
                							$(this).removeClass("hidden");
                						});
                						
                					}
                				}
                				
                			</script>
                		</div>
                		<div class="panel-footer">
                			<div class="container-fluid">
	                			<a class="btn btn-sm btn-default pull-left" onclick="window.location.href='admin_authors.php?new=1'">Nuevo autor</a>
	                			<?php if($AUTOR && count($AUTOR->getBooks())<=0){ ?>
	                				<a id="btn_delete" class="btn btn-sm btn-warning pull-right" data-loading-text="Borrando..." onclick="deleteAuthor()" >Borrar</a>
	                			<?php } ?>
                			</div>
                		</div>
                	</div>
                </div>
                
                <div class="col-md-8 <?php if (!$AUTOR && !$NUEVO_AUTOR){ echo "hidden";} ?>">
                	<div class="panel panel-default">
                		<div class="panel-heading">Autor: 
                			<strong>
                				<?php
	                				if ($AUTOR) echo $AUTOR->getNombreApellido();
									elseif($NUEVO_AUTOR) echo "Nuevo Autor"; 
                				?>
                			</strong>
                		</div>
                		<div class="panel-body" style="max-height: 475px; min-height:475px;">
                			<div class="">
	                			<form id="author_form" role="form" class="">
	                				<input id="auth_ID" type="hidden" value="<?php if ($AUTOR) echo $AUTOR->getID(); ?>" />
	                				<div class="form-group">
	                					<label for="auth_name" class="pull-left">Nombre</label>
	                					<input class="form-control" id="auth_name" type="text" placeholder="Nombre del autor" value="<?php if ($AUTOR) echo $AUTOR->getNombre(); ?>"/>
	                				</div>
	                				<div class="form-group">
	                					<label for="auth_lastname" class="pull-left">Apellido</label>
	                					<input class="form-control" id="auth_lastname" type="text" placeholder="Apellido del autor" value="<?php if ($AUTOR) echo $AUTOR->getApellido(); ?>"/>
	                				</div>
	                				<div class="form-group">
	                					<label for="auth_birthdate" class="pull-left">Fecha de nacimiento</label>
	                					<input class="form-control" id="auth_birthdate" type="date" placeholder="dd/mm/aaaa" value="<?php if ($AUTOR) echo $AUTOR->getFechaNacimiento(); ?>"/>
	                				</div>
	                				<div class="form-group">
	                					<label for="auth_birthplace" class="pull-left">Lugar de nacimiento</label>
	                					<input class="form-control" id="auth_birthplace" type="text" placeholder="Ciudad/País de nacimiento" value="<?php if ($AUTOR) echo $AUTOR->getLugarNacimiento(); ?>"/>
	                				</div>
	                				
	                				<script>
	                					<?php if ($AUTOR){ ?>
		                					/** Envia los datos del formulario para ser actualizados*/
		                					function saveAuthor(){
		                						if (!validateAutor()) return;
		                						$('#btn_save').button('loading');
		                						$('#error_alert').text(''); $('#error_alert').addClass("hidden");
		                						$.ajax({
													url:"ajax.php",
													type:"POST",
													data:{
														type: "AUTHOR",
														data:JSON.stringify({
															action: "UPDATE",
															id: $('#author_form').find('#auth_ID').val(),
															nombre: $('#author_form').find('#auth_name').val(),
															apellido: $('#author_form').find('#auth_lastname').val(),
															fecha_n: $('#author_form').find('#auth_birthdate').val(),
															lugar_n: $('#author_form').find('#auth_birthplace').val()
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
		                					
		                					/** Redirecciona a la pagina de búsqueda usando el nombre del autor como query. */
		                					function seeAuthorsBooks(){
		                						window.location.href = "search.php?query=&authID=<?php echo $AUTOR->getID(); ?>";
		                					}
		                					
		                					/** Borra el autor */
		                					function deleteAuthor(){
		                						if (!confirm("De verdad desea eliminar este autor?")) return;
		                						$('#btn_delete').button('loading');
		                						$('#error_alert').text(''); $('#error_alert').addClass("hidden");
		                						$.ajax({
		                							url:"ajax.php",
		                							type:"POST",
		                							data:{
		                								type:"AUTHOR",
		                								data:JSON.stringify({
		                									action: "REMOVE",
		                									id:"<?php echo $AUTOR->getID(); ?>"
		                								})
		                							},
		                							success:function(data){
		                								var resp = $.parseJSON(data);
		                								if (resp.ok){
		                									alert("El autor fue eliminado correctamente!");
		                									window.location.href = "admin_authors.php";
		                								}else{
		                									$('#error_alert').html('Error al eliminar autor. Por favor intente nuevamente.<br />'+resp.message);
															$('#error_alert').removeClass("hidden");
		                								}
		                								$('#btn_delete').button('reset');
		                							}
		                						});
		                					}
	                					<?php } ?>
	                					<?php if ($NUEVO_AUTOR){ ?>
	                						/** Agrega un nuevo actor a la base de datos */
                							function saveAuthor(){
                								if (!validateAutor()) return;
                								$('#btn_save').button('loading');
                								$('#error_alert').text(''); $('#error_alert').addClass("hidden");
                								
		                						$.ajax({
													url:"ajax.php",
													type:"POST",
													data:{
														type: "AUTHOR",
														data: JSON.stringify({
															action: "CREATE",
															id: "",
															nombre: $('#author_form').find('#auth_name').val(),
															apellido: $('#author_form').find('#auth_lastname').val(),
															fecha_n: $('#author_form').find('#auth_birthdate').val(),
															lugar_n: $('#author_form').find('#auth_birthplace').val()
														})
													},
													success:function(data){
														var resp = $.parseJSON(data);
														if (resp.ok){
															if ($.isNumeric(resp.id_new)){
																$('#error_alert').text('');
																$('#error_alert').addClass("hidden");
																window.location.href = "admin_authors.php?id="+resp.id_new;
															}else{
																$('#error_alert').html('Error al agregar el nuevo autor. Por favor intente nuevamente.<br />'+resp.message);
																$('#error_alert').removeClass("hidden");
															}
														}else{
															$('#error_alert').html('Error al guardar los cambios. Por favor intente nuevamente.<br />'+resp.message);
															$('#error_alert').removeClass("hidden");
														}
														$('#btn_save').button('reset');
													}
												});
                							}
                						<?php } ?>
	                				</script>
	                			</form>
	                			<p><div id="error_alert" class="alert alert-warning hidden"></div></p>
                			</div>
                		</div>
                		<div class="panel-footer">
                			<div class="container-fluid">
	                			<?php if ($AUTOR){ ?>
	                			<a id="btn_booksby" class="btn btn-sm btn-info pull-right" onclick="seeAuthorsBooks()" >Ver libros</a>
	                			<?php } ?>
	            				<a id="btn_save" class="btn btn-sm btn-default" onclick="saveAuthor()" data-loading-text="Guardando...">Guardar</a>
            				</div>
                		</div>
                	</div>
                </div>
            </div>
        </div>
        <script src="bootstrap-3.1.1-dist/js/bootstrap.min.js"></script>
        <script>
    	<?php if ($NUEVO_AUTOR){?>
    		//Poner en foco el campo de nombre, solo cuando esta en modo NUEVO AUTOR
        	$('#auth_name').focus();
        <?php } ?>
        	
        	/** Valida los campos y retorna true/false si el formulario es valido*/
        	function validateAutor(){
        		var nombre	= $('#author_form').find('#auth_name');
        		var apellido= $('#author_form').find('#auth_lastname');
        		var fecha	= $('#author_form').find('#auth_birthdate');
        		var lugar	= $('#author_form').find('#auth_birthplace');
        		
        		if (nombre.val().trim()==""){
        			alert("El nombre es obligatorio!");
        			return false;
        		}else if (apellido.val().trim()==""){
        			alert("El apellido es obligatorio!");
        			return false;
        		}
        		return true;
        	}
        </script>
        <style type="text/css">
        	body{
        		background-image: url('website/img/1656182.png');
        	}
        </style>
    </body>
</html>
