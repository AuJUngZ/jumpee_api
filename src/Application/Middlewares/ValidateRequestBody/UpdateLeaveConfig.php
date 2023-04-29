<?php
namespace App\Application\Middlewares\ValidateRequestBody;

use Slim\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;

class UpdateLeaveConfig
{
    public function __invoke(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $body = getBodyUpdateLeaveConfig($request->getBody()->getContents());
        $errors = $this->validateBodyUpdateLeaveConfig($body);

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

    private function validateBodyUpdateLeaveConfig(array $body): array
    {
        $errors = [];
        if (empty($body['business_leave_senior_days'])) {
            $errors[] = [
                'business_leave_senior_days' => 'business_leave_senior_day is required'
            ];
        }
        if (empty($body['business_leave_junior_days'])) {
            $errors[] = [
                'business_leave_junior_days' => 'business_leave_junior_day is required'
            ];
        }
        if (empty($body['sick_leave_senior_days'])) {
            $errors[] = [
                'sick_leave_senior_days' => 'sick_leave_senior_day is required'
            ];
        }
        if (empty($body['sick_leave_junior_days'])) {
            $errors[] = [
                'sick_leave_junior_days' => 'sick_leave_junior_day is required'
            ];
        }
        if (empty($body['continuous_leave_senior_days'])) {
            $errors[] = [
                'continuous_leave_senior_days' => 'continuous_leave_senior_day is required'
            ];
        }
        if (empty($body['continuous_leave_junior_days'])) {
            $errors[] = [
                'continuous_leave_junior_days' => 'continuous_leave_junior_day is required'
            ];
        }
        if (empty($body['num_approvals_business_leave'])) {
            $errors[] = [
                'num_approvals_business_leave' => 'num_approvals_business_leave is required'
            ];
        }
        if (empty($body['num_approvals_sick_leave'])) {
            $errors[] = [
                'num_approvals_sick_leave' => 'num_approvals_sick_leave is required'
            ];
        }
        return $errors;
    }
}
