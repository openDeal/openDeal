<?php
require_once('content_pos.php');
class ControllerCommonColumnLeft extends ControllerCommonContentPos {

    protected function index() {
        $this->fetch('column_left');
    }

}
