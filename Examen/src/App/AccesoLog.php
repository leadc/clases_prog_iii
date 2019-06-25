<?php
    namespace App;
    use BasicORM\BORMEntities\BORMObject;
    use BasicORM\BORMEntities\BORMObjectInterface;
    use BasicORM\LOGS\Log;

    /**
     * Clase para el manejo de usuarios
     */
    class AccesoLog extends BORMObject implements BORMObjectInterface{
        /** Id de log */
        public $id;
        /** id de usuario */
        public $idusuario;
        /** Método solicitado */
        protected $metodo;
        /** Ruta solicitada */
        public $ruta;
        /** Fecha y hora de acceso */
        public $fechayhora;

        function __construct(){
            $mappingString = '{ 
                "dbConnectionClass":"DB",
                "dbTable" : "accesosLog",
                "attributes" : {
                    "id" :  {"fieldName" : "id", "type" : "NUMERIC", "onInsert" : "NO_INSERT", "onUpdate" : "NO_UPDATE"},
                    "idusuario" :  {"fieldName" : "idusuario", "type" : "NUMERIC", "onInsert" : "INSERT", "onUpdate" : "UPDATE"},
                    "metodo" :  {"fieldName" : "metodo", "type" : "STRING", "onInsert" : "INSERT", "onUpdate" : "UPDATE"},
                    "ruta" :  {"fieldName" : "ruta", "type" : "STRING", "onInsert" : "INSERT", "onUpdate" : "UPDATE"},
                    "fechayhora" :  {"fieldName" : "fechayhora", "type" : "DATE", "onInsert" : "NO_INSERT", "onUpdate" : "NO_UPDATE"}
                }
            }';
            parent::__construct(json_decode($mappingString));
        }

        /**
         * Crea y devuelve un nuevo registro de log
         * arroja un error en caso de haberlo
         */
        public static function CrearRegistroDeAcceso($usuario, $metodo, $ruta){
            $acceso = new AccesoLog();
            $acceso->idusuario = $usuario;
            $acceso->metodo = $metodo;
            $acceso->ruta = $ruta;
            $acceso->Save();
            return $acceso;
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