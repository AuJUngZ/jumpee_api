<?php

namespace App\Application\Middlewares\ValidateRequestBody;
use Slim\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;

class CreateUser
{
    public function __invoke(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $body = $request->getBody()->getContents();
        $body = json_decode($body, true);
        $errors = [];
        if(empty($body['email']))
            $errors['email'] = 'Email is required';
        if(empty($body['password']))
            $errors['password'] = 'Password is required';
        if(empty($body['employee_code']))
            $errors['employee_code'] = 'Employee code is required';
        if(empty($body['first_name']))
            $errors['first_name'] = 'First name is required';
        if(empty($body['last_name']))
            $errors['last_name'] = 'Last name is required';
        if(empty($body['nickname']))
            $errors['nickname'] = 'Nickname is required';
        if(empty($body['role']))
            $errors['role'] = 'Role is required';
        if(empty($body['department']))
            $errors['department'] = 'Department is required';

        if(!empty($errors)){
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
}
