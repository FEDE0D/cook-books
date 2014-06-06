<?php
    //Comprobamos que llego el path de la foto
    if(!empty($_FILES['image']['name'])){
 
		//creamos una variable random para que las imagenes no se 
        //pisen si subimos 2 con el mismo nombre
        $randomNum = 
 		
        $carpeta="books/img/tapas";
        $img=$_FILES['image']['tmp_name'];
        $imgNom=$_FILES['image']['name'];
		
        //Subimos la foto
        move_uploaded_file($img,$carpeta."/".$imgNom);
		
       // echo ($isbn."es el isbn");
        //mostramos el nombre de la imagen subida
     //   echo '<div id="resultado">'.$rand."_".$imgNom.'</div>';
 echo '<div id="resultado">'.$imgNom.'</div>';
 
    }
?>