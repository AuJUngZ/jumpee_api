<?php
use App\Application\Database\DatabaseInterface;
use App\Application\Middlewares\ValidateToken\VerifyAdminToken;
use Slim\App;
use Slim\Routing\RouteCollectorProxy;

require_once __DIR__ . '/../../../../../src/BusinessLogic/employees/leaves/certifier/approve-leave-method.php';

return (
function (App $app) {
    $app->group('/employee', function (RouteCollectorProxy $group) use ($app) {
        $group->post('/leaves/certifier/approve', function ($request, $response, $args) use ($app) {
            try{
                $body = json_decode($request->getBody()->getContents(), true);
                $status = updateApproveStatus($this->get(DatabaseInterface::class)->getConnection(), $body['leaveId'], $body['certifierId']);
                $response->getBody()->write(json_encode([
                    'status' => '200 OK',
                    'message' => 'Leave status updated',
                    'current_status' => $status[0]['status']
                ]));
            }catch(Exception $e){
                $response->getBody()->write(json_encode([
                    'status' => '400 Bad Request',
                    'message' => $e->getMessage(),
                ]));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
            }
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        })->add(new VerifyAdminToken($app));
    });
});