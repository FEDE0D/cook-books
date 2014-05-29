<!DOCTYPE html>
<html lang="es">
  <head>  
    <title>Cook-Book</title>
    <meta content="text/html"; charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!--JQUERY-->
    <script src="http://code.jquery.com/jquery-1.11.0.js"></script>
    <!--BOOTSTRAP-->
    <script src="bootstrap-3.1.1-dist/js/bootstrap.min.js"></script>
    <link href="bootstrap-3.1.1-dist/css/bootstrap_Cosmo.css" rel="stylesheet" media="screen">
    <!--DATATABLES-->
    <script src="datatables/js/jquery.dataTables.js"></script>
    <link href="datatables/css/jquery.dataTables.css" rel="stylesheet" />
    <link href="datatables/css/jquery-ui-1.8.4.custom.css" rel="stylesheet" />
    <link href="datatables/css/demo_table_jui.css" rel="stylesheet" />
    <script src="datatables/js/currency.js"></script>
    <script src="datatables/js/currency-sort.js"></script>
    <!--OTROS-->
    <link href="custom.css" rel="stylesheet" />
    <?php include_once('database.php') ?>
  </head>
  <body>
		<?php include_once('navigation.php')?>
		<div class="container-fluid">
			<div class="row">
				<div class="col-md-2">
					<div class="panel panel-default">
						<div class="panel-heading">
								<h3 class="panel-title">Ver libros</h3>
						</div>
						<div class="panel-body">
							<div class="btn-group-vertical btn-block">
								<?php
									if (isset($_GET['filter']))	$filter = min(max($_GET['filter'],0),3);
									else $filter = 0;
								?>
								<a class="btn btn-default btn-block <?php if ($filter==0) echo 'active'?>" href="?filter=0">Todos</a>
								<a class="btn btn-default btn-block <?php if ($filter==1) echo 'active'?>" href="?filter=1">Los + vendidos</a>
								<a class="btn btn-default btn-block <?php if ($filter==2) echo 'active'?>" href="?filter=2">Los + nuevos</a>
								<a class="btn btn-default btn-block <?php if ($filter==3) echo 'active'?>" href="?filter=3">Los + baratos</a>
							</div>
					 	</div>
					</div>
					<p>TODO: Dar formato a la tabla</p>
					<ul>*Mostrar solo fecha, precio, autor y titulo <br>
						*Hacer que la fila sea un link al libro<br>
						*Cambiar campos del header<br>
						*Testear los diferentes filtros (SRS)<br>
						*Transformar catalogo a una Clase<br />
					</ul>
				</div>
				<div class="col-md-10">
					<div class="row">
						<div class="container-fluid">
							<h3>
								<?php
									if ($filter==0) echo "Todos los libros";
									else if ($filter==1) echo "Los más vendidos";
									else if ($filter==2) echo "Los más nuevos";
									else if ($filter==3) echo "Los más baratos";
								?>
							</h3>
							<br />
							<?php
							//		INSERTA TABLA DE LIBROS
							$BOOKS = new Books;
							echo $BOOKS->getCatalogo();
							?>							
						</div>
					</div>
				</div>
			</div>
		</div>
    <script>
		$(document).ready(
			function(){
				$("#bookstable").dataTable({
					"bPaginate": true,
					"bJQueryUI": true,
					<?php
						$sortPos = 0; $sortDir ="asc";
						switch ($filter) {
							case 0:
								$sortPos = 0; $sortDir ="asc";
								break;
							case 1:
								$sortPos = 0; $sortDir ="asc";
								break;
							case 2:
								$sortPos = 6; $sortDir ="desc";
								break;
							case 3:
								$sortPos = 4; $sortDir ="asc";
								break;
							default:
								$sortPos = 0; $sortDir ="asc";
								break;
						}
						echo '"aaSorting": [[ '.$sortPos.', "'.$sortDir.'" ]],';
					?>
					"oLanguage": {
				    	"sLengthMenu": "Mostrar _MENU_ libros por página",
				    	"sZeroRecords": "No hay coincidencias - perdón",
				    	"sInfo": "Mostrando de _START_ a _END_ de _TOTAL_ libros",
				    	"sInfoEmpty": "Mostrando 0 a 0 de 0 libros",
				    	"sInfoFiltered": "(filtrado de _MAX_ libros en total)",
				    	"oPaginate": {
							"sPrevious": "Anterior",
							"sNext": "Siguiente",
						},
					},
					"aoColumns":[
						null,//ISBN
						null,//TITULO
						null,//AUTOR
						{ "bSearchable": false, "bVisible": false },//CANT. PAGINAS
		                {"sType": "currency"},//PRECIO
		                { "bSearchable": false, "bVisible": false },//IDIOMA
		                null,//FECHA
		                { "bSearchable": false, "bVisible": false },//TAGS
					],
					"aoColumnDefs": [
						{
					    	"aTargets": [4],
					        "fnRender": function ( o ) {
					            return "$ "+o.aData[ o.iDataColumn ];
					        }
					    },
					]
				});
			}
		);
	</script>
  </body>
</html>
