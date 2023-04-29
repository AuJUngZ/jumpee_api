<?php

function getLeavesDataApproved(object $db, array $body, $param): array
{
    $startDate = $body['startDate'];
    $endDate = $body['endDate'];
    $sql = "SELECT * FROM leaves_requirement 
    WHERE employee_id = :id 
    AND (DATE(leave_start_date) BETWEEN '$startDate' AND '$endDate')
";
    $stmt = $db->prepare($sql);
    $stmt->execute(['id' => $param]);
    return extracted($stmt, $db);
}

/**
 * @param $stmt
 * @param object $db
 * @return array
 */
function extracted($stmt, object $db): array
{
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $newData = [];
    foreach ($data as $key) {
        $sql = "SELECT * FROM approved WHERE approved.leave_id = :leave_id";
        $stmt = $db->prepare($sql);
        $stmt->execute(['leave_id' => $key['leave_id']]);
        $approved = $stmt->fetchAll(PDO::FETCH_ASSOC);
        //add approved data to $key
        $key['approved'] = $approved;
        //add $key to $newData
        $newData[] = $key;
    }
    return $newData;
}