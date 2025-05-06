<?php
class Modules_AllinOneAccessibilityConfig_ConfigDefaults extends pm_Hook_ConfigDefaults
{
    //Hook for extension config defaults (panel.ini settings)
    public function getDefaults()
    {
        return [
            'homepage' => 'https://www.skynettechnologies.com/',
            'timeout' => 60,
        ];
    }
}
