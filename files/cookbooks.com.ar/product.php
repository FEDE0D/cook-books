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
                		
                		<a href="index.php" > <img src="website/img/back1.png" width="100px" height="100px alt="volver" title="volver" "  > </a>
                	
                </div>
                <div class="col-md-8">
                		<?php
                		if (isset($_REQUEST['id'])){
	                        $book = Books::getBook($_REQUEST['id']);
						?>
                	<div class="jumbotron" style="padding: 10px;min-height: 700px">
                        <img src="website/img/logo.png" class="center-block img-responsive" />
                        <h2 style="margin: 0; padding: 4px"><strong><?php echo $book->getTitulo(); ?></strong></h2>
                        <p style="padding: 4px">
                            <?php echo $book->getAutoresString();?><br />
                        </p>

     
						
						<table class="table table-hover table-hover ">
							<tr></tr>
							 <tbody>
							<tr class="active">
								<td><strong>Autores</strong></td>
								<td><strong>Idioma</strong></td>
								<td><strong>Cantidad de paginas</strong></td>
								<td><strong>Precio</strong>		</td>
								<td><strong>Fecha</strong></td>
								<td><strong>Etiquetas</strong></td>
							</tr>
							 </tbody>
							  <tbody>
						 	<tr  class="active">
								<td><?php echo $book->getAutoresString();?></br></td>
								<td><?php echo $book->getIdioma(); ?></br></td>
								<td><?php echo $book->getPaginas(); ?></br></td>
								<td>$ <?php echo $book->getPrecio() ?></br></td>
								<td><?php echo $book->getFecha() ?></br></td>
								<td><?php echo $book->getEtiquetas() ?></br></td>
							</tr>
							 </tbody>
							<tr></tr>
						</table>
						</br>
			
						<div class="panel panel-default" style="width: 700px; float: right; margin-right: 150px">
						  <div class="panel-heading"><strong>Descripcion</strong></div>
						  <div class="panel-body">
						    <?php echo $book->getTexto() ?>
						  </div>
						  
						</div>
						<div class="panel panel-default" style="float: left">
						  <div class="panel-heading"><strong>Comprar</strong></div>
						  <div class="panel-body" align="center">
						    <img src="books/img/tapas/<?php echo $book->getTapa() ?>" class="img-rounded img-responsive" style=" height: 80px;">
						    <?php
											if ($user = Users::getUserLogin()){
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
											}else {
												
												
												
											}
											?>
							
                		</div>
              			<?php } ?>
						  </div>
						  
						</div>
						
						
						
           	</div>
           <div class="col-md-2">
           	
           	</div>
       </div><!-- row -->
       </div>
       <script src="bootstrap-3.1.1-dist/js/bootstrap.min.js"></script>
       <style>
        	body{
        		background-image: url('website/img/1167024.png');
        	}
     
        </style>        
        
    </body>
</html>