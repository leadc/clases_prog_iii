<?php
    namespace API;

    use \Psr\Http\Message\ServerRequestInterface as Request;
    use \Psr\Http\Message\ResponseInterface as Response;
    use Firebase\JWT\JWT;
    use App\Usuario;
    use App\JWTClass;

    class API{

        /**
         * /login
         * [POST]
         * Login de usuario
         */
        public static function LoginUsuario(Request $request, Response $response){
            // Obtengo los datos del body
            $datos = $request->getParsedBody();
            // Valido haber recibido el usuario
            if(!isset($datos["nombre"]) || !isset($datos["clave"]) || !isset($datos["sexo"])){
                return $response->withJson("No se recibió nombre, clave o sexo", 401, JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK);
            }
            // Valido el login y devuelvo los datos del mismo en caso de ser correcto en caso contrario envio un mensaje de error
            try{
                return $response->withJson(Usuario::DoLogin($datos["nombre"],$datos["sexo"],$datos["clave"]), 200, JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK);
            }catch(\Exception $e){
                return $response->withJson($e->getMessage(), 500, JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK);
            }
        }
        

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

        /**
         * Testeos
         */
        public static function Testing($request, $response){
            $token = "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE1NjA5MDk3MDksImV4cCI6MTU2MDkwOTcwOSwidXN1YXJpbyI6eyJpZCI6Niwibm9tYnJlIjoiYWRtaW4zIiwic2V4byI6Im1hc2N1bGlubyIsInBlcmZpbCI6ImFkbWluIn19.Zu0LxAuQbmGWws1kYKWxCBrgEMe8IjekIXoZL3xfp7E";
            $decodificado = JWT::decode($token, "S3kr3t$", ["HS256"]);
            return $response->withJson($decodificado);
        }

        
    }