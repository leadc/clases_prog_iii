<?php
    namespace App;
    use BasicORM\BORMEntities\BORMObject;
    use BasicORM\BORMEntities\BORMObjectInterface;
    use BasicORM\LOGS\Log;

    /**
     * Clase para el manejo de inscripciones
     */
    class Inscripcion extends BORMObject implements BORMObjectInterface{
        public $id;
        public $idAlumno;
        protected $idMateria;
        
        function __construct(){
            $mappingString = '{ 
                "dbConnectionClass":"DB",
                "dbTable" : "alumnosinscriptos",
                "attributes" : {
                    "id" :  {"fieldName" : "id", "type" : "NUMERIC", "onInsert" : "NO_INSERT", "onUpdate" : "NO_UPDATE"},
                    "idAlumno" :  {"fieldName" : "idAlumno", "type" : "NUMERIC", "onInsert" : "INSERT", "onUpdate" : "UPDATE"},
                    "idMateria" :  {"fieldName" : "idMateria", "type" : "NUMERIC", "onInsert" : "INSERT", "onUpdate" : "UPDATE"}
                }
            }';
            parent::__construct(json_decode($mappingString));
        }

        /** Devuelve un array de materias inscriptas para el alumno */
        public static function ObtenerMateriasAlumno($idAlumno){
            $inscripciones = (new Inscripcion)->findBy(["idAlumno = '$idAlumno'"]);
            $materiasInscriptas = [];
            for($i = 0; $i < \count($inscripciones); $i++){
                array_push($materiasInscriptas,Materia::CrearPorId($inscripciones[$i]->idMateria));
            }
            return $materiasInscriptas;
        }

        /** Devuelve un array de materias inscriptas para el alumno */
        public static function ObtenerAlumnosMateria($idMateria){
            $inscripciones = (new Inscripcion)->findBy(["idMateria = '$idMateria'"]);
            $alumnosInscriptos = [];
            for($i = 0; $i < \count($inscripciones); $i++){
                array_push($alumnosInscriptos,Usuario::GetUsuarioPorId($inscripciones[$i]->idAlumno));
            }
            return $alumnosInscriptos;
        }

        

        /**
         * Crea y devuelve una inscripcion a materia
         * arroja un error en caso de haberlo
         */
        public static function Inscribir($idAlumno, $idMateria){
            $materia = Materia::CrearPorId($idMateria);
            if($materia == false){
                throw new \Exception("La materia seleccionada no existe");
            }
            if($materia->cupos > 0){
                $materia->cupos = $materia->cupos - 1;
                $materia->Save();
            }else{
                throw new \Exception("La materia no tiene cupo disponible");
            }
            $insc = new Inscripcion();
            $insc->idAlumno = $idAlumno;
            $insc->idMateria = $idMateria;
            $insc->Save();
            return $insc;
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