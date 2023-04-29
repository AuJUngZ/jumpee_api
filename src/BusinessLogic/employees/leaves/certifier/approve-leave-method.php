<?php

/**
 * @throws Exception
 */
function updateApproveStatus(object $db, string $leaveId, string $certifierId): array
{
    try{
        if(!checkDoesLeaveExist($db, $leaveId, $certifierId)){
            throw new Exception('Leave and certifier id does not match');
        }

        $sql = "UPDATE approved
        SET status = CASE
            WHEN status = 'Pending' THEN 'Approved'
            WHEN status = 'Approved' THEN 'Pending'
            ELSE status
        END
        WHERE leave_id = '$leaveId' AND certifier_id = '$certifierId'
    ";
        $stmt = $db->prepare($sql);
        $stmt->execute();

        //get the updated status
        $sql = "SELECT * FROM approved WHERE leave_id = '$leaveId' AND certifier_id = '$certifierId'";
        $stmt = $db->prepare($sql);
        $stmt->execute();
        return extracted($stmt, $db);
    }catch(Exception $e){
        throw new Exception($e->getMessage());
    }
}

function checkDoesLeaveExist(object $db, string $leaveId, string $certifierId): bool
{
    $sql = "SELECT * FROM approved WHERE leave_id = '$leaveId' AND certifier_id = '$certifierId'";
    $stmt = $db->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if(count($result) > 0){
        return true;
    }
    return false;
}
