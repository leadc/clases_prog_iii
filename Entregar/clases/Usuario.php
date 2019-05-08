<?php
    # Archivo que guardará los datos de los usuarios
    define("TXT_USUARIOS", __DIR__."/usuarios.txt");

    # Clase para el manejo de peticiones sobre usuarios
    class Usuario{
        public $nombre;
        public $clave;

        /**
         * Crea un usuario recibido por POST
         */
        public static function CrearUsuario(){
            $usNuevo = new self();
            $usNuevo->nombre = $_POST["nombre"];
            $usNuevo->clave = $_POST["clave"];
            $usNuevo->GuardarUsuario();
            return "Usuario Guardado";
        }

        /**
         * Guarda los datos del usuario en el archivo TXT
         */
        private function GuardarUsuario(){
            $listaUsuarios = self::CargarDesdeArchivo();
            $listaUsuarios[$this->nombre] = $this;
            file_put_contents(TXT_USUARIOS, json_encode($listaUsuarios));
        }

        /**
         * Verifica el usuario y clave pasadospor parametro
         */
        public static function login(){
            $usuario = self::ObtenerUsuarioPorNombre($_POST["nombre"]);

            if(is_null($usuario)){
                return "Nombre de usuario incorrecto";
            }

            if($usuario->clave != $_POST["clave"]){
                return "Clave incorrecta";
            }

            return true;
        }

        /**
         * Obtiene un usuario según su nombre pasado por parámetro
         */
        public static function ObtenerUsuarioPorNombre($nombreUsuario){
            $listaInterna = self::CargarDesdeArchivo();
            $usuario = null;
            foreach ($listaInterna as $key => $value){
                if($nombreUsuario == $value["nombre"]){
                    $usuario = new self();
                    $usuario->nombre = $value["nombre"];
                    $usuario->clave = $value["clave"];
                    break;
                }
            }
            return $usuario;
        }

        /**
         * Obtiene los usuarios que surgen de la bùsqueda del nombre pasado por parametro
         */
        public static function ObtenerUsuariosPorNombre($nombreUsuario){
            $listaInterna = self::CargarDesdeArchivo();
            $usuario = null;
            foreach ($listaInterna as $key => $value){
                if(strpos(strtoupper($nombreUsuario), strtoupper($value["nombre"]))){
                    $usuario = new self();
                    $usuario->nombre = $value["nombre"];
                    $usuario->clave = $value["clave"];
                    break;
                }
            }
            return $usuario;
        }

        /**
         * Carga los usuarios desde el archivo TXT donde se guardan
         */
        private static function CargarDesdeArchivo(){
            $usuarios = [];
            if( file_exists(TXT_USUARIOS) ){
                if(strlen(file_get_contents(TXT_USUARIOS)) > 0){
                    $usuarios = json_decode(file_get_contents(TXT_USUARIOS),true);
                }
            }
            return $usuarios;
        }

        /**
         * Lista todos los usuarios que coinciden con el nombre
         */
        public static function ListarUsuarios(){
            $listaInterna = self::CargarDesdeArchivo();
            $nombreUsuario = $_GET["nombre"];
            $lista = [];
            foreach ($listaInterna as $key => $value){
                if(strpos( strtoupper($value["nombre"]),strtoupper($nombreUsuario) ) !== false ){
                    array_push($lista,$value);
                }
            }
            if(count($lista) > 0){
                return json_encode($lista);
            }else{
                return "No existe ".$_GET["nombre"];
            }
        }
       
    }

?>