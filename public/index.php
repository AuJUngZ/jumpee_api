<?php

use DI\ContainerBuilder;
use Slim\Factory\AppFactory;
use App\Application\Database\DatabaseInterface;
require __DIR__ . '/../vendor/autoload.php';

//allow all origins
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: *');
header('Access-Control-Allow-Methods: *');


//container builder
$containerBuilder = new ContainerBuilder();

//add settings to container
$settings = require __DIR__ . '/../app/settings.php';
$settings($containerBuilder);

//add database to container
$database = require __DIR__ . '/../app/database.php';
$database($containerBuilder);


//setup container and create app
$container = $containerBuilder->build();

AppFactory::setContainer($container);
$app = AppFactory::create();
$app->setBasePath('/jumpee_api');

//add default middlewares
$middlewares = require __DIR__ . '/../app/middleware.php';
$middlewares($app);

$app->get('/', function($request, $response, $args){
    $response->getBody()->write('Hello World');
    return $response;
});

$app->get('/test-connect-db', function($request, $response, $args)use ($container){
    $db = $container->get(DatabaseInterface::class)->getConnection();
    if($db) {
        $response->getBody()->write('Connected to database');
    } else {
        $response->getBody()->write('Failed to connect to database');
    }
    return $response->withHeader('Content-Type', 'text/plain');
});

//add auth route
$auth = require __DIR__ . '/../routes/controllers/auth/auth.php';
$auth($app);

$app->run();


