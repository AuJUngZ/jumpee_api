<?php

use App\Application\Database\DatabaseInterface;
use App\Application\Middlewares\ValidateToken\VerifyToken;
use Slim\App;
use Slim\Routing\RouteCollectorProxy;

require_once __DIR__ . '/../../../../../src/BusinessLogic/employees/leaves/certifier/certifier-method.php';

return (
function (App $app) {
    $app->group('/employee', function (RouteCollectorProxy $group) use ($app) {
        $group->get('/leaves/certifier', function ($request, $response, $args) use ($app) {
            $data = getAllCertifier($app->getContainer()->get(DatabaseInterface::class)->getConnection());
            $response->getBody()->write(json_encode([
                'status' => '200 OK',
                'certifiers' => $data
            ]));
            return $response->withHeader('Content-Type', 'application/json');
        })->add(new VerifyToken($app));
    });
});