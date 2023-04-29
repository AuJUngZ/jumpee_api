<?php

function getHealth(object $db , mixed $employeeId): array
{
    $sql = "SELECT * FROM employee_health WHERE employee_id = :employeeId";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':employeeId', $employeeId);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
