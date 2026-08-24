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
 * Settings
 *
 * @package     factor_email
 * @author      Mikhail Golenkov <golenkovm@gmail.com>
 * @copyright   Catalyst IT
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use core\lang_string;
use core_admin\setting\setting\configcheckbox;
use core_admin\setting\setting\configduration;
use core_admin\setting\setting\configtext;
use core_admin\setting\setting\heading;

defined('MOODLE_INTERNAL') || die();

if ($ADMIN->fulltree) {
    $settings->add(new heading('factor_email/description', '',
        new lang_string('settings:description', 'factor_email')));
    $settings->add(new heading('factor_email/settings', new lang_string('settings', 'moodle'), ''));

    $enabled = new configcheckbox('factor_email/enabled',
        new lang_string('settings:enablefactor', 'tool_mfa'),
        new lang_string('settings:enablefactor_help', 'tool_mfa'), 1);
    $enabled->set_updatedcallback(function () {
        \tool_mfa\manager::do_factor_action('email', get_config('factor_email', 'enabled') ? 'enable' : 'disable');
    });
    $settings->add($enabled);

    $settings->add(new configtext('factor_email/weight',
        new lang_string('settings:weight', 'tool_mfa'),
        new lang_string('settings:weight_help', 'tool_mfa'), 100, PARAM_INT));

    $settings->add(new configduration('factor_email/duration',
        get_string('settings:duration', 'factor_email'),
        get_string('settings:duration_help', 'factor_email'), 30 * MINSECS, MINSECS));

    $settings->add(new configcheckbox('factor_email/suspend',
        get_string('settings:suspend', 'factor_email'),
        get_string('settings:suspend_help', 'factor_email'), 0));
}
