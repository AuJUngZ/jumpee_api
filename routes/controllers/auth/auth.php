<?php
declare(strict_types=1);

use App\Application\Database\DatabaseInterface;
use Slim\App;
use Slim\Routing\RouteCollectorProxy;

require_once __DIR__ . '/../../../src/BusinessLogic/auth-logic/auth-method.php';

return (
function (App $app) {
    $app->group('/auth', function (RouteCollectorProxy $group) use ($app) {
        $group->post('/create-user', function ($request, $response, $args) use ($app) {
            try{
                $body = getBodyCreateUser($request->getBody()->getContents());
                addNewUser($app->getContainer()->get(DatabaseInterface::class)->getConnection(), $body);
                $response->getBody()->write(json_encode(
                    [
                        'status' => 'success',
                        'message' => 'User created successfully',
                        'employee' => [
                            'id' => 1,
                            'email' => $body['email'],
                            'employee_code' => $body['employee_code'],
                            'first_name' => $body['first_name'],
                            'last_name' => $body['last_name'],
                            'nickname' => $body['nickname'],
                            'role' => $body['role'],
                            'department' => $body['department']
                        ],
                        'token' => 'token'
                    ]
                ));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
            }catch (Exception $e){
                $response->getBody()->write(json_encode(
                    [
                        'status' => 'error',
                        'message' => $e->getMessage()
                    ]
                ));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
            }
        });

        $group->post('/login', function ($request, $response, $args) use ($app) {
        });

        $group->post('/update-password', function ($request, $response, $args) use ($app) {
        });
    });
}
);