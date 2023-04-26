<?php

namespace App\Application\Middlewares\ValidateToken;

use App\Application\Settings\SettingInterface;
use Exception;
use Firebase\JWT\JWK;
use Firebase\JWT\JWT;
use Slim\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Firebase\JWT\Key;

class VerifyToken{

    private $app;

    public function __construct($app){
        $this->app = $app;
    }

    /**
     * @throws Exception
     */
    public function __invoke($request, $handler){
        try{
            $token = $this->getToken($request);
            $this->verifyToken($token);
            return $handler->handle($request);
        }catch(Exception $e){
            $response = new Response();
            $response->getBody()->write(json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]));
            return $response->withHeader('Content-Type', 'application/json');
        }
    }
    private function getToken($request){
        $body = $request->getBody()->getContents();
        $body = json_decode($body, true);
        return $body['token'];
    }

    /**
     * @throws Exception
     */
    private function verifyToken(mixed $token)
    {
        $key = $this->app->getContainer()->get(SettingInterface::class)->getSettings('key_jwt');
        $decode = JWT::decode($token, new Key($key,'HS256'));
        if($decode->role != 'admin' && $decode->role != 'employee') {
            throw new Exception('Invalid token');
        }
    }
}
