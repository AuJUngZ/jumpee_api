<?php

use DI\ContainerBuilder;
use Slim\Factory\AppFactory;
use App\Application\Database\DatabaseInterface;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
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

$app->get('/test-jwt', function($request, $response, $args)use ($container){
    $key = 'test';
    $payload = array(
        "role" => "admin",
    );
    $jwt = JWT::encode($payload, $key, 'HS256');
    echo $jwt . "\n";
    $decoded = JWT::decode($jwt, new Key($key,'HS256'));

    if($decoded->role == 'admin') {
        $response->getBody()->write('Admin');
    } else {
        $response->getBody()->write('Not admin');
    }
    return $response;
});

//add auth route
$auth = require __DIR__ . '/../routes/controllers/auth/auth.php';
$auth($app);

//add employees route
$attendance = require __DIR__ . '/../routes/controllers/employees/attendance.php';
$attendance($app);

//add leaves route
$leaves = require __DIR__ . '/../routes/controllers/employees/leaves/leaves.php';
$leaves($app);

//add leaves stat route
$leavesStat = require __DIR__ . '/../routes/controllers/employees/leaves/stat.php';
$leavesStat($app);

//add leaves history route
$leavesHistory = require __DIR__ . '/../routes/controllers/employees/leaves/history.php';
$leavesHistory($app);

//add leaves request route
$leavesRequest = require __DIR__ . '/../routes/controllers/employees/leaves/request.php';
$leavesRequest($app);

//add leaves certifier route
$leavesCertifier = require __DIR__ . '/../routes/controllers/employees/leaves/certifier/certifier.php';
$leavesCertifier($app);

//add leaves certifier add route
$leavesCertifierAdd = require __DIR__ . '/../routes/controllers/employees/leaves/certifier/add.php';
$leavesCertifierAdd($app);

//add leaves certifier approve route
$leavesCertifierApprove = require __DIR__ . '/../routes/controllers/employees/leaves/certifier/approve.php';
$leavesCertifierApprove($app);

//add utils route
$utils = require __DIR__ . '/../routes/controllers/utils/aboutConfig.php';
$utils($app);

//add get health route
$health = require __DIR__ . '/../routes/controllers/employees/health.php';
$health($app);

$app->run();


