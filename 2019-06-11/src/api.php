<?php
    use \Psr\Http\Message\ServerRequestInterface as Request;
    use \Psr\Http\Message\ResponseInterface as Response;
    use \Firebase\JWT\JWT;

    class API{

        public static $secret = "RE_SECRET__REEE";

        /**
         * /getToken
         */
        public static function GetToken(Request $request, Response $response){
            // Obtengo los datos del body
            $datos = $request->getParsedBody();

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
            $response = $response->withHeader('Token', $token);

            // Envío la respuesta
            return $response->withJson("Mirá el header", 200, JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK);
        }

        public static function VerifyToken(Request $request, Response $response){
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
            }catch(Exception $e){
                return $response->withJson($e->getMessage(), 500, JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK);
            }
            return $response->withJson("OK: ".json_encode($decodificado), 200, JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK);
        }
    }