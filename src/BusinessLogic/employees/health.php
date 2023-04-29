<?php

function getHealth(object $db , mixed $employeeId, array $body): array
{
    $startDate = $body['startDate'];
    $endDate = $body['endDate'];

    $sql = "SELECT * FROM employee_health WHERE employee_id = :employeeId
            AND created_at BETWEEN '$startDate' AND DATE_ADD('$endDate', INTERVAL 1 DAY)
";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':employeeId', $employeeId);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
