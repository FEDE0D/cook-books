<?php
	$QUERY = NULL;	if (isset($_REQUEST['query'])) $QUERY = $_REQUEST['query'];
	$ID_AUTHOR = NULL;	if (isset($_REQUEST['authID'])) $ID_AUTHOR = $_REQUEST['authID'];
	$ID_LANG = NULL;	if (isset($_REQUEST['langID'])) $ID_LANG = $_REQUEST['langID'];
?>
<!DOCTYPE html>
<html lang="es">
    <head>
        <title>Cook-Book</title>
        <meta content="text/html"; charset="UTF-8"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="bootstrap-3.1.1-dist/css/bootstrap_Cosmo.css" rel="stylesheet" media="screen">
        <script src="http://code.jquery.com/jquery-1.11.0.js"></script>
        <?php include_once('database.php'); ?>
    </head>
    <body>
        <?php include_once('navigation.php'); ?>
        <div class="container-fluid text-center">
            <div class="row">
                <div class="col-md-2">
                	
                </div>
                <div class="col-md-8">
                	<h1>Search page</h1>
                	<h2>
                		<?php
                			echo "Query = \"".$QUERY."\" <br>";
							echo "Author ID = \"".$ID_AUTHOR."\" <br>";
							echo "Language ID = \"".$ID_LANG."\" <br>";
                		?> 
                	</h2>
                </div>
                <div class="col-md-2">
                	
                </div>
            </div>
        </div>
        <script src="bootstrap-3.1.1-dist/js/bootstrap.min.js"></script>
    </body>
</html>