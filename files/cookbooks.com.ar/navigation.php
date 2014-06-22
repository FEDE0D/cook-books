<?php include_once('database.php'); ?>
<div id ="navigationWrapper">
	<nav class="navbar navbar-default navbar-fixed-top" role="navigation">
	  <div class="container-fluid">
	    <!-- Brand and toggle get grouped for better mobile display -->
	    <div class="navbar-header">
	      <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-navbar-collapse-1">
	        <span class="sr-only">Toggle navigation</span>
	        <span class="icon-bar"></span>
	        <span class="icon-bar"></span>
	        <span class="icon-bar"></span>
	      </button>
	      <a class="navbar-brand" href="./">Cookbook</a>
	    </div>
	
	    <!-- Collects the nav links, forms, and other content for toggling -->
	    <div class="collapse navbar-collapse" id="bs-navbar-collapse-1">
	    	<div class="">
				<ul class="nav navbar-nav">
					<li><a href="books.php" ><span class="glyphicon glyphicon-book"></span> Catálogo</a></li>
		        	<li><a href="help.php"><span class="glyphicon glyphicon-question-sign"></span> Ayuda</a></li>
		        	<li><a href="contact.php"><span class="glyphicon glyphicon-envelope"></span> Contacto</a></li>
				</ul>
			</div>
			<div class="">
				<form class="navbar-form navbar-left" action="books.php" method="get" role="search">
					<div class="form-group" style="padding-top: 2px; padding-bottom: 0px;">
						<input type="text" name="query" class=" form-control" placeholder="Buscar un libro..." >
						<button type="submit" class="btn btn-info form-control" style="margin-left: -2px;" ><span class="glyphicon glyphicon-search"></span> </button>
					</div>
				</form>
				
				<ul class="nav navbar-nav navbar-right">
					<?php 
					if ($user = Users::getUserLogin()){
					?>
				            <?php
				            if (!$user->getIsAdministrator()){
				            ?>
					            <li class="dropdown">
					                <a id="cartButton" href="#" class="btn btn-default dropdown-toggle" data-toggle="dropdown" rel="tooltip" data-placement="bottom" title="Ver carrito" data-loading-text="Espere...">
					                    <span class="badge" id="cartSize"><?php echo Cart::sizeCart() ?></span> <span class="glyphicon glyphicon-shopping-cart"></span>
					                </a>
					                <?php if (Cart::sizeCart()>0){ ?>
					                <ul id="cartMenu" class="dropdown-menu dropdown-menu-right" role="menu">
					                	<?php Cart::printCartHTML(); ?>
					                </ul>
					                <?php } ?>
					            </li>
				            <?php
							}
							?>
				            <li class="dropdown">
				                <a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="glyphicon glyphicon-user"></span>&nbsp;<?php echo $user->getUsername(); ?>&nbsp;<b class="caret"></b></a>
				                <ul class="dropdown-menu">
				                	<?php if ($user->getIsAdministrator()){ ?>
				                		<li><a href="admin_pedidos.php"><span class="glyphicon glyphicon-inbox"></span>&nbsp;&nbsp;&nbsp;Administrar pedidos</a></li>
				                		<li class="divider"></li>
				                		<li><a href="admin_users.php"><span class="glyphicon glyphicon-user"></span>&nbsp;&nbsp;&nbsp;Administrar usuarios</a></li>
				                		<li class="divider"></li>
				                		<li><a href="admin_books.php"><span class="glyphicon glyphicon-book"></span>&nbsp;&nbsp;&nbsp;Administrar libros</a></li>
				                		<li class="divider"></li>
				                		<li><a href="admin_authors.php"><span class="glyphicon glyphicon-tag"></span>&nbsp;&nbsp;&nbsp;Administrar autores</a></li>
					                    <li class="divider"></li>
				                	<?php }else{ ?>
					                    <li><a href="history.php"><span class="glyphicon glyphicon-time"></span>&nbsp;&nbsp;&nbsp;Ver el estado de mis compras</a></li>
					                    <li class="divider"></li>
					                    <li><a href="profile.php"><span class="glyphicon glyphicon-wrench"></span>&nbsp;&nbsp;&nbsp;Editar perfil</a></li>
					                    <li class="divider"></li>
				                    <?php } ?>
				                    <li><a href="logout.php"><span class="glyphicon glyphicon-off"></span>&nbsp;&nbsp;&nbsp;Cerrar sesión</a></li>
				                </ul>
				            </li>
		            <?php
		            }else{ ?>
				        <li><a href="register.php">Registrarse</a></li>
				        <li class="dropdown">
				        	<a id="loginDropdown" href="#" class="dropdown-toggle" data-toggle="dropdown" > Iniciar Sesión <b class="caret"></b></a>
				        	<script>
				        		$('#loginDropdown').click(
				        			//Por alguna razón .focus() funciona solo usando timeout.
				        			function(){
				        				setTimeout(
				        					function(){
				        						$('#formLogin').find('#username').focus();
				        					},
				        					0
				        				);
				        			}
				        		);
				        	</script>
				          	<ul class="dropdown-menu" style="padding: 8px;">
					          	<form class="form" id="formLogin" action="login.php" method="post"> 
					            	<input name="username" id="username" type="text" placeholder="Username" autocomplete="off" style="margin: 6px;" > 
					                <input name="password" id="password" type="password" placeholder="Password" style="margin: 6px"><br>
					                <button type="submit" id="btnLogin" class="pull-right" style="margin: 6px">Iniciar sesión</button>
								</form>
				        	</ul>
				    	</li>
			    	<?php } ?>
				</ul>
			</div>
	    </div><!-- /.navbar-collapse -->
	  </div><!-- /.container-fluid -->
	</nav>
	<p style="height: 50px"></p>
</div>
