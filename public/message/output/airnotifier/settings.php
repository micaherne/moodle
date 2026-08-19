<?php
// This file is part of Moodle - http://moodle.org/
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
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Airnotifier configuration page
 *
 * @package    message_airnotifier
 * @copyright  2012 Jerome Mouneyrac, 2014 Juan Leyva
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
use core\lang_string;
use core\output\html_writer;
use core\url;
use core_admin\setting\setting\configcheckbox;
use core_admin\setting\setting\configselect;
use core_admin\setting\setting\configtext;
use core_admin\setting\setting\heading;

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {

    $notify = new \core\output\notification(
        get_string('moodleappsportallimitswarning', 'message_airnotifier',
            (new url('https://apps.moodle.com'))->out()),
        \core\output\notification::NOTIFY_WARNING);
    $settings->add(new heading('tool_mobile/moodleappsportalfeaturesappearance', '', $OUTPUT->render($notify)));

    // The processor should be enabled by the same enable mobile setting.
    $settings->add(new configtext('airnotifierurl',
                    get_string('airnotifierurl', 'message_airnotifier'),
                    get_string('configairnotifierurl', 'message_airnotifier'), message_airnotifier_manager::AIRNOTIFIER_PUBLICURL,
                    PARAM_URL));
    $settings->add(new configtext('airnotifierport',
                    get_string('airnotifierport', 'message_airnotifier'),
                    get_string('configairnotifierport', 'message_airnotifier'), 443, PARAM_INT));
    $settings->add(new configtext('airnotifiermobileappname',
                    get_string('airnotifiermobileappname', 'message_airnotifier'),
                    get_string('configairnotifiermobileappname', 'message_airnotifier'), 'com.moodle.moodlemobile', PARAM_TEXT));
    $settings->add(new configtext('airnotifierappname',
                    get_string('airnotifierappname', 'message_airnotifier'),
                    get_string('configairnotifierappname', 'message_airnotifier'), 'commoodlemoodlemobile', PARAM_TEXT));
    $settings->add(new configtext('airnotifieraccesskey',
                    get_string('airnotifieraccesskey', 'message_airnotifier'),
                    get_string('configairnotifieraccesskey', 'message_airnotifier'), '', PARAM_ALPHANUMEXT));

    $settings->add(new configcheckbox('message_airnotifier/encryptnotifications',
        new lang_string('encryptnotifications', 'message_airnotifier'),
        new lang_string('encryptnotifications_help', 'message_airnotifier'),
        false
    ));

    $options = [
        message_airnotifier_manager::ENCRYPT_UNSUPPORTED_NOT_SEND => new lang_string('donotsendnotification', 'message_airnotifier'),
        message_airnotifier_manager::ENCRYPT_UNSUPPORTED_SEND => new lang_string('sendnotificationnotenc', 'message_airnotifier'),
    ];
    $settings->add(new configselect('message_airnotifier/encryptprocessing',
        new lang_string('encryptprocessing', 'message_airnotifier'),
        new lang_string('encryptprocessing_desc', 'message_airnotifier'),
        message_airnotifier_manager::ENCRYPT_UNSUPPORTED_NOT_SEND,
        $options
    ));
    $settings->hide_if('message_airnotifier/encryptprocessing', 'message_airnotifier/encryptnotifications',
        'neq', 1);

    $url = new url('/message/output/airnotifier/requestaccesskey.php', array('sesskey' => sesskey()));
    $link = html_writer::link($url, get_string('requestaccesskey', 'message_airnotifier'));
    $settings->add(new heading('requestaccesskey', '', $link));
    // Check configuration.
    $url = new url('/message/output/airnotifier/checkconfiguration.php');
    $link = html_writer::link($url, get_string('checkconfiguration', 'message_airnotifier'));
    $settings->add(new heading('checkconfiguration', '', $link));
}
