<?php
    namespace App;
    use BasicORM\BORMEntities\BORMObject;
    use BasicORM\BORMEntities\BORMObjectInterface;
    use BasicORM\LOGS\Log;

    /**
     * Clase para el manejo de usuarios
     */
    class Usuario extends BORMObject implements BORMObjectInterface{
        /** legajo de usuario */
        public $legajo;
        /** Nombre de usuario */
        public $nombre;
        /** Clave de usuario */
        protected $clave;
        /** Tipo de usuario (admin/alumno/profesor) */
        public $tipo;
        /** Email del usuario */
        public $email;
        /** Materias que dicta el usuario */
        public $materiasDictadas;
        /** foto del usuario */
        public $foto;

        const TIPO_ADMINISTRADOR = "admin";
        const TIPO_ALUMNO = "alumno";
        const TIPO_PROFESOR = "profesor";

        
        function __construct(){
            $mappingString = '{ 
                "dbConnectionClass":"DB",
                "dbTable" : "usuarios",
                "attributes" : {
                    "legajo" :  {"fieldName" : "legajo", "type" : "NUMERIC", "onInsert" : "NO_INSERT", "onUpdate" : "NO_UPDATE"},
                    "nombre" :  {"fieldName" : "nombre", "type" : "STRING", "onInsert" : "INSERT", "onUpdate" : "UPDATE"},
                    "clave" :  {"fieldName" : "clave", "type" : "STRING", "onInsert" : "INSERT", "onUpdate" : "UPDATE"},
                    "tipo" :  {"fieldName" : "tipo", "type" : "STRING", "onInsert" : "INSERT", "onUpdate" : "UPDATE"},
                    "email" :  {"fieldName" : "email", "type" : "STRING", "onInsert" : "INSERT", "onUpdate" : "UPDATE"},
                    "materiasDictadas" :  {"fieldName" : "materiasDictadas", "type" : "STRING", "onInsert" : "INSERT", "onUpdate" : "UPDATE"},
                    "foto" :  {"fieldName" : "foto", "type" : "STRING", "onInsert" : "INSERT", "onUpdate" : "UPDATE"}
                }
            }';
            parent::__construct(json_decode($mappingString));
        }

        /**
         * Devuevlve true o false para indicar si existen administradores dados de alta en el sistema
         */
        public static function HayAdministradores(){
            $usuario = new Usuario();
            $result = (new Usuario())->FindBy(["tipo = '".USUARIO::TIPO_ADMINISTRADOR."'"]);
            return (count($result) > 0);
        }

        /**
         * Crea y devuelve un nuevo usuario
         * arroja un error en caso de haberlo
         */
        public static function CrearUsuario($nombre, $clave, $tipo){
            if(Usuario::GetUsuarioPorNombre($nombre) !== false){
                throw new \Exception("El nombre de usuario ya está usado.");
            }
            $usuario = new Usuario();
            $usuario->nombre = $nombre;
            $usuario->clave = $clave;
            $usuario->SetTipo($tipo);
            $usuario->Save();
            return $usuario;
        }

        /** Valida y establece el tipo de usuario */
        public function SetTipo($dato){
            if($dato != self::TIPO_ADMINISTRADOR && $dato != self::TIPO_PROFESOR && $dato != self::TIPO_ALUMNO){
                throw new \Exception("El tipo de usuario es incorrecto");
            }
            $this->tipo = $dato;
        }

        /**
         * Verifica el login segùn los datos pasados por parámetro
         * Devuelve al usuario o lanza excepciones con mensajes correspondientes
         */
        public static function DoLogin($nombre, $legajo){
            $usuario = self::GetUsuarioPorNombre($nombre);
            
            if($usuario === false){
                throw new \Exception("El nombre de usuario no existe");
            }

            if($usuario->legajo != $legajo){
                throw new \Exception("El legajo seleccionado es incorrecto");
            }
            
            return $usuario;
        }

        /**
         * Devuelve un usuario buscàndolo por nombre o false en caso de no encontrar coinc  encias
         */
        public static function GetUsuarioPorNombre($nombre){
            $usuario = new Usuario();
            $res = $usuario->findBy(["nombre = '$nombre'"]);
            if(count($res)> 0){
                return $res[0];
            }
            return false;
        }

        /** Devuelve un usuario según su ID o false */
        public static function GetUsuarioPorId($id){
            $usuario = new Usuario();
            $res = $usuario->findBy(["legajo = '$id'"]);
            if(count($res)> 0){
                return $res[0];
            }
            return false;
        }

        /**
         * Devuelve un array de usuarios ordenados por nombre
         */
        public static function ListarUsuarios(){
            return (new Usuario)->FindBy([],["nombre asc"]);
        }

         /**
         * Save function to store or update the object in the database
         */
        function Save(){
            $rowsAffected = 0;
            if(isset($this->legajo)){
                $rowsAffected = $this->UpdateSQL(["legajo = ".$this->legajo]);
            }else{
                $rowsAffected = $this->InsertSQL();
                if($rowsAffected > 0){
                    $this->legajo = $this->Max("legajo");
                }
            }
            if($rowsAffected == 0){
                throw new \Exception("Error al guardar Usuario");
            }else{
                $this->Refresh();
            }
        }
        /**
         * Deletes the current object from the database
         */
        function Delete(){
            if(isset($this->legajo)){
                $this->DeleteBy(["legajo = ".$this->legajo]);
                return true;
            }
            return false;
        }
        /**
         * Refresh function to update the object with the database values
         */
        function Refresh(){
            $this->RefreshBy(["legajo = ".$this->legajo]);
        }
    }


?>