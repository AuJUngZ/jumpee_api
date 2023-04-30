<?php

namespace App\Application\Middlewares\ValidateRequestBody;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Psr7\Response;

class CreateHoliday
{
    public function __invoke(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $body = json_decode($request->getBody()->getContents(), true);
        $errors = $this->ValidateBody($body);
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

    private function ValidateBody($body): array
    {
        $errors = [];
        if (empty($body['created_by'])) {
            $errors['created_by'] = 'created_by is required';
        }
        if (empty($body['holiday_type'])) {
            $errors['holiday type'] = 'holiday type is required';
        }
        if (empty($body['employees'])) {
            $errors['employees'] = 'employees is required';
        }
        if (empty($body['holiday_name'])) {
            $errors['holiday_name'] = 'holiday_name is required';
        }
        if (empty($body['holiday_des'])) {
            $errors['holiday_des'] = 'holiday_des is required';
        }
        if (empty($body['holiday_date'])) {
            $errors['holiday_date'] = 'holiday_date is required';
        }
        if (empty($body['startTime'])) {
            $errors['startTime'] = 'startTime is required';
        }
        if (empty($body['endTime'])) {
            $errors['endTime'] = 'endTime is required';
        }
        return $errors;
    }
}