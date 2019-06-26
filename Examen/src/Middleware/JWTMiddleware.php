<?php

    namespace Middleware;
    use App\JWTClass;
    use App\Usuario;
    /**
     * Clase para el manejo de tokens a través de middleware
     */
    class JWTMiddleware{
        /** Constante que define el header en el que se enviará y recibirá el token */
        const TOKEN_HEADER = "Token";

        /**
         * Middleware para controlar el acceso al login 
         * Si la respuesta es correcta agrega el token a la misma o devuelve el mensaje de error
         */
        public static function AccesoLogin($request,$response, $next){
            
            // Valido el usuario con el método de validación de la API
            $newResponse = $next($request, $response);

            // Evalúo si la respuesta de la API es satisfactoria
            if($newResponse->getStatusCode() == 200){
                // Si la respuesta es OK genero un token y lo agrego a la respuesta
                $datos = json_decode($newResponse->getBody());
                
                // Creo el token
                $token = JWTClass::CrearToken($datos);

                // Seteo el token en el body de la respuesta
                $newResponse = $newResponse->withJson($token,200);
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
            $token = $request->getHeader(JWTMiddleware::TOKEN_HEADER);
            
            // Verifico que se haya recibido el token
            if(count($token) == 0){
                return $response->withJson("Debe iniciar sesión para poder realizar esta acción", 401, JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK);
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
            return $response->withHeader(JWTMiddleware::TOKEN_HEADER, $token); 
        }

        /**
         * Middleware para verificar un token de acceso a los distintos servicios de la API
         * En caso de no haber usuarios de administrador creados deja permite el paso para crear uno
         * El token debe ser recibido en el header 'Token'
         */
        public static function AccesoCrearUsuario($request, $response, $next){
            // Busco el token en el header
            $token = $request->getHeader(JWTMiddleware::TOKEN_HEADER);

            // Verifico si no hay usuarios administradores
            if(!Usuario::HayAdministradores()){
                // Devuelvo un Token vacío para obligar a hacer login y permito seguir sin validar más nada
                $token = '';
            }else{
                // Verifico que se haya recibido el token
                if(count($token) == 0){
                    return $response->withJson("Debe iniciar sesión para poder realizar esta acción", 401, JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK);
                }else{
                    $token = $token[0];
                }
                // Valido el token recibido 
                if(JWTClass::ValidarToken($token) === false){
                    return $response->withJson("Su sesión expiró", 500, JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK);
                }
                // Renuevo el token de usuario con un nuevo tiempo de expiración
                $token = JWTClass::RenovarToken($token);
            }
            // Continúo a travéz del middleware
            $response = $next($request, $response);
            // Devuelvo la respuesta con el nuevo token en el header
            return $response->withHeader(JWTMiddleware::TOKEN_HEADER, $token); 
        }
    }


?>