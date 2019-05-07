<?php
    # Archivo que guardará los datos de los productos
    define("TXT_PRODUCTOS", __DIR__."/productos.txt");
    define("RUTA_FOTOS", __DIR__."/Productos_fotos/");
    define("RUTA_FOTOS_BKP", __DIR__."/Productos_fotos/backUpFotos/");

    # Clase para el manejo de peticiones sobre productos
    class Producto{
        public $id;
        public $nombre;
        public $precio;
        public $usuarioCarga;
        public $foto;

        /**
         * Crea un producto recibido por POST
         */
        public static function cargarProducto(){
            $productoNuevo = new self();
            $productoNuevo->id = $_POST["id"];
            $productoNuevo->nombre = $_POST["nombre"];
            $productoNuevo->precio = $_POST["precio"];
            $productoNuevo->usuarioCarga = $_POST["usuarioCarga"];
            $productoNuevo->foto = self::CargarFoto();
            $productoNuevo->GuardarProducto();
            return "Producto Guardado";
        }

        /**
         * Guarda los datos del producto en el archivo TXT
         */
        private function GuardarProducto(){
            $listaProductos = self::CargarDesdeArchivo();
            $listaProductos[$this->id] = $this;
            file_put_contents(TXT_PRODUCTOS, json_encode($listaProductos));
        }

        /** Busca un producto por ID */
        public static function buscarPorID($id){
            $listaInterna = self::CargarDesdeArchivo();
            $listaProductos = [];
            $producto = null;
            foreach ($listaInterna as $key => $value){
                if( $value["id"] == $id){
                    $producto = new self();
                    $producto->id = $value["id"];
                    $producto->nombre = $value["nombre"];
                    $producto->precio = $value["precio"];
                    $producto->usuarioCarga = $value["usuarioCarga"];
                    $producto->foto = $value["foto"];
                    break;
                }
            }
            return $producto;
        }

        /**
         * Obtiene los productos que surgen de la bùsqueda segùn el criterio recibido
         * en caso de no haber criterio devuelve todo
         */
        public static function ListarProductos(){
            $listaInterna = self::CargarDesdeArchivo();
            $listaProductos = [];
            foreach ($listaInterna as $key => $value){
                $producto = new self();
                $producto->id = $value["id"];
                $producto->nombre = $value["nombre"];
                $producto->precio = $value["precio"];
                $producto->usuarioCarga = $value["usuarioCarga"];
                $producto->foto = RUTA_FOTOS.$value["foto"];

                if(isset($_GET["criterio"])){
                    switch($_GET["criterio"]){
                        case "producto":
                            if( $producto->nombre == $_GET["valor"]){
                                array_push($listaProductos, $producto);
                            }
                            break;
                        case "usuario":
                            if( $producto->usuarioCarga == $_GET["valor"]){
                                array_push($listaProductos, $producto);
                            }
                            break;
                        default: 
                            break;
                    }
                }else{
                    array_push($listaProductos, $producto);
                }
            }
            return json_encode($listaProductos);
        }

        /**
         * Modifica un producto recibido haciendo backup de su foto
         */
        public static function modificarProducto(){

            $producto = self::buscarPorID($_POST["id"]);
            if($producto == null){
                return "Producto no encontrado";
            }
            // BACKUP DE FOTO
            self::BackUpFoto($producto->id, $producto->foto);
            // CARGAR DE NUEVO PISANDO EL ANTERIOR
            $productoNuevo = new self();
            $productoNuevo->id = $_POST["id"];
            $productoNuevo->nombre = $_POST["nombre"];
            $productoNuevo->precio = $_POST["precio"];
            $productoNuevo->usuarioCarga = $_POST["usuarioCarga"];
            $productoNuevo->foto = self::CargarFoto();
            $productoNuevo->GuardarProducto();
            return "Producto Modificado";
        }

        /**
         * Carga los productos desde el archivo TXT donde se guardan
         */
        private static function CargarDesdeArchivo(){
            $usuarios = [];
            if( file_exists(TXT_PRODUCTOS) ){
                if(strlen(file_get_contents(TXT_PRODUCTOS)) > 0){
                    $usuarios = json_decode(file_get_contents(TXT_PRODUCTOS),true);
                }
            }
            return $usuarios;
        }
        

        /**
         * Carga la foto enviada
         */
        private static function CargarFoto(){
            if(isset($_FILES['foto'])){
                $extension = array_reverse(explode(".",$_FILES['foto']["name"]))[0];
                $nombre_archivo = $_POST['id']." - ".$_POST['nombre'].".$extension";
                move_uploaded_file($_FILES['foto']["tmp_name"], RUTA_FOTOS.$nombre_archivo);
                return $nombre_archivo;
            }
        }

        /**
         * Guarda el backup de una foto cargada anteriormente
         */
        private static function BackUpFoto($id, $nombreFoto){
            $extension = array_reverse(explode(".",$nombreFoto))[0];
            $nombreArchivoBKP = $id." - ".date('y-M-d').".$extension";
            rename(RUTA_FOTOS.$nombreFoto, RUTA_FOTOS_BKP.$nombreArchivoBKP);
        }
       
    }

?>