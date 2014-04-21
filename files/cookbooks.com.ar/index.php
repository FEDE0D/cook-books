<!DOCTYPE html>
<html lang="es">
    <head>
        <title>Cook-Book</title>
        <meta content="text/html"; charset="UTF-8"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="bootstrap-3.1.1-dist/css/bootstrap_Cosmo.css" rel="stylesheet" media="screen">
        <script src="http://code.jquery.com/jquery-1.11.0.js"></script>
        <?php include_once('database.php') ?>
    </head>
    <body>
        <?php include_once('navigation.php');
		
			##TEST
			
        ?>
        <div class="container-fluid text-center">
            <div class="row">
                <div class="col-md-2">
                </div>
                
                <div class="col-md-8">
                    <div class="jumbotron" style="padding: 10px">
                        <img src="img/logo.png" class="center-block img-responsive" />
                        <h2>Encuentra tu libro!</h2>
                        <p>
                            Libros más vendidos ésta semana<br />
                        </p>

                        <div class="panel panel-default">
                            <?php
                            	$usos = new DataBase;
                            	$books = $usos->rawQuery("SELECT * FROM libros");
								if ($books){
									?>
									<table class='table table-bordered'>
										<tr style='font-weight: bold'><td>Descripción</td><td>Precio</td></tr>
										<?php
										foreach ($books as $key => $book) {
										?>
											<tr>
												<td class="text-left">
													<div class="col-md-3"><img src="img/tapas/<?php echo $book["tapa"]?>" class="img-rounded img-responsive" style="margin: 0 auto;"></div>
													<div class="col-md-9">
														<strong>Título</strong>: <?php echo $book["titulo"]?></br>
														<strong>Autor</strong>: <?php echo $book["autor"]?></br>
														<strong>Idioma</strong>: <?php echo $book["idioma"]?>
													</div>
												</td>
												<td>$ <?php echo $book["precio"]?><br>
													<?php if ($usos->userGetLogin()){?>
														<button 
															value="<?php echo $book['ISBN']; ?>"
															data-loading-text="Espere..."
															onclick="
																<?php
																//Javascript pide agregar un libro al carrito, al finalizar recarga el navbar
																echo ("
																	var btn = $(this);
																	var btnCart = $('#cartButton');
	                												btnCart.button('loading');
																	btn.button('loading');
																	$.post('ajax.php', {type:'sc',action:'ADD', bookid:'".$book['ISBN']."'}).done(
																		function(data){
																			$.post('navigation.php').done(
																				function(navbar){
																					$('#navigationBar').replaceWith(navbar);
																					btn.button('reset');
																				}
																			);
																		}
																	);"
																);
																?>
															">Comprar
														</button>
														
														
													<?php } ?>
												</td>
											</tr>
											<?php
										}
										?>									
									</table>
									<?php
								}
							?>
                            </div>
	                            <br />
	                            <p><a href="books.php" class=" btn-link" role="button">Ver más libros</a></p>
                            </div>
                        </div>
                    <div class="col-md-2">
                    </div>
                </div>
            </div>
        <!-- Include all compiled plugins (below), or include individual files as needed -->
        <script src="bootstrap-3.1.1-dist/js/bootstrap.min.js"></script>
        <script src="datatables/js/jquery.dataTables.js"></script>
        <script src="scripts.js"></script>
    </body>
</html>
