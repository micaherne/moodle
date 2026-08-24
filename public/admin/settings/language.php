<?php

// This file defines settingpages and externalpages under the "appearance" category

use core\lang_string;
use core_admin\local\settings\setting_scheduled_task_status;
use core_admin\setting\setting\configcheckbox;
use core_admin\setting\setting\configselect;
use core_admin\setting\setting\configtext;
use core_admin\setting\setting\langlist;
use core_admin\setting\settingpage\settingpage;

if ($hassiteconfig) {

    // "languageandlocation" settingpage
    $temp = new settingpage('langsettings', new lang_string('languagesettings', 'admin'));
    $temp->add(new configcheckbox('autolang', new lang_string('autolang', 'admin'), new lang_string('configautolang', 'admin'), 1));
    $temp->add(new configselect('lang', new lang_string('lang', 'admin'), new lang_string('configlang', 'admin'), current_language(), get_string_manager()->get_list_of_translations())); // $CFG->lang might be set in installer already, default en is in setup.php
    $temp->add(new configcheckbox('autolangusercreation', new lang_string('autolangusercreation', 'admin'),
        new lang_string('configautolangusercreation', 'admin'), 1));
    $temp->add(new configcheckbox('langmenu', new lang_string('langmenu', 'admin'), new lang_string('configlangmenu', 'admin'), 1));
    $temp->add(new langlist('langlist'));
    $temp->add(new langlist('langscrawlable'));
    $temp->add(new configcheckbox('langcache', new lang_string('langcache', 'admin'), new lang_string('langcache_desc', 'admin'), 1));
    $temp->add(new configcheckbox('langstringcache', new lang_string('langstringcache', 'admin'), new lang_string('configlangstringcache', 'admin'), 1));
    $temp->add(new configtext('locale', new lang_string('localetext', 'admin'), new lang_string('configlocale', 'admin'), '', PARAM_FILE));
    $temp->add(new configselect('latinexcelexport', new lang_string('latinexcelexport', 'admin'), new lang_string('configlatinexcelexport', 'admin'), '0', array('0'=>'Unicode','1'=>'Latin')));
    $temp->add(new configcheckbox('enablepdfexportfont', new lang_string('enablepdfexportfont', 'admin'),
        new lang_string('enablepdfexportfont_desc', 'admin'), 0));
    $temp->add(new setting_scheduled_task_status('langimporttaskstatus', '\tool_langimport\task\update_langpacks_task'));

    $ADMIN->add('language', $temp);

} // end of speedup
