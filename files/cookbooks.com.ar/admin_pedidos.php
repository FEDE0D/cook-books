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
        if (isset($_REQUEST['tratados'])) $Ptratados = true; else $Ptratados=false;
		if (isset($_REQUEST['cancelados'])) $Pcancelados = true; else $Pcancelados=false;
		if (isset($_REQUEST['espera'])) $Pespera = true; else $Pespera=false;
		if (isset($_REQUEST['info'])) $Pinfo = true; else $Pinfo=false;
		$ID_compra = -1;		
		if (isset($_REQUEST['espera'])) {
			$ID_compra = $_REQUEST['espera'];
			$Compra= Compras::getCompra($ID_compra);
		}
		elseif (isset($_REQUEST['info'])) {
			$ID_compra = $_REQUEST['info'];
			$Compra= Compras::getCompra($ID_compra);
		}
        ?>
        
    </head>
    <body>
        <?php include_once('navigation.php'); ?>
        <div class="container-fluid text-center">
            <div class="row">
                <div class="col-md-4">
                	<div class="panel panel-default">
                		<div class="panel-heading"><strong>Pedidos pendientes</strong></div>
                		<div class="panel-body" style="max-height: 705px; min-height:400px; overflow-y: scroll;">
                			<div class="list-group text-left">
                					<?php
                					
                					//Obtener los pedidos con estado  en espera
                					$compras = Compras::getCompras(); 
									foreach ($compras as $key => $value) { 
										 if($value->getEstado()=="pendiente"){ ?>
			                				<a href="admin_pedidos.php?espera=<?php echo $value->getId(); ?>" class="list-group-item <?php if ($ID_compra==$value->getId()) echo 'active' ?>">
												<?php echo $value->getId().' usuario: '.$value->getUsername(); ?>
												<span class="badge" title="Cantidad de libros"><?php echo $value->getCantidadLibros() ?></span>
											</a>
									<?php  } 
									 }
									 ?> 
                		
                			</div>
                		</div>
                		<div class="panel-footer">
                			<div class="container-fluid">
	                			<a  class="btn btn-sm btn-default pull-right" onclick="window.location.href='admin_pedidos.php?cancelados=1'">Pedidos cancelados</a>
                				<a class="btn btn-sm btn-success pull-left" onclick="window.location.href='admin_pedidos.php?tratados=1'">Pedidos efectuados</a>
                			</div>
                		</div>
                	</div>
                </div>
                
                <div class="col-md-8 <?php if (!$Ptratados && !$Pcancelados && !$Pespera && !$Pinfo){ echo "hidden";} ?>">
                	<div class="panel panel-default">
                		<div class="panel-heading">Pedidos: 
                			<strong>
                				<?php
	                				if ($Pespera) echo ("Pedido pendiente ").$ID_compra;
									elseif($Pcancelados) echo "Pedidos cancelados"; 
									elseif($Ptratados) echo "Pedidos confirmados";
									elseif($Pinfo) echo "Informacion del pedido";
                				?>
                			</strong>
                		</div>
                		<div class="panel-body" style="max-height: 475px; min-height:475px;">
                			<?php
                			if ($Pespera){ ?>
                				<table class="table table-striped table-hover ">
										  <thead>
										    <tr>
										      <th>#</th>
										      <th>Usuario</th>
										      <th>Precio total</th>
										      <th>Fecha</th>
										    </tr>
										  </thead>
										  <tbody>
										    <tr>
										      <td><?php echo $Compra->getId(); ?></td>
										      <td><?php echo $Compra->getUsername(); ?></td>
										      <td>$<?php echo $Compra->getTotal(); ?></td>
										      <td><?php echo $Compra->getFecha(); ?></td>
										    </tr>
										  </tbody>
								</table>
								<h2 align="left">Lista de libros</h2>
								<table class="table table-striped table-hover ">
										  <thead>
										    <tr>
										      <th>ISBN</th>
										      <th>Cantidad</th>
										      <th>Precio</th> 
										      <th>Total</th>
										    </tr>
										  </thead>
										  <tbody>
								<?php
								$libros= $Compra->getPedidos();   //retorna un array de obj pedido
								foreach ($libros as $key => $value) { ?>
										    <tr>
										      <td><?php echo $value->getISBN(); ?></td>
										      <td><?php echo $value->getCantidad(); ?></td>
										      <td>$ <?php echo $value->getPrecioUnitario(); ?></td>
										      <td>$<?php echo $value->getSubtotal(); ?></td>     
										    </tr>
								<?php }
								?>
								</tbody>
								</table> <?php
                			 }
                			elseif($Pcancelados){
                				?> 
                				<table class="table table-striped table-hover ">
										  <thead>
										    <tr>
										      <th>#</th>
										      <th>Nombre de usuario</th>
										      <th>Precio total</th>
										      <th>Fecha</th>
										    </tr>
										  </thead>
										  <tbody>
                				<?php 
                				foreach ($compras as $key => $value) {
                					if($value->getEstado()=="cancelado"){ ?>
		                				
										   <tr  class="text-left resaltado" onfocus="cursor: hand" onclick="document.location= 'admin_pedidos.php?info= <?php echo $value->getId();?>'">
										      <td><?php echo $value->getId(); ?></td>
										      <td><?php echo $value->getUsername(); ?></td>
										      <td><?php echo $value->getTotal(); ?></td>
										      <td><?php echo $value->getFecha(); ?></td>
										    </tr>
										  
                			<?php }
								}
								
								?>
											</tbody>
								</table>
										<?php
							}
							elseif($Ptratados){
								?>
								<table class="table table-striped table-hover ">
										  <thead>
										    <tr>
										      <th>#</th>
										      <th>Usuario</th>
										      <th>Precio total</th>
										      <th>Fecha</th>
										    </tr>
										  </thead>
										  <tbody>
								<?php
								foreach ($compras as $key => $value) {
                					if($value->getEstado()=="efectuado"){ ?>
										    <tr class="text-left resaltado" onfocus="cursor: hand" onclick="document.location= 'admin_pedidos.php?info= <?php echo $value->getId();?>'">
										      <td><?php echo $value->getId(); ?></td>
										      <td><?php echo $value->getUsername(); ?></td>
										      <td><?php echo $value->getTotal(); ?></td>
										      <td><?php echo $value->getFecha(); ?></td>
										    </tr>
										
                			<?php }
								}
								?>  </tbody>
										</table><?php
							}
							elseif($Pinfo){ ?>
								<table class="table table-striped table-hover ">
										  <thead>
										    <tr>
										      <th>#</th>
										      <th>Usuario</th>
										      <th>Precio total</th>
										      <th>Fecha</th>
										      <th>Estado</th>
										    </tr>
										  </thead>
										  <tbody>
										    <tr>
										      <td><?php echo $Compra->getId(); ?></td>
										      <td><?php echo $Compra->getUsername(); ?></td>
										      <td>$<?php echo $Compra->getTotal(); ?></td>
										      <td><?php echo $Compra->getFecha(); ?></td>
										      <td><?php echo $Compra->getEstado(); ?></td>
										    </tr>
										  </tbody>
								</table>
								<h2 align="left">Lista de libros</h2>
								<table class="table table-striped table-hover ">
										  <thead>
										    <tr>
										      <th>ISBN</th>
										      <th>Cantidad</th>
										      <th>Precio</th> 
										      <th>Total</th>
										    </tr>
										  </thead>
										  <tbody>
								<?php
								$libros= $Compra->getPedidos();   //retorna un array de obj pedido
								foreach ($libros as $key => $value) { ?>
										    <tr>
										      <td><?php echo $value->getISBN(); ?></td>
										      <td><?php echo $value->getCantidad(); ?></td>
										      <td>$ <?php echo $value->getPrecioUnitario(); ?></td>
										      <td>$<?php echo $value->getSubtotal(); ?></td>     
										    </tr>
								<?php }
								?>
								</tbody>
								</table> <?php
								
							}
							?>
                		</div>
                		<div class="panel-footer">
                			<div class="container-fluid">
	                			<?php  if ($Pespera){ ?>
	                			<a  class="btn btn-success"  data-loading-text="confirmando...">Confirmar</a>
	            				<a class="btn btn-default"  data-loading-text="cancelando...">Cancelar</a>
            					<?php } ?>
            				</div>
                		</div>
                	</div>
                </div>
            </div>
        </div>
        <script src="bootstrap-3.1.1-dist/js/bootstrap.min.js"></script>
        <style type="text/css">
        	body{
        		background-image: url('website/img/1519444.png');
        	}
									.resaltado:hover , .resaltado td :hover{
										background-color: #f0e4e8;
										cursor: pointer;
									}
							
        </style>
    </body>
</html>
