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
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($data as $key){
        $sql = "SELECT * FROM approved WHERE approved.leave_id = :id";
        $stmt = $db->prepare($sql);
        $stmt->execute(['id' => $key['id']]);
        $approved = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    return array_merge($data, [$approved]);
}