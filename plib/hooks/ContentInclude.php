<?php
class Modules_PleskAllInOneAccessibility_ContentInclude extends pm_Hook_ContentInclude
{
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
