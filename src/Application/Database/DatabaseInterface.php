<?php
declare(strict_types=1);
namespace App\Application\Database;
interface DatabaseInterface
{
    public function getConnection();
}