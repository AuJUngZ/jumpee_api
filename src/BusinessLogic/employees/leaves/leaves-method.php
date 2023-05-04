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

/**
 * @throws Exception
 */
function checkLeaveDays(array $body): void
{
    $startDate = $body['leaveStartDate'];
    $endDate = $body['leaveEndDate'];

    $days_count = 0;
    for($i = date_create($startDate) ; $i <= date_create($endDate) ; date_add($i,date_interval_create_from_date_string("1 days"))){
        $day = date_format($i,"D");
        if($day != "Sun"){
            $days_count++;
        }
    }

    if($days_count != $body['leaveDays']){
        throw new Exception("Leave days are not correct should be $days_count days");
    }
}