<?php

defined('MOODLE_INTERNAL') || die();

class core_plugin {
    
    public $type = null;
    public $name = null;
    
    public function __construct() {
        $parts = explode('_', get_class($this));
        $this->type = $parts[0];
        $this->name = $parts[1];
    }

    public function plugin_url($url, array $params = null, $anchor = null) {
        global $CFG;
        $dir = core_component::get_plugin_directory($this->type, $this->name);
        $path = str_replace($CFG->dirroot, '', $dir);
        $realurl = $path . $url;
        return new moodle_url($realurl, $params, $anchor);
    }

}