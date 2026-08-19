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
 * Settings for unoconv.
 *
 * @package   fileconverter_unoconv
 * @copyright 2017 Andrew Nicols <andrew@nicols.co.uk>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use core\lang_string;
use core\output\html_writer;
use core\url;
use core_admin\setting\setting\configexecutable;
use core_admin\setting\setting\heading;

defined('MOODLE_INTERNAL') || die();

// Unoconv setting.
$settings->add(new configexecutable('pathtounoconv',
        new lang_string('pathtounoconv', 'fileconverter_unoconv'),
        new lang_string('pathtounoconv_help', 'fileconverter_unoconv'),
        '/usr/bin/unoconv')
    );

$url = new url('/files/converter/unoconv/testunoconv.php');
$link = html_writer::link($url, get_string('test_unoconv', 'fileconverter_unoconv'));
$settings->add(new heading('test_unoconv', '', $link));
