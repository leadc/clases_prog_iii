<?php
    namespace API;

    use \Psr\Http\Message\ServerRequestInterface as Request;
    use \Psr\Http\Message\ResponseInterface as Response;
    use App\Usuario;

    class API{

        /**
         * /usuario
         * [POST]
         * Alta de usuario
         */
        public static function AltaUsuario(Request $request, Response $response){
            // Obtengo los datos del body
            $datos = $request->getParsedBody();
            // Valido haber recibido el usuario
            if(!isset($datos["nombre"]) || !isset($datos["clave"]) || !isset($datos["sexo"])){
                return $response->withJson("No se recibió nombre, clave o sexo", 401, JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK);
            }
            $perfil = "usuario";
            if( isset($datos["perfil"]) ){
                $perfil = $datos["perfil"];
            }

            try{
                // Creo el nuevo usuario
                $usuario = Usuario::CrearUsuario($datos["nombre"], $datos["clave"], $datos["sexo"], $perfil);
                // Devuelvo la respuesta
                return $response->withJson($usuario, 200, JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK);
            }catch(\Exception $e){
                return $response->withJson($e->getMessage(), 500, JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK);
            }
        }

        
    }