<?php


function getStatOfLeaveApproved(object $db, array $body,$params): array
{
    if ($params != null) {
        $employee = getEmployeeById($db, $params);
        $employee_data = [];
        $businessLeaveStat = getStatOfLeaveApproved_businessLeave($db, $body, $employee[0]['id']);
        $sickLeaveStat = getStatOfLeaveApproved_sickLeave($db, $body, $employee[0]['id']);
        $specialLeaveStat = getStatOfLeaveApproved_specialLeave($db, $body, $employee[0]['id']);

        if ($employee[0]['id'] != $businessLeaveStat[0]['employee_id'] && $employee[0]['id'] != $sickLeaveStat[0]['employee_id'] && $employee[0]['id'] != $specialLeaveStat[0]['employee_id']) {
            return [];
        } else {
            $data[] = addDataToArray($employee_data, $employee[0], $businessLeaveStat, $sickLeaveStat, $specialLeaveStat);
        }
    } else {
        $allEmployees = getAllEmployees($db);
        $data = [];
        foreach ($allEmployees as $employee) {
            $employee_data = [];
            $businessLeaveStat = getStatOfLeaveApproved_businessLeave($db, $body, $employee['id']);
            $sickLeaveStat = getStatOfLeaveApproved_sickLeave($db, $body, $employee['id']);
            $specialLeaveStat = getStatOfLeaveApproved_specialLeave($db, $body, $employee['id']);

            if ($employee['id'] != $businessLeaveStat[0]['employee_id'] && $employee['id'] != $sickLeaveStat[0]['employee_id'] && $employee['id'] != $specialLeaveStat[0]['employee_id']) {
                continue;
            } else {
                $data[] = addDataToArray($employee_data, $employee, $businessLeaveStat, $sickLeaveStat, $specialLeaveStat);
            }
        }
    }
    return $data;
}

function addDataToArray($employee_data, $employee, $businessLeaveStat, $sickLeaveStat, $specialLeaveStat)
{
    $employee_data['employee_id'] = $employee['id'];
    $employee_data['employee_name'] = $employee['first_name'] . ' ' . $employee['last_name'];
    $employee_data['business_leave'] = $businessLeaveStat[0]['counter'] == null ? 0 : $businessLeaveStat[0]['counter'];
    $employee_data['sick_leave'] = $sickLeaveStat[0]['counter'] == null ? 0 : $sickLeaveStat[0]['counter'];
    $employee_data['special_leave'] = $specialLeaveStat[0]['counter'] == null ? 0 : $specialLeaveStat[0]['counter'];
    return $employee_data;
}

function getEmployeeById(object $db, string $params)
{
    $sql = "SELECT * FROM employees WHERE id = '$params'";
    $stmt = $db->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getAllEmployees(object $db)
{
    $sql = "SELECT * FROM employees";
    $stmt = $db->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getStatOfLeaveApproved_businessLeave(object $db, array $body, int $id): array
{
    return sqlQuery('business_leave', $body, $db, $id);
}

function getStatOfLeaveApproved_sickLeave(object $db, array $body, int $id): array
{
    return sqlQuery('sick_leave', $body, $db, $id);
}

function getStatOfLeaveApproved_specialLeave(object $db, array $body, int $id): array
{
    return sqlQuery('special_leave', $body, $db, $id);
}

function sqlQuery(string $type_of_leave, array $body, object $db, int $id): array
{
    $start_date = $body['startDate'];
    $end_date = $body['endDate'];
    if ($type_of_leave != 'special_leave') {
        $sql = "SELECT COUNT(*) as counter, employee_id, CONCAT(first_name, ' ' , last_name) as employee_name FROM leaves_requirement 
                JOIN employees ON employees.id = leaves_requirement.employee_id
                WHERE leave_type = '$type_of_leave' 
                  AND employee_id = '$id'
                  AND DATE(leave_start_date) >= '$start_date' 
                  AND DATE(leave_end_date) <= '$end_date' 
                  AND leave_status = 'Pending'
                  AND leave_by_special_day = 0
        GROUP BY employee_id
        ";
    } else {
        $sql = "
            SELECT COUNT(*) as counter, employee_id, CONCAT(first_name, ' ' , last_name) as employee_name FROM leaves_requirement 
                JOIN employees ON employees.id = leaves_requirement.employee_id
                WHERE leave_type = 'sick_leave'
                  AND employee_id = '$id'
                  AND DATE(leave_start_date) >= '$start_date' 
                  AND DATE(leave_end_date) <= '$end_date' 
                  AND leave_status = 'Pending'
                  AND leave_by_special_day = 1;
        GROUP BY employee_id
        ";
    }
    $stmt = $db->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


function getQueryParam($request)
{
    $query = $request->getQueryParams();
    return $query['employeeId'] != null ? $query['employeeId'] : null;
}