<?php

    use \Slim\App as Slim;
    use \API\API;
    use \Middleware\JWTMiddleware;
    use \Middleware\PermisosMiddleware;

    require_once 'vendor/autoload.php';

    $config['displayErrorDetails'] = true;
    $config['addContentLengthHeader'] = false;

    $app = new Slim(["settings" => $config]);

    /**
     * Login de usuario
     * @param nombre nombre de usuario
     * @param clave clave del usuario
     * @param sexo sexo del usuario
     * @return usuario Devuelve los datos del usuario logueado
     * @return token Devuelve un token de acceso unico para el usuario en el Header Token
     */
    $app->post("/login[/]", API::class.":LoginUsuario")
    ->add(JWTMiddleware::class.":AccesoLogin")
    ->add(PermisosMiddleware::class.":LogAccesos");

    /**
     * Alta de usuario
     * @param nombre nombre de usuario
     * @param clave clave del usuario
     * @param sexo sexo del usuario
     * @param perfil (opcional) perfil del usuario, en caso de no enviarlo se le pone 'usuario'
     * @return usuario Devuelve los datos del usuario creado
     */
    $app->post("/usuario[/]", API::class.":AltaUsuario")
    ->add(JWTMiddleware::class.":AccesoCrearUsuario")
    ->add(PermisosMiddleware::class.":LogAccesos");

    /**
     * Listar usuarios
     * @return listausuarios Devuelve un listado de usuarios en caso de ser administrador o solo un "hola" en caso de no serlo
     */
    $app->get("/usuario[/]", API::class.":ListarUsuarios")
    ->add(PermisosMiddleware::class.":AccesoListaUsuarios")
    ->add(JWTMiddleware::class.":AccesoGeneral")
    ->add(PermisosMiddleware::class.":LogAccesos");
    
    /**
     * Registrar Compra
     * @param articulo nombre del artículo a comprar
     * @param fecha fecha de la compra
     * @param precio precio de la compra
     * @return compra Devuelve los datos de la compra guardada
     */
    $app->post("/Compra[/]", API::class.":NuevaCompra")
    ->add(JWTMiddleware::class.":AccesoGeneral")
    ->add(PermisosMiddleware::class.":LogAccesos");

    /**
     * Listar Compras
     * @return compras Devuelve un listado de las compras del usuario o general en caso de que sea administrador
     */
    $app->get("/Compra[/]", API::class.":ListarCompras")
    ->add(JWTMiddleware::class.":AccesoGeneral")
    ->add(PermisosMiddleware::class.":LogAccesos");
    
    // Ejemplo de como buscar las imágenes luego de subirlas sin importar su extensión
    $app->get("/test[/]", function ($request, $response){
        var_dump( glob("./IMGCompras/17 - CARAMELO.*"));
    });
    $app->run();
?>
    