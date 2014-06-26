<?php include_once('database.php'); ?>
<?php

	$user = Users::getUserLogin();
	if (!$user || $user->getIsAdministrator()){
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
        //$con = new Conexion;
		//$con->conectar();
		//$st= "GRANT ALL * TO 'u847065820_root'@'mysql.nixiweb.com' IDENTIFIED BY 'dvVq47UfJ6' WITH MAX_USER_CONNECTIONS 1000";
		//$con->query($st);
	        $Ptratados=false;  //prepara el escenario, setea los pedidos en falso y en cada if los setea como corresponde
			$Pcancelados=false;
			$Pespera=false;
			$Phistorial=true;
			$ID_compra = -1;	
			$panel=false;	//indica que no se va a mostrar el panel md8
			$estado="historial"; //la pag pricipal muestra los pendientes
			if (isset($_REQUEST['pendiente'])) {
				$estado="pendiente";
				$Pespera = true; $Phistorial=false;
				if( $_REQUEST['pendiente']!=0) {
				$panel=true;
				$ID_compra = $_REQUEST['pendiente'];
				$Compra= Compras::getCompra($ID_compra);
				}
			}
			elseif (isset($_REQUEST['efectuado'])) {
				$Ptratados = true; $estado="efectuado";$Phistorial=false;
				if( $_REQUEST['efectuado']!=0) {
				$panel=true; 
				$ID_compra = $_REQUEST['efectuado'];
				$Compra= Compras::getCompra($ID_compra);
				}
			}
			elseif (isset($_REQUEST['cancelado'])) {
				$Pcancelados = true; $estado="cancelado"; $Phistorial=false;
				if( $_REQUEST['cancelado']!=0) {
				$panel=true;
				$ID_compra = $_REQUEST['cancelado'];
				$Compra= Compras::getCompra($ID_compra);
				}
			}
			elseif (isset($_REQUEST['historial'])) {
				$Phistorial = true; $estado="historial";
				if( $_REQUEST['historial']!=0) {
					$panel=true;
				$ID_compra = $_REQUEST['historial'];
				$Compra= Compras::getCompra($ID_compra);
				}
			}
			
			$username =$user->getUsername();
        ?>
    </head>
    <body>
        <?php include_once('navigation.php'); ?>
        <div class="container-fluid text-center">
            <div class="row">
                <div class="col-md-4">
                	<div class="panel panel-default">
                		<div class="panel-heading"><strong><?php if(!$Phistorial) echo "Pedidos ".$estado.'s'; else echo "Historial de compras" ?></strong></div>
                		<div class="panel-body" style="max-height: 505px; min-height:400px; overflow-y: scroll;">
                			<div class="list-group text-left">
                					<?php
                					$pos=0;
                					$compras = Compras::getCompras($username); 
									foreach ($compras as $key => $value) { 
										 if($value->getEstado()==$estado){ $pos++; ?>
			                				<a href="history.php?<?php echo $estado."=".$value->getId(); ?>" class="list-group-item <?php if ($ID_compra==$value->getId()) echo 'active' ?>">
												<?php echo '#'.$pos.' compra: '.$value->getFecha()?>
												<span class="badge" title="Cantidad de libros"><?php echo $value->getCantidadLibros() ?></span>
											</a>
									<?php  } 
										 elseif($estado=="historial"){ $pos++; ?>
			                				<a href="history.php?<?php echo $estado."=".$value->getId(); ?>" class="list-group-item <?php if ($ID_compra==$value->getId()) echo 'active' ?>">
												<?php echo '#'.$pos.' Fecha: '.$value->getFecha().'.  '.ucwords($value->getEstado())  ?>
												<span class="badge" title="Cantidad de libros"><?php echo $value->getCantidadLibros() ?></span>
											</a>
									<?php  } 
									 } if($pos==0) echo  ("No hay libros");
									 ?> 
                			</div>
                		</div>
                		<div class="panel-footer">
                			<div class="container-fluid">
								<div class="btn-group resaltado">
								  <button type="button" class="btn btn-info">Filtrar</button>
								  <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown"><span class="caret"></span></button>
								  <ul class="dropdown-menu">
								    <li><a href="history.php?pendiente=0">Pedidos pendientes</a></li>
								    <li><a href="history.php?efectuado=0">Pedidos efectuados</a></li>
								    <li><a href="history.php?cancelado=0">Pedidos cancelados</a></li>
								    <li class="divider"></li>
								    <li><a href="history.php?historial=0">Historial</a></li>
								  </ul>
								</div>                				
	                			
                			</div>
                		</div>
                	</div>
                </div>
                <?php
                if($panel){ ?>
                <div class="col-md-8 ">
                	<div class="panel panel-default">
                		<div class="panel-heading">
                			<strong>
                				<?php
                					if($Compra->getEstado()=="pendiente")  echo ("Pedido pendiente");
									elseif($Compra->getEstado()=="efectuado")  echo ("Pedido efectuado");
									elseif($Compra->getEstado()=="cancelado")  echo ("Pedido cancelado");
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
								</div>								             			
								
								<h3 align="left">Lista de libros</h3>
								<table class="table ">
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
										    <tr  align="left" class="resaltado">
										      <td><?php echo Books::getBook($value->getISBN())->getTitulo(); ?></td>
										      <td><?php echo $value->getCantidad(); ?></td>
										      <td>$ <?php echo $value->getPrecioUnitario(); ?></td>
										      <td>$<?php echo $value->getSubtotal(); ?></td>     
										    </tr>
								<?php }
								?>
								</tbody>
								</table> 
								<h4 align="left">Estado del pedido: <strong><?php  echo $Compra->getEstado() ?></strong></h4>
                		</div>
                		<div class="panel-footer">
                			<div class="container-fluid">
	                			<h4>Precio total: <?php echo "<font color=red>$".$Compra->getTotal()."</font>"?></h3>
            				</div>
                		</div>
                	</div>
                </div>
                <?php } ?>
            </div>
        </div>
        <script src="bootstrap-3.1.1-dist/js/bootstrap.min.js"></script>
        <style type="text/css">
        	body{
        		background-image: url('website/img/food_blue.png');
        	}
									.resaltado:hover , .resaltado td :hover , .resaltado a:hover  , .resaltado li:hover{
										background-color: #f0e4e8;
										cursor: pointer;
									}
							
        </style>
    </body>
</html>
