<?php
    namespace App;
    use BasicORM\BORMEntities\BORMObject;
    use BasicORM\BORMEntities\BORMObjectInterface;
    use BasicORM\LOGS\Log;

    /**
     * Clase para el manejo de usuarios
     */
    class Compra extends BORMObject implements BORMObjectInterface{
        /** Id de compra */
        public $id;
        /** Fecha de compra */
        public $fecha;
        /** Artículo comprados */
        public $articulo;
        /** Precio de compra */
        public $precio;
        /** Usuario que registró la compra */
        public $idUsuario;

        
        function __construct(){
            $mappingString = '{ 
                "dbConnectionClass":"DB",
                "dbTable" : "compras",
                "attributes" : {
                    "id" :  {"fieldName" : "id", "type" : "NUMERIC", "onInsert" : "NO_INSERT", "onUpdate" : "NO_UPDATE"},
                    "fecha" :  {"fieldName" : "fecha", "type" : "DATE", "onInsert" : "NO_INSERT", "onUpdate" : "NO_UPDATE"},
                    "articulo" :  {"fieldName" : "articulo", "type" : "STRING", "onInsert" : "INSERT", "onUpdate" : "UPDATE"},
                    "precio" :  {"fieldName" : "precio", "type" : "NUMERIC", "onInsert" : "INSERT", "onUpdate" : "UPDATE"},
                    "idUsuario" :  {"fieldName" : "idusuario", "type" : "NUMERIC", "onInsert" : "INSERT", "onUpdate" : "UPDATE"}
                }
            }';
            parent::__construct(json_decode($mappingString));
        }

    
        /**
         * Crea y guarda los datos de una nueva compra
         * arroja un error en caso de haberlo
         */
        public static function NuevaCompra($articulo, $fecha, $precio, $idUsuario){
            try{
                $compra = new Compra();
                $compra->SetArticulo($articulo);
                $compra->SetPrecio($precio);
                $compra->SetFecha($fecha);
                $compra->idUsuario = $idUsuario;
                $compra->Save();
                return $compra;
            }catch(\Exception $e){
                Log::WriteLog(MAIN_LOG, ["Error al guardar artículo: ".$e->getMessage(), "$articulo, $fecha, $precio"]);
                throw $e;
            }
        }

        /**
         * Si se pasa el usuario devuelve el listado de compras de ese usuario sino un listado de compras global
         */
        public static function ListarCompras($usuario = null){
            $compras = [];
            if($usuario != null){
                $compras = (new Compra)->FindBy(["idusuario = $usuario"], ["fecha asc"]);
            }else{
                $compras = (new Compra)->FindBy([], ["fecha asc"]);
            }
            return $compras;
        }

        /** Establece el nombre del artículo en la instacia actual */
        public function SetArticulo($data){
            $this->articulo = $data;
        }

        /** Establece la fecha de compra validándola, arroja una excepción en caso de error */
        public function SetFecha($data){
            try{
                if(\strtotime($data)){
                    $this->fecha = $data;
                }else{
                    throw new \Exception();
                }
            }catch(\Exception $e){
                throw new \Exception("Fecha de compra no válida");
            }
        }

        /** Establece el precio validandolo, arroja una excepción en caso de error */
        public function SetPrecio($data){
            try{
                if(\is_numeric($data)){
                    $this->precio = $data;
                }else{
                    throw new \Exception();
                }
            }catch(\Exception $e){
                throw new \Exception("Precio inválido");
            }
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
                throw new \Exception("Error al guardar Compra");
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