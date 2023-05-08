<?php
use App\Application\Database\DatabaseInterface;
use App\Application\Middlewares\ValidateToken\VerifyToken;
use App\Application\Middlewares\ValidateToken\VerifyAdminToken;
use Slim\App;
use Slim\Routing\RouteCollectorProxy;

require_once __DIR__ . '/../../../src/BusinessLogic/utils/profile-method.php';

return (
function (App $app) {
    $app->group('/utils', function (RouteCollectorProxy $group) use ($app) {
        $group->post('/profile-template/create', function ($request, $response, $args) {
            $body = json_decode($request->getBody()->getContents(), true);
            createProfileTemplate($this->get(DatabaseInterface::class)->getConnection(), $body);
            $response->getBody()->write(json_encode([
                'status' => '200 OK',
                'message' => 'Profile template created'
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        });
        $group->post('/select-profile-template', function ($request, $response, $args) {
            try{
                $body = json_decode($request->getBody()->getContents(), true);
                selectProfileTemplate($this->get(DatabaseInterface::class)->getConnection(), $body);
                $response->getBody()->write(json_encode([
                    'status' => '200 OK',
                    'message' => 'Profile template selected',
                ]));
            }catch(Exception $e){
                $response->getBody()->write(json_encode([
                    'status' => '400 Bad Request',
                    'message' => $e->getMessage(),
                ]));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
            }
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        });
    })->add(new VerifyToken($app));
});