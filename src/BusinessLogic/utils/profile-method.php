<?php

function createProfileTemplate(object $db, array $body): void
{
    $sql = "INSERT INTO profile_template (work_start_time, work_end_time, work_late_time, allow_level, monday, tuesday, wednesday, thursday, friday, saturday, sunday)
    VALUES (:work_start_time, :work_end_time, :work_late_time, :allow_level, :monday, :tuesday, :wednesday, :thursday, :friday, :saturday, :sunday)";
    $stmt = $db->prepare($sql);
    $stmt->execute([
        ':work_start_time' => $body['work_start_time'],
        ':work_end_time' => $body['work_end_time'],
        ':work_late_time' => $body['work_late_time'],
        ':allow_level' => $body['allow_level'],
        ':monday' => $body['monday'],
        ':tuesday' => $body['tuesday'],
        ':wednesday' => $body['wednesday'],
        ':thursday' => $body['thursday'],
        ':friday' => $body['friday'],
        ':saturday' => $body['saturday'],
        ':sunday' => $body['sunday']
    ]);
}

/**
 * @throws Exception
 */
function selectProfileTemplate(object $db, array $body): void
{
    //check if employee already has a profile template in that selected date or not
    checkSelectedProfile($db, $body);
    //if passed, then insert the profile template
    $sql = "INSERT INTO employees_profile_template (employee_id, template_id, start_date, end_date)
    VALUES (:employee_id, :profile_template_id, :start_date, :end_date)";
    $stmt = $db->prepare($sql);
    $stmt->execute([
        ':employee_id' => $body['employee_id'],
        ':profile_template_id' => $body['profile_template_id'],
        ':start_date' => $body['start_date'],
        ':end_date' => $body['end_date']
    ]);
}

/**
 * @throws Exception
 */
function checkSelectedProfile(object $db, array $body): void
{
    $sql = "SELECT * FROM employees_profile_template WHERE employee_id = :id
    AND (start_date <= :start_date AND end_date >= :start_date
    OR start_date <= :end_date AND end_date >= :end_date)";
    $stmt = $db->prepare($sql);
    $stmt->execute([
        ':id' => $body['employee_id'],
        ':start_date' => $body['start_date'],
        ':end_date' => $body['end_date']
    ]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($result) {
        throw new Exception("Employee already has a profile template in that selected date");
    }
}
