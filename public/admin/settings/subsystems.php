<?php

use core\lang_string;
use core_admin\setting\setting\configcheckbox;
use core_admin\setting\setting\configselect;
use core_admin\setting\setting\description;

if ($hassiteconfig) { // speedup for non-admins, add all caps used on this page

    $optionalsubsystems->add(new configcheckbox('enableoutcomes', new lang_string('enableoutcomes', 'grades'), new lang_string('enableoutcomes_help', 'grades'), 0));
    $optionalsubsystems->add(new configcheckbox('usecomments', new lang_string('enablecomments', 'admin'), new lang_string('configenablecomments', 'admin'), 1));

    $optionalsubsystems->add(new configcheckbox('usetags', new lang_string('usetags','admin'),new lang_string('configusetags', 'admin'), '1'));

    $optionalsubsystems->add(new configcheckbox('enablenotes', new lang_string('enablenotes', 'notes'), new lang_string('configenablenotes', 'notes'), 1));

    $optionalsubsystems->add(new configcheckbox('enableportfolios', new lang_string('enabled', 'portfolio'), new lang_string('enableddesc', 'portfolio'), 0));

    $optionalsubsystems->add(new configcheckbox('enablewebservices', new lang_string('enablewebservices', 'admin'), new lang_string('configenablewebservices', 'admin'), 0));

    $optionalsubsystems->add(new configcheckbox('enablestats', new lang_string('enablestats', 'admin'), new lang_string('configenablestats', 'admin'), 0));

    $optionalsubsystems->add(new configcheckbox('enablerssfeeds', new lang_string('enablerssfeeds', 'admin'), new lang_string('configenablerssfeeds', 'admin'), 0));

    $optionalsubsystems->add(new configcheckbox('enableblogs', new lang_string('enableblogs', 'admin'), new lang_string('configenableblogs', 'admin'), 1));

    $options = array('off'=>new lang_string('off', 'mnet'), 'strict'=>new lang_string('on', 'mnet'));
    $optionalsubsystems->add(new configselect('mnet_dispatcher_mode', new lang_string('net', 'mnet'), new lang_string('configmnet', 'mnet'), 'off', $options));

    // Conditional activities: completion and availability
    $optionalsubsystems->add(new configcheckbox('enablecompletion',
        new lang_string('enablecompletion','completion'),
        new lang_string('configenablecompletion', 'completion'), 1));

    $options = array(
        1 => get_string('completionactivitydefault', 'completion'),
        0 => get_string('completion_none', 'completion')
    );

    $optionalsubsystems->add($checkbox = new configcheckbox('enableavailability',
            new lang_string('enableavailability', 'availability'),
            new lang_string('enableavailability_desc', 'availability'), 1));
    $checkbox->set_affects_modinfo(true);

    $optionalsubsystems->add(new configcheckbox('enableplagiarism', new lang_string('enableplagiarism','plagiarism'), new lang_string('configenableplagiarism','plagiarism'), 0));

    $optionalsubsystems->add(new configcheckbox('enablebadges', new lang_string('enablebadges', 'badges'), new lang_string('configenablebadges', 'badges'), 1));

    $optionalsubsystems->add(new configcheckbox('enableglobalsearch', new lang_string('enableglobalsearch', 'admin'),
        new lang_string('enableglobalsearch_desc', 'admin'), 0, 1, 0));

    $optionalsubsystems->add(new configcheckbox('allowstealth', new lang_string('allowstealthmodules'),
        new lang_string('allowstealthmodules_help'), 0, 1, 0));
    // TODO: Analytics is now disable on new installs until MDL-84481 overcomes the config of mlbackend_python.
    $optionalsubsystems->add(new configcheckbox('enableanalytics', new lang_string('enableanalytics', 'admin'),
        new lang_string('configenableanalytics', 'admin'), 0, 1, 0));

    $optionalsubsystems->add(new configcheckbox('core_competency/enabled',
        new lang_string('enablecompetencies', 'core_competency'),
        new lang_string('enablecompetencies_desc', 'core_competency'),
        1)
    );

    $optionalsubsystems->add(new configcheckbox('messaging',
        new lang_string('messaging', 'admin'),
        new lang_string('configmessaging', 'admin'),
        1)
    );

    $optionalsubsystems->add(new configcheckbox('enablecustomreports',
        new lang_string('enablecustomreports', 'core_reportbuilder'),
        new lang_string('enablecustomreports_desc', 'core_reportbuilder'),
        1
    ));

    $fullunicodesupport = true;
    if ($DB->get_dbfamily() == 'mysql') {
        $collation = $DB->get_dbcollation();
        $collationinfo = explode('_', $collation);
        $charset = reset($collationinfo);
        $fullunicodesupport = $charset === 'utf8mb4';
    }

    if ($fullunicodesupport) {
        $optionalsubsystems->add(new configcheckbox(
            'allowemojipicker',
            new lang_string('allowemojipicker', 'admin'),
            new lang_string('configallowemojipicker', 'admin'),
            1
        ));
    } else {
        $optionalsubsystems->add(new description(
            'allowemojipicker',
            new lang_string('allowemojipicker', 'admin'),
            new lang_string('configallowemojipickerincompatible', 'admin')
        ));
    }
    $optionalsubsystems->add(new configcheckbox(
        'enableasyncresets',
        new lang_string('enableasyncresets', 'admin'),
        new lang_string('configenableasyncresets', 'admin'),
        0
    ));
}
