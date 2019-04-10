<?php
    /**
     * Muevo las imágenes recibidas a assets/img_clientes
     */
    define("FOTOS_TXT", "listado_fotos.txt");
    define("RUTA_FOTOS", __DIR__."/../../assets/");
    $i = 0;
    // Guardo todas las imàgenes recibidas
    foreach ($_FILES as $key => $value){
        // var_dump($_FILES[$key]);
        // Extraigo la extensión de la imagen
        $extension = array_reverse(explode(".",$_FILES[$key]["name"]))[0];
        // Creo el nombre del archivo que voy a guardar
        // Le pongo un número consecutivo con la variable $i para no pisarla
        // Si se quiere pisar las fotos guardar sin validar que exista el archivo
        do{
            $i++;
            $nombre_archivo = $_POST["nombre"]. " " . $_POST["apellido"] ." ". $_POST["legajo"] . " - $i.". $extension;
        }while(file_exists(RUTA_FOTOS.$nombre_archivo));
        // Muevo los archivos enviados a la nueva ruta
        move_uploaded_file($_FILES[$key]["tmp_name"], RUTA_FOTOS.$nombre_archivo);
        // Registro a la persona que subió la foto
        // Obtengo el listado de personas actual
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