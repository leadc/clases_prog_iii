<?php

    namespace App;
    use Firebase\JWT\JWT;

    /**
     * Clase para manejo de JWT
     */
    class JWTClass{

        // TOKEN DE PRUEBA ADMINISTRADOR eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE1NjE0MzM3NDMsImV4cCI6MTU2NDAyNTc0MywidXN1YXJpbyI6eyJpZCI6MSwibm9tYnJlIjoiYWRtaW4iLCJzZXhvIjoiZmVtZW5pbm8iLCJwZXJmaWwiOiJhZG1pbmlzdHJhZG9yIn19.CNa8vj7jr5PRxx3G3fzSFeNudHET1lSCEGt1QN2QvE8
        // TOKEN DE PRUEBA USUARIO eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE1NjE0MzM3ODIsImV4cCI6MTU2NDAyNTc4MiwidXN1YXJpbyI6eyJpZCI6Miwibm9tYnJlIjoibGVhbmRybyIsInNleG8iOiJtYXNjdWxpbm8iLCJwZXJmaWwiOiJ1c3VhcmlvIn19.RbIFF67BlDXzTSVfIZMVLGnoQrZ3j7pb3In564zb2Rs
        /** Clave secreta para codificar la firma de los token */
        private static $secret = "S3kr3t$";
        /** Tiempo en segundos para que expiren los token generados */
        private static $secondsExp = 60*60*24*30;

        /**
         * Crea un token con incluyendo los datos del usuario pasado por paràmetro
         */
        public static function CrearToken($usuario){
            // Tiempo actual en formato UNIX
            $time = time();
            $timeExp = time() + self::$secondsExp;
            // Payload con los datos a enviar en el token
            $payload = array(
                'iat' => $time,
                'exp' => $timeExp,
                'usuario' => $usuario
            );

            // Creación del TOKEN
            $token = JWT::encode($payload, self::$secret);

            return $token;
        }

        /**
         * Devuelve true o false segùn el token sea vàlido o no
         */
        public static function ValidarToken($token){
            try{
                $decodificado = JWT::decode($token, self::$secret, ["HS256"]);
                return true;
            }catch(Exception $e){
                return false;
            }
        }

        /**
         * Renueva un token asignandole un nuevo tiempo de exiparción
         */
        public static function RenovarToken($token){
            $decodificado = JWT::decode($token, self::$secret, ["HS256"]);
            $time = time() + self::$secondsExp;
            $decodificado->exp = $time;
            // Creación del TOKEN
            return JWT::encode($decodificado, self::$secret);
        }

        /**
         * Devuelve el usuario obtenido en un token
         */
        public static function ObtenerUsuario($token){
            $decodificado = JWT::decode($token, self::$secret, ["HS256"]);
            return $decodificado->usuario;
        }
    }


?>