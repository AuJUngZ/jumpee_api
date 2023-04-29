<?php


function getBodyForAddCertifier(string $body)
{
    $body = json_decode($body, true);
    return $body['certifiers'];
}

/**
 * @throws Exception
 */
function addCertifier($db, array $certifiers): void
{
    //we not add but remove all then add again
    $sql = "DELETE FROM create_approve_leave";
    $stmt = $db->prepare($sql);
    $stmt->execute();

    foreach ($certifiers as $certifier) {
        try {
            if ($certifier['type'] != 'create' && $certifier['type'] != 'sick_leave' && $certifier['type'] != 'business_leave') {
                throw new Exception('Type of leave is not valid');
            }
            $sql = "INSERT INTO create_approve_leave (employee_id, type_of_leave) VALUES (:employee_id, :type_of_leave)";
            $stmt = $db->prepare($sql);
            $stmt->execute([
                ':employee_id' => $certifier['employeeId'],
                ':type_of_leave' => $certifier['type']
            ]);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
}