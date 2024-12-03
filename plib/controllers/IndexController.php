<?php
// Copyright 1999-2016. Parallels IP Holdings GmbH.
class IndexController extends pm_Controller_Action
{

    public function indexAction()
    {
        $this->view->timeout = pm_Config::get('timeout');
        $this->view->homepage = pm_Config::get('homepage');
        $this->view->undefined = pm_Config::get('undefined');
    }

}
