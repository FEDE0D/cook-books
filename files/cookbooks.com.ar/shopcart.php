<?php

class Cart{
	
	private $articulos;
	
	function __construct(){
		if (session_status() == PHP_SESSION_NONE) session_start();
		
		if (!isset($_SESSION['articulos']))	$_SESSION['articulos'] = Array();
		$this->articulos = $_SESSION['articulos'];
	}
	
	/** Agrega un libro al carrito. Si ya está en el carrito aumenta su cantidad */
	function addToCart($bookId){
		if (array_key_exists($bookId, $this->articulos)){
			$this->articulos[$bookId]['cantidad']++;
		}else{
			$this->articulos[$bookId] = array();
			$this->articulos[$bookId]['cantidad'] = 1;
			
			//por motivos de optimización además guardo datos sobre el libro
			include_once 'database.php';
			$usos = new DataBase;
			$info = $usos->bookInfo($bookId);
			$this->articulos[$bookId] = array_merge($this->articulos[$bookId], $info[0]);
		}
	}
	
	/** Elimina un libro del carrito */
	function removeFromCart($bookId){
		if (array_key_exists($bookId, $this->articulos)){
			if ($this->articulos[$bookId]['cantidad']>1) $this->articulos[$bookId]['cantidad']--;
			else unset($this->articulos[$bookId]);
		}
	}
	
	/** Vacía el carrito */
	function emptyCart(){
		$this->articulos = Array();
	}
	
	/** Guarda la información del carrito en la sesion */
	function saveCart(){
		$_SESSION['articulos'] = $this->articulos;
	}
	
	/** Retorna la cantidad de libros en el carrito */
	function sizeCart(){
		//return array_sum($this->articulos);
		$sum = 0;
		foreach ($this->articulos as $bookid => $book) {
			$cantidad = $book['cantidad'];
			$sum += $cantidad;
		}
		
		return $sum;
	}
	
	/** Retorna el precio total del carrito */
	function priceCart(){
		$sum = 0;
		foreach ($this->articulos as $bookid => $book) {
			$sum += $book['precio']*$book['cantidad'];
		}
		return $sum;
	}
	
	/** Crea la vista para el dropmenu del carrito*/
	function printCartMenu(){
		?>
		<div class="" style="width: 500px; padding: 10px">
		<?php
		foreach ($this->articulos as $bookid => $book) {
			$cantidad = $book['cantidad'];
			?>
			<li>
        		<div >
	                <a href='producto?id=<?php echo $bookid ?>' style='text-decoration: none'>
	                	<span class='badge pull-left'><?php echo $cantidad ?></span>&nbsp;<?php echo $book['titulo'] ?>
	                </a>&nbsp;
	                <button onclick="
	                	<?php
	                		//Javascript activado con el click, pide que se remueva el libro, al finalizar recarga el navbar.
	                		echo (
	                			"
	                			var btn = $('#cartButton');
	                			btn.button('loading');
	                			$.post('ajax.php', {type:'sc',action:'REMOVE', bookid:'".$bookid."'}).done(
									function(data){
										$.post('navigation.php').done(
											function(navbar){
												$('#navigationBar').replaceWith(navbar);
											}
										);
									}
								);"
							);
	                	?>
	                " class="pull-right" >
	                	<span class='glyphicon glyphicon-trash'></span>
	                </button>
                </div>
        	</li><br />
        	<?php
		}
		?>
			<p>Total: $<?php echo $this->priceCart() ?>
			<button onclick="
            	<?php
            		echo (
            			"
            			var btn = $('#cartButton');
            			btn.button('loading');
            			$.post('ajax.php', {type:'sc',action:'EMPTY'}).done(
							function(data){
								$.post('navigation.php').done(
									function(navbar){
										$('#navigationBar').replaceWith(navbar);
									}
								);
							}
						);"
					);
            	?>
	            " class="pull-right" >
	            	<span class='glyphicon glyphicon-trash'> Vaciar</span>
            </button>
            
			<button class="pull-right" onclick="document.location='buy.php'">
				<span class='glyphicon glyphicon-shopping-cart'> Comprar</span>
			</button>
            </p>
		</div>
		<?php
	}
	
	/** Imprime el carrito. Solo para debug. */
	function printCart(){
		print_r($this->articulos);
	}
	
}
	

?>