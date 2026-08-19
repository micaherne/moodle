<?php

use core_admin\setting\setting\configselect;

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {
    if (empty($CFG->enablerssfeeds)) {
        $options = array(0 => get_string('rssglobaldisabled', 'admin'));
        $str = get_string('configenablerssfeeds', 'data').'<br />'.get_string('configenablerssfeedsdisabled2', 'admin');

    } else {
        $options = array(0=>get_string('no'), 1=>get_string('yes'));
        $str = get_string('configenablerssfeeds', 'data');
    }
    $settings->add(new configselect('data_enablerssfeeds', get_string('enablerssfeeds', 'admin'),
                       $str, 0, $options));
}
