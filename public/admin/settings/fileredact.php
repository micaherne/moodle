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
 * Configure the settings for file redaction service.
 *
 * @package   core_admin
 * @copyright Meirza <meirza.arson@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use core\lang_string;
use core_admin\setting\settingpage\settingpage;
use core_admin\setting\tree\category;

defined('MOODLE_INTERNAL') || die();

if (!$ADMIN->locate('file_redactor')) {
    $ADMIN->add('server', new category('file_redactor', get_string('redactor', 'core_files')));
}
if ($hassiteconfig) {

    $manager = \core\di::get(\core_files\redactor\manager::class);

    // Get settings from each service.
    foreach ($manager->get_service_classnames() as $servicename => $service) {
        $servicesettings = new settingpage(
            $servicename,
            new lang_string("redactor:{$servicename}", 'core_files'),
        );
        $service::add_settings($servicesettings);

        $ADMIN->add('file_redactor', $servicesettings);
    }
}
