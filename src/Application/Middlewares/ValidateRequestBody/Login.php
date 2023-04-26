<?php

namespace App\Application\Middlewares\ValidateRequestBody;

use Slim\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
class Login
{
   private function ValidateRequestBody(array $body): array
   {
       $errors = [];
       if(empty($body['emailOrUsername']))
           $errors['emailOrUsername'] = 'Email or Username is required';
       if(empty($body['password']))
           $errors['password'] = 'Password is required';
       return $errors;
   }

   public function __invoke(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
   {
       $body = $request->getBody()->getContents();
       $body = json_decode($body, true);
       $errors = $this->ValidateRequestBody($body);
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
