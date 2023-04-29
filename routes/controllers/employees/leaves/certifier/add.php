<?php

use App\Application\Database\DatabaseInterface;
use App\Application\Middlewares\ValidateToken\VerifyAdminToken;
use Slim\App;
use Slim\Routing\RouteCollectorProxy;

require_once __DIR__ . '/../../../../../src/BusinessLogic/employees/leaves/certifier/add-certifier-method.php';


return (
function (App $app) {
    $app->group('/employee', function (RouteCollectorProxy $group) use ($app) {
        $group->post('/leaves/certifier/add', function ($request, $response, $args) use ($app) {
            $body = getBodyForAddCertifier($request->getBody());
            addCertifier($app->getContainer()->get(DatabaseInterface::class)->getConnection(), $body);
            $response->getBody()->write(json_encode([
                'status' => 'success',
                'message' => 'Certifiers added successfully'
            ]));
            return $response->withHeader('Content-Type', 'application/json');
        })->add(new VerifyAdminToken($app));
    });
});