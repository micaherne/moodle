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
 * Settings file for plugin 'media_youtube'
 *
 * @package   media_youtube
 * @copyright 2023 Matt Porritt <matt.porritt@moodle.com
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use core\lang_string;
use core_admin\setting\setting\configcheckbox;
use core_admin\setting\setting\heading;

defined('MOODLE_INTERNAL') || die();

if ($ADMIN->fulltree) {
    // Add the settings page.
    $settings->add(new heading('media_youtube_settings',
                    get_string('pluginname', 'media_youtube'),
                    get_string('pluginname_help', 'media_youtube')));
    // Add a settings checkbox to enable or disable no cookie YouTube links.
    $settings->add(new configcheckbox('media_youtube/nocookie',
        new lang_string('nocookie', 'media_youtube'),
        new lang_string('nocookie_desc', 'media_youtube'), 0));
}
