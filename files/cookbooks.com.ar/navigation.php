<?php
	include_once('database.php');
	include_once('shopcart.php');
	
	$usos = new DataBase;
	$login = $usos->userGetLogin();
	
	$cart = new Cart;
?>
<nav id ="navigationBar" class="navbar navbar-default" role="navigation">
  <div class="container-fluid">
    <!-- Brand and toggle get grouped for better mobile display -->
    <div class="navbar-header">
      <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a class="navbar-brand" href="./">Cookbook</a>
    </div>

    <!-- Collect the nav links, forms, and other content for toggling -->
    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
		<ul class="nav navbar-nav">
			<li><a href="books.php" ><span class="glyphicon glyphicon-book"></span> Catálogo</a></li>
        	<li><a href="#"><span class="glyphicon glyphicon-question-sign"></span> Ayuda</a></li>
		</ul>
		<!--<form class="navbar-form navbar-left" role="search">
			<div class="form-group">
				<input type="text" class="form-control" placeholder="Search">
			</div>
			<button type="submit" class="btn btn-default">Submit</button>
		</form>-->
		<ul class="nav navbar-nav navbar-right">
			<?php if (isset($login)){ ?>
	            <li class="dropdown">
	                <a id="cartButton" href="#" class="btn btn-default dropdown-toggle" data-toggle="dropdown" rel="tooltip" data-placement="bottom" title="Ver carrito" data-loading-text="Espere...">
	                    <span class="badge" id="cartSize"><?php echo $cart->sizeCart() ?></span> <span class="glyphicon glyphicon-shopping-cart"></span>
	                </a>
	                <?php if ($cart->sizeCart()>0){ ?>
	                <ul id="cartMenu" class="dropdown-menu dropdown-menu-right" role="menu">
	                	<?php 
	                		$cart->printCartMenu();
	                	?>
	                </ul>
	                <?php } ?>
	            </li>
	            <li class="dropdown">
	                <a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="glyphicon glyphicon-user"></span>&nbsp;<?php echo $login['username']; ?>&nbsp;<b class="caret"></b></a>
	                <ul class="dropdown-menu">
	                    <li><a href="#"><span class="glyphicon glyphicon-time"></span> Ver el estado de mis compras</a></li>
	                    <li class="divider"></li>
	                    <li><a href="#"><span class="glyphicon glyphicon-wrench"></span> Editar perfil</a></li>
	                    <li class="divider"></li>
	                    <li><a href="logout.php"><span class="glyphicon glyphicon-off"></span> Cerrar sesión</a></li>
	                </ul>
	            </li>
            <?php }else{ ?>
		        <li><a href="register.php">Registrarse</a></li>
		        <li class="dropdown">
		        	<a href="#" class="dropdown-toggle" data-toggle="dropdown"> Iniciar Sesión <b class="caret"></b></a>
		          	<ul class="dropdown-menu" style="padding: 8px;">
			          	<form class="form" id="formLogin" action="login.php" method="post"> 
			            	<input name="username" id="username" type="text" placeholder="Username" autocomplete="off" style="margin: 6px;"> 
			                <input name="password" id="password" type="password" placeholder="Password" style="margin: 6px"><br>
			                <button type="submit" id="btnLogin" class="" style="margin: 6px">Iniciar sesión</button>
						</form>
		        	</ul>
		    	</li>
	    	<?php } ?>
		</ul>
    </div><!-- /.navbar-collapse -->
  </div><!-- /.container-fluid -->
</nav>
