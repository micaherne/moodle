<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once($CFG->libdir.'/formslib.php');
require_once($CFG->libdir.'/csvlib.class.php');

class mod_data_import_form extends moodleform {

    function definition() {
        $mform =& $this->_form;

        $dataid = $this->_customdata['dataid'];
        $backtourl = $this->_customdata['backtourl'];

        $mform->addElement('filepicker', 'recordsfile', get_string('csvfile', 'data'),
            null, ['accepted_types' => ['application/zip', 'text/csv']]);

        $delimiters = csv_import_reader::get_delimiter_list();
        $mform->addElement('select', 'fielddelimiter', get_string('fielddelimiter', 'data'), $delimiters);
        $mform->setDefault('fielddelimiter', 'comma');

        $mform->addElement('text', 'fieldenclosure', get_string('fieldenclosure', 'data'));
        $mform->setType('fieldenclosure', PARAM_CLEANHTML);

        $choices = core_text::get_encodings();
        $mform->addElement('select', 'encoding', get_string('fileencoding', 'mod_data'), $choices);
        $mform->setDefault('encoding', 'UTF-8');

        // Database activity ID.
        $mform->addElement('hidden', 'd');
        $mform->setType('d', PARAM_INT);
        $mform->setDefault('d', $dataid);

        // Back to URL.
        $mform->addElement('hidden', 'backto');
        $mform->setType('backto', PARAM_LOCALURL);
        $mform->setDefault('backto', $backtourl);

        $this->add_action_buttons(true, get_string('submit'));
    }
}
