<?php
    use \Psr\Http\Message\ServerRequestInterface as Request;
    use \Psr\Http\Message\ResponseInterface as Response;

    require 'vendor/autoload.php';

    $config['displayErrorDetails'] = true;
    $config['addContentLengthHeader'] = false;

    $app = new \Slim\App(["settings" => $config]);

    $app->get("/persona/{nombre}", function ($request, $response, $args) {
        // Args es un array asociativo de los parámetros que ponemos en la ruta
        // Devuelve un array asociativo de los query params NO INCLUYE LO DE ARGS
        $params = $request->getQueryParams();
        $response->getBody()->write("HOLA COMO TE VA ".$args['nombre']."?");
        return $response;
    });

    $app->post("[/]", function (Request $request, Response $response){
        // Obtiene el body de la request
        $body = $request->getBody();
        // Obtiene el body de la request convertido a objeto
        $parsedBody = $request->getParsedBody();
        // Obtiene los archivos subidos al servidor
        $files = $request->getUploadedFiles();

        $response->getBody()->write("Bienvenido a Slim!! (POR post)");
        return $response;
    });

    $app->put("[/]", function (Request $request, Response $response){
        $response->getBody()->write("Bienvenido a Slim!! (POR put)");
        return $response;
    });

    $app->delete("[/]", function (Request $request, Response $response){
        $response->getBody()->write("Bienvenido a Slim!! (POR delete)");
        return $response;
    });

    $app->run();
?>