<?php

function createHoliday(object $db, array $body): void
{
    $allEmployee = $body['employees'];
    //upload holiday info to custom_holidays table
    $holiday_id = uploadDataToTable($db, $body);

    //create holiday for each employee in employee_custom_holidays table
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

/**
 * @throws Exception
 */
function checkConflictDate(object $db, array $body){
    $allEmployees = $body['employees'];
    $errors = [];
    foreach ($allEmployees as $employee){
        $sql = "
              SELECT employee_id, concat(first_name, ' ' , last_name) as name FROM employee_custom_holidays
              JOIN employees
              WHERE employee_id = :employee_id AND holiday_id IN (
                  SELECT id FROM custom_holidays
                  WHERE holiday_date = :holiday_date
              )
        ";
        $stmt = $db->prepare($sql);
        $stmt->execute([
            'employee_id' => $employee['employee_id'],
            'holiday_date' => $body['holiday_date'],
        ]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result){
            $errors[] = [
                'employee_id' => $employee['employee_id'],
                'name' => $result['name'],
                'message' => 'Employee is already on leave on the given date'
            ];
        }
    }
        if($errors){
            throw new Exception(json_encode($errors));
        }
}