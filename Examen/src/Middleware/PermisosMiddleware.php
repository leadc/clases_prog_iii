<?php

    namespace Middleware;
    use App\JWTClass;
    use App\Usuario;
    use App\AccesoLog;
    /**
     * Clase para el manejo de tokens a través de middleware
     */
    class PermisosMiddleware{

        /**
         * Valida el acceso solo a administradores
         */
        public static function AccesoListaUsuarios($request,$response, $next){
            $newRespose = '';
            // Obtener token
            $token = $request->getHeader(JWTMiddleware::TOKEN_HEADER);
            // Obtener el usuario a partir del token
            $usuario = JWTClass::ObtenerUsuario($token[0]);
            // Validar que sea un administrador
            if($usuario->perfil == Usuario::PERFIL_ADMINISTRADOR){
                // Si es administrador continúo la ejecución
                $newRespose = $next($request, $response);
            }else{
                // Si no lo es devuelvo un Hola
                $newRespose = $response->withJson('Hola', 401, JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK);
            }
            // Devuelvo la respuesta
            return $newRespose;
        }

        /**
         * Registra un log de accesos
         */
        public static function LogAccesos($request,$response, $next){
            // Ejecuto la ruta solicitada
            $newRespose = $next($request, $response);
            // Obtener token
            $token = $newRespose->getHeader(JWTMiddleware::TOKEN_HEADER);
            // Si se generó un token hay registro del usuario por lo tanto se puede guardar el registro del acceso por usuario
            if(count($token)>0){
                // Obtener el usuario a partir del token
                $usuario = JWTClass::ObtenerUsuario($token[0]);
                // Registro el acceso
                $route = $request->getAttribute('route')->getPattern();
                AccesoLog::CrearRegistroDeAcceso($usuario->id, $request->getMethod(),$route);
            }
            // Devuelvo la respuesta
            return $newRespose;
        }
    }


?>