<?php

use App\Application\Database\DatabaseInterface;
use App\Application\Middlewares\ValidateToken\VerifyToken;
use App\Application\Middlewares\ValidateToken\VerifyAdminToken;
use Slim\App;
use Slim\Routing\RouteCollectorProxy;
use App\Application\Middlewares\ValidateRequestBody\UpdateLeaveConfig;
use App\Application\Middlewares\ValidateRequestBody\UpdateAttendanceConfig;
use App\Application\Middlewares\ValidateRequestBody\UpdateTimeIntervals;

require_once __DIR__ . '/../../../src/BusinessLogic/utils/utils-method.php';

return (
function (App $app) {
    $app->group('/utils', function (RouteCollectorProxy $group) use ($app) {
        $group->get('/get-config', function ($request, $response, $args) {
            $data = getAllConfig($this->get(DatabaseInterface::class)->getConnection());
            $response->getBody()->write(json_encode([
                'status' => '200 OK',
                'configs' => $data
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        });

        $group->post('/update-config/leave', function ($request, $response, $args) {
            $body = getBodyUpdateLeaveConfig($request->getBody()->getContents());
            updateLeaveConfig($this->get(DatabaseInterface::class)->getConnection(), $body);
            $response->getBody()->write(json_encode([
                'status' => '200 OK',
                'message' => 'Update leave config successfully'
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        })->add(new VerifyAdminToken($app))->add(new UpdateLeaveConfig());

        $group->post('/update-config/attendance', function ($request, $response, $args) {
            $body = json_decode($request->getBody()->getContents(), true);
            updateAttendanceConfig($this->get(DatabaseInterface::class)->getConnection(), $body);
            $response->getBody()->write(json_encode([
                'status' => '200 OK',
                'message' => 'Update attendance config successfully'
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        })->add(new VerifyAdminToken($app))->add(new UpdateAttendanceConfig());

        $group->post('/update-time-interval', function ($request, $response, $args) {
            $body = json_decode($request->getBody()->getContents(), true);
            updateTimeIntervals($this->get(DatabaseInterface::class)->getConnection(), $body);
            $response->getBody()->write(json_encode([
                'status' => '200 OK',
                'message' => 'Update time interval successfully'
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        })->add(new VerifyAdminToken($app))->add(new UpdateTimeIntervals());
    })->add(new VerifyToken($app));
});