<?php
    namespace API;

    use \Psr\Http\Message\ServerRequestInterface as Request;
    use \Psr\Http\Message\ResponseInterface as Response;
    use App\Usuario;
    use App\Compra;
    use App\JWTClass;
    use BasicORM\LOGS\Log;

    use Middleware\JWTMiddleware;

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
         * /usuario
         * [POST]
         * Alta de usuario
         */
        public static function NuevaCompra(Request $request, Response $response){
            // Obtengo los datos del body
            $datos = $request->getParsedBody();
            // Valido haber recibido el usuario
            if(!isset($datos["articulo"]) || !isset($datos["fecha"]) || !isset($datos["precio"])){
                return $response->withJson("No se recibió articulo, fecha o precio", 401, JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK);
            }
            $token = $request->getHeader(JWTMiddleware::TOKEN_HEADER);
            $usuario = JWTClass::ObtenerUsuario($token[0]);
            try{
                // Guardo la nueva compra
                $compra = Compra::NuevaCompra($datos["articulo"], $datos["fecha"], $datos["precio"], $usuario->id);
                $fotos = $request->getUploadedFiles();
                if(count($fotos)>0){
                    $foto = $fotos['foto'];
                    $ext = \explode(".",$foto->getClientFilename());
                    $ext = $ext[count($ext)-1];
                    $direccion = __DIR__."/../../IMGCompras/".$compra->id." - ".$compra->articulo.".".$ext;
                    $foto->moveTo($direccion);
                }
                // Devuelvo la respuesta
                return $response->withJson($compra, 200, JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK);
            }catch(\Exception $e){
                return $response->withJson($e->getMessage(), 500, JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK);
            }
        }

        /**
         * Devuelve un listado completo de usuarios en el sistema
         */
        public static function ListarUsuarios($request, $response){
            try{
                $listaUsuairos = Usuario::ListarUsuarios();
                return $response->withJson($listaUsuairos, 200, JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK);
            }catch(\Exception $e){
                return $response->withJson("Ocurrió un error al generar el listado", 500, JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK);
            }
        }

        /**
         * Devuelve un listado de compras ingresadas por usuario o todas las compras si es un administrador
         */
        public static function ListarCompras($request, $response){
            try{
                // Obtengo el TOKEN
                $token = $request->getHeader(JWTMiddleware::TOKEN_HEADER);
                // Obtengo el usuario
                $usuario = JWTClass::ObtenerUsuario($token[0]);
                $compras = [];
                // Si es admnistrador genero un listado global sino uno con sus compras
                if($usuario->perfil == Usuario::PERFIL_ADMINISTRADOR){
                    $compras = Compra::ListarCompras();
                }else{
                    $compras = Compra::ListarCompras($usuario->id);
                }
                // Devuelvo los resultados
                return $response->withJson($compras, 200, JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK);
            }catch(\Exception $e){
                return $response->withJson("Ocurrió un error al generar el listado", 500, JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK);
            }
        }

    }