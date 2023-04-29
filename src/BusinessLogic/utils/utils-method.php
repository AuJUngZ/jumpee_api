<?php

function getAllConfig(object $db): array
{
    $sql = "
        SELECT * FROM leave_config
        JOIN work_time_config
    ";
    $stmt = $db->prepare($sql);
    $stmt->execute();
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $sql = "
        SELECT * FROM time_intervals
    ";
    $stmt = $db->prepare($sql);
    $stmt->execute();
    $data2 = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $data[0]['time_intervals'] = $data2;

    return $data;
}

function getBodyUpdateLeaveConfig(string $body): array
{
    return json_decode($body, true);
}

function updateLeaveConfig(object $db, array $body): void
{
    $sql = "
        UPDATE leave_config
        SET business_leave_senior_days = :business_leave_senior_days,
            business_leave_junior_days = :business_leave_junior_days,
            sick_leave_senior_days = :sick_leave_senior_days,
            sick_leave_junior_days = :sick_leave_junior_days,
            continuous_leave_senior_days = :continuous_leave_senior_days,
            continuous_leave_junior_days = :continuous_leave_junior_days,
            num_approvals_business_leave = :num_approvals_business_leave,
            num_approvals_sick_leave = :num_approvals_sick_leave
    ";
    $stmt = $db->prepare($sql);
    $stmt->execute([
        'business_leave_senior_days' => $body['business_leave_senior_days'],
        'business_leave_junior_days' => $body['business_leave_junior_days'],
        'sick_leave_senior_days' => $body['sick_leave_senior_days'],
        'sick_leave_junior_days' => $body['sick_leave_junior_days'],
        'continuous_leave_senior_days' => $body['continuous_leave_senior_days'],
        'continuous_leave_junior_days' => $body['continuous_leave_junior_days'],
        'num_approvals_business_leave' => $body['num_approvals_business_leave'],
        'num_approvals_sick_leave' => $body['num_approvals_sick_leave']
    ]);
}

function updateAttendanceConfig(object $db, array $body): void
{
    $sql = "
        UPDATE work_time_config
        SET work_start_time = :work_start_time,
            work_end_time = :work_end_time,
            work_late_time = :work_late_time
    ";
    $stmt = $db->prepare($sql);
    $stmt->execute([
        'work_start_time' => $body['work_start_time'],
        'work_end_time' => $body['work_end_time'],
        'work_late_time' => $body['work_late_time']
    ]);
}

function updateTimeIntervals(object $db, array $body): void
{
    $time_intervals = $body['time_interval'];

    $sql = "
        DELETE FROM time_intervals";
    $stmt = $db->prepare($sql);
    $stmt->execute();

    foreach ($time_intervals as $time_interval) {
        $sql = "
            INSERT INTO time_intervals (turn_on_time, turn_off_time)
            values (:turn_on_time, :turn_off_time)
            ";
        $stmt = $db->prepare($sql);
        $stmt->execute([
            'turn_on_time' => $time_interval['on_duty_time'],
            'turn_off_time' => $time_interval['off_duty_time']
        ]);
    }
}
