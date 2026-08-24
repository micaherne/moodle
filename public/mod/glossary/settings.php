<?php

use core_admin\setting\setting\configcheckbox;
use core_admin\setting\setting\configselect;
use core_admin\setting\setting\configtext;
use core_admin\setting\setting\heading;

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {
    require_once($CFG->dirroot.'/mod/glossary/lib.php');

    $settings->add(new heading('glossary_normal_header', get_string('glossaryleveldefaultsettings', 'glossary'), ''));

    $settings->add(new configtext('glossary_entbypage', get_string('entbypage', 'glossary'),
                       get_string('entbypage', 'glossary'), 10, PARAM_INT));


    $settings->add(new configcheckbox('glossary_dupentries', get_string('allowduplicatedentries', 'glossary'),
                       get_string('cnfallowdupentries', 'glossary'), 0));

    $settings->add(new configcheckbox('glossary_allowcomments', get_string('allowcomments', 'glossary'),
                       get_string('cnfallowcomments', 'glossary'), 0));

    $settings->add(new configcheckbox('glossary_linkbydefault', get_string('usedynalink', 'glossary'),
                       get_string('cnflinkglossaries', 'glossary'), 1));

    $settings->add(new configcheckbox('glossary_defaultapproval', get_string('defaultapproval', 'glossary'),
                       get_string('cnfapprovalstatus', 'glossary'), 1));


    if (empty($CFG->enablerssfeeds)) {
        $options = array(0 => get_string('rssglobaldisabled', 'admin'));
        $str = get_string('configenablerssfeeds', 'glossary').'<br />'.get_string('configenablerssfeedsdisabled2', 'admin');

    } else {
        $options = array(0=>get_string('no'), 1=>get_string('yes'));
        $str = get_string('configenablerssfeeds', 'glossary');
    }
    $settings->add(new configselect('glossary_enablerssfeeds', get_string('enablerssfeeds', 'admin'),
                       $str, 0, $options));


    $settings->add(new heading('glossary_levdev_header', get_string('entryleveldefaultsettings', 'glossary'), ''));

    $settings->add(new configcheckbox('glossary_linkentries', get_string('usedynalink', 'glossary'),
                       get_string('cnflinkentry', 'glossary'), 0));

    $settings->add(new configcheckbox('glossary_casesensitive', get_string('casesensitive', 'glossary'),
                       get_string('cnfcasesensitive', 'glossary'), 0));

    $settings->add(new configcheckbox('glossary_fullmatch', get_string('fullmatch', 'glossary'),
                       get_string('cnffullmatch', 'glossary'), 0));

    // This is unfortunately necessary to ensure that the glossary_formats table is populated and up to date.
    // Ensure the table is in sync with what display formats are available in code.
    glossary_get_available_formats();

    $settings->add(new mod_glossary_admin_setting_display_formats());
}
