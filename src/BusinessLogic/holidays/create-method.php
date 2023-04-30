<?php

function createHoliday(object $db, array $body): void
{
    $allEmployee = $body['employees'];
    $holiday_id = uploadDataToTable($db, $body);
    foreach ($allEmployee as $employee) {
        $sql = "
            INSERT INTO employee_custom_holidays (employee_id, holiday_id)
            VALUES (:employee_id, :holiday_id)
        ";
        $stmt = $db->prepare($sql);
        $stmt->execute([
            'employee_id' => $employee['employee_id'],
            'holiday_id' => $holiday_id,
        ]);
    }
}

function uploadDataToTable(object $db, array $body): int
{
    $sql = "INSERT INTO custom_holidays (created_by, holiday_type, holiday_name, holiday_des, holiday_date, holiday_start_time, holiday_end_time) 
    VALUES (:created_by, :holiday_type, :holiday_name, :holiday_des, :holiday_date, :holiday_start_time, :holiday_end_time)";
    $stmt = $db->prepare($sql);
    $stmt->execute([
        'created_by' => $body['created_by'],
        'holiday_type' => $body['holiday_type'],
        'holiday_name' => $body['holiday_name'],
        'holiday_des' => $body['holiday_des'],
        'holiday_date' => $body['holiday_date'],
        'holiday_start_time' => $body['startTime'],
        'holiday_end_time' => $body['endTime'],
    ]);

    //get holiday id
    $sql = "SELECT id FROM custom_holidays WHERE created_by = :created_by AND holiday_type = :holiday_type AND holiday_name = :holiday_name AND holiday_des = :holiday_des AND holiday_date = :holiday_date AND holiday_start_time = :holiday_start_time AND holiday_end_time = :holiday_end_time";
    $stmt = $db->prepare($sql);
    $stmt->execute([
        'created_by' => $body['created_by'],
        'holiday_type' => $body['holiday_type'],
        'holiday_name' => $body['holiday_name'],
        'holiday_des' => $body['holiday_des'],
        'holiday_date' => $body['holiday_date'],
        'holiday_start_time' => $body['startTime'],
        'holiday_end_time' => $body['endTime'],
    ]);
    return $stmt->fetch(PDO::FETCH_ASSOC)['id'];
}
