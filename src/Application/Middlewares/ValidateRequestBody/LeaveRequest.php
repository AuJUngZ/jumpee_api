<?php

namespace App\Application\Middlewares\ValidateRequestBody;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Psr7\Response;

require_once __DIR__ . '/../../../../src/BusinessLogic/employees/leaves/request-method.php';
class LeaveRequest
{
    public function __invoke(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $body = getBodyOfRequestLeave($request->getBody()->getContents());
        $errors = $this->validateLeaveRequestBody($body);
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
    private function validateLeaveRequestBody(array $body): array
    {
        $errors = [];
        if (empty($body['employeeId'])) {
            $errors[] = [
                'employeeId' => 'Employee ID is required'
            ];
        }
        if (empty($body['emergencyLeave']) && $body['emergencyLeave'] != 0) {
            $errors[] = [
                'emergencyLeave' => 'Emergency leave is required'
            ];
        }
        if (empty($body['leaveType'])) {
            $errors[] = [
                'leaveType' => 'Leave type is required'
            ];
        }
        if (empty($body['leaveReason'])) {
            $errors[] = [
                'leave Reason' => 'Leave reason is required'
            ];
        }
        if (empty($body['reasonDes'])) {
            $errors[] = [
                'reason Description' => 'Reason description is required'
            ];
        }
        if (empty($body['leavePlace'])) {
            $errors[] = [
                'leave Place' => 'Leave place is required'
            ];
        }
        if (empty($body['leaveGps'])) {
            $errors[] = [
                'leave Gps' => 'Leave GPS is required'
            ];
        }
        if (empty($body['leaveDuration']) && $body['leaveDuration'] != 0) {
            $errors[] = [
                'leave Duration' => 'Leave duration is required'
            ];
        }
        if (empty($body['leaveStartDate'])) {
            $errors[] = [
                'leave Start Date' => 'Leave start date is required'
            ];
        }
        if (empty($body['leaveEndDate'])) {
            $errors[] = [
                'leave End Date' => 'Leave end date is required'
            ];
        }
        if (empty($body['leaveBySpecialDay']) && $body['leaveBySpecialDay'] != 0) {
            $errors[] = [
                'leave By Special Day' => 'Leave by special day is required'
            ];
        }
        return $errors;
    }
}