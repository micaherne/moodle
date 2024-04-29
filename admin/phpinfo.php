<?php
// This file is part of Moodle - https://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

       // phpinfo.php - shows phpinfo for the current server

    require_once("../config.php");
    require_once($CFG->libdir.'/adminlib.php');

    admin_externalpage_setup('phpinfo');

    echo $OUTPUT->header();

    echo '<div class="phpinfo text-ltr">';

    ob_start();
    phpinfo(INFO_GENERAL + INFO_CONFIGURATION + INFO_MODULES + INFO_VARIABLES);
    $html = ob_get_contents();
    ob_end_clean();

/// Delete styles from output
    $html = preg_replace('#(\n?<style[^>]*?>.*?</style[^>]*?>)|(\n?<style[^>]*?/>)#is', '', $html);
    $html = preg_replace('#(\n?<head[^>]*?>.*?</head[^>]*?>)|(\n?<head[^>]*?/>)#is', '', $html);
/// Delete DOCTYPE from output
    $html = preg_replace('/<!DOCTYPE html PUBLIC.*?>/is', '', $html);
/// Delete body and html tags
    $html = preg_replace('/<html.*?>.*?<body.*?>/is', '', $html);
    $html = preg_replace('/<\/body><\/html>/is', '', $html);

    echo $html;

    echo '</div>';

    echo $OUTPUT->footer();


