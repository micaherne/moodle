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

require_once('../../config.php');
require_once('lib.php');

$id       = required_param('id', PARAM_INT);          // Entry ID
$confirm  = optional_param('confirm', 0, PARAM_BOOL); // export confirmation
$prevmode = required_param('prevmode', PARAM_ALPHA);
$hook     = optional_param('hook', '', PARAM_CLEAN);

$url = new moodle_url('/mod/glossary/exportentry.php', array('id'=>$id,'prevmode'=>$prevmode));
if ($confirm !== 0) {
    $url->param('confirm', $confirm);
}
if ($hook !== 'ALL') {
    $url->param('hook', $hook);
}
$PAGE->set_url($url);

if (!$entry = $DB->get_record('glossary_entries', array('id'=>$id))) {
    throw new \moodle_exception('invalidentry');
}

if ($entry->sourceglossaryid) {
    //already exported
    if (!$cm = get_coursemodule_from_id('glossary', $entry->sourceglossaryid)) {
        throw new \moodle_exception('invalidcoursemodule');
    }
    redirect('view.php?id='.$cm->id.'&amp;mode=entry&amp;hook='.$entry->id);
}

if (!$cm = get_coursemodule_from_instance('glossary', $entry->glossaryid)) {
    throw new \moodle_exception('invalidcoursemodule');
}

if (!$glossary = $DB->get_record('glossary', array('id'=>$cm->instance))) {
    throw new \moodle_exception('invalidid', 'glossary');
}

if (!$course = $DB->get_record('course', array('id'=>$cm->course))) {
    throw new \moodle_exception('coursemisconf');
}

require_course_login($course->id, true, $cm);
$context = context_module::instance($cm->id);
require_capability('mod/glossary:export', $context);

$returnurl = "view.php?id=$cm->id&amp;mode=$prevmode&amp;hook=".urlencode($hook);

if (!$mainglossary = $DB->get_record('glossary', array('course'=>$cm->course, 'mainglossary'=>1))) {
    //main glossary not present
    redirect($returnurl);
}

if (!$maincm = get_coursemodule_from_instance('glossary', $mainglossary->id)) {
    throw new \moodle_exception('invalidcoursemodule');
}

$context     = context_module::instance($cm->id);
$maincontext = context_module::instance($maincm->id);

if (!$course = $DB->get_record('course', array('id'=>$cm->course))) {
    throw new \moodle_exception('coursemisconf');
}


$strglossaries     = get_string('modulenameplural', 'glossary');
$entryalreadyexist = get_string('entryalreadyexist','glossary');
$entryexported     = get_string('entryexported','glossary');

if (!$mainglossary->allowduplicatedentries) {
    if ($DB->record_exists_select('glossary_entries',
            'glossaryid = :glossaryid AND LOWER(concept) = :concept', array(
                'glossaryid' => $mainglossary->id,
                'concept'    => core_text::strtolower($entry->concept)))) {
        $PAGE->set_title($glossary->name);
        $PAGE->set_heading($course->fullname);
        echo $OUTPUT->header();
        echo $OUTPUT->notification(get_string('errconceptalreadyexists', 'glossary'));
        echo $OUTPUT->continue_button($returnurl);
        echo $OUTPUT->box_end();
        echo $OUTPUT->footer();
        die;
    }
}

if (!data_submitted() or !$confirm or !confirm_sesskey()) {
    $PAGE->set_title($glossary->name);
    $PAGE->set_heading($course->fullname);
    echo $OUTPUT->header();
    echo '<div class="boxaligncenter">';
    $areyousure = '<h2>'.format_string($entry->concept).'</h2><p align="center">'.get_string('areyousureexport','glossary').'<br /><b>'.format_string($mainglossary->name).'</b>?';
    $linkyes    = 'exportentry.php';
    $linkno     = 'view.php';
    $optionsyes = array('id'=>$entry->id, 'confirm'=>1, 'sesskey'=>sesskey(), 'prevmode'=>$prevmode, 'hook'=>$hook);
    $optionsno  = array('id'=>$cm->id, 'mode'=>$prevmode, 'hook'=>$hook);

    echo $OUTPUT->confirm($areyousure, new moodle_url($linkyes, $optionsyes), new moodle_url($linkno, $optionsno));
    echo '</div>';
    echo $OUTPUT->footer();
    die;

} else {
    $entry->glossaryid       = $mainglossary->id;
    $entry->sourceglossaryid = $glossary->id;

    $DB->update_record('glossary_entries', $entry);

    // move attachments too
    $fs = get_file_storage();

    if ($oldfiles = $fs->get_area_files($context->id, 'mod_glossary', 'attachment', $entry->id)) {
        foreach ($oldfiles as $oldfile) {
            $file_record = new stdClass();
            $file_record->contextid = $maincontext->id;
            $fs->create_file_from_storedfile($file_record, $oldfile);
        }
        $fs->delete_area_files($context->id, 'mod_glossary', 'attachment', $entry->id);
        $entry->attachment = '1';
    } else {
        $entry->attachment = '0';
    }
    $DB->update_record('glossary_entries', $entry);

    redirect ($returnurl);
}

