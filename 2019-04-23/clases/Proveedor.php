<?php
    # Archivo que guardará los datos de los proveedores
    define("TXT_PROVEEDORES", __DIR__."/Proveedores.txt");
    define("RUTA_FOTOS", __DIR__."/Proveedor_fotos/");
    define("RUTA_FOTOS_BKP", __DIR__."/Proveedor_fotos/backUpFotos/");

    # Clase para el manejo de peticiones sobre proveedores
    class Proveedor{
        public $id;
        public $nombre;
        public $email;
        public $foto;

        /**
         * Carga un proveedor recibido por POST
         */
        public static function CargarProveedor(){
            $proveedorNuevo = new self();
            $proveedorNuevo->id = $_POST["id"];
            $proveedorNuevo->nombre = $_POST["nombre"];
            $proveedorNuevo->email = $_POST["email"];
            $proveedorNuevo->foto = self::CargarFoto();
            $proveedorNuevo->GuardarProveedor();
        }

        /**
         * Modifica un proveedor recibido por POST
         */
        public static function ModificarProveedor(){
            // Existe?  
            $provedorExistente = self::ObtenerProveedorPorID($_POST['id']);
            
            if(is_null($provedorExistente)){
                echo "No existe el proveedor";
                die;
            }
            // Backup de foto
            self::BackUpFoto($provedorExistente->id, $provedorExistente->foto);

            // Guardar Proveedor
            $proveedorNuevo = new self();
            $proveedorNuevo->id = $_POST["id"];
            $proveedorNuevo->nombre = $_POST["nombre"];
            $proveedorNuevo->email = $_POST["email"];
            $proveedorNuevo->foto = self::CargarFoto();
            $proveedorNuevo->GuardarProveedor();
            echo "Modificado Correctamente";
        }

        /**
         * Carga la foto enviada en el método cargarProveedor
         */
        private static function CargarFoto(){
            if(isset($_FILES['foto'])){
                $extension = array_reverse(explode(".",$_FILES['foto']["name"]))[0];
                $nombre_archivo = $_POST['id']." - ".$_POST['nombre'].".$extension";
                move_uploaded_file($_FILES['foto']["tmp_name"], RUTA_FOTOS.$nombre_archivo);
                return $nombre_archivo;
            }
        }

        private static function BackUpFoto($id, $nombreFoto){
            $extension = array_reverse(explode(".",$nombreFoto))[0];
            $nombreArchivoBKP = $id." - ".date('y-M-d').".$extension";
            rename(RUTA_FOTOS.$nombreFoto, RUTA_FOTOS_BKP.$nombreArchivoBKP);
        }

        

        /**
         * Consulta un proveedor según el nombre recibido por GET
         */
        public static function ConsultarProveedor(){
            if(isset($_GET['nombre'])){
                $listaInterna = self::CargarDesdeArchivo();
                $resultado = [];
                foreach ($listaInterna as $key =>  $value){
                    if(strtoupper($value["nombre"]) == strtoupper($_GET["nombre"])){
                        array_push($resultado,$value);
                    }
                }
                if(count($lista) > 0){
                    echo json_encode($resultado);
                }else{

                    echo "No existe proveedor ".$_GET["nombre"];
                }
            }
        }

        /**
         * Obtiene un proveedor según su ID pasado por parámetro
         */
        public static function ObtenerProveedorPorID($id_proveedor){
            $listaInterna = self::CargarDesdeArchivo();
            $proveedor = null;
            foreach ($listaInterna as $key => $value){
                if($id_proveedor == $value["id"]){
                    $proveedor = new self();
                    $proveedor->id = $value["id"];
                    $proveedor->nombre = $value["nombre"];
                    $proveedor->foto = $value["foto"];
                    $proveedor->email = $value["email"];
                    break;
                }
            }
            return $proveedor;
        }

        /**
         * Lista todos los proveedores
         */
        public static function ListarTodos(){
            $listaInterna = self::CargarDesdeArchivo();
            $lista = [];
            foreach ($listaInterna as $key => $value){
                array_push($lista,$value);
            }
            echo json_encode($lista);
        }

        /**
         * Guarda los datos del proveedor en el archivo TXT
         */
        private function GuardarProveedor(){
            $listaProveedores = self::CargarDesdeArchivo();
            $listaProveedores[$this->id] = $this;
            file_put_contents(TXT_PROVEEDORES, json_encode($listaProveedores));
        }

        /**
         * Carga los proveedores desde el archivo TXT donde se guardan
         */
        private static function CargarDesdeArchivo(){
            $listaProveedores = [];
            if( file_exists(TXT_PROVEEDORES) ){
                if(strlen(file_get_contents(TXT_PROVEEDORES)) > 0){
                    $listaProveedores = json_decode(file_get_contents(TXT_PROVEEDORES),true);
                }
            }
            return $listaProveedores;
        }
    }

?>