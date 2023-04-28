<?php
use App\Application\Database\DatabaseInterface;
use Slim\Routing\RouteCollectorProxy;
use Slim\App;
use App\Application\Middlewares\ValidateRequestBody\Leaves;
use App\Application\Middlewares\ValidateToken\VerifyToken;
use App\Application\Middlewares\ValidateQueryParams\Leaves_History;

//To useLeaveBody method
require_once __DIR__ . '/../../../../src/BusinessLogic/employees/leaves/leaves-method.php';
//To use getQueryParam of employeeId method
require_once __DIR__ . '/../../../../src/BusinessLogic/employees/leaves/leaves-stat-method.php';
//To use getLeavesDataApproved method
require_once __DIR__ . '/../../../../src/BusinessLogic/employees/leaves/leaves-history-method.php';

return (
function (App $app) {
    $app->group('/employees', function (RouteCollectorProxy $group) use ($app) {
        $group->get('/leaves/history', function ($request, $response, $args)use ($app) {
            $body = getLeavesBody($request->getBody()->getContents());
            $params = getQueryParam($request);
            $data = getLeavesDataApproved($app->getContainer()->get(DatabaseInterface::class)->getConnection(), $body, $params);
            $response->getBody()->write(json_encode([
                'status' => '200 OK',
                'leave_history' => $data
            ]));
            return $response->withHeader('Content-Type', 'application/json');
        })->add(new VerifyToken($app))->add(new Leaves())->add(new Leaves_History());
    });
});