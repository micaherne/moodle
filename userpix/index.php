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

  // This simple script displays all the users with pictures on one page.
  // By default it is not linked anywhere on the site.  If you want to
  // make it available you should link it in yourself from somewhere.
  // Remember also to comment or delete the lines restricting access
  // to administrators only (see below)


require('../config.php');

$PAGE->set_url('/userpix/index.php');

require_login();

/// Remove the following three lines if you want everyone to access it
$syscontext = context_system::instance();
require_capability('moodle/site:config', $syscontext);

$title = get_string("users");
$PAGE->set_context($syscontext);
$PAGE->navbar->add($title);
$PAGE->set_title($title);
$PAGE->set_heading($title);
echo $OUTPUT->header();

$rs = $DB->get_recordset_select("user", "deleted = 0 AND picture > 0", array(), "lastaccess DESC",
        implode(',', \core_user\fields::get_picture_fields()));
foreach ($rs as $user) {
    $fullname = s(fullname($user));
    echo "<a href=\"$CFG->wwwroot/user/view.php?id=$user->id&amp;course=1\" ".
         "title=\"$fullname\">";
    echo $OUTPUT->user_picture($user);
    echo "</a> \n";
}
$rs->close();

echo $OUTPUT->footer();
