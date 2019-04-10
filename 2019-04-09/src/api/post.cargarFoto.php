<?php
    /**
     * Muevo las imágenes recibidas a assets/img_clientes
     */
    define("FOTOS_TXT", "listado_fotos.txt");
    define("RUTA_FOTOS", __DIR__."/../../assets/");
    define("RUTA_FOTOS_BKP", __DIR__."/../../assets/fotos_bkp/");
    // Guardo todas las imàgenes recibidas
    foreach ($_FILES as $key => $value){
        // var_dump($_FILES[$key]);
        // Extraigo la extensión de la imagen
        $extension = array_reverse(explode(".",$_FILES[$key]["name"]))[0];

        // CREAR MARCA DE AGUA
        // Cargar la estampa y la foto para aplicarle la marca de agua
        $estampa = imagecreatefrompng(RUTA_FOTOS.'marca_de_agua/marca.png');
        if(strtolower($extension) == 'png'){
            $im = imagecreatefrompng($_FILES[$key]["tmp_name"]);
        }else{
            $im = imagecreatefromjpeg($_FILES[$key]["tmp_name"]);
        }
        // Màrgenes
        $margen_dcho = 10;
        $margen_inf = 10;
        $sx = imagesx($estampa);
        $sy = imagesy($estampa);
        // Copiar la imagen de la estampa sobre nuestra foto usando los índices de márgen y el
        // ancho de la foto para calcular la posición de la estampa. 
        imagecopy($im, $estampa, imagesx($im) - $sx - $margen_dcho, imagesy($im) - $sy - $margen_inf, 0, 0, imagesx($estampa), imagesy($estampa));

        // Creo el nombre del archivo que voy a guardar
        // Le pongo un número consecutivo con la variable $i para no pisarla
        $nombre_archivo = $_POST["nombre"]. " " . $_POST["apellido"] ." ". $_POST["legajo"] . ".jpeg";
        if(file_exists(RUTA_FOTOS.$nombre_archivo)){
            $i = 0;
            do{
                $i++;
                $nombre_archivo_bkp = $_POST["nombre"]. " " . $_POST["apellido"] ." ". $_POST["legajo"] . " - $i.jpeg";
            }while(file_exists(RUTA_FOTOS_BKP.$nombre_archivo_bkp));
            rename(RUTA_FOTOS.$nombre_archivo, RUTA_FOTOS_BKP.$nombre_archivo_bkp);
        }
        // Guardo la foto nueva con la marca de agua
        imagejpeg($im,RUTA_FOTOS.$nombre_archivo);

        // Muevo los archivos enviados a la nueva ruta
        // move_uploaded_file($_FILES[$key]["tmp_name"], RUTA_FOTOS.$nombre_archivo);

        
        


        // Registro a la persona que subió la foto
        // Obtengo el listado de personas actual
        $listado = null;
        if(file_exists(RUTA_FOTOS . FOTOS_TXT)){ $listado = json_decode(file_get_contents(RUTA_FOTOS . FOTOS_TXT)); }
        // Si está vacío evito el null
        if(is_null($listado)){ $listado = []; }
        // Creo la nueva pesona
        $persona = new stdClass();
        $persona->legajo = $_POST["legajo"];
        $persona->nombre = $_POST["nombre"];
        $persona->apellido = $_POST["apellido"];
        $persona->foto = $nombre_archivo;
        // Agrego la nueva persona al array
        array_push($listado,$persona);
        // Guardo los datos en el archivo nuevamente
        file_put_contents( RUTA_FOTOS.FOTOS_TXT ,json_encode($listado));
    }

?>