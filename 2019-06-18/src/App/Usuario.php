<?php
    namespace App;
    use BasicORM\BORMEntities\BORMObject;
    use BasicORM\BORMEntities\BORMObjectInterface;

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