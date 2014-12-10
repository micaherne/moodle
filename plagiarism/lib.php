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
 * lib.php - Contains Plagiarism base class used by plugins.
 *
 * @since Moodle 2.0
 * @package    moodlecore
 * @subpackage plagiarism
 * @copyright  2010 Dan Marsden http://danmarsden.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

//dummy class - all plugins should be based off this.
class plagiarism_plugin {

    /**
     * Return the list of form element names.
     *
     * @return array contains the form element names.
     */
    public function get_configs() {
        return array();
    }

    /**
     * hook to allow plagiarism specific information to be displayed beside a submission
     * @param plagiarism_checkable $checkable a data object for plagiarism checking
     * @return string
     *
     */
    public function get_links(plagiarism_checkable $checkable) {
        return '';
    }
    /**
     * hook to allow plagiarism specific information to be returned unformatted
     * @param int $cmid
     * @param int $userid
     * @param $file file object
     * @return array containing at least:
     *   - 'analyzed' - whether the file has been successfully analyzed
     *   - 'score' - similarity score - ('' if not known)
     *   - 'reporturl' - url of originality report - '' if unavailable
     */
    public function get_file_results($cmid, $userid, $file) {
        return array('analyzed' => '', 'score' => '', 'reporturl' => '');
    }
    /**
     * hook to add plagiarism specific settings to a module settings page
     * @param object $mform  - Moodle form
     * @param object $context - current context
     * @param string $modulename - Name of the module
     */
    public function get_form_elements_module($mform, $context, $modulename = "") {
    }
    /* hook to save plagiarism specific settings on a module settings page
     * @param object $data - data from an mform submission.
     */
    public function save_form_elements($data) {
    }
    /**
     * hook to allow a disclosure to be printed notifying users what will happen with their submission
     * @param int $cmid - course module id
     * @return string
     */
    public function print_disclosure($cmid) {
    }
    /**
     * hook to allow status of submitted files to be updated - called on grading/report pages.
     *
     * @param object $course - full Course object
     * @param object $cm - full cm object
     */
    public function update_status($course, $cm) {
    }
    /**
     * hook for cron
     *
     */
    public function plagiarism_cron() {
    }
}

interface plagiarism_checkable {

    public function get_user();

    public function get_course();

    public function get_cmid();

    public function get_context();

    public function get_component();

    public function get_area();

    public function get_itemid();

    public function get_content();

    public function get_content_type();

}

class plagiarism_checkable_file implements plagiarism_checkable, ArrayAccess  {

    protected $file = null;
    protected $course = null;
    protected $cm = null;

    public function __construct(stored_file $file, $course = null, $cmid = null) {
        $this->file = $file;
        $this->course = $course;
        $this->cm = $cm;
    }

    public function get_user() {
        return $this->file->get_userid();
    }

    public function get_course() {
        return $this->course;
    }

    public function get_cmid() {
        return $this->cmid;
    }

    public function get_context() {
        return $this->file->get_contextid();
    }

    public function get_component()  {
        return $this->file->get_component();
    }

    public function get_area() {
        return $this->file->get_filearea();
    }

    public function get_itemid() {
        return $this->file->get_itemid();
    }

    public function get_content() {
        return $this->f->get_content();
    }

    public function get_content_type() {
        return $this->file->get_mimetype();
    }

    /* Backwards compatibility stuff */
    public function offsetExists($offset) {
        return array_key_exists($offset, array('userid', 'content', 'cmid', 'course'));
    }
    public function offsetGet($offset) {
        switch ($offset) {
            case 'userid':
                return $this->get_user();
                break;
            case 'content':
                return $this->get_content();
                break;
            case 'cmid':
                return $this->get_cmid();
                break;
            case 'course':
                return $this->get_course();
                break;
            default:
                return null;
        }
    }
    public function offsetSet ($offset, $value) {
        // Do nothing
    }
    public function offsetUnset($offset) {
        // Do nothing
    }
}

class plagiarism_checkable_string implements plagiarism_checkable {

    protected $content = null;
    protected $userid = null;
    protected $contextid = null;
    protected $component = null;
    protected $area = null;
    protected $itemid = null;
    protected $course = null;
    protected $cmid = null;

    public function __construct($content, $userid, $contextid, $component, $area, $itemid, $course, $cmid) {
        $this->content = $content;
        $this->userid = $userid;
        $this->contextid = $contextid;
        $this->component = $component;
        $this->area = $area;
        $this->itemid = $itemid;
        $this->course = $course;
        $this->cmid = $cmid;
    }

    public function get_user() {
        return $this->userid;
    }

    public function get_course() {
        return $this->course;
    }

    public function get_cmid() {
        return $this->cmid;
    }

    public function get_context() {
        return $this->contextid;
    }

    public function get_component()  {
        return $this->component;
    }

    public function get_area() {
        return $this->area;
    }

    public function get_itemid() {
        return $this->itemid;
    }

    public function get_content() {
        return $this->content;
    }

    public function get_content_type() {
        return 'text/plain';
    }
}
