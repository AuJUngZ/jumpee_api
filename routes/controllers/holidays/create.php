<?php
use App\Application\Database\DatabaseInterface;
use App\Application\Middlewares\ValidateToken\VerifyToken;
use Slim\App;
use Slim\Routing\RouteCollectorProxy;
use App\Application\Middlewares\ValidateRequestBody\CreateHoliday;

require_once __DIR__ . '/../../../src/BusinessLogic/holidays/create-method.php';

return (
function (App $app) {
    $app->group('/holidays', function (RouteCollectorProxy $group) use ($app) {
        $group->post('/create', function ($request, $response, $args) {
            $body = json_decode($request->getBody()->getContents(), true);
            createHoliday($this->get(DatabaseInterface::class)->getConnection(), $body);
            $response->getBody()->write(json_encode([
                'status' => '200 OK',
                'message' => 'Holiday created successfully',
            ]));
            return $response->withHeader('Content-Type', 'application/json');
        });
    })->add(new VerifyToken($app))->add(new CreateHoliday());
});