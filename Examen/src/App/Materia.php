<?php
    namespace App;
    use BasicORM\BORMEntities\BORMObject;
    use BasicORM\BORMEntities\BORMObjectInterface;
    use BasicORM\LOGS\Log;

    /**
     * Clase para el manejo de Materias
     */
    class Materia extends BORMObject implements BORMObjectInterface{
        /** Id de Materia */
        public $id;
        /** Nombre de la materia */
        public $nombre;
        /** Cuatrimestre al que corresponde (1 o 2) */
        public $cuatrimestre;
        /** Cupo para la materia */
        public $cupos;
        
        function __construct(){
            $mappingString = '{ 
                "dbConnectionClass":"DB",
                "dbTable" : "materia",
                "attributes" : {
                    "id" :  {"fieldName" : "id", "type" : "NUMERIC", "onInsert" : "NO_INSERT", "onUpdate" : "NO_UPDATE"},
                    "nombre" :  {"fieldName" : "nombre", "type" : "STRING", "onInsert" : "INSERT", "onUpdate" : "UPDATE"},
                    "cuatrimestre" :  {"fieldName" : "cuatrimestre", "type" : "NUMERIC", "onInsert" : "INSERT", "onUpdate" : "UPDATE"},
                    "cupos" :  {"fieldName" : "cupos", "type" : "NUMERIC", "onInsert" : "INSERT", "onUpdate" : "UPDATE"}
                }
            }';
            parent::__construct(json_decode($mappingString));
        }

    
        /**
         * Crea y guarda los datos de una nueva materia
         * arroja un error en caso de haberlo
         */
        public static function NuevaMateria($nombre, $cuatrimestre, $cupo){
            try{
                $mat = new Materia();
                $mat->SetNombre($nombre);
                $mat->SetCuatrimestre($cuatrimestre);
                $mat->SetCupo($cupo);
                $mat->Save();
                return $mat;
            }catch(\Exception $e){
                Log::WriteLog(MAIN_LOG, ["Error al guardar materia: ".$e->getMessage(), "$nombre, $cuatrimestre, $cupo"]);
                throw $e;
            }
        }

        /** Establece el nombre del artículo en la instacia actual */
        public function SetNombre($data){
            $this->nombre = $data;
        }

        /** Establece el cuatrimestre validandolo, arroja una excepción en caso de error */
        public function SetCuatrimestre($data){
            try{
                if($data != "2" && $data != "1"){
                    throw new \Exception();
                }else{
                    $this->cuatrimestre = $data;
                }
            }catch(\Exception $e){
                throw new \Exception("Número de cuatrimestre inválido (Debe ser 1 o 2)");
            }
        }

        /** Establece el cupo validandolo, arroja una excepción en caso de error */
        public function SetCupo($data){
            try{
                if(\is_numeric($data) && $data >= 0){
                    $this->cupos = $data;
                }else{
                    throw new \Exception();
                }
            }catch(\Exception $e){
                throw new \Exception("El cupo no es correcto (Debe ser un número mayor o igual a 0)");
            }
        }

        /** Busca y devuelve una materia por id, si no la encuentra devuelve false */
        public static function CrearPorId($idMateria){
            $mat = new Materia();
            $res = $mat->findBy(["id = '$idMateria'"]);
            if(count($res)> 0){
                return $res[0];
            }
            return false;
        }

        /** Devuelve un listado completo de materias */
        public static function ListarMaterias(){
            return (new Materia())->findBy();
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
                throw new \Exception("Error al guardar Materia");
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