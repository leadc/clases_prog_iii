<?php
    namespace App;
    use BasicORM\BORMEntities\BORMObject;
    use BasicORM\BORMEntities\BORMObjectInterface;
    use BasicORM\LOGS\Log;

    /**
     * Clase para el manejo de usuarios
     */
    class Usuario extends BORMObject implements BORMObjectInterface{
        /** Id de usuario */
        public $id;
        /** Nombre de usuario */
        public $nombre;
        /** Clave de usuario */
        protected $clave;
        /** Sexo de usuario (F/M) */
        public $sexo;
        /** Perfil del usuario */
        public $perfil;

        /** Perfil de administrador */
        const PERFIL_ADMINISTRADOR = "administrador";
        /** Perfil de usuario general */
        const PERFIL_USUARIO = "usuario";

        
        function __construct(){
            $mappingString = '{ 
                "dbConnectionClass":"DB",
                "dbTable" : "usuarios",
                "attributes" : {
                    "id" :  {"fieldName" : "id", "type" : "NUMERIC", "onInsert" : "NO_INSERT", "onUpdate" : "NO_UPDATE"},
                    "nombre" :  {"fieldName" : "nombre", "type" : "STRING", "onInsert" : "INSERT", "onUpdate" : "UPDATE"},
                    "clave" :  {"fieldName" : "clave", "type" : "STRING", "onInsert" : "INSERT", "onUpdate" : "UPDATE"},
                    "perfil" :  {"fieldName" : "perfil", "type" : "STRING", "onInsert" : "INSERT", "onUpdate" : "UPDATE"},
                    "sexo" :  {"fieldName" : "sexo", "type" : "STRING", "onInsert" : "INSERT", "onUpdate" : "UPDATE"}
                }
            }';
            parent::__construct(json_decode($mappingString));
        }

        /**
         * Devuevlve true o false para indicar si existen administradores dados de alta en el sistema
         */
        public static function HayAdministradores(){
            $usuario = new Usuario();
            $result = (new Usuario())->FindBy(["perfil = '".USUARIO::PERFIL_ADMINISTRADOR."'"]);
            return (count($result) > 0);
        }

        /**
         * Crea y devuelve un nuevo usuario
         * arroja un error en caso de haberlo
         */
        public static function CrearUsuario($nombre, $clave, $sexo, $perfil = 'usuario'){
            if(Usuario::GetUsuarioPorNombre($nombre) !== false){
                throw new \Exception("El nombre de usuario ya está usado.");
            }
            $usuario = new Usuario();
            $usuario->nombre = $nombre;
            $usuario->clave = $clave;
            $usuario->sexo = $sexo;
            $usuario->perfil = $perfil;
            $usuario->Save();
            return $usuario;
        }

        /**
         * Verifica el login segùn los datos pasados por parámetro
         * Devuelve al usuario o lanza excepciones con mensajes correspondientes
         */
        public static function DoLogin($nombre, $sexo, $clave){
            $usuario = self::GetUsuarioPorNombre($nombre);
            
            if($usuario === false){
                throw new \Exception("El nombre de usuario no existe");
            }

            if($usuario->sexo != $sexo){
                throw new \Exception("El sexo seleccionado es incorrecto");
            }

            if($usuario->clave != $clave){
                throw new \Exception("Clave de usuario incorrecta");
            }

            return $usuario;
        }

        /**
         * Devuelve un usuario buscàndolo por nombre o false en caso de no encontrar coincidencias
         */
        public static function GetUsuarioPorNombre($nombre){
            $usuario = new Usuario();
            $res = $usuario->findBy(["nombre = '$nombre'"]);
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
            if(isset($this->id)){
                $rowsAffected = $this->UpdateSQL(["id = ".$this->id]);
            }else{
                $rowsAffected = $this->InsertSQL();
                if($rowsAffected > 0){
                    $this->id = $this->Max("id");
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
            if(isset($this->id)){
                $this->DeleteBy(["id = ".$this->id]);
                return true;
            }
            return false;
        }
        /**
         * Refresh function to update the object with the database values
         */
        function Refresh(){
            $this->RefreshBy(["id = ".$this->id]);
        }
    }


?>