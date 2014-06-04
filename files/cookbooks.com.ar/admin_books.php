<?php include_once('database.php'); ?>
<?php
	//SOLO UN ADMINISTRADOR PUEDE VER ESTA PAGINA
	$Users = new Users;
	$user = $Users->getUserLogin();
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
        <link href="bootstrap-3.1.1-dist/css/bootstrap_Cosmo.css" rel="stylesheet" media="screen">
        <script src="website/jquery-1.11.0.js"></script>
        <?php include_once('database.php'); ?>
        
        <!-- <script language="javascript" src="website/subir/js/jquery-1.3.1.min.js"></script>
		<script language="javascript" src="website/subir/js/AjaxUpload.2.0.min.js"></script>
		<script language="javascript">
			$(document).ready(function(){
				var button = $('#upload_button'), interval;
				new AjaxUpload('#upload_button', {
			        action: 'website/subir/upload.php',
					onSubmit : function(file , ext){
					if (! (ext && /^(jpg|png|jpeg|gif)$/.test(ext))){
						// extensiones permitidas
						alert('Error: Solo se permiten imagenes');
						// cancela upload
						return false;
					} else {
						button.text('Subiendo');
						this.disable();
					}
					},
					onComplete: function(file, response){
						button.text('Seleccionar tapa');
						// enable upload button
						this.enable();			
						// Agrega archivo a la lista
						$('#lista').appendTo('.files').text(file);
						
					}	
				});
			});
		</script>
		<link href="website/subir/style.css" rel="stylesheet" type="text/css" /> -->
    </head>
    <body>
        <?php include_once('navigation.php'); ?>
        <?php
        	//TODO: Limpiar este codigo!
        	//Guardo el id del autor activo, muestro los datos de ese autor y preparo para actualizar.
        	//Guardo la opcion de autor nuevo, muestro un formulario vacio y preparo para agregar.
			$ID_ACTIVE = -1;		if (isset($_REQUEST['id'])) $ID_ACTIVE = $_REQUEST['id'];
			$NUEVO_LIBRO = FALSE;	if (isset($_REQUEST['new'])) $NUEVO_LIBRO = TRUE;
			if ($NUEVO_LIBRO) $ID_ACTIVE = -1;
			
	    	//Obtengo el autor pasado como parametro, NULL si no existe.
	    	$LIBRO = NULL;
	    	if ($ID_ACTIVE > 0){
	    		$LIBRO = Books::getBook($ID_ACTIVE);
	    	}
	    ?>
        <div class="container-fluid text-center">
        	<p class="alert alert-info">
        		NOTAS:	
        		ERROR de AjaxUpload: no se debe poner javascript en el header. Además sobreescribir el metodo .ready() de JQuery hace que no funcione el dropdown del usuario.
        		Dejemos la subida de fotos para lo último.
        		Verificar que el ISBN del libro no exista en la BBDD al crear uno nuevo, o  avisar error?
        		En la modificacion verificar que no se pueda dejar a un libro sin autores (como minimo 1 tiene que tener).
        	</p> 
            <div class="row">
                <div class="col-md-4">
                	<div class="panel panel-default">
                		<div class="panel-heading">Libros registrados</div>
                		<div class="panel-body" style="max-height: 475px; min-height:475px; overflow-y: scroll;">
                			<div class="list-group text-left">
                				<?php
                					$libros = Books::getLibros();
                					foreach ($libros as $key => $value) { ?>
										<a id="libro_<?php echo $value->getISBN(); ?>" href="admin_books.php?id=<?php echo $value->getISBN(); ?>" class="list-group-item <?php if ($ID_ACTIVE==$value->getISBN()) echo 'active' ?>">
											<?php echo $value->getTitulo().', '.$value->getAutoresIDs(); ?>
										</a>
									<?php
                					}
                				?>
                			</div>
                		</div>
                		<div class="panel-footer">
                			<div class="container-fluid">
	                			<a class="btn btn-sm btn-default pull-left" onclick="window.location.href='admin_books.php?new=1'">Nuevo libro</a>
	                			<?php if($LIBRO){ ?>
	                				<a id="btn_delete" class="btn btn-sm btn-info pull-right" onclick="deleteAuthor()" >Borrar</a>
	                			<?php } ?>
                			</div>
                		</div>
                	</div>
                </div>
                
                <div class="col-md-8 <?php if (!$LIBRO && !$NUEVO_LIBRO){ echo "hidden";} ?>">
                	<div class="panel panel-default">
                		<div class="panel-heading">Libro:<strong><?php
                				if ($LIBRO) echo $LIBRO->getTitulo();
								elseif($NUEVO_LIBRO) echo "Nuevo Libro"; 
                				?></strong>
                		</div>
                		<div class="panel-body" style="max-height: auto; min-height:475px;">
                			<div class="">
	                			<form id="libro_form" role="form" class="" enctype="multipart/form-data">
	                				
	                				<div class="col-xs-3">
	                					<label for="libro_ISBN" class="pull-left">ISBN</label>
	                					<?php if ($LIBRO){ ?>
	                						<input class="form-control" id="libro_ISBN" type="text" placeholder="ISBN" disabled="true" value="<?php if ($LIBRO) echo $LIBRO->getISBN(); ?>" />
	                					<?php } if(!$LIBRO)	{ ?>
	                						<input class="form-control" id="libro_ISBN" type="text" placeholder="ISBN"  value="<?php if ($LIBRO) echo $LIBRO->getISBN(); ?>" />
	                					<?php } ?>
	                				</div>
	                				
	                				<div class="col-xs-8">
	                					<label for="libro_titulo" class="pull-left">Titulo</label>
	                					<input class="form-control" id="libro_titulo" type="text" placeholder="Titulo del libro" value="<?php if ($LIBRO) echo $LIBRO->getTitulo() ?>"/>
	                				</div>
	                				<div class="col-xs-5"  >
	                					<label for="libro_autor" class="pull-left">Autor/es</label></br>
	                					<?php if ($LIBRO){  ?>
		                					<div id="contiene2"  style="overflow-y: scroll; height: 178px; text-align: left; ">
			                					<?php
			                					$autores = Authors::getAuthors();
												foreach ($autores as $key => $value) {
													if ( in_array($value->getID(), $LIBRO->getAutoresIDs_arr()) ){?>
														<input id="libro_autor" type="checkbox" checked="true" value="<?php echo $value->getID()?>">
														<?php echo $value->getNombreApellido();?> 
													<?php 
													}else{ ?>
														<input id="libro_autor" type="checkbox" value="<?php echo $value->getID()?>"> <?php echo $value->getNombreApellido();?></br>
													<?php } ?>
												<?php } ?>
											</div>
	                					<?php }  if (!$LIBRO) { ?>
		                					<div id="contiene"  style="overflow-y: scroll; height: 178px; text-align: left">
			                					<?php
			                					$autores = Authors::getAuthors();
												foreach ($autores as $key => $value) {?>
													<input id="libro_autor" type="checkbox" value="<?php echo $value->getID()?>"> <?php echo $value->getNombreApellido(); ?></br>
			                					   
												<?php }  ?>
											</div>
										<?php } ?>
	                				</div>
	                				
	                				<div class="col-xs-3">
	                					<label for="libro_fecha" class="pull-left">Fecha del libro</label>
	                					<input class="form-control" id="libro_fecha" type="date" placeholder="dd/mm/aaaa" value="<?php if ($LIBRO) echo $LIBRO->getFecha(); ?>"/>
	                				</div>
	                				<div class="col-xs-3">
	                					<label for="libro_precio" class="pull-left">Precio</label>
	                					<input class="form-control" id="libro_precio" type="text" placeholder="Precio del libro" value="<?php if ($LIBRO) echo $LIBRO->getPrecio() ?>"/>
	                				</div>
	                			
	                				<div class="col-xs-3">
	                					<label for="libro_idioma" class="pull-left">Idioma</label>
	                					<input class="form-control" id="libro_idioma" type="text" placeholder="Idioma del libro" value="<?php if ($LIBRO) echo $LIBRO->getIdioma() ?>"/>
	                				</div>
	                			
	                				
	                				<div class="col-xs-3">
	                					<label for="libro_pag" class="pull-left">Paginas</label>
	                					<input class="form-control" id="libro_pag" type="text" placeholder="Cantidad de paginas" value="<?php if ($LIBRO) echo $LIBRO->getPaginas() ?>"/>
	                				</div>
	                				<div class="col-xs-6">
	                					<label for="libro_tags" class="pull-left">Etiquetas</label>
	                					<input class="form-control" id="libro_tags" type="text" placeholder="Tags" value="<?php if ($LIBRO) echo $LIBRO->getEtiquetas() ?>"/></br>
	                				</div>
	                					<div class="col-xs-11">
	                					<label for="libro_texto" class="pull-left">Descripcion</label>
	                					<input class="form-control" id="libro_texto" type="text" placeholder="Descripcion del libro" value="<?php if ($LIBRO) echo $LIBRO->getTexto() ?>"/>
	                				</div>
	                				<div class="col-xs-5" align="left">
	                					<table>
	                						<tr>
	                							<label>Tapa</label>
	                						</tr>
	                						<tr>
	                							<?php if ($LIBRO){?>
	                							<img src="books/img/tapas/<?php echo $LIBRO->getTapa(); ?>" class="img-rounded img-responsive" style="height: 80px;">
	                							<?php } ?>
	                						</tr>
	                						<tr>
	                							</br>
	                							<div id="upload_button">Seleccionar tapa</div>
												<ul id="lista">
												</ul>
	                						</tr>
	                						</table>
	                						
	                				</div>
	                				<script>
	                					<?php if ($LIBRO){ ?>
		                					/** Envia los datos del formulario para ser actualizados*/
		                					function saveLibro(){
		                						var autores="";
		                						$('#libro_form').find('#libro_autor:checked').each(
		                							function(i){
		                								autores+=$( this ).val()+",";
		                							}
		                							
		                						);
		                						alert(autores);
		                						return;
		                						$('#btn_save').button('loading');
		                						$.ajax({
													url:"ajax.php",
													type:"POST",
													data:{
														type: "libro",
														action: "UPDATE",
														libro_ISBN: $('#libro_form').find('#libro_ISBN').val(),
														libro_titulo: $('#libro_form').find('#libro_titulo').val(),
														libro_idioma: $('#libro_form').find('#libro_idioma').val(),
														libro_fecha: $('#libro_form').find('#libro_fecha').val(),
														libro_precio: $('#libro_form').find('#libro_precio').val(),
														libro_tags: $('#libro_form').find('#libro_tags').val(),
														libro_texto: $('#libro_form').find('#libro_texto').val(),
														libro_autor: autores;
														libro_pag: $('#libro_form').find('#libro_pag').val()
														
													},
													success:function(data){
														if(data=='true'){
															$('#error_alert').text('');
															$('#error_alert').addClass("hidden");
															location.reload();
														}else{
														
															$('#error_alert').text('Error al guardar los cambios\nPor favor intente nuevamente.\n');
															$('#error_alert').removeClass("hidden");
														}
														$('#btn_save').button('reset');
													}
												});
		                					}
		                					
		                					/** Redirecciona a la pagina de búsqueda usando el nombre del autor como query. */
		                					function seeAuthorsBooks(){
		                						window.location.href = "admin_authors.php";
		                					}
		                					
		                					/** Borra el autor */
		                					function deleteAuthor(){
		                						alert("AJAX:\ntype=author\naction=REMOVE\nAvisar errores");
		                					}
	                					<?php } ?>
	                					<?php if ($NUEVO_LIBRO){ ?>
	                						/** Agrega un nuevo LIBRO a la base de datos */
                							function saveLibro(){
                								$('#btn_save').button('loading');
		                						$.ajax({
													url:"ajax.php",
													type:"POST",
													data:{
														type: "libro",
														action: "NEW",
														libro_ISBN: $('#libro_form').find('#libro_ISBN').val(),
														libro_titulo: $('#libro_form').find('#libro_titulo').val(),
														libro_idioma: $('#libro_form').find('#libro_idioma').val(),
														libro_fecha: $('#libro_form').find('#libro_fecha').val(),
														libro_precio: $('#libro_form').find('#libro_precio').val(),
														libro_tags: $('#libro_form').find('#libro_tags').val(),
														libro_texto: $('#libro_form').find('#libro_texto').val(),
														libro_autor: $('#libro_form').find('#libro_autor').val(),
														libro_tapa: $('#libro_form').find('#libro_tapa').val(),
														libro_pag: $('#libro_form').find('#libro_pag').val()
													},
													success:function(data){
														if(isNaN(data)){
															$('#error_alert').text('Error al guardar los cambios\nPor favor intente nuevamente.\n');
															$('#error_alert').removeClass("hidden");
														}else{
															$('#error_alert').text('');
															$('#error_alert').addClass("hidden");
															window.location.href = "admin_books.php?id="+data;
														}
														$('#btn_save').button('reset');
													}
												});
                							}

                						<?php } ?>
	                				</script>
	                			</form>
	                			
                			</div>
                		</div>
                		<div class="panel-footer">
                			<div class="container-fluid">
	                			<a id="btn_booksby" class="btn btn-sm btn-info pull-right" onclick="location='admin_authors.php'" >Ver autores</a>
	            				<a id="btn_save" class="btn btn-sm btn-default" onclick="saveLibro()" data-loading-text="Guardando...">Guardar</a>
            				</div>
                		</div>
                	</div>
                </div>
                
                <p><div id="error_alert" class="list-group-item list-group-item-info hidden" style="float: right"></div></p>
            </div>
        </div>
        <script src="bootstrap-3.1.1-dist/js/bootstrap.min.js"></script>
        <style>
        	body{
        		background-image: url('website/img/1672440.png');
        	}
        </style>
    </body>
</html>