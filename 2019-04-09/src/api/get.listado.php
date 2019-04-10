<?php
    /**
     * Devuelve el listado de fotos guardados en assets/listado_fotos.txt
     */
    define("FOTOS_TXT", "listado_fotos.txt");

    $listado_fotos = json_decode(file_get_contents(__DIR__."/../../assets/" . FOTOS_TXT));

    # TODO

    echo json_encode($listado_fotos);
    

?>