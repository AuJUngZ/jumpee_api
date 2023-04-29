<?php

namespace App\Application\Middlewares\ValidateRequestBody;

use Slim\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;

class UpdateTimeIntervals
{

    public function __invoke(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $body = json_decode($request->getBody()->getContents(), true);
        $errors = $this->validateBodyUpdateTimeIntervals($body);

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

    private function validateBodyUpdateTimeIntervals(array $body): array
    {
        $errors = [];
        if(empty($body['time_interval'])){
            $errors[] = [
                'time_interval' => 'time_interval is required'
            ];
            return $errors;
        }

        $body = $body['time_interval'];
        foreach ($body as $key) {
            if (empty($key['on_duty_time'])) {
                $errors[] = [
                    'start_time' => 'start_time is required'
                ];
            }
            if (empty($key['off_duty_time'])) {
                $errors[] = [
                    'end_time' => 'end_time is required'
                ];
            }
        }
        return $errors;
    }
}