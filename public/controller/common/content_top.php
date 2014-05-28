<?php
require_once('content_pos.php');
class ControllerCommonContentTop extends ControllerCommonContentPos {

    protected function index() {
        $this->fetch('content_top');
    }

}
