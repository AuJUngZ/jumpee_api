<?php
declare(strict_types=1);
namespace App\Application\Settings;

class Settings implements SettingInterface
{
    private array $settings;

    public function __construct(array $settings)
    {
        $this->settings = $settings;
    }

    public function getSettings(string $key = "")
    {
        return (empty($key)) ? $this->settings : $this->settings[$key];
    }
}
