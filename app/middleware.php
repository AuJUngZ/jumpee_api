<?php
use App\Application\Settings\SettingInterface;
use Slim\App;

return function(App $app){
    $settings = $app->getContainer()->get(SettingInterface::class);
    $app->addRoutingMiddleware();
    $app->addErrorMiddleware($settings->getSettings('displayErrorDetails'), $settings->getSettings('logErrors'), $settings->getSettings('logErrorDetails'));
};