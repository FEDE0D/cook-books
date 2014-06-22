<?php 
	include_once('database.php');
	 
	$user = Users::getUserLogin();
	if (!$user || ($user && $user->getIsAdministrator())) header('location: ./');
	if (Cart::sizeCart()==0) header('location: ./');

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
        <div style="height: 50px;"></div>
        <div class="container-fluid text-center">
            <div class="row">
                <div class="col-md-2">
                	
                </div>
                <div class="col-md-8">
                	<div class="jumbotron">
                		<div class="panel panel-default">
                			<div class="panel-heading">
	            				<!-- <img src="website/img/logo.png" class="pull-right" style="height: 25px; " /> -->
	            				<div class="page-header" style="margin:0; ">
	            					<h2><strong>Detalle de compra</strong></h2>
	        					</div>
                			</div><!-- heading -->
                			<div class="panel-body">
                				<div class="pull-left" style="padding: 2px; padding-bottom: 10px;">Artículos en la compra: </div>
                				<div class="col-md-12">
                					<table class="table table-bordered table-responsive text-left">
                						<thead>
                							<th>Libro</th>
                							<th>Cantidad</th>
                							<th>Precio Unidad</th>
                							<th>Subtotal</th>
                						</thead>
                						<tbody>
            								<?php
            									$articulos = Cart::getArticulos();
												foreach ($articulos as $ISBN => $datos) {?>
													<tr>
														<td><?php echo $datos['titulo']; ?></td>
														<td><?php echo $datos['cantidad']; ?></td>
														<td>$<?php echo $datos['precio']; ?></td>
														<td>$<?php echo $datos['cantidad']*$datos['precio']; ?></td>
													</tr>
													<?php
												}
												?>
												<tr>
													<td class="invisible"></td>
													<td class="invisible"></td>
													<td class="invisible"></td>
													<td><strong>$<?php echo Cart::priceCart(); ?></strong></td>
												</tr>
												<?php
            								?>
                						</tbody>
                					</table>
                				</div>
                			</div><!-- body -->
                			<div class="panel-footer">
            					<div class="pull-left">
                					<span class="label label-default">
                						Métodos de pago
                					</span>
                				</div><br />
                				<div id="metodos_pago" class="">
	                				<div class="well">
	                					<div>
		                					<div class="row">
		                						<div class="col-md-12">
		                							<label class="pull-left">Utilice su tarjeta de crédito</label>
		                						</div>
		                					</div>
		                					<div class="row">
		                						<div class="col-md-12"> 
				                					<div class="input-group">
														<input type="number" placeholder="XXXX-XXXX-XXXX" class="form-control">
														<span class="input-group-addon">
															<input type="radio" />
														</span>
													</div>
													TODO: separar inputs
												</div>
											</div>
										</div>             					
                					</div>
                				</div><!-- metodo_pago -->
                				<div class="alert alert-warning">
                					Estás a punto de realizar la compra de los artículos detallados encima.
                					Por favor verifica que tus artículos son los correctos antes de enviar tus datos. 
                				</div>
                				<div class="well">
                					<button title="Realizar la compra" onclick="comprar()">Comprar</button>
                				</div>
                			</div><!-- footer -->
                		</div><!-- panel -->
                	</div>
                	
                </div>
                <div class="col-md-2">
                	
                </div>
            </div>
        </div>
        <script src="bootstrap-3.1.1-dist/js/bootstrap.min.js"></script>
        <script>
        
        	var done = false;
        	
        	/** Realiza la petición ajax para efectuar la compra */
        	function comprar(){
        		//alert("1º Verificar Tarjeta.\n2º Además verificar que los datos de pefil esten completos (direccion, nombre)\n3º Ajax: COMPRA: CREATE");
        		if (done) return;
        		
        		$.ajax({
        			url:'ajax.php',
        			type:'POST',
        			data:{
        				type:'CART',
        				data:JSON.stringify({
        					action: 'PURCHASE'
        				})
        			},
        			success:function(data){
        				var resp = $.parseJSON(data);
        				if(resp.ok){
        					done = true;
        					alert("La compra se efectuo con éxito!\n");
        					document.location.href="history.php?new="+resp.id_new;
        				}else{
        					alert("Hubo un error al efectuar la compra.\nPor favor intente nuevamente.");
        				}
        			}
        		});
        		
        	}
        </script>
    </body>
</html>