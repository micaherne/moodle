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
 * Configure the setting page of the custom file type as an external page.
 *
 * @package tool_filetypes
 * @copyright 2014 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use core\lang_string;
use core_admin\setting\tree\externalpage;

defined('MOODLE_INTERNAL') || die;

if ($hassiteconfig) {
    $ADMIN->add('server', new externalpage('tool_filetypes',
            new lang_string('pluginname', 'tool_filetypes'),
            $CFG->wwwroot . '/admin/tool/filetypes/index.php'));
}
