<?php

// This file defines everything related to frontpage

use core\context\course;
use core\lang_string;
use core_admin\setting\setting\configselect;
use core_admin\setting\setting\configtext;
use core_admin\setting\setting\courselist_frontpage;
use core_admin\setting\setting\sitesetcheckbox;
use core_admin\setting\setting\sitesetselect;
use core_admin\setting\setting\sitesettext;
use core_admin\setting\setting\special_frontpagedesc;
use core_admin\setting\settingpage\settingpage;

if (!during_initial_install()) { //do not use during installation
    $frontpagecontext = course::instance(SITEID);

    if ($hassiteconfig or has_any_capability(array(
            'moodle/course:update',
            'moodle/role:assign',
            'moodle/restore:restorecourse',
            'moodle/backup:backupcourse',
            'moodle/course:managefiles',
            'moodle/question:add',
            'moodle/question:editmine',
            'moodle/question:editall',
            'moodle/question:viewmine',
            'moodle/question:viewall',
            'moodle/question:movemine',
            'moodle/question:moveall'), $frontpagecontext)) {

        // "frontpage" settingpage
        $temp = new settingpage('frontpagesettings', new lang_string('frontpagesettings','admin'), 'moodle/course:update', false, $frontpagecontext);
        $temp->add(new sitesettext('fullname', new lang_string('fullsitename'), '', NULL)); // no default
        $temp->add(new sitesettext('shortname', new lang_string('shortsitename'), '', NULL)); // no default
        $temp->add(new special_frontpagedesc());
        $temp->add(new courselist_frontpage(false)); // non-loggedin version of the setting (that's what the parameter is for :) )
        $temp->add(new courselist_frontpage(true)); // loggedin version of the setting

        $options = array();
        $options[] = new lang_string('unlimited');
        for ($i=1; $i<100; $i++) {
            $options[$i] = $i;
        }
        $temp->add(new configselect('maxcategorydepth', new lang_string('configsitemaxcategorydepth','admin'), new lang_string('configsitemaxcategorydepthhelp','admin'), 2, $options));

        $temp->add(new configtext('frontpagecourselimit', new lang_string('configfrontpagecourselimit','admin'), new lang_string('configfrontpagecourselimithelp','admin'), 200, PARAM_INT));

        $temp->add(new sitesetcheckbox('numsections', new lang_string('sitesection'), new lang_string('sitesectionhelp','admin'), 1));
        $temp->add(new sitesetselect('newsitems', new lang_string('newsitemsnumber'), '', 3,
             array('0' => '0',
                   '1' => '1',
                   '2' => '2',
                   '3' => '3',
                   '4' => '4',
                   '5' => '5',
                   '6' => '6',
                   '7' => '7',
                   '8' => '8',
                   '9' => '9',
                   '10' => '10')));
        $temp->add(new configtext('commentsperpage', new lang_string('commentsperpage', 'admin'), '', 15, PARAM_INT));

        // front page default role
        $options = array(0=>new lang_string('none')); // roles to choose from
        $defaultfrontpageroleid = 0;
        $roles = role_fix_names(get_all_roles(), null, ROLENAME_ORIGINALANDSHORT);
        foreach ($roles as $role) {
            if (empty($role->archetype) or $role->archetype === 'guest' or $role->archetype === 'frontpage' or $role->archetype === 'student') {
                $options[$role->id] = $role->localname;
                if ($role->archetype === 'frontpage' && !$defaultfrontpageroleid) {
                    $defaultfrontpageroleid = $role->id;
                }
            }
        }
        if ($defaultfrontpageroleid and (!isset($CFG->defaultfrontpageroleid) or $CFG->defaultfrontpageroleid)) {
            //frotpage role may not exist in old upgraded sites
            unset($options[0]);
        }
        $temp->add(new configselect('defaultfrontpageroleid', new lang_string('frontpagedefaultrole', 'admin'), '', $defaultfrontpageroleid, $options));

        $ADMIN->add('frontpage', $temp);
    }
}
