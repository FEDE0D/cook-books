<!DOCTYPE html>
<html lang="es">
  <head>  
    <title>Cook-Book</title>
    <meta content="text/html"; charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="website/favicon/1.png"/>
    <!--JQUERY-->
    <script src="website/jquery-1.11.0.js"></script>
    <!--BOOTSTRAP-->
    <script src="bootstrap-3.1.1-dist/js/bootstrap.min.js"></script>
    <link href="bootstrap-3.1.1-dist/css/bootstrap_Cosmo.css" rel="stylesheet" media="screen">
	<!-- DATATABLES -->
	<link href="website/datatables1.10.0/css/dataTables.bootstrap.css" rel="stylesheet"/>
	<script src="website/datatables1.10.0/js/jquery.dataTables.min.js"></script>
	<script src="website/datatables1.10.0/js/dataTables.bootstrap.js"></script>
	<!-- <link href="website/datatables1.10.0/css/jquery.dataTables.min.css" rel="stylesheet" type="text/css" /> -->
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
				<div class="hidden">
					<p>TODO: Dar formato a la tabla</p>
					<ul>*Mostrar solo fecha, precio, autor y titulo <br>
						*Hacer que la fila sea un link al libro<br>
						*Cambiar campos del header<br>
						*Testear los diferentes filtros (SRS)<br>
						*Transformar catalogo a una Clase<br />
					</ul>
				</div>
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
						<table id="book_table" class="table table-striped table-bordered " width="100%" style="background-color:#EEEEEE;">
							<?php echo Books::getCatalogo(); ?>
						</table>		
					</div>
				</div>
			</div>
		</div>
	</div>
	<script>
		$(document).ready(function(){
			$("#book_table").dataTable({
				"language": {
					"url": "website/datatables1.10.0/lang/Spanish.json"
				},
				"columnDefs": [
					{
						"targets": [0],
						"title": "ISBN",
						"visible": false,
						"searchable": false
					},
					{
						"targets": [1],
						"title": "Titulo",
						"visible": true,
						"searchable": true
					},
					{
						"targets": [2],
						"title": "Autores",
						"visible": true,
						"searchable": true,
						"render":function(data,type,row){
							return ""+data;
						}
					},
					{
						"targets": [3],
						"title": "Cant. Paginas",
						"visible": false,
						"searchable": false
					},
					{
						"targets": [4],
						"title": "Precio",
						"visible": true,
						"searchable": true,
						"render":function(data,type,row){
							return '$'+data;
						},
						"class":"text-center"
					},
					{
						"targets": [5],
						"title": "Idioma",
						"visible": true,
						"searchable": true,
						"class":"text-center"
					},
					{
						"targets": [6],
						"title": "Año",
						"visible": true,
						"searchable": true,
						"type":"date",
						"render":function(data,type,row){
							var date = new Date(data);
							return date.getFullYear();
						},
						"class":"text-center"
					},
					{
						"targets": [7],
						"title": "Tags",
						"visible": false,
						"searchable": true
					},
					{
						"targets": [8],
						"title": "Ver más",
						"visible": true,
						"searchable": true,
						"sortable":false,
						"render":function(data,type,row){
							return "<a href='product.php?id="+row[0]+"'><img src='books/img/tapas/"+data+"' style='height:50px;'></img></a>";
						},
						"class":"text-center"
					}
				]
			});
    	}); 
	</script>
	<style type="text/css">
    	body{
    		background-image: url('website/img/food.png');
    	}
    </style>
  </body>
</html>
