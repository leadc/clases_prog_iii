<?php

    use \Slim\App as Slim;
    use \API\API;

    require_once 'vendor/autoload.php';
    //require_once 'src/API/API.php';

    $config['displayErrorDetails'] = true;
    $config['addContentLengthHeader'] = false;

    $app = new Slim(["settings" => $config]);

    $app->post("/usuario[/]", API::class.":AltaUsuario");
    $app->post("/jwt/VerificarToken[/]", API::class.":VerifyToken");

    $app->run();
?>
    