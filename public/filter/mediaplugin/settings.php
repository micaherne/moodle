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
 * Mediaplugin filter settings
 *
 * @package    filter_mediaplugin
 * @copyright  2016 Marina Glancy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use core\lang_string;
use core\url;
use core_admin\setting\setting\heading;

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {
    $url = new url('/admin/settings.php', ['section' => 'managemediaplayers']);
    $item = new heading('filter_mediaplugin/about',
        '',
        new lang_string('linktomedia', 'filter_mediaplugin', $url->out()));
    $settings->add($item);

}
