<?php

namespace App\Application\Middlewares\ValidateToken;

use App\Application\Settings\SettingInterface;
use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Slim\Psr7\Response;

class VerifyAdminToken
{
    private $app;

    public function __construct($app)
    {
        $this->app = $app;
    }

    /**
     * @throws Exception
     */
    public function __invoke($request, $handler)
    {
        try {
            $token = $this->getToken($request);
            $this->verifyToken($token);
            return $handler->handle($request);
        } catch (Exception $e) {
            $response = new Response();
            $response->getBody()->write(json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
        }
    }

    /**
     * @throws Exception
     */
    private function getToken($request): string
    {
        $token = $request->getHeader('Authorization');
        if (empty($token)) {
            throw new Exception('Token not found');
        }
        //remove 'Bearer ' from token
        $token = explode(' ', $token[0]);
        return $token[1];
    }

    /**
     * @throws Exception
     */
    private function verifyToken(mixed $token): void
    {
        $key = $this->app->getContainer()->get(SettingInterface::class)->getSettings('key_jwt');
        try {
            $decode = JWT::decode($token, new Key($key, 'HS256'));
            if ($decode->role != 'admin') {
                throw new Exception('You are not allowed to access this route');
            }
        } catch (Exception $e) {
            throw new Exception('Invalid token');
        }
    }
}
