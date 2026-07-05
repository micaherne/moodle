<?php

require_once(dirname(__DIR__) . '/convertlib.php');

class moodle1_export_converter extends base_converter {
    public static function is_available() {
        return false;
    }

    protected function execute() {

    }
}