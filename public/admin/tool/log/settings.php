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
 * Logging settings.
 *
 * @package    tool_log
 * @copyright  2013 Petr Skoda {@link http://skodak.org/}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use core\lang_string;
use core\plugin_manager;
use core_admin\setting\setting\configcheckbox;
use core_admin\setting\settingpage\settingpage;
use core_admin\setting\tree\category;

defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {

    $privacysettings = $ADMIN->locate('privacysettings');

    if ($ADMIN->fulltree) {
        $privacysettings->add(new configcheckbox('tool_log/exportlog',
                new lang_string('exportlog', 'tool_log'),
                new lang_string('exportlogdetail', 'tool_log'), 1)
        );
    }

    $ADMIN->add('modules', new category('logging', new lang_string('logging', 'tool_log')));

    $temp = new settingpage('managelogging', new lang_string('managelogging', 'tool_log'));
    $temp->add(new tool_log_setting_managestores());
    $ADMIN->add('logging', $temp);

    foreach (plugin_manager::instance()->get_plugins_of_type('logstore') as $plugin) {
        /** @var \tool_log\plugininfo\logstore $plugin */
        $plugin->load_settings($ADMIN, 'logging', $hassiteconfig);
    }
}
