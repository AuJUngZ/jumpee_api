<?php

function getLeavesBody(string $body):array
{
    $body = json_decode($body, true);
    $startDate = $body['startDate'];
    $endDate = $body['endDate'];
    return [
        'startDate' => $startDate,
        'endDate' => $endDate,
    ];
}

function getLeavesDataNotApproved(object $db, $body): array
{
    $startDate = $body['startDate'];
    $endDate = $body['endDate'];
    $sql = "SELECT * FROM leaves_requirement 
    WHERE leave_status = 'Pending' 
    AND (DATE(leave_start_date) BETWEEN '$startDate' AND '$endDate')
";
    $stmt = $db->prepare($sql);
    $stmt->execute();
    return extracted($stmt, $db);
}