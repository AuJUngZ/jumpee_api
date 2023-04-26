<?php

namespace App\Application\Middlewares\ValidateRequestBody;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Psr7\Response;

class Attendance
{

    public function __invoke(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $body = $request->getBody()->getContents();
        $body = json_decode($body, true);
        $errors = [];

        if (empty($body['startDate']))
            $errors['startDate'] = 'Start date is required';
        if (empty($body['endDate']))
            $errors['endDate'] = 'End date is required';
        if (!empty($errors)) {
            $response = new Response();
            $response->getBody()->write(json_encode(
                [
                    'status' => '400 Bad Request',
                    'message' => $errors
                ]
            ));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        } else {
            return $handler->handle($request);
        }
    }

}
