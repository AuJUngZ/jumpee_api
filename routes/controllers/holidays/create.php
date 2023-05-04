<?php

use App\Application\Database\DatabaseInterface;
use App\Application\Middlewares\ValidateRequestBody\CreateHoliday;
use App\Application\Middlewares\ValidateToken\VerifyToken;
use Slim\App;
use Slim\Routing\RouteCollectorProxy;

require_once __DIR__ . '/../../../src/BusinessLogic/holidays/create-method.php';

return (
function (App $app) {
    $app->group('/holidays', function (RouteCollectorProxy $group) use ($app) {
        $group->post('/create', function ($request, $response, $args) {
            try {
                $body = json_decode($request->getBody()->getContents(), true);
                //To check if employee is already on leave on the given date
                checkConflictDate($this->get(DatabaseInterface::class)->getConnection(), $body);
                createHoliday($this->get(DatabaseInterface::class)->getConnection(), $body);
                $response->getBody()->write(json_encode([
                    'status' => '200 OK',
                    'message' => 'Holiday created successfully',
                ]));
            } catch (Exception $e) {
                $response->getBody()->write(json_encode([
                    'status' => '400 Bad Request',
                    'message' => json_decode($e->getMessage()),
                ]));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
            }
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        });
    })->add(new VerifyToken($app))->add(new CreateHoliday());
});