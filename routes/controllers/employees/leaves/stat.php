<?php
use App\Application\Database\DatabaseInterface;
use Slim\Routing\RouteCollectorProxy;
use Slim\App;
use App\Application\Middlewares\ValidateRequestBody\Leaves;
use App\Application\Middlewares\ValidateToken\VerifyToken;

require_once __DIR__ . '/../../../../src/BusinessLogic/employees/leaves/leaves-method.php';
require_once __DIR__ . '/../../../../src/BusinessLogic/employees/leaves/leaves-stat-method.php';


return (
function (App $app) {
    $app->group('/employees/leaves', function (RouteCollectorProxy $group) use ($app) {
        $group->get('/stat', function ($request, $response, $args)use ($app) {
            $params = getQueryParam($request);
            $body = getLeavesBody($request->getBody()->getContents());
            $data = getStatOfLeaveApproved($app->getContainer()->get(DatabaseInterface::class)->getConnection(), $body, $params);
            $response->getBody()->write(json_encode([
                'status' => 'success',
                'leaves_stat' => $data
            ]));
            return $response->withHeader('Content-Type', 'application/json');
        })->add(new VerifyToken($app))->add(new Leaves());
    });
});