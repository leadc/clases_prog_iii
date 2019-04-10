<?php
    /**
     * Cargar imágenes por post en el servidor 
     */
    
    switch ($_SERVER['REQUEST_METHOD']){
        case 'GET': 
            # Obtener listado
            require_once './src/api/get.listado.php';
        break;

        case 'POST': 
            # Cargar Fotos
            require_once './src/api/post.cargarFoto.php';
        break;

        case 'PUT': 
            # Modificar
        break;
        case 'DELETE': 
            # Eliminar
        break;

    }

?>