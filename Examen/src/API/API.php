<?php
    namespace API;

    use \Psr\Http\Message\ServerRequestInterface as Request;
    use \Psr\Http\Message\ResponseInterface as Response;
    use App\Usuario;
    use App\Materia;
    use App\Inscripcion;
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
            if(!isset($datos["nombre"]) || !isset($datos["legajo"]) ){
                return $response->withJson("No se recibió nombre o legajo", 401, JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK);
            }
            // Valido el login y devuelvo los datos del mismo en caso de ser correcto en caso contrario envio un mensaje de error
            try{
                return $response->withJson(Usuario::DoLogin($datos["nombre"],$datos["legajo"]), 200, JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK);
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
            if(!isset($datos["nombre"]) || !isset($datos["clave"]) || !isset($datos["tipo"])){
                return $response->withJson("No se recibió nombre, clave o tipo", 401, JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK);
            }
            try{
                // Creo el nuevo usuario
                $usuario = Usuario::CrearUsuario($datos["nombre"], $datos["clave"], $datos["tipo"]);
                // Devuelvo la respuesta
                return $response->withJson($usuario, 200, JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK);
            }catch(\Exception $e){
                return $response->withJson($e->getMessage(), 500, JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK);
            }
        }

        /**
         * /materia
         * [POST]
         * Alta de materia
         */
        public static function NuevaMateria(Request $request, Response $response){
            // Obtengo los datos del body
            $datos = $request->getParsedBody();
            // Valido haber recibido el usuario
            if(!isset($datos["nombre"]) || !isset($datos["cuatrimestre"]) || !isset($datos["cupos"])){
                return $response->withJson("No se recibió nombre, cuatrimestre o cupos", 401, JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK);
            }

            try{
                // Guardo la nueva materia
                $materia = Materia::NuevaMateria($datos["nombre"], $datos["cuatrimestre"], $datos["cupos"]);
                // Devuelvo la respuesta
                return $response->withJson($materia, 200, JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK);
            }catch(\Exception $e){
                return $response->withJson($e->getMessage(), 500, JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK);
            }
        }

        /**
         * Modifica un usuario
         * /usuario/{legajo}
         */
        public static function ModificarUsuario($request, $response, $args){
            try{
                // Valido haber recibido el usuario
                if(!isset($args["legajo"])){
                    return $response->withJson("No se recibió el id del usuario", 401, JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK);
                }
                $usuario = Usuario::GetUsuarioPorId($args["legajo"]);
                // Valido que el legajo corresponda a un usuario
                if($usuario == false){
                    return $response->withJson("El legajo no corresponde con un usuario registrado", 404, JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK);
                }
                // Obtengo los datos a modificar
                $datos = $request->getParsedBody();
                if(isset($datos["email"])){
                    $usuario->email = $datos["email"];
                }

                // Si es administrador o alumno recibe foto
                if($usuario->tipo == Usuario::TIPO_ADMINISTRADOR || $usuario->tipo == Usuario::TIPO_ALUMNO){
                    // Obtengo y guardo la foto
                    $fotos = $request->getUploadedFiles();
                    if(count($fotos)>0){
                        $foto = $fotos['foto'];
                        $ext = \explode(".",$foto->getClientFilename());
                        $ext = $ext[count($ext)-1];
                        $direccion = __DIR__."/../../IMGUsuarios/".$usuario->legajo.".".$ext;
                        $foto->moveTo($direccion);
                        $usuario->foto = "$usuario->legajo.$ext";
                    }
                }
                // Si es profesor puede recibir materias dictadas
                if($usuario->tipo == Usuario::TIPO_ADMINISTRADOR || $usuario->tipo == Usuario::TIPO_PROFESOR){
                    if(isset($datos["materiasDictadas"])){
                        $usuario->materiasDictadas = json_encode($datos["materiasDictadas"]);
                    }
                }
                // Guardo las modificaciones
                $usuario->save();
                return $response->withJson($usuario, 200, JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK);
            }catch(\Exception $e){
                return $response->withJson("Ocurrió un error al modificar el usuario", 500, JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK);
            }
        }

        /**
         * Realiza la inscripcion a una materia
         * /inscripcion/{idMateria}
         */
        public static function InscripcionMateria($request, $response, $args){
            try{
                // Obtengo el TOKEN
                $token = $request->getHeader(JWTMiddleware::TOKEN_HEADER);
                // Obtengo el usuario
                $usuario = JWTClass::ObtenerUsuario($token[0]);
                if($usuario->tipo != Usuario::TIPO_ALUMNO){
                    throw new \Exception("Solo alumnos pueden inscribirse a materias");
                }

                if(!isset($args["idMateria"])){
                    throw new \Exception("No se recibió idMateria");                    
                }
                
                Inscripcion::Inscribir($usuario->legajo, $args["idMateria"]);
                // Devuelvo los resultados
                return $response->withJson("Inscripción completa!", 200, JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK);
            }catch(\Exception $e){
                return $response->withJson($e->getMessage(), 500, JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK);
            }
        }

        /**
         * Listado de materias
         * /materias
         */
        public static function ListarMaterias($request, $response, $args){
            try{
                // Obtengo el TOKEN
                $token = $request->getHeader(JWTMiddleware::TOKEN_HEADER);
                // Obtengo el usuario del token
                $usuario = JWTClass::ObtenerUsuario($token[0]);
                // Obtengo el usuario de la base de datos
                $usuario = Usuario::GetUsuarioPorId($usuario->legajo);

                // Si es alumno puede ver sus materias inscriptas
                if($usuario->tipo == Usuario::TIPO_ALUMNO){
                    $inscripciones = Inscripcion::ObtenerMateriasAlumno($usuario->legajo);
                    return $response->withJson($inscripciones, 200, JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK);
                }

                // Si es profesor puede ver que materias tiene a cargo
                if($usuario->tipo == Usuario::TIPO_PROFESOR){
                    return $response->withJson(json_decode($usuario->materiasDictadas),200,JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK);
                }

                // Si es admin puede ver la lista de todas las materias
                if($usuario->tipo == Usuario::TIPO_ADMINISTRADOR){
                    $materias = Materia::ListarMaterias();
                    return $response->withJson($materias,200,JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK);
                }
            }catch(\Exception $e){
                return $response->withJson($e->getMessage(), 500, JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK);
            }
        }

        /**
         * Lista alumnos por materia
         * /materias/{id}
         */
        public static function ListarAlumnosPorMateria($request, $response, $args){
            try{
                // Obtengo el TOKEN
                $token = $request->getHeader(JWTMiddleware::TOKEN_HEADER);
                // Obtengo el usuario del token
                $usuario = JWTClass::ObtenerUsuario($token[0]);
                // Obtengo el usuario de la base de datos
                $usuario = Usuario::GetUsuarioPorId($usuario->legajo);
                // Valido recibir el id 
                if(!isset($args["id"])){
                    throw new \Exception("No se recibió idMateria");                    
                }
                // Valido que el id sea de materia
                $materia = Materia::CrearPorId($args["id"]);
                if($materia == false){
                    throw new \Exception("El id recibido no corresponde a una materia");                    
                }

                // Si es alumno no puede acceder al listado
                if($usuario->tipo == Usuario::TIPO_ALUMNO){
                    return $response->withJson("Solo profesores o administradores pueden acceder al listado", 500, JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK);
                }

                // Si es profesor y tiene a cargo la materia puede ver los alumnos inscriptos en la materia
                if($usuario->tipo == Usuario::TIPO_PROFESOR){
                    $materiasACargo = json_decode($usuario->materiasDictadas);
                    if(!in_array($materia->nombre, $materiasACargo)){
                        throw new \Exception("Debe estar a cargo de la materia");                    
                    }
                    $materias = Inscripcion::ObtenerAlumnosMateria($args["id"]);
                    return $response->withJson($materias,200,JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK);
                }

                // Si es admin puede ver los alumnos de la materia
                if($usuario->tipo == Usuario::TIPO_ADMINISTRADOR){
                    $materias = Inscripcion::ObtenerAlumnosMateria($args["id"]);
                    return $response->withJson($materias,200,JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK);
                }
            }catch(\Exception $e){
                return $response->withJson($e->getMessage(), 500, JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK);
            }
        }

    }