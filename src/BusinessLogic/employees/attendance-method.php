<?php

function getAttendanceBody(string $body): array{
    $body = json_decode($body, true);
    $startDate = $body['startDate'];
    $endDate = $body['endDate'];
    return [
        'startDate' => $startDate,
        'endDate' => $endDate
    ];
}

function getData(object $db,string $startDate, string $endDate): array
{
    $sql = "SELECT
            employee_id,
            employees.first_name,
            employees.last_name,
            employees.hire_date,
            employees.department,
            employees.level,
            employees.hire_date,
            DATE(in_out_time) AS work_date,
            MIN(TIME(in_out_time)) AS first_in,
            MAX(TIME(in_out_time)) AS last_out
        FROM attendance_raw
        JOIN employees ON attendance_raw.employee_id = employees.id
        WHERE DATE(in_out_time) BETWEEN '$startDate' AND '$endDate'
        GROUP BY employee_id, work_date";
    return duplicated_part($db, $sql);
}

/**
 * @param object $db
 * @param string $sql
 * @return array
 */
function duplicated_part(object $db, string $sql): array
{
    $stmt = $db->prepare($sql);
    $stmt->execute();
    $first_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $new_data = [];
    //To add temperature data
    foreach ($first_data as $key) {
        $sql = "
            SELECT temperature FROM attendance_raw
            WHERE  $key[employee_id] = employee_id
            AND DATE(in_out_time) = '$key[work_date]'
            AND TIME(in_out_time) = '$key[first_in]'
        ";
        $stmt = $db->prepare($sql);
        $stmt->execute();
        $in_temperature = $stmt->fetch(PDO::FETCH_ASSOC);

        $sql = "
            SELECT temperature FROM attendance_raw
            WHERE  $key[employee_id] = employee_id
            AND DATE(in_out_time) = '$key[work_date]'
            AND TIME(in_out_time) = '$key[last_out]'
        ";
        $stmt = $db->prepare($sql);
        $stmt->execute();
        $out_temperature = $stmt->fetch(PDO::FETCH_ASSOC);

        $key['in_temperature'] = $in_temperature['temperature'];
        $key['out_temperature'] = $out_temperature['temperature'];

        $new_data[] = $key;
    }
    return $new_data;
}

function getAllEmployee($db){
    $sql = "SELECT id FROM employees";
    $stmt = $db->prepare($sql);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function getIndividualData(object $db,array $body,string $employeeId): array
{
    $startDate = $body['startDate'];
    $endDate = $body['endDate'];
    $sql = "SELECT
            employee_id,
            employees.first_name,
            employees.last_name,
            employees.hire_date,
            employees.department,
            employees.level,
            employees.hire_date,
            DATE(in_out_time) AS work_date,
            MIN(TIME(in_out_time)) AS first_in,
            MAX(TIME(in_out_time)) AS last_out
        FROM attendance_raw
        JOIN employees ON attendance_raw.employee_id = employees.id
        WHERE DATE(in_out_time) BETWEEN '$startDate' AND '$endDate' AND employee_id = '$employeeId'
        GROUP BY employee_id, work_date";
    return duplicated_part($db, $sql);
}