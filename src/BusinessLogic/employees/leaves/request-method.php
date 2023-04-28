<?php

function getBodyOfRequestLeave(string $body): array
{
    $body = json_decode($body, true);
    return [
        'employeeId' => $body['employeeId'],
        'emergencyLeave' => $body['emergencyLeave'],
        'leaveType' => $body['leaveType'],
        'leaveReason' => $body['leaveReason'],
        'reasonDes' => $body['reasonDes'],
        'leavePlace' => $body['leavePlace'],
        'leaveGps' => $body['leaveGps'],
        'leaveDuration' => $body['leaveDuration'],
        'leaveStartDate' => $body['leaveStartDate'],
        'leaveEndDate' => $body['leaveEndDate'],
        'leaveBySpecialDay' => $body['leaveBySpecialDay'],
    ];
}

/**
 * @throws Exception
 */
function postDataToDB(object $db, $body): void
{
    try{
        $sql = "INSERT INTO leaves_requirement (employee_id, emergency_leaves, leave_type, leave_reason, leave_reason_des, leave_place, leave_gps, leave_duration, leave_start_date, leave_end_date, leave_by_special_day) VALUES (:employeeId, :emergencyLeave, :leaveType, :leaveReason, :reasonDes, :leavePlace, :leaveGps, :leaveDuration, :leaveStartDate, :leaveEndDate, :leaveBySpecialDay)";
        $stmt = $db->prepare($sql);
        $stmt->execute($body);
    }catch(Exception $e){
        throw new Exception($e->getMessage());
    }
}