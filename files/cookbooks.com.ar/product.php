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
					<div class="panel panel-default" align="left">
                		<?php
                		if (isset($_REQUEST['id'])){
	                        $book = Books::getBook($_REQUEST['id']);
						?>
						<table class='table table-bordered'>
							<style>
								.resaltado , .resaltado td{
									background-color: #f0e4e8;
								}
							</style>
					
							<tr >
								<td class="text-left resaltado" >
									<div class="col-md-3">
										<img src="books/img/tapas/<?php echo $book->getTapa() ?>" class="img-rounded img-responsive" style=" height: 80px;">
									</div>
									<div class="col-md-6" align="center">
										<h1><strong><?php echo $book->getTitulo(); ?></strong></h1>
									</div> <!--  -->
								</td>
							</tr>
						</table>
						</br>
						<table class='table table-bordered'>
							<tr></tr>
							<tr>
								<td><strong>Autor</strong></td>
								<td><strong>Idioma</strong></td>
								<td><strong>Cantidad de paginas</strong></td>
								<td><strong>Precio</strong>		</td>
								<td><strong>Fecha</strong></td>
								<td><strong>Etiquetas</strong></td>
							</tr>
							
						 	<tr>
								<td><?php echo $book->getAutor()->getNombre(); ?></br></td>
								<td><?php echo $book->getIdioma()->getNombre(); ?></br></td>
								<td><?php echo $book->getPaginas(); ?></br></td>
								<td><?php echo $book->getPrecio() ?></br></td>
								<td><?php echo $book->getFecha() ?></br></td>
								<td><?php echo $book->getEtiquetas() ?></br></td>
							</tr>
							<tr></tr>
						</table>
						</br>
						<strong>Descripcion</strong>: <?php echo $book->getTexto() ?></br></br></br></br>
							
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
							</br>	
                		</div>
              			<?} ?>
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
        <div id="abajo" style="position:absolute;bottom:0px; background-image: url('website/img/fondo.png'); background-repeat: repeat; height: auto; width: 100%">
        	<img src="website/img/logo.png" class="center-block img-responsive" width="72px" height="35px"/>
       </div>
    </body>
</html>