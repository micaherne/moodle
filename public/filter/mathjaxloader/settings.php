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
 * MathJAX filter settings
 *
 * @package    filter_mathjaxloader
 * @copyright  2014 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use core\lang_string;
use core_admin\setting\setting\configcheckbox;
use core_admin\setting\setting\configtext;
use core_admin\setting\setting\configtextarea;
use core_admin\setting\setting\heading;

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {
    $item = new heading(
        'filter_mathjaxloader/localinstall',
        new lang_string('localinstall', 'filter_mathjaxloader'),
        new lang_string('localinstall_help', 'filter_mathjaxloader')
    );
    $settings->add($item);

    $item = new configtext(
        'filter_mathjaxloader/httpsurl',
        new lang_string('httpsurl', 'filter_mathjaxloader'),
        new lang_string('httpsurl_help', 'filter_mathjaxloader'),
        'https://cdn.jsdelivr.net/npm/mathjax@4.0.0/tex-mml-chtml.js',
        PARAM_RAW
    );
    $settings->add($item);

    $item = new configcheckbox(
        'filter_mathjaxloader/texfiltercompatibility',
        new lang_string('texfiltercompatibility', 'filter_mathjaxloader'),
        new lang_string('texfiltercompatibility_help', 'filter_mathjaxloader'),
        0
    );
    $settings->add($item);

    $item = new configtextarea(
        'filter_mathjaxloader/mathjaxconfig',
        new lang_string('mathjaxsettings', 'filter_mathjaxloader'),
        new lang_string('mathjaxsettings_desc', 'filter_mathjaxloader'),
        ''
    );

    $settings->add($item);

    $item = new configtext(
        'filter_mathjaxloader/additionaldelimiters',
        new lang_string('additionaldelimiters', 'filter_mathjaxloader'),
        new lang_string('additionaldelimiters_help', 'filter_mathjaxloader'),
        '',
        PARAM_RAW
    );
    $settings->add($item);
}
