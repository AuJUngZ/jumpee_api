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

function updateApproveCountAndStatus(object $db, string $leaveId, string $currentStatus): void{
    //get config
    $config = getNumberOfApproval($db);
    //get leave type
    $sql = "SELECT leave_type FROM leaves_requirement WHERE id = '$leaveId'";
    $stmt = $db->prepare($sql);
    $stmt->execute();
    $leaveType = $stmt->fetchAll(PDO::FETCH_ASSOC)[0]['leave_type'];

    //select approve_count
    $approveCount = 0;
    if($leaveType === 'business_leave'){
        $approveCount = $config[0]['num_approvals_business_leave'];
    }else if($leaveType === 'sick_leave'){
        $approveCount = $config[0]['num_approvals_sick_leave'];
    }

    if($currentStatus === 'Approved'){
        //count up
        $sql = "UPDATE leaves_requirement SET approve_count = leaves_requirement.approve_count + 1,
        leave_status = CASE
            WHEN approve_count >= '$approveCount' THEN 'Approved'
            ELSE leave_status
        END
        WHERE id = '$leaveId'";
    }else{
        //count down
        $sql = "UPDATE leaves_requirement SET approve_count = leaves_requirement.approve_count - 1,
        leave_status = CASE
            WHEN approve_count <= '$approveCount' THEN 'Pending'
            ELSE leave_status
        END
        WHERE id = '$leaveId'
        ";
    }
    $stmt = $db->prepare($sql);
    $stmt->execute();
}

function getNumberOfApproval(object $db){
    $sql = "SELECT num_approvals_business_leave, num_approvals_sick_leave FROM leave_config";
    $stmt = $db->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}