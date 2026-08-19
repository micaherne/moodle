<?php
       // log.php - old scheduled backups report. Now redirecting
       // to the new admin one

    use core\context\system;

require_once("../config.php");

    require_login();

    require_capability('moodle/backup:backupcourse', system::instance());

    redirect("$CFG->wwwroot/report/backups/index.php", '', 'admin', 1);
