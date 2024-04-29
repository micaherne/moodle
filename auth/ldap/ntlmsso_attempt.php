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

require(__DIR__.'/../../config.php');

$PAGE->set_url('/auth/ldap/ntlmsso_attempt.php');
$PAGE->set_context(context_system::instance());

// Define variables used in page
$site = get_site();

$authsequence = get_enabled_auth_plugins(); // Auths, in sequence.
if (!in_array('ldap', $authsequence, true)) {
    throw new \moodle_exception('ldap_isdisabled', 'auth');
}

$authplugin = get_auth_plugin('ldap');
if (empty($authplugin->config->ntlmsso_enabled)) {
    throw new \moodle_exception('ntlmsso_isdisabled', 'auth_ldap');
}

$sesskey = sesskey();

// Display the page header. This makes redirect respect the timeout we specify
// here (and not add 3 more secs) which in turn prevents a bug in both IE 6.x
// and FF 3.x (Windows version at least) where javascript timers fire up even
// when we've already left the page that set the timer.
$loginsite = get_string("loginsite");
$PAGE->navbar->add($loginsite);
$PAGE->set_title($loginsite);
$PAGE->set_heading($site->fullname);
echo $OUTPUT->header();

$msg = '<p>'.get_string('ntlmsso_attempting', 'auth_ldap').'</p>'
    . '<img width="1", height="1" '
    . ' src="' . $CFG->wwwroot . '/auth/ldap/ntlmsso_magic.php?sesskey='
    . $sesskey . '" />';
redirect($CFG->wwwroot . '/auth/ldap/ntlmsso_finish.php', $msg, 3);
