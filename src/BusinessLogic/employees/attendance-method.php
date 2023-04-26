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

function getData(object $db,string $startDate, string $endDate){
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
    $stmt = $db->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getAllEmployee($db){
    $sql = "SELECT id FROM employees";
    $stmt = $db->prepare($sql);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}