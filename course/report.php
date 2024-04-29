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

      // Display all the interfaces for importing data into a specific course

    require_once('../config.php');

    $id = required_param('id', PARAM_INT);   // course id to import TO
    $course = $DB->get_record('course', array('id'=>$id), '*', MUST_EXIST);

    $PAGE->set_pagelayout('standard');
    require_login($course);

    $context = context_course::instance($course->id);
    require_capability('moodle/site:viewreports', $context); // basic capability for listing of reports

    $strreports = get_string('reports');

    $PAGE->set_url(new moodle_url('/course/report.php', array('id'=>$id)));
    $PAGE->set_title($course->fullname.': '.$strreports);
    $PAGE->set_heading($course->fullname.': '.$strreports);
    echo $OUTPUT->header();

    $reports = core_component::get_plugin_list('coursereport');

    foreach ($reports as $report => $reportdirectory) {
        $pluginfile = $reportdirectory.'/mod.php';
        if (file_exists($pluginfile)) {
            ob_start();
            include($pluginfile);  // Fragment for listing
            $html = ob_get_contents();
            ob_end_clean();
            // add div only if plugin accessible
            if ($html !== '') {
                echo '<div class="plugin">';
                echo $html;
                echo '</div>';
            }
        }
    }

    echo $OUTPUT->footer();

