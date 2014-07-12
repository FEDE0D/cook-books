<?php include_once('database.php'); ?>
<?php

	$user = Users::getUserLogin();
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
        <link rel="shortcut icon" href="website/favicon/1.png"/>
        <link href="bootstrap-3.1.1-dist/css/bootstrap_Cosmo.css" rel="stylesheet" media="screen">
		<script src="website/jquery-1.11.0.js"></script>
        <?php 
        if (isset($_REQUEST['efectuado'])) $Ptratados = true; else $Ptratados=false;
		if (isset($_REQUEST['cancelado'])) $Pcancelados = true; else $Pcancelados=false;
		if (isset($_REQUEST['pendiente'])) $Pespera = true; else $Pespera=false;
		$ID_compra = -1;	
		$panel=false;	
		if (isset($_REQUEST['pendiente'])) {
			if( $_REQUEST['pendiente']!=0) {
			$panel=true;
			$ID_compra = $_REQUEST['pendiente'];
			$Compra= Compras::getCompra($ID_compra);
			}
		}
		elseif (isset($_REQUEST['efectuado'])) {
			if( $_REQUEST['efectuado']!=0) {
			$panel=true;
			$ID_compra = $_REQUEST['efectuado'];
			$Compra= Compras::getCompra($ID_compra);
			}
		}
		elseif (isset($_REQUEST['cancelado'])) {
			if( $_REQUEST['cancelado']!=0) {
			$panel=true;
			$ID_compra = $_REQUEST['cancelado'];
			$Compra= Compras::getCompra($ID_compra);
			}
		}
		
		//FEDE
		$FILTER_TEXT = isset($_REQUEST['fb'])? $_REQUEST['fb']:'';
		
        ?>
    </head>
    <body>
        <?php include_once('navigation.php'); ?>
        <div class="container-fluid text-center">
            <div class="row">
                <div class="col-md-4">
                	<?php 
                		if($Ptratados) $estado= "efectuado";
						elseif($Pcancelados) $estado= "cancelado";
						else $estado= "pendiente"; 
                	 ?>
                	<div class="panel panel-default">
                		<div class="panel-heading"><strong>Pedidos <?php echo $estado.'s' ?></strong></div>
                		<div class="panel-body" style="max-height: 555px; min-height:400px; overflow-y: scroll;">
                			<div class="well">
                				<input id="filter_box" type="text" class="form-control" />
                				<script type="text/javascript">
            						var filter = $("#filter_box");
                					filter.keyup(function(){
                						var list = $("#purchase_list");
                						var text = filter.val();
                						
                						list.children().each(function(i){
                							$(this).addClass("hidden");
                						});
                						
                						var result = list.find(":contains("+text+")");
                						result.each(function(i){
                							$(this).removeClass("hidden");
                						});
                					});
                					
                					$(document).ready(function(){
                						$("#filter_box").val('<?php echo $FILTER_TEXT; ?>');
                						$("#filter_box").keyup();
                					});
                					
                				</script>
                			</div>
                			<div id="purchase_list" class="list-group text-left">
                					<?php
                					$pos=0;
                					$compras = Compras::getCompras(); 
									foreach ($compras as $key => $value) { 
										 if($value->getEstado()==$estado){ $pos++; ?>
			                				<a href="admin_pedidos.php?<?php echo $estado."=".$value->getId(); ?>" class="list-group-item <?php if ($ID_compra==$value->getId()) echo 'active' ?>" onclick="resolver(this)">
			                					<script>
			                						function resolver(elem){
			                							var elem = $(elem);
			                							var fb = $("#filter_box").val();
			                							elem.attr("href",elem.attr("href")+'&fb='+fb);
			                						}
			                					</script>
												<?php echo '#'.$pos.' Fecha: '.$value->getFecha().'.  '.$value->getUsername();  ?>
												<span class="badge" title="Cantidad de libros"><?php echo $value->getCantidadLibros() ?></span>
											</a>
									<?php  } 
									 }  if($pos==0) echo  ("No hay libros");
									 ?> 
                		
                			</div>
                		</div>
                		<div class="panel-footer">
                			<div class="container-fluid">
                				<?php 
									if($Pcancelados){ ?>
		                				<a class="btn btn-sm btn-success pull-right" onclick="window.location.href='admin_pedidos.php?efectuado=0'">Efectuados</a>
		                				<a class="btn btn-sm btn-primary pull-left" onclick="window.location.href='admin_pedidos.php?pendiente=0'">Pendientes</a>
		                		<?php }
									elseif($Ptratados){ ?>
										<a  class="btn btn-sm btn-default pull-right" onclick="window.location.href='admin_pedidos.php?cancelado=0'">Cancelados</a>
		                				<a class="btn btn-sm btn-primary pull-left" onclick="window.location.href='admin_pedidos.php?pendiente=0'">Pendientes</a>
		                		<?php }
									else{ ?>
			                			<a  class="btn btn-sm btn-default pull-right" onclick="window.location.href='admin_pedidos.php?cancelado=0'">Cancelados</a>
		                				<a class="btn btn-sm btn-success pull-left" onclick="window.location.href='admin_pedidos.php?efectuado=0'">Efectuados</a>
		                				
                				<?php }
									?>
                			</div>
                		</div>
                	</div>
                </div>
                <?php
                if($panel){ ?>
                <div class="col-md-8 <?php if (!$Ptratados && !$Pcancelados && !$Pespera && !$Pinfo){ echo "hidden";} ?>">
                	<div class="panel panel-default">
                		<div class="panel-heading">Pedidos: 
                			<strong>
                				<?php
	                				if ($Pespera) echo ("Pedido pendiente ").$ID_compra;
									elseif($Pcancelados) echo "Pedidos cancelados"; 
									elseif($Ptratados) echo "Pedidos confirmados";
                				?>
                			</strong>
                		</div>
                		<div class="panel-body" style="max-height: 475px; min-height:475px;">
                				<div class="panel panel-info">
								  <div class="panel-heading">
								    <h3 class="panel-title">Orden de compra # <?php echo $Compra->getId() ?></h3>
								  </div>
								  <div class="panel-body">
								    Fecha de encargo: <?php echo $Compra->getFecha() ?>
								  </div>
								   <div class="panel-body">
								    Usuario: <?php echo $Compra->getUsername() ?>
								  </div>
								</div>	
								
								<h2 align="left">Lista de libros</h2>
								<table class="table table-striped table-hover ">
										  <thead>
										    <tr>
										      <th>Titulo</th>
										      <th>Cantidad</th>
										      <th>Precio</th> 
										      <th>Subtotal</th>
										    </tr>
										  </thead>
								<tbody>
								<?php
								$libros= $Compra->getPedidos();   //retorna un array de obj pedido
								foreach ($libros as $key => $value) { ?>
										    <tr  align="left">
										      <td><?php echo Books::getBook($value->getISBN())->getTitulo(); ?></td>
										      <td><?php echo $value->getCantidad(); ?></td>
										      <td>$ <?php echo $value->getPrecioUnitario(); ?></td>
										      <td>$<?php echo $value->getSubtotal(); ?></td>     
										    </tr>
								<?php }
								?>
								</tbody>
								</table>
								<?php if($Pespera){ ?>
									<h4>Precio total: <?php echo "<font color=red>$".$Compra->getTotal()."</font>"?></h3>
								<?php } ?>
								<h4 align="left">Estado del pedido: <strong><?php  echo $Compra->getEstado() ?></strong></h4>
                		</div>
                		<div class="panel-footer">
                			<div class="container-fluid">
	                			<?php if($Pespera) { ?>
	                			<a class="btn btn-m btn-success pull-center" id="btn-efectuar"  onclick="actualizar(true)">Confirmar</a>
	            				<a class="btn btn-m btn-default pull-center" id="btn-cancelar" data-loading-text="cancelando..." onclick="actualizar(false)">Cancelar</a>
	            				<?php }
									else{ ?>
										<h4>Precio total: <?php echo "<font color=red>$".$Compra->getTotal()."</font>"?></h3>
									<?php } ?>
            					
            				</div>
                		</div>
                	</div>
                </div>
                <?php } ?>
            </div>
        </div>
        <style type="text/css">
        	body{
        		background-image: url('website/img/food_blue.png');
        	}
									.resaltado:hover , .resaltado td :hover{
										background-color: #f0e4e8;
										cursor: pointer;
									}
							
        </style>
        <script src="bootstrap-3.1.1-dist/js/bootstrap.min.js"></script>
         <script>
        	var done = false;
        	function actualizar(estado){
        		if (done) return;
        		$.ajax({
        			url:'ajax.php',
        			type:'POST',
        			data:{
        				type:'ORDER',
        				data:JSON.stringify({
        					action: estado?'CONFIRM':'CANCEL',
        					idCompra: '<?php echo $Compra->getId() ?>'
        				})
        			},
        			success:function(data){
        				var resp = $.parseJSON(data);
        				if(resp.ok){
        					done = true;
        					if (estado){//confirmar la compra
        						alert("La compra se confirmó con éxito!\n");
        						document.location.href = "admin_pedidos.php?efectuado="+resp.id;
        					}else{//cancelar la compra
        						alert("La compra se canceló con éxito\n");
        						document.location.href = "admin_pedidos.php?cancelado="+resp.id;
        					}
        				}else{
        					alert("Hubo un error al modificar la compra.\nPor favor intente nuevamente.");
        				}
        			}
        		});
        		}
        </script>
        
    </body>
</html>
