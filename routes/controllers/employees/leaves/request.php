<?php

use App\Application\Database\DatabaseInterface;
use App\Application\Middlewares\ValidateRequestBody\LeaveRequest;
use App\Application\Middlewares\ValidateToken\VerifyToken;
use Slim\App;
use Slim\Routing\RouteCollectorProxy;

require_once __DIR__ . '/../../../../src/BusinessLogic/employees/leaves/request-method.php';

return (
function (App $app) {
    $app->group('/employee', function (RouteCollectorProxy $group) use ($app) {
        $group->post('/leaves/request', function ($request, $response, $args) use ($app) {
            try {
                $body = getBodyOfRequestLeave($request->getBody()->getContents());
                //To check if the leave days are matching or not
                checkLeaveDays($body);
                //TODO : To uncomment this line after ready
//                postDataToDB($app->getContainer()->get(DatabaseInterface::class)->getConnection(), $body);
                $response->getBody()->write(json_encode([
                    'status' => '200 OK',
                    'message' => 'Leave request submitted successfully'
                ]));
            } catch (Exception $e) {
                $response->getBody()->write(json_encode([
                    'status' => '400 Bad Request',
                    'message' => $e->getMessage()
                ]));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
            }
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        })->add(new VerifyToken($app))->add(new LeaveRequest());
    });
});