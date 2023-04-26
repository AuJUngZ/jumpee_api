<?php
declare(strict_types=1);

use App\Application\Database\DatabaseInterface;
use App\Application\Middlewares\ValidateRequestBody\CreateUser;
use App\Application\Middlewares\ValidateRequestBody\Login;
use Slim\App;
use Slim\Routing\RouteCollectorProxy;

require_once __DIR__ . '/../../../src/BusinessLogic/auth-logic/auth-method.php';

return (
function (App $app) {
    $app->group('/auth', function (RouteCollectorProxy $group) use ($app) {
        $group->post('/create-user', function ($request, $response, $args) use ($app) {
            try {
                $body = getBodyCreateUser($request->getBody()->getContents());
                addNewUser($app->getContainer()->get(DatabaseInterface::class)->getConnection(), $body);
                $response->getBody()->write(json_encode(
                    [
                        'status' => '200 OK',
                        'message' => 'User created successfully',
                        'employee' => [
                            'emailOrUsername' => $body['emailOrUsername'],
                            'employee_code' => $body['employee_code'],
                            'first_name' => $body['first_name'],
                            'last_name' => $body['last_name'],
                            'nickname' => $body['nickname'],
                            'role' => $body['role'],
                            'department' => $body['department']
                        ],
                    ]
                ));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
            } catch (Exception $e) {
                $response->getBody()->write(json_encode(
                    [
                        'status' => '400 Bad Request',
                        'message' => $e->getMessage()
                    ]
                ));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
            }
        })->add(new CreateUser());

        $group->post('/login', function ($request, $response, $args) use ($app) {
            try {
                $body = getBodyLogin($request->getBody()->getContents());
                $user = verifyPassword($app->getContainer()->get(DatabaseInterface::class)->getConnection(), $body, $app);
                $response->getBody()->write(json_encode(
                    [
                        'status' => '200 OK',
                        'message' => 'Login successfully',
                        'token' => $user['token'],
                        'employee' => [
                            'id' => $user['id'],
                            'emailOrUsername' => $user['emailOrUsername'],
                            'employee_code' => $user['employee_code'],
                            'first_name' => $user['first_name'],
                            'last_name' => $user['last_name'],
                            'nickname' => $user['nickname'],
                            'hire_date' => $user['hire_date'],
                            'role' => $user['role'],
                            'department' => $user['department'],
                            'level' => $user['level']
                        ],
                    ]
                ));
            } catch (Exception $e) {
                $response->getBody()->write(json_encode(
                    [
                        'status' => '401 Unauthorized',
                        'message' => $e->getMessage()
                    ]
                ));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
            }
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        })->add(new Login());

        $group->post('/update-password', function ($request, $response, $args) use ($app) {
            try {
                $body = getBodyUpdatePassword($request->getBody()->getContents());
                updatePassword($app->getContainer()->get(DatabaseInterface::class)->getConnection(), $body);
                $response->getBody()->write(json_encode(
                    [
                        'status' => '200 OK',
                        'message' => 'Password updated successfully',
                    ]
                ));
            } catch (Exception $e) {
                $response->getBody()->write(json_encode(
                    [
                        'status' => '401 Unauthorized',
                        'message' => $e->getMessage()
                    ]
                ));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
            }
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        });
    });
}
);