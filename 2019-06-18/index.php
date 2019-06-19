<?php

    use \Slim\App as Slim;
    use \API\API;
    use \Middleware\JWTMiddleware;

    require_once 'vendor/autoload.php';
    //require_once 'src/API/API.php';

    $config['displayErrorDetails'] = true;
    $config['addContentLengthHeader'] = false;

    $app = new Slim(["settings" => $config]);

    /**
     * Login de usuario
     */
    $app->post("/login[/]", API::class.":LoginUsuario")->add(JWTMiddleware::class.":AccesoLogin");

    /**
     * Alta de usuario
     */
    $app->post("/usuario[/]", API::class.":AltaUsuario")->add(JWTMiddleware::class.":AccesoGeneral");
    
    
    $app->run();
?>
    