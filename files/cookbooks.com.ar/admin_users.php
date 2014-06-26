<?php include_once('database.php'); ?>
<?php
	$login = Users::getUserLogin();
	if (!$login || !$login->getIsAdministrator()){
		Errors::error("Sin privilegios", "No tienes privilegios para ver esta pagina!");
	}
	
	
	$ACTIVE_USER = NULL;
	if (isset($_REQUEST['user'])){
		$ACTIVE_USER = Users::getUser($_REQUEST['user']);
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
    </head>
    <body>
        <?php include_once('navigation.php'); ?>
        <div class="container-fluid text-center">
            <div class="row">
            	<div class="col-md-2">
            		
            	</div>
                <div class="col-md-8">
                	<div class="panel panel-info" style="max-height: 570px; min-height:570px;">
                		<div class="panel-heading"><strong>Usuarios registrados</strong></div>
                		<div class="panel-body">
                			<input id="filter_text" class="form-control" type="text" placeholder="Buscar usuario..." onkeyup="filter()" />
                		</div>
                		<div id="panel_c" class="panel-body">
                			<div id="user_list" class="list-group text-left" style="max-height: 350px; min-height:350px; overflow-y: scroll;">
                				<?php
                					//Obtener autores
                					$usuarios = Users::getUsers();
									foreach ($usuarios as $key => $value) {
										?>
										<a id="user_<?php echo $value->getUsername(); ?>" 
											href="admin_users.php?user=<?php echo $value->getUsername(); ?>" 
											class="	list-group-item 
													<?php if ($ACTIVE_USER && ($ACTIVE_USER->getUsername()==$value->getUsername())) echo 'active' ?>
													" 
											style="<?php if(!$value->getEnabled()){ ?>
													background-color: #dddddd;
													font-weight: bold;
													color: #BBBBBB;
													<?php } ?>
												  "
											>
											<?php 
												if (!$value->getEnabled())
													echo "(D) ";
												echo $value->getUsername();
											?>
											<div class="pull-right" style="color: #888888; font-weight: bold;">
												<?php echo $value->getNombre().' '.$value->getApellido(); ?>
											</div>
										</a>
										<?php
									}
                				?>
                			</div>
                			<script>
                				var usuarios = $("#user_list");
                				var filter_text = $("#filter_text");
                				
                				function filter(){
                					var filter = filter_text.val();
                					if ($.trim(filter)==''){
                						usuarios.children().each(function(i){
	                						$(this).removeClass("hidden");
	                					});
                						return;
                					}
                					usuarios.children().each(function(i){
                						if (!$(this).hasClass("active"))//el usuario activo nunca se oculta
                							$(this).addClass("hidden");
                					});
                					var result = usuarios.find(":contains("+filter_text.val()+")");
                					result.each(function(i){
                						$(this).removeClass("hidden");
                					});
                				};
                				
                				$(document).ready(function(){
                					$("#user_list").animate({
	                					scrollTop: $("#user_list").find(".active").offset().top-210+$("#user_list").scrollTop()
	                				},
	                				0
                					);
                				});
                				
                			</script>
                		</div>
                		<div class="panel-footer">
                			<div class="container-fluid">
                				<?php if($ACTIVE_USER){ ?>
                					<a href="profile.php?u=<?php echo $ACTIVE_USER->getUsername(); ?>" class="btn-sm btn-default pull-left">Ver perfil</a>
                					<strong style="text-decoration: underline">
                						<?php echo $ACTIVE_USER->getUsername(); ?>
                					</strong>
                					<strong>
                						<?php if(!$ACTIVE_USER->getEnabled()) echo "(deshabilitado)" ?>
                					</strong>
                					<?php if($ACTIVE_USER->getEnabled()){ ?>
                						<a id="btn_disable" class="btn btn-sm btn-danger pull-right" data-loading-text="Deshabilitando..." onclick="disableUser(false)" title="Deshabilita este usuario" >Deshabilitar</a>
                					<?php }else{ ?>
                						<a id="btn_disable" class="btn btn-sm btn-warning pull-right" data-loading-text="Habilitando..." onclick="disableUser(true)" title="Habilita este usuario" >Habilitar</a>
            						<?php } ?>
                				<?php } ?>
                			</div>
                		</div>
                	</div>
                	<script type="text/javascript">
                		<?php if($ACTIVE_USER){ ?>
                			
	                		/** Peticion Ajax para deshabilitar el usuario. */
	                		function disableUser(enable){
	                			$("#btn_disable").button("loading");
	                			$.ajax({
	                				url:'ajax.php',
	                				type:'POST',
	                				data:{
	                					type:'USER',
	                					data:JSON.stringify({
	                						action:enable?'ENABLE':'DISABLE',
	                						username: '<?php echo $ACTIVE_USER->getUsername() ?>'
	                					})
	                				},
	                				success:function(data){
	                					var resp = $.parseJSON(data);
	                					if (resp.ok){
	                						location.reload();
	                					}else{
	                						alert("Error: \n"+resp.message);
	                					}
	                					$("#btn_disable").button("reset");
	                				}
	                			});
	                		}
	                		
                		<?php } ?>
                	</script>
                </div><!-- fin col 8 -->
                <div class="col-md-2">
                </div><!-- fin col 2 -->
            </div><!-- fin row -->
        </div>
        <script src="bootstrap-3.1.1-dist/js/bootstrap.min.js"></script>
    </body>
    <style>
    	body{
    		background-image: url('website/img/food_blue.png');
    	}
    </style>
</html>