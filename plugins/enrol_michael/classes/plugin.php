<?php

class enrol_michael_plugin extends enrol_plugin {

    public function add_instance($course, array $fields = NULL) {

    }

    public function get_newinstance_link($courseid) {
        $context = context_course::instance($courseid, MUST_EXIST);
        if (!has_capability('moodle/course:enrolconfig', $context)) {
            return NULL;
        }
        // multiple instances supported - multiple parent courses linked
        $url =  $this->plugin_url('/addinstance.php', array('id'=>$courseid));
        return $url;
    }

}