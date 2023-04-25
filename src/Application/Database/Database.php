<?php
declare(strict_types=1);
namespace App\Application\Database;

use PDO;

class Database implements DatabaseInterface
{
    private string $host;
    private string $user;
    private string $password;
    private string $database;
    private PDO $connection;

    public function __construct(string $host, string $user, string $password, string $database)
    {
        $this->host = $host;
        $this->user = $user;
        $this->password = $password;
        $this->database = $database;
    }

    private function connect(): void
    {
        $this->connection = new PDO("mysql:host=$this->host;dbname=$this->database", $this->user, $this->password);
        $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public function getConnection(): PDO
    {
        if (!isset($this->connection)) {
            $this->connect();
        }
        return $this->connection;
    }
}