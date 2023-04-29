<?php

namespace App\Application\Middlewares\ValidateRequestBody;

use Slim\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;

class UpdateAttendanceConfig
{
    public function __invoke(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $body = json_decode($request->getBody()->getContents(), true);
        $errors = $this->validateBodyUpdateAttendanceConfig($body);

        if(!empty($errors)) {
            $response = new Response();
            $response->getBody()->write(json_encode(
                [
                    'status' => '400 Bad Request',
                    'message' => $errors
                ]
            ));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }else{
            return $handler->handle($request);
        }
    }

    private function validateBodyUpdateAttendanceConfig(mixed $body): array
    {
        $errors = [];
        if (empty($body['work_start_time'])) {
            $errors[] = [
                'work_start_time' => 'work_start_time is required'
            ];
        }
        if (empty($body['work_end_time'])) {
            $errors[] = [
                'work_end_time' => 'work_end_time is required'
            ];
        }
        if (empty($body['work_late_time'])) {
            $errors[] = [
                'work_late_time' => 'work_late_time is required'
            ];
        }
        return $errors;
    }
}