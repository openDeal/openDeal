<?php

class ControllerCommonStartup extends \Core\Controller {

    public function index() {
        $this->response->setOutput($this->render());
    }
    
}