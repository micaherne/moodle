<?php

use core_admin\setting\setting\configcheckbox;

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {

    $settings->add(new configcheckbox(
        'filter_multilang_force_old',
        get_string('forceoldsyntax', 'filter_multilang'),
        get_string('forceoldsyntax_desc', 'filter_multilang'),
        0
    ));
}
