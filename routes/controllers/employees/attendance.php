<?php

use App\Application\Database\DatabaseInterface;
use App\Application\Middlewares\ValidateRequestBody\Attendance;
use App\Application\Middlewares\ValidateToken\VerifyToken;
use Slim\App;
use Slim\Routing\RouteCollectorProxy;

require_once __DIR__ . '/../../../src/BusinessLogic/employees/attendance-method.php';

return (
function (App $app) {
    $app->group('/employees', function (RouteCollectorProxy $group) use ($app) {
        $group->get('/attendance', function ($request, $response, $args) {
            $params = $request->getQueryParams();
            $body = getAttendanceBody($request->getBody()->getContents());
            //get attendance data
            $db = $this->get(DatabaseInterface::class)->getConnection();
            //get total employee
            $total_employee = count(getAllEmployee($db));
            //find day difference
            $start = new DateTime($body['startDate']);
            $end = new DateTime($body['endDate']);
            $day_difference = $start->diff($end)->days;

            if ($params['employeeId'] != null) {
                $data_attendance = getIndividualData($db, $body, $params['employeeId']);
            } else {
                $data_attendance = getData($db, $body['startDate'], $body['endDate']);
            }

            if ($day_difference >= 1) {
                $response->getBody()->write(json_encode([
                    'status' => '200 OK',
                    'employee_attendance' => $data_attendance,
                ]));
            } else {
                $response->getBody()->write(json_encode([
                    'status' => '200 OK',
                    'employee_attendance' => $data_attendance,
                    'summary' => [
                        'total_employee' => $total_employee,
                        'present' => count($data_attendance),
                    ]
                ]));
            }
            return $response->withHeader('Content-Type', 'application/json');
        });
    })->add(new VerifyToken($app))->add(new Attendance());
});