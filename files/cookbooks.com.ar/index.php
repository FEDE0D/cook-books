<?php include_once('database.php'); ?>
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
                
                <div class="col-md-8">
                    <div class="jumbotron" style="padding: 10px">
                        <img src="website/img/logo.png" class="center-block img-responsive" />
                        <h2 style="margin: 0; padding: 4px">Encuentra tu libro!</h2>
                        <p style="padding: 4px">
                            Libros más vendidos ésta semana<br />
                        </p>

                        <div class="panel panel-default">
                            <?php
                            	$books = Books::getBestSellers(5);
							?>
								
							<table class='table table-bordered'>
								<style>
									.resaltado:hover , .resaltado td :hover{
										background-color: #f0e4e8;
										cursor: pointer;
									}
								</style>
								<!-- <tr style='font-weight: bold'><td>Descripción</td><td>Precio</td></tr> -->
								<?php
								foreach ($books as $key => $book) {	?>
									<tr >
										<td class="text-left resaltado" onclick="location='product.php?id=<?php echo $book->getISBN() ?>'">
											<div class="col-md-3"><img src="books/img/tapas/<?php echo $book->getTapa() ?>" class="img-rounded img-responsive" style="margin: 0 auto; height: 80px;"></div>
											<div class="col-md-9">
												<strong>Título</strong>: <?php echo $book->getTitulo(); ?></br>
												<strong>Autores</strong>: 
													<?php 
														$autores= $book->getAutores();
														$strautores = "";
														foreach ($autores as $key => $value){
															$strautores.=$value->getNombreApellido().", ";
														}
														echo substr($strautores, 0, -2);
													?>
												</br>
												<strong>Idioma</strong>: <?php echo $book->getIdioma(); ?>
											</div>
										</td>
										<td>$ <?php echo $book->getPrecio() ?><br>
											<?php
											if ($user = Users::getUserLogin()){
												if (!$user->getIsAdministrator()){
												?>
													<button 
														value="<?php echo $book->getISBN();?>"
														data-loading-text="Espere..."
														onclick="addToCart(this)
															<?php
															//Javascript pide agregar un libro al carrito, al finalizar recarga el navbar
															// echo ("
																// var btn = $(this);
																// var btnCart = $('#cartButton');
	            												// btnCart.button('loading');
																// btn.button('loading');
																// $.post('ajax.php', {type:'cart',action:'ADD', bookid:'".$book->getISBN()."'}).done(
																	// function(data){
																		// $.post('navigation.php').done(
																			// function(navbar){
																				// $('#navigationWrapper').replaceWith(navbar);
																				// btn.button('reset');
																			// }
																		// );
																	// }
																// );"
															// );
															?>
														">Comprar
													</button>
												<?php 
												}
											}
											?>
										</td>
									</tr>
									<?php
								}
								?>									
							</table>
                        </div>
                        <br />
                        <p><a href="books.php" class=" btn-link" role="button">Ver más libros</a></p>
                    </div>
                </div>
                <div class="col-md-2">
                </div>
            </div>
        </div>
        <script src="bootstrap-3.1.1-dist/js/bootstrap.min.js"></script>
        <script>
        	/** Peticion de agregar un libro al carrito, al finalizar recarga el navbar*/
        	function addToCart(elem){
        		$(elem).button('loading');
        		$('#cartButton').button('loading');
        		$.ajax({
        			url:'ajax.php',
        			type:'POST',
        			data:{
        				type:'CART',
        				data:JSON.stringify({
        					action:'ADD',
        					bookid:$(elem).val()
        				})
        			},
        			success:function(data){
        				$.post('navigation.php').done(
        					function(navbar){
        						$('#navigationWrapper').replaceWith(navbar);
        					}
        				);
        				$(elem).button('reset');
        			}
        		});
        	}
        	
        	/**  */
        </script>
        <style>
        	body{
        		background-image: url('website/img/779730.png');
        	}
        </style>
    </body>
</html>
