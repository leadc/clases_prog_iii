<?php

    namespace Middleware;
    use App\JWTClass;
    /**
     * Clase para el manejo de tokens a través de middleware
     */
    class JWTMiddleware{

        /**
         * Valida el acceso solo a administradores
         */
        public static function AccesoAdministradores($request,$response, $next){
            
            // Valido el usuario con el método de validación de la API
            $newResponse = $next($request, $response);

            // Evalúo si la respuesta de la API es satisfactoria
            if($newResponse->getStatusCode() == 200){
                // Si la respuesta es OK genero un token y lo agrego a la respuesta
                $datos = json_decode($newResponse->getBody());
                
                // Creo el token
                $token = JWTClass::CrearToken($datos);

                // Se teo el nuevo header para enviarlo 
                $newResponse = $newResponse->withHeader('Token', $token);
            }
            
            // Envío la respuesta
            return $newResponse;
        }

        /**
         * Middleware para verificar un token de acceso a los distintos servicios de la API
         * El token debe ser recibido en el header 'Token'
         */
        public static function AccesoGeneral($request, $response, $next){
            // Busco el token en el header
            $token = $request->getHeader('Token');
            
            // Verifico que se haya recibido el token
            if(count($token) == 0){
                return $response->withJson("No se recibió token de autentificación", 401, JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK);
            }else{
                $token = $token[0];
            }

            // Valido el token
            if(JWTClass::ValidarToken($token) === false){
                return $response->withJson("Su sesión expiró", 500, JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK);
            }
            // Continúo a travéz del middleware
            $response = $next($request, $response);

            // Renuevo el token de usuario con un nuevo tiempo de expiración
            $token = JWTClass::RenovarToken($token);

            // Devuelvo la respuesta con el nuevo token en el header
            return $response->withHeader('Token', $token); 
        }
    }


?>