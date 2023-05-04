<?php

function getAttendanceBody(string $body): array
{
    return json_decode($body, true);
}

function getData(object $db, string $startDate, string $endDate): array
{
    $sql = "
        SELECT employee_id, CONCAT(first_name, ' ', last_name) AS employee_name, employee_code, hire_date, DATE(in_time) as attendance_date, in_time as first_in, out_time as last_out, in_temperature, out_temperature
        FROM attendance
        JOIN employees e on attendance.employee_id = e.id
        WHERE DATE(in_time) >= '$startDate' AND DATE(in_time) <= '$endDate'
    ";
    $stmt = $db->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getAllEmployee($db)
{
    $sql = "SELECT id FROM employees";
    $stmt = $db->prepare($sql);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function getIndividualData(object $db, array $body, string $employeeId): array
{
    $startDate = $body['startDate'];
    $endDate = $body['endDate'];
    $sql = "
        SELECT employee_id, CONCAT(first_name, ' ', last_name) AS employee_name, employee_code, hire_date, DATE(in_time) as attendance_date, in_time as first_in, out_time as last_out, in_temperature, out_temperature
        FROM attendance
        JOIN employees e on attendance.employee_id = e.id
        WHERE DATE(in_time) >= '$startDate' AND DATE(in_time) <= '$endDate' AND employee_id = '$employeeId'
    ";
    $stmt = $db->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}