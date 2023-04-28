<?php

namespace App\Application\Middlewares\ValidateQueryParams;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Psr7\Response;
class Leaves_History
{
    public function __invoke(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $params = $request->getQueryParams();
        $errors = $this->validateLeavesHistory($params);
        if (count($errors) > 0) {
            $response = new Response();
            $response->getBody()->write(json_encode([
                'status' => '400 Bad Request',
                'errors' => $errors
            ]));
            return $response->withHeader('Content-Type', 'application/json');
        } else {
            return $handler->handle($request);
        }
    }

    private function validateLeavesHistory(mixed $params): array
    {
        $errors = [];
        if (empty($params['employeeId'])) {
            $errors[] = [
                'page' => 'Employee ID is required'
            ];
        }
        return $errors;
    }
}