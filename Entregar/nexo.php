<?php
    # Maneja todas las consultas, determinar el método de la petición y el parámetro
    # caso para saber a qué función llamar

    // Clase para el manejo de las peticiones
    require_once 'clases/Usuario.php';
    require_once 'clases/Producto.php';

    
    switch ($_SERVER['REQUEST_METHOD']){
        case 'GET': 
            switch ($_GET["caso"]){
                # PUNTO 3
                case 'listarUsuarios':
                    echo Usuario::ListarUsuarios();
                    break;
                # PUNTO 4 (bis) Y  PUNTO 5
                case 'listarProductos':
                    echo Producto::ListarProductos();
                    break;
                
                default: 
                    echo "Caso no encontrado";
                    break;
            }
            break;

        case 'POST': 
            switch ($_POST["caso"]){
                # PUNTO 1
                case 'crearUsuario':
                    echo Usuario::crearUsuario();
                    break;

                # PUNTO 2
                case 'login':
                    echo Usuario::login();
                    break;

                # PUNTO 4
                case 'cargarProducto':
                    echo Producto::cargarProducto();
                    break;
                
                # PUNTO 6
                case 'modificarProducto':
                    echo Producto::modificarProducto();
                    break;
                default: 
                    echo "Caso no encontrado";
                    break;
            }
            break;

        default:
            echo "Método no soportado";
            break;
    }
?>