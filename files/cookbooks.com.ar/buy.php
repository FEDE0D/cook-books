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
													<td><strong>Total: </strong></td>
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
		                							<label class="pull-left" style="padding: 4px;">Utilice su tarjeta de crédito</label>
		                						</div>
		                					</div>
		                					<div class="row">
		                						<div class="col-md-1"></div>
		                						<div class="col-md-2">
		                							<div class="form-group">
			                							<input id="tn_1" type="number" class="form-control" placeholder="1234"/>
		                							</div>
		                						</div>
		                						<div class="col-md-2">
		                							<div class="form-group">
			                							<input id="tn_2" type="number" class="form-control" placeholder="1234"/>
		                							</div>
		                						</div>
		                						<div class="col-md-2">
		                							<div class="form-group">
			                							<input id="tn_3" type="number" class="form-control" placeholder="1234"/>
		                							</div>
		                						</div>
		                						<div class="col-md-2">
		                							<div class="form-group">
			                							<input id="tn_4" type="number" class="form-control" placeholder="1234"/>
		                							</div>
		                						</div>
		                						<div class="col-md-3">
		                							<label>Número de tarjeta de crédito</label>
		                						</div>
											</div>
										</div>             					
                					</div>
                				</div><!-- metodo_pago -->
                				<br />
                				<div class="alert alert-danger">
                					Estás a punto de realizar la compra de los artículos detallados encima.<br />
                					Por favor verifica que tus artículos son los correctos antes de enviar tus datos.
                				</div>
                				<div class="well">
                					<button title="Realizar la compra" class="btn-lg btn-warning" onclick="comprar()">
                						<span class="glyphicon glyphicon-shopping-cart"></span>
                						Comprar
                					</button>
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
        		if (!verificar()) return;
        		if (!confirm("De verdad desea realizar el pago por esta compra?")) return;
        		if (done) return;
        		done = true;
        		
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
        					alert("La compra se efectuo con éxito!\n");
        					document.location.href="history.php?historial="+resp.id_new;
        				}else{
        					done = false;
        					alert("Hubo un error al efectuar la compra.\nPor favor intente nuevamente.");
        				}
        			}
        		});
        		
        	}
        	
        	/** Realiza la verificación de los datos ingresados */
        	function verificar(){
        		var num1 = $("#tn_1").val();
        		var num2 = $("#tn_2").val();
        		var num3 = $("#tn_3").val();
        		var num4 = $("#tn_4").val();
        		
        		if ($.trim(num1)=="" || $.trim(num2)=="" || $.trim(num3)=="" || $.trim(num4)==""){
        			alert("El número de tarjeta es inválido!");
        			return false;
        		}else if (isNaN(num1) || isNaN(num2) || isNaN(num3) || isNaN(num4)){
        			alert("El número de tarjeta es inválido!");
        			return false;
        		}else if (num1.length!=4 || num2.length!=4 || num3.length!=4 || num4.length!=4 ){
        			alert("El número de tarjeta es inválido!");
        			return false;
        		}else{
        			return true;
        		}
        		
        		
        		//Check direccion del usuario.
        		
        		return false;
        	}
        </script>
    </body>
    <style>
    	body{
    		background-image: url('website/img/1801005.png');
    		background-position: center;
    	}
    </style>
</html>