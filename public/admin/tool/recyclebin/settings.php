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
 * Recycle bin settings.
 *
 * @package    tool_recyclebin
 * @copyright  2015 University of Kent
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use core\lang_string;
use core_admin\setting\setting\configcheckbox;
use core_admin\setting\setting\configduration;
use core_admin\setting\settingpage\settingpage;

defined('MOODLE_INTERNAL') || die();

global $PAGE;

if ($hassiteconfig) {
    $settings = new settingpage('tool_recyclebin', get_string('pluginname', 'tool_recyclebin'));
    $ADMIN->add('tools', $settings);

    $settings->add(new configcheckbox(
        'tool_recyclebin/coursebinenable',
        new lang_string('coursebinenable', 'tool_recyclebin'),
        '',
        1
    ));

    $settings->add(new configduration(
        'tool_recyclebin/coursebinexpiry',
        new lang_string('coursebinexpiry', 'tool_recyclebin'),
        new lang_string('coursebinexpiry_desc', 'tool_recyclebin'),
        WEEKSECS
    ));

    $settings->add(new configcheckbox(
        'tool_recyclebin/categorybinenable',
        new lang_string('categorybinenable', 'tool_recyclebin'),
        '',
        1
    ));

    $settings->add(new configduration(
        'tool_recyclebin/categorybinexpiry',
        new lang_string('categorybinexpiry', 'tool_recyclebin'),
        new lang_string('categorybinexpiry_desc', 'tool_recyclebin'),
        WEEKSECS
    ));

    $settings->add(new configcheckbox(
        'tool_recyclebin/autohide',
        new lang_string('autohide', 'tool_recyclebin'),
        new lang_string('autohide_desc', 'tool_recyclebin'),
        1
    ));
}
