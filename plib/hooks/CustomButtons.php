<?php
// Copyright 1999-2021. Plesk International GmbH.
//PleskAllInOneAccessibility
class Modules_PleskAllInOneAccessibility_CustomButtons extends pm_Hook_CustomButtons
{

    public function getButtons()
    {
        return [
            [
                'place' => self::PLACE_ADMIN_NAVIGATION,
                'section' => self::SECTION_NAV_SERVER_MANAGEMENT,
                'title' => 'All in One Accessibility',
                'description' => 'Quick Web Accessibility Implementation with All In One Accessibility!',
                'icon' => pm_Context::getBaseUrl() . 'icons/aioa-icon-type-1.svg',
                'link' => pm_Context::getBaseUrl(),
                'contextParams' => true
            ],
            [
                'place' => self::PLACE_HOSTING_PANEL_NAVIGATION,
                'title' => 'All in One Accessibility',
                'description' => 'Quick Web Accessibility Implementation with All In One Accessibility!',
                'icon' => pm_Context::getBaseUrl() . 'icons/aioa-icon-type-1.svg',
                'link' => pm_Context::getBaseUrl(),
                'contextParams' => true
            ],
            [
                'place' => [self::PLACE_ADMIN_TOOLS_AND_SETTINGS,self::PLACE_DOMAIN_PROPERTIES],
                'title' => 'All in One Accessibility',
                'description' => 'Quick Web Accessibility Implementation with All In One Accessibility!',
                'icon' => pm_Context::getBaseUrl() . 'icons/aioa-icon-type-1.svg',
                'link' => pm_Context::getBaseUrl(),
                'newWindow' => false,
                'contextParams' => true
            ],
        ];
    }


}
