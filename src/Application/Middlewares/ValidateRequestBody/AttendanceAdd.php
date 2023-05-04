<?php

namespace App\Application\Middlewares\ValidateRequestBody;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Psr7\Response;
class AttendanceAdd
{
    public function __invoke(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $body = json_decode($request->getBody()->getContents(), true);
        $errors = $this->validateBody($body);
        if(!empty($errors)){
            $response = new Response();
            $response->getBody()->write(json_encode([
                'status' => '400 Bad Request',
                'message' => $errors,
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }
        return $handler->handle($request);
    }

    private function validateBody(array $body): array
    {
        $errors= [];
        if(empty($body['employee_id'])){
            $errors['employee_id'] = 'Employee id is required';
        }
        if(empty($body['temperature'])){
            $errors['temperature'] = 'Temperature is required';
        }
        if(empty($body['in_out_time'])){
            $errors['in_out_time'] = 'In out time is required';
        }
        if(empty($body['device_ip'])){
            $errors['device_ip'] = 'Device ip is required';
        }
        if(empty($body['device_key'])){
            $errors['device_key'] = 'Device key is required';
        }
        return $errors;
    }
}
