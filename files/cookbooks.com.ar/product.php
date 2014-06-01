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
                <div class="col-md-2">
                		<a id="btn_booksby" class="btn btn-sm btn-info pull-left" onclick="index.php" >Volver atrás</a>
                	
                </div>
                <div class="col-md-8">

					<div class="panel panel-default" align="left">
					
                	<?php
                		if (isset($_REQUEST['id'])){
                			
							$BOOKS = new Books;
                            $book = $BOOKS -> getBook($_REQUEST['id']);
							
							?>
							<table class='table table-bordered'>
								<style>
									.resaltado , .resaltado td{
										background-color: #f0e4e8;
								
										
									}
								</style>
						
								<tr >
										<td class="text-left resaltado" >
											<div class="col-md-3"><img src="books/img/tapas/<?php echo $book->getTapa() ?>" class="img-rounded img-responsive" style="margin: 0 auto; height: 80px;"></div>
											<div class="col-md-9">
												<h1><strong><?php echo $book->getTitulo(); ?></strong></h1>
											</td></tr> <tr><td>
												<h3><strong>Autor</strong>: <?php echo $book->getAutor()->getNombre(); ?></br>
												<strong>Idioma</strong>: <?php echo $book->getIdioma()->getNombre(); ?></br>
												<strong>Cantidad de paginas</strong>: <?php echo $book->getPaginas(); ?></br>
												<strong>Precio</strong>: <?php echo $book->getPrecio() ?></br>
												<strong>Fecha</strong>: <?php echo $book->getFecha() ?></br>
												<strong>Etiquetas</strong>: <?php echo $book->getEtiquetas() ?></br>
												<strong>Descripcion</strong>: <?php echo $book->getTexto() ?></br>
												
												</h3>
											
											</div>
									
											<?php
											$USERS = new Users;
											if ($user = $USERS->getUserLogin()){
												if (!$user->getIsAdministrator()){
												?>
													<button 
														value="<?php echo $book->getISBN();?>"
														data-loading-text="Espere..."
														onclick="
															<?php
															//Javascript pide agregar un libro al carrito, al finalizar recarga el navbar
															echo ("
																var btn = $(this);
																var btnCart = $('#cartButton');
	            												btnCart.button('loading');
																btn.button('loading');
																$.post('ajax.php', {type:'cart',action:'ADD', bookid:'".$book->getISBN()."'}).done(
																	function(data){
																		$.post('navigation.php').done(
																			function(navbar){
																				$('#navigationWrapper').replaceWith(navbar);
																				btn.button('reset');
																			}
																		);
																	}
																);"
															);
															?>
														">Comprar
													</button>
												<?php 
												}
											}
											?>
										</td>
									</tr>
							</table>
                		
              			<?} ?>
              			
                	</div>
              
                </div>
                <div class="col-md-2">
                	
                </div>
            </div>
        </div>
        <script src="bootstrap-3.1.1-dist/js/bootstrap.min.js"></script>
       <style>
        	body{
        		background-image: url('website/img/1167024.png');
        	}
     
        </style>        
        <div id="abajo" style="position:absolute;bottom:0px; background-image: url('website/img/fondo.png'); background-repeat: repeat; height: auto; width: 100%">
                	<img src="website/img/logo.png" class="center-block img-responsive" width="72px" height="35px"/>
               </div>
    </body>
</html>