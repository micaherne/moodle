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

/**
* script for bulk user force password change
*/

require_once('../../config.php');
require_once('lib.php');
require_once($CFG->libdir.'/adminlib.php');

$confirm = optional_param('confirm', 0, PARAM_BOOL);

admin_externalpage_setup('userbulk');
require_capability('moodle/user:update', context_system::instance());

$returnurl = optional_param('returnurl', '', PARAM_LOCALURL);
$return = new moodle_url($returnurl ?: '/admin/user/user_bulk.php');

if (empty($SESSION->bulk_users)) {
    redirect($return);
}

$PAGE->set_primary_active_tab('siteadminnode');
$PAGE->set_secondary_active_tab('users');

echo $OUTPUT->header();

if ($confirm and confirm_sesskey()) {
    // only force password change if user may actually change the password
    $authsavailable = get_enabled_auth_plugins();
    $changeable = array();

    foreach($authsavailable as $authplugin) {
        if (!$auth = get_auth_plugin($authplugin)) {
            continue;
        }
        if ($auth->is_internal() and $auth->can_change_password()) {
            $changeable[$authplugin] = true;
        }
    }

    $parts = array_chunk($SESSION->bulk_users, 300);
    foreach ($parts as $users) {
        list($in, $params) = $DB->get_in_or_equal($users);
        $rs = $DB->get_recordset_select('user', "id $in", $params);
        foreach ($rs as $user) {
            if (!empty($changeable[$user->auth])) {
                set_user_preference('auth_forcepasswordchange', 1, $user->id);
                unset($SESSION->bulk_users[$user->id]);
            } else {
                echo $OUTPUT->notification(get_string('forcepasswordchangenot', '', fullname($user, true)));
            }
        }
        $rs->close();
    }
    echo $OUTPUT->notification(get_string('changessaved'), 'notifysuccess');
    echo $OUTPUT->continue_button($return);

} else {
    list($in, $params) = $DB->get_in_or_equal($SESSION->bulk_users);
    $userlist = $DB->get_records_select_menu('user', "id $in", $params, 'fullname', 'id,'.$DB->sql_fullname().' AS fullname', 0, MAX_BULK_USERS);
    $usernames = implode(', ', $userlist);
    if (count($SESSION->bulk_users) > MAX_BULK_USERS) {
        $usernames .= ', ...';
    }
    echo $OUTPUT->heading(get_string('confirmation', 'admin'));
    $formcontinue = new single_button(new moodle_url('/admin/user/user_bulk_forcepasswordchange.php',
        ['confirm' => 1, 'returnurl' => $returnurl]), get_string('yes'));
    $formcancel = new single_button($return, get_string('no'), 'get');
    echo $OUTPUT->confirm(get_string('forcepasswordchangecheckfull', '', $usernames), $formcontinue, $formcancel);
}

echo $OUTPUT->footer();
