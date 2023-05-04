<?php

namespace App\Application\Config;
use App\Application\Database\DatabaseInterface;
use PDO;

class Config implements ConfigInterface
{
    private $db;
    public function __construct($app)
    {
        $this->db = $app->getContainer()->get(DatabaseInterface::class)->getConnection();
    }
    public function getAllConfig()
    {
        $sql = "SELECT * FROM jumpee.leave_config
        JOIN jumpee.work_time_config
        JOIN jumpee.time_intervals
    ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}