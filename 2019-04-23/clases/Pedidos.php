<?php

    require_once __DIR__.'/Proveedor.php';

    # Archivo que guardará los datos de los proveedores
    define("TXT_PEDIDOS", __DIR__."/Pedidos.txt");

    # Clase para el manejo de peticiones sobre proveedores
    class Pedido{
        //public $id;
        public $producto;
        public $cantidad;
        public $idProveedor;

        /**
         * Carga un pedido para un proveedor recibido por POST
         */
        public static function HacerPedido(){
            $proveedor = Proveedor::ObtenerProveedorPorID( $_POST["idProveedor"]);
            if(is_null($proveedor)){
                echo "El proveedor no existe";
                die;
            }

            $pedidoNuevo = new self();
            $pedidoNuevo->producto = $_POST["producto"];
            $pedidoNuevo->cantidad = $_POST["cantidad"];
            $pedidoNuevo->idProveedor = $_POST["idProveedor"];
            $pedidoNuevo->GuardarPedido();

            echo "Pedido registrado";
        }

        /**
         * Consulta pedidos para un determinado proveedor según el id recibido por parámetro
         */
        public static function ListarPorProveedor(){
            if(isset($_GET['idProveedor'])){
                $listaInterna = self::CargarDesdeArchivo();
                $lista = [];
                foreach ($listaInterna as $key => $value){
                    if($value["idProveedor"] == $_GET['idProveedor']){
                        $proveedor = Proveedor::ObtenerProveedorPorID($value["idProveedor"]);
                        $value["nombreProveedor"] = $proveedor->nombre;
                        array_push($lista,$value);
                    }
                }
                echo json_encode($lista);
            }
        }

        /**
         * Lista todos los pedidos
         */
        public static function ListarTodos(){
            $listaInterna = self::CargarDesdeArchivo();
            $lista = [];
            foreach ($listaInterna as $key => $value){
                $proveedor = Proveedor::ObtenerProveedorPorID($value["idProveedor"]);
                $value["nombreProveedor"] = $proveedor->nombre;
                array_push($lista,$value);
            }
            echo json_encode($lista);
        }

        /**
         * Guarda los datos del pedido en el archivo TXT
         */
        private function GuardarPedido(){
            $listaPedidos = self::CargarDesdeArchivo();
            array_push($listaPedidos, $this);
            file_put_contents(TXT_PEDIDOS, json_encode($listaPedidos));
        }

        /**
         * Carga los pedidos desde el archivo TXT donde se guardan
         */
        private static function CargarDesdeArchivo(){
            $listaPedidos = [];
            if( file_exists(TXT_PEDIDOS) ){
                if(strlen(file_get_contents(TXT_PEDIDOS)) > 0){
                    $listaPedidos = json_decode(file_get_contents(TXT_PEDIDOS),true);
                }
            }
            return $listaPedidos;
        }
    }

?>