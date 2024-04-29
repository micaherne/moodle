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

class mod_glossary_import_form extends moodleform {

    function definition() {
        global $CFG;
        $mform =& $this->_form;
        $cmid = $this->_customdata['id'] ?? null;

        $mform->addElement('filepicker', 'file', get_string('filetoimport', 'glossary'));
        $mform->addHelpButton('file', 'filetoimport', 'glossary');
        $options = array();
        $options['current'] = get_string('currentglossary', 'glossary');
        $options['newglossary'] = get_string('newglossary', 'glossary');
        $mform->addElement('select', 'dest', get_string('destination', 'glossary'), $options);
        $mform->addHelpButton('dest', 'destination', 'glossary');
        $mform->addElement('checkbox', 'catsincl', get_string('importcategories', 'glossary'));
        $submit_string = get_string('submit');
        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);
        $this->add_action_buttons(true, $submit_string);
    }
}
