<?php
// Copyright (c) 2025 Skynet Technologies USA LLC
class IndexController extends pm_Controller_Action
{

    public function indexAction()
    {
        $this->view->headScript()->appendFile(pm_Context::getBaseUrl() . 'js/all_in_one_accessibility.js');
        
    }

}
