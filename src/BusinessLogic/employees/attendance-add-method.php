<?php

function postAttendance($db, $body): void
{
    $sql = "INSERT INTO attendance_raw (employee_id, temperature, in_out_time, device_ip, device_key)
    VALUES (:employee_id, :temperature, :in_out_time, :device_ip, :device_key)";
    $stmt = $db->prepare($sql);
    $stmt->execute([
        ':employee_id' => $body['employee_id'],
        ':temperature' => $body['temperature'],
        ':in_out_time' => $body['in_out_time'],
        ':device_ip' => $body['device_ip'],
        ':device_key' => $body['device_key'],
    ]);

    //check if attendance table is has data of this day for this employee or not
    $sql = "SELECT * FROM attendance WHERE employee_id = :employee_id AND date(in_time) = :date";
    $stmt = $db->prepare($sql);
    $stmt->execute([
        ':employee_id' => $body['employee_id'],
        ':date' => date('Y-m-d', strtotime($body['in_out_time'])),
    ]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);


    //if not, insert new data of this day for this employee
    if(!$result){
        $time_config = getWorkTimeConfig($db);
        $work_start_time = $time_config['work_start_time'];
        $work_late_time = $time_config['work_end_time'];
        $sql = "INSERT INTO attendance (employee_id, in_time, out_time, in_temperature, out_temperature, status)
    VALUES (:employee_id, :in_time, :out_time, :in_temperature, :out_temperature,
            CASE
                WHEN TIME(:in_time) >= '$work_start_time' AND TIME(:in_time) <= '$work_late_time' THEN 'On Time'
                WHEN TIME(:in_time) > '$work_late_time' THEN 'Late'
                ELSE 'Early'
            END)";
        $stmt = $db->prepare($sql);
        $stmt->execute([
            ':employee_id' => $body['employee_id'],
            ':in_time' => $body['in_out_time'],
            ':out_time' => null,
            ':in_temperature' => $body['temperature'],
            ':out_temperature' => null,
        ]);
    }else{
       //update out_time and out_temperature
         $sql = "UPDATE attendance SET out_time = :out_time, out_temperature = :out_temperature WHERE employee_id = :employee_id AND date(in_time) = :date";
        $stmt = $db->prepare($sql);
        $stmt->execute([
            ':employee_id' => $body['employee_id'],
            ':out_time' => $body['in_out_time'],
            ':out_temperature' => $body['temperature'],
            ':date' => date('Y-m-d', strtotime($body['in_out_time'])),
        ]);
    }
}

function getWorkTimeConfig(object $db){
    $sql = "SELECT * FROM work_time_config";
    $stmt = $db->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC)[0];
}