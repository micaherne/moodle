<?php

require(__DIR__ . '/../config.php');
$PAGE->set_url('/calendar/view.php');
redirect($CFG->wwwroot.'/calendar/view.php');