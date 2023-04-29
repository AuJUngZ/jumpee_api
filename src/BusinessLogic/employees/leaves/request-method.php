<?php

function getBodyOfRequestLeave(string $body): array
{
    return json_decode($body, true);
}

/**
 * @throws Exception
 */
function postDataToDB(object $db, $body): void
{
    try {
        if ($body['leaveType'] != 'business_leave' && $body['leaveType'] != 'sick_leave') {
            throw new Exception('Invalid leave type');
        }
        $sql = "INSERT INTO leaves_requirement (employee_id, emergency_leaves, leave_type, leave_reason, leave_reason_des, leave_place, leave_gps, leave_duration, leave_start_date, leave_end_date, leave_by_special_day, leave_days) 
                VALUES (:employee_id, :emergency_leaves, :leave_type, :leave_reason, :leave_reason_des, :leave_place, :leave_gps, :leave_duration, :leave_start_date, :leave_end_date, :leave_by_special_day, :leave_days)";
        $stmt = $db->prepare($sql);
        $stmt->execute([
            'employee_id' => $body['employeeId'],
            'emergency_leaves' => $body['emergencyLeave'],
            'leave_type' => $body['leaveType'],
            'leave_reason' => $body['leaveReason'],
            'leave_reason_des' => $body['reasonDes'],
            'leave_place' => $body['leavePlace'],
            'leave_gps' => $body['leaveGps'],
            'leave_duration' => $body['leaveDuration'],
            'leave_start_date' => $body['leaveStartDate'],
            'leave_end_date' => $body['leaveEndDate'],
            'leave_by_special_day' => $body['leaveBySpecialDay'],
            'leave_days' => $body['leaveDays']
        ]);
    } catch (Exception $e) {
        throw new Exception($e->getMessage());
    }
}