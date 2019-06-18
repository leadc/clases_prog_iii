<?php

    namespace Middleware;

    /**
     * Clase para el manejo de tokens a través de middleware
     */
    class JWTMiddleware{

        /** Clave  para codificar los token */
        public static $secret = "RE_SECRET__REEE";

        /**
         * Devuelve un token
         */
        public static function GetToken(Request $request, Response $response, $next){
            
            // Valido el usuario con el método de validación de la API
            $newRespose = $next($request, $response);
            
            // Evalúo si la respuesta de la API es satisfactoria
            if($newResponse->getStatusCode() == 200){
                // Si la respuesta es OK genero un token y lo agrego a la respuesta
                $datos = json_decode($newResponse->getBody());
                // Tiempo actual en formato UNIX
                $time = time();

                // Payload con los datos a enviar en el token
                $payload = array(
                    'iat' => $time,
                    'exp' => $time + (60*60), // seconds
                    'data' => $datos,
                    'app' => "API REST"
                );

                // Creación del TOKEN
                $token = JWT::encode($payload, self::$secret);

                // Se teo el nuevo header para enviarlo 
                $newResponse = $newResponse->withHeader('Token', $token)->getBody()->write("Login ok");
            }
            
            // Envío la respuesta
            return $newResponse;
        }

        /**
         * Verifica un token
         */
        public static function VerifyToken(Request $request, Response $response, $next){
            // Busco el token en el header
            $token = $request->getHeader('Token');
            
            // Verifico que se haya recibido el token
            if(count($token) == 0){
                return $response->withJson("No se recibió token de autentificación", 401, JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK);
            }else{
                $token = $token[0];
            }

            // Decodifico el Token
            try{
                $decodificado = JWT::decode($token, self::$secret, ["HS256"]);
                $response = $next($request, $response);
            }catch(Exception $e){
                return $response->withJson($e->getMessage(), 500, JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK);
            }
            return $response->withJson("OK: ".json_encode($decodificado), 200, JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK);
        }
    }


?>