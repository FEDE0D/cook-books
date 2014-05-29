<?php
	if (session_status() == PHP_SESSION_NONE) session_start();
	$title = (isset($_SESSION['error_title']))? $_SESSION['error_title'] : '';
	$info =  (isset($_SESSION['error_info']))? $_SESSION['error_info'] : '';
	unset($_SESSION['error_title']);
	unset($_SESSION['error_info']);
?>
<!DOCTYPE html>
<html lang="es">
    <head>
        <title>Cook-Book</title>
        <meta content="text/html"; charset="UTF-8"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="bootstrap-3.1.1-dist/css/bootstrap_Cosmo.css" rel="stylesheet" media="screen">
        <!-- <script src="http://code.jquery.com/jquery-1.11.0.js"></script> -->
    </head>
    <body>
    	<div style="height: 100px;"></div>
    	<div class="container-fluid">
    		<div class="row">
    			<div class="col-md-2"></div>
    			<div class="col-md-8">
    				<div class="panel panel-danger">
                		<div class="panel-heading">
                			<strong>Error!</strong> : <strong id="ERROR_TITLE"><?php echo $title; ?></strong>
                		</div>
                		<div class="panel-body">
                			<?php if ($info){?>
	                			<div id="ERROR_INFO" class="alert alert-info">
	                				<?php echo $info; ?>
	                			</div>
                			<?php }?>
                			<a class="btn btn-danger pull-right" href="./">Volver</a>
                		</div>
            		</div>
    			</div>
    			<div class="col-md-2"></div>
    		</div>
    		
    	</div>
    </body>
</html>