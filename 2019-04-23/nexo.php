<?php
    # Maneja todas las consultas, determinar el método de la petición y el parámetro
    # caso para saber a qué función llamar

    // Clase para el manejo de las peticiones
    require_once 'clases/Proveedor.php';
    require_once 'clases/Pedidos.php';

    
    switch ($_SERVER['REQUEST_METHOD']){
        case 'GET': 
            switch ($_GET["caso"]){
                case 'consultarProveedor':
                    Proveedor::ConsultarProveedor();
                    break;
                case 'proveedores':
                    Proveedor::ListarTodos();
                    break;
                
                case 'listarPedidos':
                    Pedido::ListarTodos();
                    break;
                case 'listarPedidoProveedor':
                    Pedido::ListarPorProveedor();
                    break;
                default: 
                    echo "Caso no encontrado";
                    break;
            }
            break;

        case 'POST': 
            switch ($_POST["caso"]){
                case 'cargarProveedor':
                    Proveedor::CargarProveedor();
                    break;
                case 'modificarProveedor':
                    Proveedor::ModificarProveedor();
                    break;
                case 'hacerPedido':
                    Pedido::HacerPedido();
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