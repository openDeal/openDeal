<?php
require_once('content_pos.php');
class ControllerCommonColumnRight extends ControllerCommonContentPos {

    protected function index() {
        $this->fetch('column_right');
    }

}
