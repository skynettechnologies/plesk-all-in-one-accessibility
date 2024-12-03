<?php
// Copyright 1999-2016. Parallels IP Holdings GmbH.
class Modules_CustomConfig_ConfigDefaults extends pm_Hook_ConfigDefaults
{

    public function getDefaults()
    {
        return [
            'homepage' => 'https://www.plesk.com/',
            'timeout' => 60,
        ];
    }
    public function getJsConfig()
    {
        return [
            'dynamicVar' => date(DATE_ATOM),
        ];
    }

    public function getJsOnReadyContent()
    {
        return 'PleskExt.AllinOneAccessibility.init();';
    }

    public function getJsContent()
    {
        return '// example';
    }

    public function getHeadContent()
    {
        return '<!-- additional content for head tag -->';
    }

    public function getBodyContent()
    {
        return '<!-- additional content for body tag -->';
    }


}
