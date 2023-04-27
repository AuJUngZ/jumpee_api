<?php
use App\Application\Database\DatabaseInterface;
use Slim\Routing\RouteCollectorProxy;
use Slim\App;
use App\Application\Middlewares\ValidateRequestBody\Leaves;
use App\Application\Middlewares\ValidateToken\VerifyToken;

require_once __DIR__ . '/../../../../src/BusinessLogic/employees/leaves/leaves-method.php';

return (
function (App $app) {
    $app->group('/employees', function (RouteCollectorProxy $group) use ($app) {
        $group->get('/leaves', function ($request, $response, $args)use ($app) {
            $body = getLeavesBody($request->getBody()->getContents());
            $data = getLeavesDataNotApproved($app->getContainer()->get(DatabaseInterface::class)->getConnection(), $body);
            $response->getBody()->write(json_encode([
                'status' => '200 OK',
                'data' => [
                    'leave_information' => $data[0],
                    'approved' => $data[1],
                ],
            ]));
            return $response->withHeader('Content-Type', 'application/json');
        })->add(new VerifyToken($app))->add(new Leaves());
    });
});