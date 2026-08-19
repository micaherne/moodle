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
 * Settings for assignfeedback PDF plugin
 *
 * @package   assignfeedback_editpdf
 * @copyright 2013 Davo Smith
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use core\lang_string;
use core\output\html_writer;
use core\url;
use core_admin\setting\setting\configcheckbox;
use core_admin\setting\setting\configstoredfile;
use core_admin\setting\setting\heading;

defined('MOODLE_INTERNAL') || die();

// Enabled by default.
$settings->add(new configcheckbox('assignfeedback_editpdf/default',
                   new lang_string('default', 'assignfeedback_editpdf'),
                   new lang_string('default_help', 'assignfeedback_editpdf'), 1));

// Stamp files setting.
$name = 'assignfeedback_editpdf/stamps';
$title = get_string('stamps','assignfeedback_editpdf');
$description = get_string('stampsdesc', 'assignfeedback_editpdf');

$setting = new configstoredfile($name, $title, $description, 'stamps', 0,
    array('maxfiles' => 8, 'accepted_types' => array('image')));
$settings->add($setting);

// Ghostscript setting.
$systempathslink = new url('/admin/settings.php', array('section' => 'systempaths'));
$systempathlink = html_writer::link($systempathslink, get_string('systempaths', 'admin'));
$settings->add(new heading('pathtogs', get_string('pathtogs', 'admin'),
        get_string('pathtogspathdesc', 'assignfeedback_editpdf', $systempathlink)));

$url = new url('/mod/assign/feedback/editpdf/testgs.php');
$link = html_writer::link($url, get_string('testgs', 'assignfeedback_editpdf'));
$settings->add(new heading('testgs', '', $link));
