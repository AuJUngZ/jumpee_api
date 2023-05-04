<?php
declare(strict_types=1);

use DI\ContainerBuilder;
use App\Application\Database\Database;
use App\Application\Database\DatabaseInterface;

return function(ContainerBuilder $containerBuilder){
    $containerBuilder->addDefinitions([
        DatabaseInterface::class => function(){
            return new Database("localhost", "au-db", "Z)7qD9u>{z{YM&`n", "au-db_jumpee");
        }
    ]);
};