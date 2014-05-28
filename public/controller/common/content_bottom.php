<?php
require_once('content_pos.php');
class ControllerCommonContentBottom extends ControllerCommonContentPos {

    protected function index() {
        $this->fetch('content_bottom');
    }

}
