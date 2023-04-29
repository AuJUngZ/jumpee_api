<?php
use App\Application\Database\DatabaseInterface;
use App\Application\Middlewares\ValidateToken\VerifyToken;
use Slim\App;
use Slim\Routing\RouteCollectorProxy;


require_once __DIR__ . '/../../../src/BusinessLogic/employees/health.php';

return (
function (App $app) {
    $app->group('/employee', function (RouteCollectorProxy $group) use ($app) {
        $group->get('/health', function ($request, $response, $args) use ($app) {
            $params = $request->getQueryParams();
            $data = getHealth($app->getContainer()->get(DatabaseInterface::class)->getConnection(), $params['employeeId']);
            $response->getBody()->write(json_encode([
                'status' => '200 OK',
                'data' => $data
            ]));
            return $response->withHeader('Content-Type', 'application/json');
        });
    })->add(new VerifyToken($app));
});