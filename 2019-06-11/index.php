<?php

    use \Psr\Http\Message\ServerRequestInterface as Request;
    use \Psr\Http\Message\ResponseInterface as Response;
    use \Slim\App as Slim;

    require_once 'vendor/autoload.php';
    require_once 'src/api.php';

    $config['displayErrorDetails'] = true;
    $config['addContentLengthHeader'] = false;

    $app = new Slim(["settings" => $config]);

    $app->post("/jwt/CrearToken[/]", API::class.":GetToken");
    $app->post("/jwt/VerificarToken[/]", API::class.":VerifyToken");

    $app->run();
?>
    