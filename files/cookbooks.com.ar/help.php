<?php include_once('database.php'); ?>
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
        <?php include_once('navigation.php'); 
		$esAdm;
        $user = Users::getUserLogin();
		if ($user && $user->getIsAdministrator())
		  $esAdm=true;
		else $esAdm=false;
        ?>
        <div class="container-fluid text-center">
            <div class="row">
                <div class="col-md-2">'
                	<?php if (!$esAdm){ ?>
					<div class="list-group" align="left">
					  <a href="#" class="list-group-item">Como registrarse</a>
					  <a href="#" class="list-group-item">Como agregar libros al carrito</a>
					  <a href="#" class="list-group-item">Como efectuar una compra</a>
					  <a href="#" class="list-group-item">Como contactarse con el administrador</a>
					</div>
					<?php }
						else { ?>
							<div class="list-group" align="left">
							  <a href="#admPedidos" class="list-group-item">Como administrar pedidos</a>
							  <a href="#admUser" class="list-group-item">Como administrar usuarios</a>
							  <a href="#admBook" class="list-group-item">Como administrar libros</a>
							  <a href="#admAutor" class="list-group-item">Como administrar autores</a>
							  <a href="#admReportes" class="list-group-item">Como ver los reportes</a>
							</div>	
					<?php }  ?>
               </div> 
                <div class="col-md-8">
                	
                		<?php if ($esAdm) { ?>
	                	<h2><strong>Manual de ayuda para el administrador</strong> <a name="admPedidos"></a></h2> </br>
	                	<div class="panel panel-default" align="left" >
	                		<div class="btn pull-right">
    							  <a href="admin_pedidos.php" class="btn btn-sm btn-primary">entrar</a>
	                		</div>
	                	<div class="panel-heading"><h4><span class="glyphicon glyphicon-inbox"></span> Como administrar los pedidos</h4></div>
	                	<div style="margin-left: 20px">
	                	<h5><strong><span class="badge">1</span> Ver pedidos pendientes:</strong> situarse en el menu desplegable del administrador, situado en la barra principal a la derecha donde dice admin, al hacer click se despliegan varias opciones. Seleccionar la opcion de Administrar pedidos.</h5>
	                	<h5><strong><span class="badge">2</span> Ver detalle de pedido:</strong> situarse en el panel de pedidos tal como se detallo en (1). A su izquierda se va a encontrar con una lista de pedidos que no fueron confirmados, junto con una breve descripcion de los mismos (usuario comprador, fecha, cantidad de unidades). Para obtener mas informacion del pedido debe hacerse click sobre el detalle del mismo, lo cual desplegara un panel con mas informacion al respecto.</h5>
	                	<h5><strong><span class="badge">3</span> Confirmar pedido:</strong> una vez dentro de la vista en detalle de un pedido (2), para confirmarlo solamente basta con hacer click en el boton Confirmar, de color verde, situado justo abajo del panel de detalle. Una vez hecho esto, el sistema realizara la modificacion y mostrara un cartel con el resultado de la accion realizada y se mostraran los pedidos efectuados, donde se encuentra el pedido recien confirmado.  </h5>
	                	<h5><strong><span class="badge">4</span> Ver pedidos efectuados:</strong> dentro del panel de pedidos (1), al final del listado se encontrara con el boton de Efectuados, el mismo al ser clickeado mostrara los pedidos que ya fueron confirmados. Para volver a ver los pedidos pendientes, al final del listado se encuentra el boton de Pendientes, que al hacer click en el detallara los pedidos pendientes.</h5>
	                	<a href="#">regresar</a>
	                	</div>
	                	<a name="admUser"></a>
	                	</div>
	                	<div class="panel panel-default" align="left">
	                		<div class="btn pull-right">
    							  <a href="admin_users.php" class="btn btn-sm btn-primary">entrar</a>
	                		</div>
	                	<div class="panel-heading"><h4><span class="glyphicon glyphicon-user"> Como administrar a los usuarios</h4></div>
	                	<div style="margin-left: 20px">
	                	<h5><strong><span class="badge">1</span> Ver usuarios registrados:</strong> situarse en el menu desplegable del administrador, situado en la barra principal a la derecha donde dice admin, al hacer click se despliegan varias opciones. Seleccionar la opcion de Administrar usuarios, el cual mostrara un listado de todos los usuarios registrados en el sistema (habilitados-deshabilitados). Aquellos usuarios que han sido deshabilitados seran mostrados al final del listado, dentro de una casilla de color mas oscura. </h5>
	                	<h5><strong><span class="badge">2</span> Buscar un usuario:</strong> dentro del panel de administracion de usuarios (1), para buscar un usuario especifico, se debe introducir el nombre de usuario o parte del nombre en el campo de busqueda donde dice Buscar usuario, el cual filtrara el contenido del listado de acuerdo al nombre ingresado.<h5>
	                	<h5><strong><span class="badge">3</span> Ver perfil de un usuario:</strong> dentro del panel de administracion de usuarios (1), para acceder al perfil de un usuario especifico, se debe seleccionar al usuario en cuestion del listado y por debajo del listado se presentara el boton de ver perfil, el cual al hacer click va a presentar toda la informacion del usuario.</h5>
	                	<h5><strong><span class="badge">4</span> Habilitar-deshabilitar usuario:</strong> dentro del panel de administracion de usuarios (1), se debe buscar al usuario en cuestion dentro del listado, o bien ingresar su nombre en campo de busqueda (2). Seleccionar el usuario correspondiente, y por debajo del listado se presentara un boton con la opcion de Habilitar o de Deshabilitar segun corresponda de acuerdo con su estado actual. Al cambiar de estado de un usuario el listado se actualizara automaticamente.</h5>
	                	<a href="#">regresar</a>
	                	</div>
	                	<a name="admBook"></a>
	                	</div>
	                	<div class="panel panel-default" align="left">
	                		<div class="btn pull-right">
    							  <a href="admin_books.php" class="btn btn-sm btn-primary">entrar</a>
	                		</div>
	                	<div class="panel-heading"><h4><span class="glyphicon glyphicon-book"> Como administrar los libros</h4></div>
	                	<div style="margin-left: 20px">
	                	<h5><strong><span class="badge">1</span> Ver libros registrados:</strong> situarse en el menu desplegable del administrador, situado en la barra principal a la derecha donde dice admin, al hacer click se despliegan varias opciones. Seleccionar la opcion de Administrar libros, el cual mostrara un listado de todos los libros registrados en el sistema (ocultos y no ocultos). Aquellos usuarios que han sido ocultados seran mostrados dentro de una casilla de color mas oscura. </h5>
	                	<h5><strong><span class="badge">2</span> Buscar un libro:</strong> dentro del panel de administracion de libros (1), para buscar un libro especifico, se debe introducir el nombre del libro o parte del nombre en el campo de busqueda donde dice Buscar libro, el cual filtrara el contenido del listado de acuerdo al nombre ingresado.<h5>
	                	<h5><strong><span class="badge">3</span> Ver el detalle de un libro:</strong> dentro del panel de administracion de libros (1), para ver en detalle un libro especifico, se debe seleccionar al libro en cuestion del listado, lo que va a presentar toda la informacion del libro en detalle en un panel aparte.</h5>
	                	<h5><strong><span class="badge">4</span> Mostrar-ocultar libro:</strong> dentro del panel de administracion de libros (1), se debe buscar al libro en cuestion dentro del listado, o bien ingresar su nombre en campo de busqueda (2). Seleccionar el libro correspondiente, y por debajo del listado se presentara un boton con la opcion de Mostrar o Ocultar segun corresponda de acuerdo a su estado actual. Al cambiar de estado el listado se actualizara automaticamente.</h5>
	                	<h5><strong><span class="badge">5</span> Agregar nuevo libro:</strong> dentro del panel de administracion de libros (1), para agregar un libro se debe hacer click sobre el boton Nuevo libro, situado por debajo del panel. Esto desplegara un panel a la derecha con los campos a rellenar del libro. Los autores del libro deben estar previamente registrados en el sistema (seccion como administrar autores, inciso 4), si ya fueron registrados se los puede buscar a traves del buscador o recorriendo el listado de autores y tildando los correspondientes. En la seccion 5.1 se detalla el ingreso de una tapa para el libro </h5>
	                	<h5><strong><span class="badge">5.1</span> Seleccionar tapa al libro:</strong> para poner una imagen de tapa para el libro, la imagen debe estar previamente almacenada en su computadora. Basta con con seleccionar el boton Upload, el cual desplegara una ventana con los archivos dentro de su computadora, buscar la imagen ahi dentro y seleccionarla. Luego la imagen sera presetada abajo del boton Upload.</h5>
	                	<h5><strong><span class="badge">6</span> Modificar libro:</strong> dentro de la vista en detalle del libro (3), apareceran todos los datos del libro seleccionado. Toda la informacion del libro se puede modificar con el simple acto de volver a reescribir lo que se desea cambiar. Luego de sobreescribir los datos se debera confirmar la modificacion haciendo click sobre el boton Guardar, situado abajo del panel de informacion. </h5>
	                	<a href="#">regresar</a>
	                	</div>
	                	<a name="admAutor"></a>
	                	</div>
	                	<div class="panel panel-default" align="left">
	                		<div class="btn pull-right">
    							  <a href="admin_authors.php" class="btn btn-sm btn-primary">entrar</a>
	                		</div>
	                	<div class="panel-heading"><h4><span class="glyphicon glyphicon-tag"> Como administrar a los autores</h4></div>
	                	<div style="margin-left: 20px">
	                	<h5><strong><span class="badge">1</span> Ver autores registrados:</strong> situarse en el menu desplegable del administrador, situado en la barra principal a la derecha donde dice admin, al hacer click se despliegan varias opciones. Seleccionar la opcion de Administrar autores, el cual mostrara un listado de todos los autores registrados en el sistema con la cantidad de libros escritos.</h5>
	                	<h5><strong><span class="badge">2</span> Buscar un autor:</strong> dentro del panel de administracion de autores (1), para buscar un autor especifico, se debe introducir el nombre de usuario o parte del nombre en el campo de busqueda donde dice Buscar autor, el cual filtrara el contenido del listado de acuerdo al nombre ingresado.<h5>
	                	<h5><strong><span class="badge">3</span> Ver perfil de un autor:</strong> dentro del panel de administracion de autores(1), para acceder al perfil de un autor especifico, se debe seleccionar al autor en cuestion del listado, lo cual va a presentar toda la informacion del mismo.</h5>
	                	<h5><strong><span class="badge">4</span> Agregar nuevo autor:</strong> dentro del panel de administracion de autores (1), abajo del listado se encuenta el boton de Nuevo autor, el cual al seleccionarlo desplegara un panel con la informacion a completar del nuevo autor. Luego de completar todos los campos, se debe seleccionar el boton de Guardar situado en la parte inferior.</h5>
	                	<h5><strong><span class="badge">5</span> Modificar autor:</strong> para hacer la modificacion de un autor, se debe realizar ingresar al panel perfil de autor (3) y se debe cambiar aquellos datos que se desean modificar. Luego se debe confirmar la modificacion haciendo click en Guardar.</h5>
	                	<a href="#">regresar</a>
	                	</div>
	                	<a name="admReportes"></a>
	                	</div>
						<div class="panel panel-default" align="left">
							<div class="btn pull-right">
    							  <a href="admin_reports.php" class="btn btn-sm btn-primary">entrar</a>
	                		</div>
	                	<div class="panel-heading"><h4><span class="glyphicon glyphicon-tag"> Como ver los reportes</h4></div>
	                	<div style="margin-left: 20px">
	                	<h5><strong><span class="badge">1</span> Ver reportes:</strong> situarse en el menu desplegable del administrador, situado en la barra principal a la derecha donde dice admin, al hacer click se despliegan varias opciones. Seleccionar la opcion de Ver reportes, el cual mostrara un listado de todos los reportes que se registran en el sistema.</h5>
	                	<h5><strong><span class="badge">2</span> Seleccionar reporte:</strong> dentro del panel reportes (1), a su izquierda se muestran los posibles reportes que se generan dentro del sistema, para ingresar a uno solo debe seleccionar el que desee, y se mostrara un listado en detalle del reporte en cuestion.<h5>
	                	<a href="#">regresar</a>
	                	</div>
	                	</div>
	                	<?php } ?>
                	
                </div>
                <div class="col-md-2">
                	
                </div>
            </div>
        </div>
        <script src="bootstrap-3.1.1-dist/js/bootstrap.min.js"></script>
        <style type="text/css">
    	body{
    		background-image: url('website/img/congruent_pentagon.png');
    	}
    </style>
    </body>
</html>