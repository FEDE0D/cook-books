<?php include_once('database.php'); ?>
<?php
	$user = Users::getUserLogin();
	if (!$user || !$user->getIsAdministrator()){
		Errors::error("Sin privilegios", "No tienes privilegios para ver esta pagina!");
	}

	$SYSTEM_TOTAL = isset($_REQUEST['system']) && isset($_REQUEST['total']);
	$USER_PURCHASE = isset($_REQUEST['user']) && isset($_REQUEST['purchase']);
	$USER_REGISTER = isset($_REQUEST['user']) && isset($_REQUEST['register']);
	$BOOK_PURCHASE = isset($_REQUEST['book']) && isset($_REQUEST['purchase']);
	
	if (!$SYSTEM_TOTAL && !$USER_PURCHASE && !$USER_REGISTER && !$BOOK_PURCHASE) header("Location: admin_reports.php?system&total");
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
        <!-- DATATABLES -->
		<link href="website/datatables1.10.0/css/dataTables.bootstrap.css" rel="stylesheet"/>
		<script src="website/datatables1.10.0/js/jquery.dataTables.min.js"></script>
		<script src="website/datatables1.10.0/js/dataTables.bootstrap.js"></script>
    </head>
    <body>
        <?php include_once('navigation.php'); ?>
        <div class="container-fluid text-center">
            <div class="row">
                <div class="col-md-3">
                	<div class="panel panel-warning">
                		<div class="panel-heading">
                			<strong>Reportes</strong>
                		</div><!-- fin heading -->
                		<!-- <div class="panel-body">
                			
                		</div><!-- fin body -->
                		<div class="list-group">
                			<li class="list-group-item list-group-item-success">Sistema</li>
                			<div class="text-left">
	                			<a href="?system&total" class="list-group-item  <?php if ($SYSTEM_TOTAL) echo 'active'; ?>">Total de compras</a>
            				</div>
                			<li class="list-group-item list-group-item-success">Usuarios</li>
                			<div class="text-left">
            					<a href="?user&purchase" class="list-group-item <?php if ($USER_PURCHASE) echo 'active'; ?>">Compras por usuario</a>
            					<a href="?user&register" class="list-group-item <?php if ($USER_REGISTER) echo 'active'; ?>">Registros de usuario</a>
            				</div>
            				<li class="list-group-item list-group-item-success">Libros</li>
            				<div class="text-left">
            					<a href="?book&purchase" class="list-group-item <?php if ($BOOK_PURCHASE) echo 'active'; ?>">Resumen de compras</a>
            				</div>
            			</div>
                		<!-- <div class="panel-footer">
                			
                		</div><!-- fin footer -->
                	</div><!-- fin de panel -->
                </div>
                <div class="col-md-9">
                	<div id="" class="well">
                		<div class="h3">
                			<?php
                				if ($SYSTEM_TOTAL) echo "Sistema | Total";
								if ($USER_PURCHASE) echo "Usuarios | Compras";
								if ($USER_REGISTER) echo "Usuarios | Registros";
								if ($BOOK_PURCHASE) echo "Libros | Compras";
							?>
                		</div>
                		<hr />
                		<div class="container-fluid">
                			<?php
                				if ($SYSTEM_TOTAL){
                					$compras = Compras::getCompras();
                					$efectuadas = Compras::getComprasConEstado('efectuado');
                					$total = 0;
									foreach ($efectuadas as $key => $e) {
										$total += $e->getTotal();
									}
									
									$meses = Compras::getByMonth();
									
									?>
									
									<div class="panel panel-default">
										<div class="panel-body text-left">
											<h3>
												Recaudación total: <span style="color: red">$<?php echo $total; ?></span>
											</h3>
											<h4>Recaudación por mes:</h4>
											<h6>
												<table id="system_total_mes_table" class="table table-striped table-bordered table-responsive text-right">
													<thead>
														<th class="text-right">Mes</th>
														<th class="text-right">Año</th>
														<th class="text-right">Subtotal del mes</th>
													</thead>
													<tbody>
												<?php
													foreach ($meses as $key => $meses) { ?>
														<tr>
															<td><?php
																switch ($meses['month']) {
																	case 1:
																		echo "Enero";
																		break;
																	case 2:
																		echo "Febrero";
																	break;
																	case 3:
																		echo "Marzo";
																		break;
																	case 4:
																		echo "Abril";
																		break;
																	case 5:
																		echo "Mayo";
																		break;
																	case 6:
																		echo "Junio";
																		break;
																	case 7:
																		echo "Julio";
																		break;
																	case 8:
																		echo "Agosto";
																		break;
																	case 9:
																		echo "Septiembre";
																		break;
																	case 10:
																		echo "Octubre";
																		break;
																	case 11:
																		echo "Noviembre";
																		break;
																	case 12:
																		echo "Diciembre";
																		break;
																	default:
																		
																		break;
																}
															?></td>
															<td><?php echo $meses['year'] ?></td>
															<td>$<?php echo $meses['subtotal'] ?></td>
														</tr>
												<?php
													} 
												?>	</tbody>
												</table>
												<script>
													$("#system_total_mes_table").dataTable({
														"language": {
															"url": "website/datatables1.10.0/lang/Spanish.json"
														},
														info: false,
														filter: false,
														"pagingType": "simple_numbers"
													});
												</script>
											</h6>
										</div>
										<div class="panel-heading text-left"><strong>Todas las compras</strong></div>
										<div class="panel-body">
											<div class="container-fluid">
												<div class="well-sm">
													<label>Filtrar desde</label>
													<input id="min_date" type="date" class="input-sm"/>
													<label>hasta</label>
													<input id="max_date" type="date" class="input-sm"/>
												</div>
											</div>
											<table id="system_total_table" class="table table-striped table-bordered table-responsive" >
												<thead class="text-center">
													<th>id</th>
													<th>fecha</th>
													<th>usuario</th>
													<th>cantLibros</th>
													<th>subtotal</th>
												</thead>
												<tbody>
												<?php
													foreach ($efectuadas as $key => $compra) { 
												?>
														<tr>
															<td><?php echo $compra->getFecha() ?></td>
															<td><?php echo $compra->getTotal() ?></td>
															<td><?php echo $compra->getCantidadLibros() ?></td>
															<td><?php echo $compra->getUsername() ?></td>
															<td><?php echo $compra->getId() ?></td>
														</tr>	
												<?php
													}
												?>
												</tbody>
											</table>
											<script type="text/javascript">
											
												$(document).ready(function(){
													
													/* Agrego una nueva funcion de filtro para buscar entre fechas (Strings) */
													$.fn.dataTable.ext.search.push(
													    function( settings, data, dataIndex ) {
													        var min = $('#min_date').val();
													        var max = $('#max_date').val();
													        var date = ( data[0] ) || '';
													 
													        if ( ( min == '' && max == '' ) ||
													             ( min == '' && date <= max ) ||
													             ( min <= date && '' == max ) ||
													             ( min <= date && date <= max ) ){
													            return true;
													        }
													        return false;
													    }
													);
													
													var table = $("#system_total_table").DataTable({
														"language": {
															"url": "website/datatables1.10.0/lang/Spanish.json"
														},
														info: false,
														filter: true,
														order: [0, "desc"],
														"columnDefs": [
															{
																"targets":[0],
																"title": "Fecha de compra"
															},
															{
																"targets":[1],
																"title":"Precio",
																"render":function(data,type,row){
																	return "$"+data;
																	// return "<span style='color:red'>$"+data+"</span>"
																},
																"class":"text-right"
															},
															{
																"targets":[2],
																"title": "Cant. libros",
																"width":"15%",
																"visible":false
															},
															{
																"targets":[3],
																"title": "Usuario"
															},
															{
																"targets":[4],
																"title":"Información",
																"render":function(data,type,row){
																	return "<a href='admin_pedidos.php?efectuado="+data+"' target='_blank'>Ver más</a>";
																}
															}
														]
													});
													
													$('#min_date, #max_date').change(function() {
												        table.draw();
												    });
												    
												});
											</script>
										</div>
									</div>
                					<?php
                				}else if ($USER_PURCHASE){ ?>
                					<?php
                						$users = Users::getUsers(TRUE);
                					?>
                					<div class="panel panel-default">
                						<div class="panel-body">
											<label class="label label-default pull-left">Los 5 usuarios más activos</label>
											<div class="panel-body">
												<?php
													$gastos = Users::getUsersExpenses(TRUE);
													$gastos = array_slice($gastos, 0, 5);
													
													?>
													<table class="table table-responsive">
														<thead>
															<th>Usuario</th>
															<th>Gasto total</th>
															<th>Cantidad de libros</th>
														</thead>
														<tbody class="text-left">
													<?php
														foreach ($gastos as $key => $gasto) {
														?>
															<tr>
																<td><a href="admin_pedidos.php?efectuado&fb=<?php echo $gasto['username']; ?>" title="Ver todas las compras de <?php echo $gasto['username']; ?>"><?php echo $gasto['username'] ?></a></td>
																<td><span style="color: red">$<?php echo $gasto['gastos']; ?></span></td>
																<td><span style="color: blue">(<?php echo $gasto['libros']; ?> libros)</span></td>
															</tr>
														<?php
													}
													?>
														</tbody>
													</table>
													<?php
												?>
											</div>
											<label class="label label-default pull-left">Todos los usuarios</label>
											<div class="panel-body">
											<?php
												$gastos = Users::getUsersExpenses(FALSE);
												?>
												<br />
												<table id="user_purchases_table" class="table table-responsive">
													<thead>
														<th>Usuario</th>
														<th>Gasto total</th>
														<th>Cantidad de libros</th>
													</thead>
													<tbody class="text-left">
												<?php
													foreach ($gastos as $key => $gasto) {
												?>
														<tr>
															<td><a href="admin_pedidos.php?efectuado&fb=<?php echo $gasto['username']; ?>" title="Ver todas las compras de <?php echo $gasto['username']; ?>"><?php echo $gasto['username'] ?></a></td>
															<td>$<?php echo $gasto['gastos']; ?></td>
															<td><?php echo $gasto['libros']; ?></td>
														</tr>
												<?php
													}
												?>
													</tbody>
												</table>
												<script type="text/javascript">
													
													$(document).ready(function(){
														var table = $("#user_purchases_table").DataTable({
															filter: false,
															order: [2, "desc"]
														});
													});
													
												</script>
											</div>
		            					</div>
		            				</div>
								<?php
                				}else if ($USER_REGISTER){
                				?>
                					<div class="panel panel-default">
                						<label class="label label-default pull-left">Últimos usuarios registrados</label>
                						<div class="panel-body">
            							<?php
            								
            							?>
            								<table class="table table-striped table-responsive">
            									<thead>
            										<th>Usuario</th>
            										<th>Fecha de alta</th>
            									</thead>
            									<tbody class="text-left">
        										<?php
        											$registros = Users::getUsers(NULL, TRUE);
													$registros = array_slice($registros, 0, 5);
													
													foreach ($registros as $key => $user) {
													?>
														<tr>
															<td>
																<a href="profile.php?u=<?php echo $user->getUsername(); ?>"><?php echo $user->getUsername(); ?></a>
															</td>
															<td><?php echo $user->getFechaAlta(); ?></td>
														</tr>
													<?php	
													}
        										?>
            									</tbody>
            								</table>
                						</div>
                					</div>
                					<hr />
                					<div class="panel panel-default">
                						<label class="label label-default pull-left">Todos los registros de usuarios</label>
                						<div class="panel-body">
            							<?php
            								
            							?>
            								<br />
            								<div class="container-fluid">
												<div class="well-sm">
													<label>Filtrar desde</label>
													<input id="min_date" type="date" class="input-sm"/>
													<label>hasta</label>
													<input id="max_date" type="date" class="input-sm"/>
												</div>
											</div>
            								<table id="user_signup_table" class="table table-striped table-responsive">
            									<thead>
            										<th>Usuario</th>
            										<th>Fecha de alta</th>
            									</thead>
            									<tbody class="text-left">
        										<?php
        											$registros = Users::getUsers(NULL, TRUE);
													
													foreach ($registros as $key => $user) {
													?>
														<tr>
															<td>
																<a href="profile.php?u=<?php echo $user->getUsername(); ?>"><?php echo $user->getUsername(); ?></a>
															</td>
															<td><?php echo $user->getFechaAlta(); ?></td>
														</tr>
													<?php	
													}
        										?>
            									</tbody>
            								</table>
            								<script type="text/javascript">
            									$(document).ready(function(){
            										
            										/* Agrego una nueva funcion de filtro para buscar entre fechas (Strings) */
													$.fn.dataTable.ext.search.push(
													    function( settings, data, dataIndex ) {
													        var min = $('#min_date').val();
													        var max = $('#max_date').val();
													        var date = ( data[1] ) || '';
													 
													        if ( ( min == '' && max == '' ) ||
													             ( min == '' && date <= max ) ||
													             ( min <= date && '' == max ) ||
													             ( min <= date && date <= max ) ){
													            return true;
													        }
													        return false;
													    }
													);
            										
            										var table = $("#user_signup_table").DataTable();
            										
            										$('#min_date, #max_date').change(function() {
												        table.draw();
												    });
            										
            									});
            								</script>
                						</div>
                					</div>
                				<?php
                				}else if ($BOOK_PURCHASE){
                				?>
                					<div class="panel panel-default">
                						<label class="label label-default pull-left">Los libros más vendidos</label>
                						<div class="panel-body text-left">
            							<?php
            								$ventas = Books::getBestSellers(5)
											
										?>
											<table class="table table-responsive table-striped">
												<thead>
													<th>Título</th>
													<th>Precio</th>
													<th>Ventas</th>
												</thead>
												<tbody>
												<?php
													foreach ($ventas as $key => $book) {
													?>
														<tr>
															<td><a href="admin_books.php?id=<?php echo $book->getISBN(); ?>" target="_blank"><?php echo $book->getTitulo(); ?></a></td>
															<td>$<?php echo $book->getPrecio(); ?></td>
															<td><?php echo $book->ventas; ?></td>
														</tr>
													<?php
													}
		            							?>
												</tbody>
											</table>
                						</div>
                					</div>
                					<hr />
                					<div class="panel panel-default">
                						<label class="label label-default pull-left">Todos los libros</label>
                						<div class="panel-body text-left">
            							<?php
            								$ventas = Books::getBestSellers(0)
											
										?>
											<table id="book_sales_table" class="table table-responsive table-striped">
												<thead>
													<th>Título</th>
													<th>Ventas</th>
													<th>Precio</th>
													<th>Total</th>
												</thead>
												<tbody>
												<?php
													foreach ($ventas as $key => $book) {
													?>
														<tr>
															<td><a href="admin_books.php?id=<?php echo $book->getISBN(); ?>" target="_blank"><?php echo $book->getTitulo(); ?></a></td>
															<td><?php echo $book->ventas; ?></td>
															<td>$<?php echo $book->getPrecio(); ?></td>
															<td>$<?php echo $book->ventas * $book->getPrecio(); ?></td>
														</tr>
													<?php
													}
		            							?>
												</tbody>
											</table>
											<script type="text/javascript">
												$(document).ready(function(){
													var table = $("#book_sales_table").DataTable({
														filter: false,
														order: [1, "desc"],
													});
													
												});
											</script>
                						</div>
                					</div>
                				<?php
                				}
                			?>
                		</div>
                	</div>
                </div>
            </div>
        </div>
        <script src="bootstrap-3.1.1-dist/js/bootstrap.min.js"></script>
    </body>
</html>