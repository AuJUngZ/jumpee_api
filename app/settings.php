<?php
declare(strict_types=1);

use DI\ContainerBuilder;
use App\Application\Settings\Settings;
use App\Application\Settings\SettingInterface;

return function(ContainerBuilder $containerBuilder){
    $containerBuilder->addDefinitions([
        SettingInterface::class => function(){
            return new Settings([
                'displayErrorDetails' => true,
                'logErrors' => true,
                'logErrorDetails' => true,
            ]);
        }
    ]);
};