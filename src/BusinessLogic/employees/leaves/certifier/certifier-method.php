<?php

function getAllCertifier(object $db):array{
    $sql = "SELECT * FROM create_approve_leave";
    $stmt = $db->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
