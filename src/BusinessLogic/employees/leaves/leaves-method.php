<?php

function getLeavesBody(string $body):array
{
   return json_decode($body, true);
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