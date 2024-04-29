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

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {
    require_once($CFG->dirroot.'/mod/glossary/lib.php');

    $settings->add(new admin_setting_heading('glossary_normal_header', get_string('glossaryleveldefaultsettings', 'glossary'), ''));

    $settings->add(new admin_setting_configtext('glossary_entbypage', get_string('entbypage', 'glossary'),
                       get_string('entbypage', 'glossary'), 10, PARAM_INT));


    $settings->add(new admin_setting_configcheckbox('glossary_dupentries', get_string('allowduplicatedentries', 'glossary'),
                       get_string('cnfallowdupentries', 'glossary'), 0));

    $settings->add(new admin_setting_configcheckbox('glossary_allowcomments', get_string('allowcomments', 'glossary'),
                       get_string('cnfallowcomments', 'glossary'), 0));

    $settings->add(new admin_setting_configcheckbox('glossary_linkbydefault', get_string('usedynalink', 'glossary'),
                       get_string('cnflinkglossaries', 'glossary'), 1));

    $settings->add(new admin_setting_configcheckbox('glossary_defaultapproval', get_string('defaultapproval', 'glossary'),
                       get_string('cnfapprovalstatus', 'glossary'), 1));


    if (empty($CFG->enablerssfeeds)) {
        $options = array(0 => get_string('rssglobaldisabled', 'admin'));
        $str = get_string('configenablerssfeeds', 'glossary').'<br />'.get_string('configenablerssfeedsdisabled2', 'admin');

    } else {
        $options = array(0=>get_string('no'), 1=>get_string('yes'));
        $str = get_string('configenablerssfeeds', 'glossary');
    }
    $settings->add(new admin_setting_configselect('glossary_enablerssfeeds', get_string('enablerssfeeds', 'admin'),
                       $str, 0, $options));


    $settings->add(new admin_setting_heading('glossary_levdev_header', get_string('entryleveldefaultsettings', 'glossary'), ''));

    $settings->add(new admin_setting_configcheckbox('glossary_linkentries', get_string('usedynalink', 'glossary'),
                       get_string('cnflinkentry', 'glossary'), 0));

    $settings->add(new admin_setting_configcheckbox('glossary_casesensitive', get_string('casesensitive', 'glossary'),
                       get_string('cnfcasesensitive', 'glossary'), 0));

    $settings->add(new admin_setting_configcheckbox('glossary_fullmatch', get_string('fullmatch', 'glossary'),
                       get_string('cnffullmatch', 'glossary'), 0));

    // This is unfortunately necessary to ensure that the glossary_formats table is populated and up to date.
    // Ensure the table is in sync with what display formats are available in code.
    glossary_get_available_formats();

    $settings->add(new mod_glossary_admin_setting_display_formats());
}
