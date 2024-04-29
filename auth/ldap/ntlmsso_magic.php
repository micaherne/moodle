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

// Don't let lib/setup.php set any cookies
// as we will be executing under the OS security
// context of the user we are trying to login, rather than
// of the webserver.
define('NO_MOODLE_COOKIES', true);

require(__DIR__.'/../../config.php');

$PAGE->set_context(context_system::instance());

$authsequence = get_enabled_auth_plugins(); // Auths, in sequence.
if (!in_array('ldap', $authsequence, true)) {
    throw new \moodle_exception('ldap_isdisabled', 'auth');
}

$authplugin = get_auth_plugin('ldap');
if (empty($authplugin->config->ntlmsso_enabled)) {
    throw new \moodle_exception('ntlmsso_isdisabled', 'auth_ldap');
}

$sesskey = required_param('sesskey', PARAM_RAW);
$file = $CFG->dirroot.'/pix/spacer.gif';

if ($authplugin->ntlmsso_magic($sesskey) && file_exists($file)) {
    if (!empty($authplugin->config->ntlmsso_ie_fastpath)) {
        if (core_useragent::is_ie()) {
            redirect($CFG->wwwroot.'/auth/ldap/ntlmsso_finish.php');
        }
    }

    // Serve GIF
    // Type
    header('Content-Type: image/gif');
    header('Content-Length: '.filesize($file));

    // Output file
    $handle = fopen($file, 'r');
    fpassthru($handle);
    fclose($handle);
    exit;
} else {
    throw new \moodle_exception('ntlmsso_iwamagicnotenabled', 'auth_ldap');
}


