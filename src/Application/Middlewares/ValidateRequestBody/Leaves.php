<?php

namespace App\Application\Middlewares\ValidateRequestBody;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Psr7\Response;

require_once __DIR__ . '/../../../../src/BusinessLogic/employees/leaves/leaves-method.php';

class Leaves
{
    public function __invoke(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $body = getLeavesBody($request->getBody()->getContents());
        $errors = $this->validateLeavesBody($body);


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

    private function validateLeavesBody(array $body): array
    {
        $errors = [];
        if (empty($body['startDate'])) {
            $errors[] = [
                'startDate' => 'Start date is required'
            ];
        }
        if (empty($body['endDate'])) {
            $errors[] = [
                'endDate' => 'End date is required'
            ];
        }
        return $errors;
    }
}
