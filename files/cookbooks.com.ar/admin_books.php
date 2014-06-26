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
		<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
		
		<!-- Carga de imagenes -->
		<link href="website/uploadFile/uploadfile.min.css" rel="stylesheet">
		<script src="website/uploadFile/jquery.min.js"></script>
		<script src="website/uploadFile/jquery.uploadfile.min.js"></script>
		<!-- Carga de imagenes -->
    </head>
    <body>
        <?php include_once('navigation.php'); ?>
        <?php
			$ID_ACTIVE = -1;		if (isset($_REQUEST['id'])) $ID_ACTIVE = $_REQUEST['id'];
			$NUEVO_LIBRO = FALSE;	if (isset($_REQUEST['new'])) $NUEVO_LIBRO = TRUE;
			if ($NUEVO_LIBRO) $ID_ACTIVE = -1;
			
	    	$LIBRO = NULL;
	    	if ($ID_ACTIVE > 0){
	    		$LIBRO = Books::getBook($ID_ACTIVE);
	    	}
	    ?>
        <div class="container-fluid text-center"> 
            <div class="row">
                <div class="col-md-4">
                	<div class="panel panel-default">
                		<div class="panel-heading">Libros registrados</div>
                		<div class="panel-body" style="max-height: 475px; min-height:475px; overflow-y: scroll;">
                			<div class="list-group text-left">
                				<?php
	            					$libros = Books::getBooksAvailable();
	            					foreach ($libros as $key => $value) { 
            					?>
										<a	id="libro_<?php echo $value->getISBN(); ?>" 
											href="admin_books.php?id=<?php echo $value->getISBN(); ?>"
											<?php if ($value->getOculto()){ ?>
											style="color: #999999; font-weight: bold;"
											<?php } ?>
											class="list-group-item <?php if ($ID_ACTIVE==$value->getISBN()) echo 'active' ?>"
											>
											<?php echo $value->getTitulo().' ('.$value->getAutoresString().')'; ?>
										</a>
								<?php
									}
								?>
                			</div>
                		</div>
                		<div class="panel-footer">
                			<div class="container-fluid">
	                			<a class="btn btn-sm btn-default pull-left" onclick="window.location.href='admin_books.php?new=1'">Nuevo libro</a>
	                			<?php 
	                			if($LIBRO){
	                				if ($LIBRO->getOculto()){ ?>
	                					<a id="btn_delete" class="btn btn-sm btn-info pull-right" onclick="deleteLibro()" data-loading-text="Borrando..." title="Borrar este libro del sistema">Borrar</a>
	                					<a id="btn_show" class="btn btn-sm btn-info pull-right" onclick="showLibro()" data-loading-text="Mostrando..." title="Mostrar este libro en el catálogo">Mostrar</a>
                					<?php
                					}else{ ?>
                						<a id="btn_hide" class="btn btn-sm btn-info pull-right" onclick="hideLibro()" data-loading-text="Ocultando..." title="Ocultar este libro del catálogo">Ocultar</a>
                					<?php
                					}
								}
                				?>
                			</div>
                		</div>
                	</div>
                </div>
                
                <div class="col-md-8 <?php if (!$LIBRO && !$NUEVO_LIBRO){ echo "hidden";} ?>">
                	<div class="panel panel-default">
                		<div class="panel-heading text-left">Libro: 
                			<strong>
                			<?php
                				if ($LIBRO){
                					echo $LIBRO->getTitulo();
									if ($LIBRO->getOculto()) echo " (oculto)";
                				}elseif($NUEVO_LIBRO){
                					echo "Nuevo Libro";
                				}
                			?>
                			</strong>
                		</div>
                		<div class="panel-body" style="max-height: auto; min-height:475px;">
                			<form id="libro_form" role="form" class="" enctype="multipart/form-data">
                				<input id="libro_old_ISBN" type="hidden" value="<?php if($LIBRO) echo $LIBRO->getISBN(); ?>"/>
                				<!-- En modificaciones, necesito el ISBN anterior para ver si cambio -->
                				<div class="row">
	                				<div class="col-xs-4">
	                					<label for="libro_ISBN" class="pull-left">ISBN</label>
	            						<input class="form-control" id="libro_ISBN" type="text" placeholder="ISBN" value="<?php if ($LIBRO) echo $LIBRO->getISBN(); ?>" />
	                				</div>
	                				
	                				<div class="col-xs-8">
	                					<label for="libro_titulo" class="pull-left">Titulo</label>
	                					<input class="form-control" id="libro_titulo" type="text" placeholder="Titulo del libro" value="<?php if ($LIBRO) echo $LIBRO->getTitulo() ?>"/>
	                				</div>
                				</div>
                				<div class="row">
	                				<div class="col-xs-5">
	                					<div class="row">
                							<label for="libro_autor" class="pull-left" style="margin-left: 16px;">Autor/es</label>
                						</div>
        								<div class="row">
        									<input id="filter_txt" type="text" placeholder="Buscar autor..." class="pull-left input-sm" style="margin-left: 16px; margin-bottom: 4px;" onkeyup="filter()"/>
        								</div>
	                					<div id="authors_list" style="overflow-y: scroll; height: 178px; margin-left: 10px;" class="text-left" >
	                					<?php 
	                							$autores = Authors::getAuthors();
												$selected = NULL;
												if ($LIBRO) $selected = $LIBRO->getAutoresIDs_arr();
												
												foreach ($autores as $key => $autor) { ?>
													<div id="auth_<?php echo $autor->getID(); ?>" value="author">
														<input
															id="libro_autor"
															type="checkbox" 
															value="<?php echo $autor->getID(); ?>"
															<?php
																if($selected && in_array($autor->getID(), $selected))
																echo 'checked="true"';
															?>
														/>
														<?php echo $autor->getNombreApellido(); ?>
														<div class="hidden-lg">
															<?php
																//Para ser encontrados por el filtro
																echo strtoupper($autor->getNombreApellido());
																echo strtolower($autor->getNombreApellido());
															?>
														</div>
													</div>
										<?php	} ?>
	                					</div>
	                					<script type="text/javascript">
	                						/** Filtra los autores */
	                						var autores = $("#authors_list");
	                						var filter_txt = $("#filter_txt");
	                						
	                						function filter(){
	                							var filter = filter_txt.val();
	                							if ($.trim(filter)==""){
	                								autores.children().each(function(i){
	                									$(this).removeClass("hidden");
	                								});
	                							}else{
	                								autores.children().each(function(i){
	                									$(this).addClass("hidden");
	                								});
	                								var r = autores.find(":contains("+filter+")");
	                								r.each(function(i){
	                									$(this).removeClass("hidden");
	                								});
	                								
	                							}
	                						}
	                					</script>
									</div><!-- fin col5 -->
	                				<div class="col-sm-7">
		                				<div class="col-xs-6">
		                					<label for="libro_fecha" class="pull-left">Fecha del libro</label>
		                					<input class="form-control" id="libro_fecha" type="date" placeholder="dd/mm/aaaa" value="<?php if ($LIBRO) echo $LIBRO->getFecha(); ?>"/>
		                				</div>
		                				<div class="col-xs-6">
		                					<label for="libro_precio" class="pull-left">Precio</label>
		                					<input class="form-control" id="libro_precio" type="text" placeholder="Precio del libro" value="<?php if ($LIBRO) echo $LIBRO->getPrecio() ?>"/>
		                				</div>
		                				
		                				<div class="col-xs-6">
		                					<label for="libro_idioma" class="pull-left">Idioma</label>
		                					<input class="form-control" id="libro_idioma" type="text" placeholder="Idioma del libro" value="<?php if ($LIBRO) echo $LIBRO->getIdioma() ?>"/>
		                				</div>	                				
		                				<div class="col-xs-6">
		                					<label for="libro_pag" class="pull-left">Paginas</label>
		                					<input class="form-control" id="libro_pag" type="text" placeholder="Cantidad de paginas" value="<?php if ($LIBRO) echo $LIBRO->getPaginas() ?>"/>
		                				</div>
		                				<div class="col-xs-12">
		                					<label for="libro_tags" class="pull-left">Etiquetas</label>
		                					<input class="form-control" id="libro_tags" type="text" placeholder="Tags" value="<?php if ($LIBRO) echo $LIBRO->getEtiquetas() ?>"/></br>
		                				</div>
	                				</div>
                				</div>
                				<div class="col-md-12">
	            					<label for="libro_texto" class="pull-left">Descripcion</label>
	            					<div class="col-lg-10">
								        <textarea class="form-control" rows="3" id="libro_texto" ><?php if ($LIBRO) echo $LIBRO->getTexto() ?></textarea>
							        </div>
						        </div>
						        
						        <!-- Carga de imagenes -->
                                <div class="col-md-12">
                                	<label for="img_load_btn" class="pull-left">Tapa</label><br /><br />
									<div id="fileuploader">Upload</div>
                    				<img id="img_tapa" src="books/img/tapas/<?php
										if ($LIBRO){
											echo $LIBRO->getTapa();
										}else{
											echo "_DEFAULT_.jpg";
										}
                    					?>"
                    					class="img-rounded img-responsive pull-left" 
                    					style="height: 100px; padding:10px;"
                    					value="<?php
                    							if ($LIBRO){
													echo $LIBRO->getTapa();
												}else{
													echo "_DEFAULT_.jpg";
												}
											?>"
										>
									</table>
										
										<script>
											$(document).ready(
												function(){
													$("#fileuploader").uploadFile({
														url:"upload.php",
														allowedTypes:"png,gif,jpg,jpeg",
														fileName:"myfile",
														showStatusAfterSuccess:false,
														showAbort:false,
														showDone:false,
														onSuccess:function(files,data,xhr){
															$("#img_tapa").attr("src","books/img/tapas/"+files);
															$("#img_tapa").attr("value",files);
														}
													});
												}
											);
										</script>
								</div>
								<!-- Fin carga de imagenes -->
								
                				<script>
                					<?php if ($LIBRO){ ?>
	                					/** Envia los datos del formulario para ser actualizados*/
	                					function saveLibro(){
	                						if (!validateBook()) return;
	                						
                                            var ats = $('#libro_form').find('#libro_autor:checked');
	                						var autores_param="";
	                						ats.each(
	                							function(i){
	                								autores_param+=$( this ).val()+",";
	                							}
	                						);
	                						autores_param = autores_param.substring(0,autores_param.length-1);
	                						$('#btn_save').button('loading');
	                						//Enviar formulario
	                						$.ajax({
												url:"ajax.php",
												type:"POST",
												data:{
													type: "BOOK",
													data: JSON.stringify({
														action: "UPDATE",
														old_ISBN: $('#libro_form').find('#libro_old_ISBN').val(),
														ISBN: $('#libro_form').find('#libro_ISBN').val(),
														titulo: $('#libro_form').find('#libro_titulo').val(),
														idioma: $('#libro_form').find('#libro_idioma').val(),
														fecha: $('#libro_form').find('#libro_fecha').val(),
														precio: $('#libro_form').find('#libro_precio').val(),
														tags: $('#libro_form').find('#libro_tags').val(),
														texto: $('#libro_form').find('#libro_texto').val(),
														tapa: $("#img_tapa").attr("value"),
														autores: autores_param,
														paginas: $('#libro_form').find('#libro_pag').val()
													})
												},
												success:function(data){
													var resp = $.parseJSON(data);
													if (resp.ok){
														$('#error_alert').text('');
														$('#error_alert').addClass("hidden");
														if (resp.id_new){
															window.location.href = "admin_books.php?id="+resp.id_new;
														}else{
															location.reload();
														}
													}else{
														$('#error_alert').html('Error al guardar los cambios. Por favor intente nuevamente.<br />'+resp.message);
														$('#error_alert').removeClass("hidden");
													}
													$('#btn_save').button('reset');
												}
											});
	                					}
	                					
	                					/** Oculta el libro del catalogo */
	                					function hideLibro(){
	                					    //confirm
	                					    if (!confirm("De verdad desea ocultar este libro?")) return;
	                					    
	                					    $("#btn_hide").button("loading");
	                					    $('#error_alert').text('');	$('#error_alert').addClass("hidden");
	                						$.ajax({
	                						    url:"ajax.php",
	                						    type:"POST",
	                						    data:{
	                						        type:"BOOK",
	                						        data: JSON.stringify({
	                						        	action:"HIDE",
	                						        	ISBN:$('#libro_form').find('#libro_ISBN').val()
	                						        })
	                						    },
	                						    success: function(data){
	                						    	var resp = $.parseJSON(data);
	                						    	if (resp.ok){
                                                        $('#error_alert').text('');
                                                        $('#error_alert').addClass("hidden");
                                                        location.reload();
	                						    	}else{
                                                        $('#error_alert').html('Error al guardar los cambios. Por favor intente nuevamente.<br />'+resp.message);
                                                        $('#error_alert').removeClass("hidden");
	                						    	}
                                                    $("#btn_hide").button("reset");
	                						    }
	                						});
	                					}
	                					
	                					/** Muestra el libro en el catalogo */
	                					function showLibro(){
	                					    //confirm
	                					    if (!confirm("De verdad desea mostrar este libro?")) return;
	                					    
	                					    $("#btn_show").button("loading");
	                					    $('#error_alert').text('');	$('#error_alert').addClass("hidden");
	                						$.ajax({
	                						    url:"ajax.php",
	                						    type:"POST",
	                						    data:{
	                						        type:"BOOK",
	                						        data: JSON.stringify({
	                						        	action:"SHOW",
	                						        	ISBN:$('#libro_form').find('#libro_ISBN').val()
	                						        })
	                						    },
	                						    success: function(data){
	                						    	var resp = $.parseJSON(data);
	                						    	if (resp.ok){
                                                        $('#error_alert').text('');
                                                        $('#error_alert').addClass("hidden");
                                                        location.reload();
	                						    	}else{
                                                        $('#error_alert').text('Error al guardar los cambios. Por favor intente nuevamente.<br />'+resp.message);
                                                        $('#error_alert').removeClass("hidden");
	                						    	}
                                                    $("#btn_show").button("reset");
	                						    }
	                						});
	                					}
	                					
	                					/** Borra el libro */
	                					function deleteLibro(){
	                					    //confirm
	                					    if (!confirm("De verdad desea eliminar este libro?")) return;
	                					    
	                					    $("#btn_delete").button("loading");
	                					    $('#error_alert').text('');	$('#error_alert').addClass("hidden");
	                						$.ajax({
	                						    url:"ajax.php",
	                						    type:"POST",
	                						    data:{
	                						        type:"BOOK",
	                						        data: JSON.stringify({
	                						        	action:"REMOVE",
	                						        	ISBN:$('#libro_form').find('#libro_ISBN').val()
	                						        })
	                						    },
	                						    success: function(data){
	                						    	var resp = $.parseJSON(data);
	                						    	if (resp.ok){
                                                        $('#error_alert').text('');
                                                        $('#error_alert').addClass("hidden");
                                                        window.location.href="admin_books.php";
	                						    	}else{
                                                        $('#error_alert').html('Error al guardar los cambios. Por favor intente nuevamente.<br />'+resp.message);
                                                        $('#error_alert').removeClass("hidden");
	                						    	}
                                                    $("#btn_delete").button("reset");
	                						    }
	                						});
	                					}
                					<?php } ?>
                					<?php if ($NUEVO_LIBRO){ ?>
                						/** Agrega un nuevo LIBRO a la base de datos */
            							function saveLibro(){
            								if (!validateBook()) return;
            								
            							    var ats = $('#libro_form').find('#libro_autor:checked');
                                            var autores_param="";
                                            ats.each(
                                                function(i){
                                                    autores_param+=$( this ).val()+",";
                                                }
                                            );
                                            autores_param = autores_param.substring(0,autores_param.length-1);
                                            
            								$('#btn_save').button('loading');
            								$('#error_alert').html('');	$('#error_alert').addClass("hidden");
	                						$.ajax({
												url:"ajax.php",
												type:"POST",
												data:{
													type: "BOOK",
													data: JSON.stringify({
														action: "CREATE",
														ISBN: $('#libro_form').find('#libro_ISBN').val(),
														titulo: $('#libro_form').find('#libro_titulo').val(),
														idioma: $('#libro_form').find('#libro_idioma').val(),
														fecha: $('#libro_form').find('#libro_fecha').val(),
														precio: $('#libro_form').find('#libro_precio').val(),
														tags: $('#libro_form').find('#libro_tags').val(),
														texto: $('#libro_form').find('#libro_texto').val(),
														autores: autores_param,
														tapa: $("#img_tapa").val(),
														paginas: $('#libro_form').find('#libro_pag').val()
													})
												},
												success:function(data){
													var resp = $.parseJSON(data);
													if (resp.ok){
														if ($.isNumeric(resp.id_new)){
															$('#error_alert').text('');
															$('#error_alert').addClass("hidden");
															window.location.href = "admin_books.php?id="+resp.id_new;
														}else{
															$('#error_alert').html('Error al agregar libro. Por favor intente nuevamente.<br />'+resp.message);
															$('#error_alert').removeClass("hidden");
														}
													}else{
														$('#error_alert').html('Error al agregar libro.<br />'+resp.message+'<br />');
														$('#error_alert').removeClass("hidden");
														
														if (resp.recreate){
															var button = $(
																'<button>',
																{
																	id :'btn_recreate',
																	class: 'btn-sm btn-default',
																	title: 'Reciclar libro eliminado',
																	onclick: 'recreateBook()',
																	html:'Recrear libro'
																}
															);
															$('#error_alert').append('<br />El libro se encuentra eliminado, desea reactivarlo? ');
															$('#error_alert').append(button);
														}
													}
													$('#btn_save').button('reset');
												}
											});
            							}
            							
            							/** Reactiva un libro anteriormente eliminado*/
            							function recreateBook(){
            								if (!confirm("De verdad desea reactivar el libro?")) return;
	                					    
	                					    $('#error_alert').text('');	$('#error_alert').addClass("hidden");
	                						$.ajax({
	                						    url:"ajax.php",
	                						    type:"POST",
	                						    data:{
	                						        type:"BOOK",
	                						        data: JSON.stringify({
	                						        	action:"RECREATE",
	                						        	ISBN:$('#libro_form').find('#libro_ISBN').val()
	                						        })
	                						    },
	                						    success: function(data){
	                						    	var resp = $.parseJSON(data);
	                						    	if (resp.ok){
                                                        $('#error_alert').text('');
                                                        $('#error_alert').addClass("hidden");
                                                        window.location.href="admin_books.php?id="+$('#libro_form').find('#libro_ISBN').val();
	                						    	}else{
                                                        $('#error_alert').html('Error al guardar los cambios. Por favor intente nuevamente.<br />'+resp.message);
                                                        $('#error_alert').removeClass("hidden");
	                						    	}
	                						    }
	                						});
            							}

            						<?php } ?>
                				</script>
            				</form>
                        </div><!-- fin de body -->
                        <div class="panel-footer">
                			<div class="container-fluid">
                				<p><div id="error_alert" class="list-group-item list-group-item-info hidden"></div></p>
	            				<a id="btn_save" class="btn btn-sm btn-default" onclick="saveLibro()" data-loading-text="Guardando...">Guardar</a>
            				</div>
                		</div><!-- fin de footer -->
            		</div><!-- fin panel -->
            	</div><!-- fin de column 8 -->
            </div><!-- fin row -->
        </div><!-- container -->
        <script src="bootstrap-3.1.1-dist/js/bootstrap.min.js"></script>
        <script>
        	/** Valida los campos y retorna true si estan listos para enviarse */
        	function validateBook(){
        		var isbn		= $("#libro_form").find("#libro_ISBN");
        		var titulo		= $("#libro_form").find("#libro_titulo");
        		var autores		= $("#libro_form").find("#libro_autor:checked");
        		var fecha		= $("#libro_form").find("#libro_fecha");
        		var precio		= $("#libro_form").find("#libro_precio");
        		var idioma		= $("#libro_form").find("#libro_idioma");
        		var paginas		= $("#libro_form").find("#libro_pag");
        		var etiquetas	= $("#libro_form").find("#libro_tags");
        		var descripcion	= $("#libro_form").find("#libro_texto");
        		var tapa		= $("#img_tapa");
        		
        		var error = $("#error_alert");
        		
        		if (isbn.val()==""){
        			alert("El ISBN es obligatorio!");
        			return false;
        		}else if(!$.isNumeric(isbn.val())){
        			alert("El ISBN es incorrecto!");
        			return false;
        		}else if(titulo.val().trim()==""){
        			alert("El titulo es obligatorio!");
        			return false;
        		}else if(autores.length==0){
        			alert("El libro debe tener por lo menos 1 autor!");
        			return false;
        		}else if(!$.isNumeric(Date.parse(fecha.val()))){
        			alert("La fecha es incorrecta!");
        			return false;
        		}else if(!$.isNumeric(precio.val())){
        			alert("El precio es incorrecto!");
        			return false;
        		}else if(idioma.val().trim()==""){
        			alert("El idioma es incorrecto!");
        			return false;
        		}else if(!$.isNumeric(paginas.val())){
        			alert("Las páginas deben ser un número!");
        			return false;
        		}
        		return true;
        		
        	}
        </script>
        <style>
        	body{
        		background-image: url('website/img/1672440.png');
        	}
        </style>
    </body>
</html>