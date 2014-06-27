<?php
	include_once('database.php');
	
	$book = NULL;
	if (!isset($_REQUEST['id'])) header('location:./');
	else{
		$book = Books::getBook($_REQUEST['id']);
		if(!$book)	header('location:./');
	}
	
	$user = Users::getUserLogin();
	
?>
<!DOCTYPE html>
<html lang="es">
    <head>
        <title>Cook-Book</title>
        <meta content="text/html"; charset="UTF-8"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="bootstrap-3.1.1-dist/css/bootstrap_Cosmo.css" rel="stylesheet" media="screen">
        <script src="http://code.jquery.com/jquery-1.11.0.js"></script>
    </head>
    <body>
        <?php include_once('navigation.php'); ?>
        <div class="container-fluid text-center">
            <div class="row">
                <div class="col-md-2">
                	
                </div>
                <div class="col-md-8">
                	<div class="jumbotron" style="padding: 10px;min-height: 700px">              		
                        <img src="website/img/logo.png" class="center-block img-responsive" />
						<div id="contiene">
							<div id="cabeza" >
								<img src="books/img/tapas/<?php echo $book->getTapa() ?>" align="left" style="margin:40px 20px 50px 50px; height:300px; width: 180px">
								<div style="position: relative; margin-right: 140px; margin-top: 30px; text-align: left">
								<h2 style="margin: 0; padding: 4px; alignment-adjust: left"><strong><?php echo $book->getTitulo(); ?></strong></h2>
		                       	<table>
		                       		
		                            <tr><h4><strong>Escritor: </strong><?php echo $book->getAutoresString();?></tr></h4>
		                            <tr><h4><strong>Idioma: </strong><?php echo $book->getIdioma(); ?></h4></tr>
		                            <tr><h4><strong>Precio: </strong><?php echo "<font color='red'>$".$book->getPrecio()."</font>" ?></h4></tr>
		                            	<div align="right" style="position:relative; margin-top: 100px ">
		                            		<?php
											if ($user){
												if (!$user->getIsAdministrator()){
												?>
													<button
														value="<?php echo $book->getISBN();?>"
														data-loading-text="Espere..."
														onclick="addToCart(this)">Comprar
													</button>
												<?php 
												}else if($user->getIsAdministrator()){ ?>
													<a href="admin_books.php?id=<?php echo $book->getISBN(); ?>" class="btn btn-default">Editar libro</a>
												<?php
												}
											}
											?>
		                            	</div>
		                            	</div>
		                        </table>
							</div>
							
							
						</div>
						
					</div>
						<div id="cuerpo">
							</br>
							<h3>Detalles del libro</h3>
							<h4 align="left"><strong>Sinopsis: </strong></h4>
							<h4 align="left">
								<?php echo $book->getTexto() ?>
							</h4>
						</div>	
						</br>
						<table class="table">
							<tr></tr>
							 <tbody>
							<tr class="active">
								<td><strong>ISBN</strong></td>
								<td><strong>Cantidad de paginas</strong></td>
								<td><strong>Fecha de edicion</strong></td>
								<td><strong>Etiquetas</strong></td>
							</tr>
							 </tbody>
							  <tbody>
						 	<tr >
						 		<td><?php echo $book->getISBN() ?></br></td>								
								<td><?php echo $book->getPaginas(); ?></br></td>								
								<td><?php echo $book->getFecha() ?></br></td>
								<td><?php echo $book->getEtiquetas() ?></br></td>
							</tr>
							 </tbody>
							<tr></tr>
						</table>
							<button onclick="window.location.href='index.php'">Volver atr&aacute;s</button>
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
        	#cabeza{
        		background-image: url('website/img/menu.png');
        		background-repeat: no-repeat;
        		width: 900px; 
        		height: 400px;
        		float:left;
        		position:absolute;
        		margin-left: 100px;	
        	}
        	#cuerpo{
        		background-image: url('website/img/line.png');
        		background-repeat: no-repeat;
        		margin-top: 400px;
        		padding-left:30px;
        		padding-right:30px;
        		max-width: 900px; 
        		height:auto;
        	}
     
        </style>  
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
        
    </body>
</html>