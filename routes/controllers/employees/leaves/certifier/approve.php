<?php
use App\Application\Database\DatabaseInterface;
use App\Application\Middlewares\ValidateToken\VerifyAdminToken;
use Slim\App;
use Slim\Routing\RouteCollectorProxy;

return (
function (App $app) {
    $app->group('/employee', function (RouteCollectorProxy $group) use ($app) {
        $group->post('/leaves/certifier/approve', function ($request, $response, $args) use ($app) {
            return $response->withHeader('Content-Type', 'application/json');
        })->add(new VerifyAdminToken($app));
    });
});