<?php
    namespace API;

    use \Psr\Http\Message\ServerRequestInterface as Request;
    use \Psr\Http\Message\ResponseInterface as Response;
    use \Firebase\JWT\JWT;

    class API{

        /**
         * Método que maneja el login de la aplicación
         */
        public static function Login(Request $request, Response $response){
            // Obtengo los datos del body
            $datos = $request->getParsedBody();
            
            // Valido haber recibido el usuario
            if(!isset($datos["usuario"]) || !isset($datos["clave"])){
                return $response->withJson("No se recibió usuario y clave", 401, JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK);
            }

            // Valido el usuario y cotraseña
            if($datos["usuario"] == "leandro" && $datos["clave"] == "1234"){
                $response = $response->withJson(array("usuario" => "leandro", "acceso" => "total"), 200, JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK);
            }else{
                $response = $response->withJson("Usuario o contraseña incorrectos", 500, JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK);
            }

            // Devuelvo la respuesta
            return $response;
        }

        
    }