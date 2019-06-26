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
     * PUNTO 2
     */
    $app->post("/login[/]", API::class.":LoginUsuario")
    ->add(JWTMiddleware::class.":AccesoLogin");
    //->add(PermisosMiddleware::class.":LogAccesos");

    /**
     * PUNTO 1
     */
    $app->post("/usuario[/]", API::class.":AltaUsuario");   
    //->add(JWTMiddleware::class.":AccesoCrearUsuario");    
    //->add(PermisosMiddleware::class.":LogAccesos");

    /**
     * PUNTO 3
     */
    $app->post("/materia[/]", API::class.":NuevaMateria")
    ->add(PermisosMiddleware::class.":AccesoAdministradores")
    ->add(JWTMiddleware::class.":AccesoGeneral");
    //->add(PermisosMiddleware::class.":LogAccesos");
    
    /**
     * PUNTO 4 MODIFICAR USUARIO
     */
    $app->post("/usuario/{legajo}[/]", API::class.":ModificarUsuario")
    ->add(JWTMiddleware::class.":AccesoGeneral");
    //->add(PermisosMiddleware::class.":LogAccesos");

    /**
     * PUNTO 5 INSCRIPCION A MATERIA
     */
    $app->post("/inscripcion/{idMateria}", API::class.":InscripcionMateria")
    ->add(JWTMiddleware::class.":AccesoGeneral");
    //->add(PermisosMiddleware::class.":LogAccesos");

    /**
     * PUNTO 6 LISTAR MATERIAS
     */
    $app->get("/materias[/]", API::class.":ListarMaterias")
    ->add(JWTMiddleware::class.":AccesoGeneral");
    //->add(PermisosMiddleware::class.":LogAccesos");
    
     /**
     * PUNTO 7 LISTAR ALUMNOS POR MATERIA
     */
    $app->get("/materias/{id}[/]", API::class.":ListarAlumnosPorMateria")
    ->add(JWTMiddleware::class.":AccesoGeneral");
    //->add(PermisosMiddleware::class.":LogAccesos");
    
    // PUN
    $app->get("/test[/]", function ($request, $response){
        var_dump( glob("./IMGCompras/17 - CARAMELO.*"));
    });
    $app->run();
?>
    