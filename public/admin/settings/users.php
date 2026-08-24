<?php

// This file defines settingpages and externalpages under the "users" category.

use core\context\system;
use core\lang_string;
use core\url;
use core_admin\setting\setting\agedigitalconsentmap;
use core_admin\setting\setting\configcheckbox;
use core_admin\setting\setting\configmulticheckbox;
use core_admin\setting\setting\configmultiselect;
use core_admin\setting\setting\configselect;
use core_admin\setting\setting\configtext;
use core_admin\setting\setting\sitepolicy_handler_select;
use core_admin\setting\settingpage\settingpage;
use core_admin\setting\tree\category;
use core_admin\setting\tree\externalpage;

$ADMIN->add('users', new category('accounts', new lang_string('accounts', 'admin')));
$ADMIN->add('users', new category('roles', new lang_string('permissions', 'role')));
$ADMIN->add('users', new category('privacy', new lang_string('privacyandpolicies', 'admin')));

$capabilities = [
    'moodle/user:create',
    'moodle/user:update',
    'moodle/user:delete',
    'moodle/role:manage',
    'moodle/role:assign',
    'moodle/cohort:manage',
    'moodle/cohort:view',
    'moodle/cohort:configurecustomfields',
    'moodle/role:safeoverride',
    'moodle/role:override',
    'moodle/notes:view',
];

if ($hassiteconfig || has_any_capability($capabilities, $systemcontext)) { // Speedup for non-admins, all caps used on this page.


    // Stuff under the "accounts" subcategory.
    $ADMIN->add('accounts', new externalpage('editusers', new lang_string('userlist','admin'), "$CFG->wwwroot/$CFG->admin/user.php", array('moodle/user:update', 'moodle/user:delete')));
    $ADMIN->add('accounts', new externalpage('userbulk', new lang_string('userbulk','admin'), "$CFG->wwwroot/$CFG->admin/user/user_bulk.php", array('moodle/user:update', 'moodle/user:delete')));
    $ADMIN->add('accounts', new externalpage('addnewuser', new lang_string('addnewuser'), "$CFG->wwwroot/user/editadvanced.php?id=-1", 'moodle/user:create'));

    // User management settingpage.
    $temp = new settingpage('usermanagement', new lang_string('usermanagement', 'admin'));
    if ($ADMIN->fulltree) {
        $choices = array();
        $choices['realname'] = new lang_string('fullnameuser');
        $choices['lastname'] = new lang_string('lastname');
        $choices['firstname'] = new lang_string('firstname');
        $choices['username'] = new lang_string('username');
        $choices['email'] = new lang_string('email');
        $choices['city'] = new lang_string('city');
        $choices['country'] = new lang_string('country');
        $choices['confirmed'] = new lang_string('confirmed', 'admin');
        $choices['suspended'] = new lang_string('suspended', 'auth');
        $choices['profile'] = new lang_string('profilefields', 'admin');
        $choices['courserole'] = new lang_string('courserole', 'filters');
        $choices['anycourses'] = new lang_string('anycourses', 'filters');
        $choices['systemrole'] = new lang_string('globalrole', 'role');
        $choices['cohort'] = new lang_string('idnumber', 'core_cohort');
        $choices['firstaccess'] = new lang_string('firstaccess', 'filters');
        $choices['lastaccess'] = new lang_string('lastaccess');
        $choices['neveraccessed'] = new lang_string('neveraccessed', 'filters');
        $choices['timecreated'] = new lang_string('timecreated');
        $choices['timemodified'] = new lang_string('lastmodified');
        $choices['nevermodified'] = new lang_string('nevermodified', 'filters');
        $choices['auth'] = new lang_string('authentication');
        $choices['idnumber'] = new lang_string('idnumber');
        $choices['lastip'] = new lang_string('lastip');
        $choices['mnethostid'] = new lang_string('mnetidprovider', 'mnet');
        $temp->add(new configmultiselect('userfiltersdefault', new lang_string('userfiltersdefault', 'admin'),
            new lang_string('userfiltersdefault_desc', 'admin'), array('realname'), $choices));
    }
    $ADMIN->add('accounts', $temp);

    // User default preferences settingpage.
    $temp = new settingpage('userdefaultpreferences', new lang_string('userdefaultpreferences', 'admin'));
    if ($ADMIN->fulltree) {
        $choices = array();
        $choices['0'] = new lang_string('emaildisplayno');
        $choices['1'] = new lang_string('emaildisplayyes');
        $choices['2'] = new lang_string('emaildisplaycourse');
        $temp->add(new configselect('defaultpreference_maildisplay', new lang_string('emaildisplay'),
            new lang_string('emaildisplay_help'), 2, $choices));

        $choices = array();
        $choices['0'] = new lang_string('textformat');
        $choices['1'] = new lang_string('htmlformat');
        $temp->add(new configselect('defaultpreference_mailformat', new lang_string('emailformat'), '', 1, $choices));

        $choices = array();
        $choices['0'] = new lang_string('emaildigestoff');
        $choices['1'] = new lang_string('emaildigestcomplete');
        $choices['2'] = new lang_string('emaildigestsubjects');
        $temp->add(new configselect('defaultpreference_maildigest', new lang_string('emaildigest'),
            new lang_string('emaildigest_help'), 0, $choices));


        $choices = array();
        $choices['1'] = new lang_string('autosubscribeyes');
        $choices['0'] = new lang_string('autosubscribeno');
        $temp->add(new configselect('defaultpreference_autosubscribe', new lang_string('autosubscribe'),
            '', 1, $choices));

        $choices = array();
        $choices['0'] = new lang_string('trackforumsno');
        $choices['1'] = new lang_string('trackforumsyes');
        $temp->add(new configselect('defaultpreference_trackforums', new lang_string('trackforums'),
            '', 0, $choices));

        $choices = [];
        $choices[\core_contentbank\content::VISIBILITY_PUBLIC] = new lang_string('visibilitychoicepublic', 'core_contentbank');
        $choices[\core_contentbank\content::VISIBILITY_UNLISTED] = new lang_string('visibilitychoiceunlisted', 'core_contentbank');
        $temp->add(new configselect('defaultpreference_core_contentbank_visibility',
            new lang_string('visibilitypref', 'core_contentbank'),
            new lang_string('visibilitypref_help', 'core_contentbank'),
            \core_contentbank\content::VISIBILITY_PUBLIC, $choices));
    }
    $ADMIN->add('accounts', $temp);

    $ADMIN->add('accounts', new externalpage('profilefields', new lang_string('profilefields','admin'), "$CFG->wwwroot/user/profile/index.php", 'moodle/site:config'));
    $ADMIN->add('accounts', new externalpage('cohorts', new lang_string('cohorts', 'cohort'), $CFG->wwwroot . '/cohort/index.php', array('moodle/cohort:manage', 'moodle/cohort:view')));
    $ADMIN->add(
        'accounts',
        new externalpage(
            'cohort_customfield',
            new lang_string('cohort_customfield', 'admin'),
            $CFG->wwwroot . '/cohort/customfield.php',
            ['moodle/cohort:configurecustomfields']
        )
    );
    $ADMIN->add(
        'accounts',
        new externalpage(
            'manageusersnotes',
            new lang_string('manageusersnotes', 'notes'),
            "$CFG->wwwroot/notes/index.php?filtertype=course&filterselect=0",
            'moodle/notes:view',
            empty($CFG->enablenotes)
        )
    );

    // Stuff under the "roles" subcategory.

    // User policies settingpage.
    $temp = new settingpage('userpolicies', new lang_string('userpolicies', 'admin'));
    if ($ADMIN->fulltree) {
        if (!during_initial_install()) {
            $context = system::instance();

            $otherroles      = array();
            $guestroles      = array();
            $userroles       = array();
            $creatornewroles = array();

            $defaultteacherid = null;
            $defaultuserid    = null;
            $defaultguestid   = null;

            $roles = role_fix_names(get_all_roles(), null, ROLENAME_ORIGINALANDSHORT);
            foreach ($roles as $role) {
                $rolename = $role->localname;
                switch ($role->archetype) {
                    case 'manager':
                        $creatornewroles[$role->id] = $rolename;
                        break;
                    case 'coursecreator':
                        break;
                    case 'editingteacher':
                        $defaultteacherid = isset($defaultteacherid) ? $defaultteacherid : $role->id;
                        $creatornewroles[$role->id] = $rolename;
                        break;
                    case 'teacher':
                        $creatornewroles[$role->id] = $rolename;
                        break;
                    case 'student':
                        break;
                    case 'guest':
                        $defaultguestid = isset($defaultguestid) ? $defaultguestid : $role->id;
                        $guestroles[$role->id] = $rolename;
                        break;
                    case 'user':
                        $defaultuserid = isset($defaultuserid) ? $defaultuserid : $role->id;
                        $userroles[$role->id] = $rolename;
                        break;
                    case 'frontpage':
                        break;
                    default:
                        $creatornewroles[$role->id] = $rolename;
                        $otherroles[$role->id] = $rolename;
                        break;
                }
            }

            if (empty($guestroles)) {
                $guestroles[0] = new lang_string('none');
                $defaultguestid = 0;
            }

            if (empty($userroles)) {
                $userroles[0] = new lang_string('none');
                $defaultuserid = 0;
            }

            $restorersnewrole = $creatornewroles;
            $restorersnewrole[0] = new lang_string('none');

            $temp->add(new configselect('notloggedinroleid', new lang_string('notloggedinroleid', 'admin'),
                          new lang_string('confignotloggedinroleid', 'admin'), $defaultguestid, ($guestroles + $otherroles)));
            $temp->add(new configselect('guestroleid', new lang_string('guestroleid', 'admin'),
                          new lang_string('guestroleid_help', 'admin'), $defaultguestid, ($guestroles + $otherroles)));
            $temp->add(new configselect('defaultuserroleid', new lang_string('defaultuserroleid', 'admin'),
                          new lang_string('configdefaultuserroleid', 'admin'), $defaultuserid, ($userroles + $otherroles)));
            $temp->add(new configselect('creatornewroleid', new lang_string('creatornewroleid', 'admin'),
                          new lang_string('creatornewroleid_help', 'admin'), $defaultteacherid, $creatornewroles));
            $temp->add(new configselect('restorernewroleid', new lang_string('restorernewroleid', 'admin'),
                          new lang_string('restorernewroleid_help', 'admin'), $defaultteacherid, $restorersnewrole));

            // Release memory.
            unset($otherroles);
            unset($guestroles);
            unset($userroles);
            unset($creatornewroles);
            unset($restorersnewrole);
        }

        $temp->add(new configcheckbox('enroladminnewcourse', new lang_string('enroladminnewcourse', 'admin'),
            new lang_string('enroladminnewcourse_help', 'admin'), 1));

        $temp->add(new configcheckbox('autologinguests', new lang_string('autologinguests', 'admin'), new lang_string('configautologinguests', 'admin'), 0));

        $temp->add(new configmultiselect('hiddenuserfields', new lang_string('hiddenuserfields', 'admin'),
                   new lang_string('confighiddenuserfields', 'admin'), array(),
                       array('description' => new lang_string('description'),
                             'email' => new lang_string('email'),
                             'city' => new lang_string('city'),
                             'country' => new lang_string('country'),
                             'timezone' => new lang_string('timezone'),
                             'firstaccess' => new lang_string('firstaccess'),
                             'lastaccess' => new lang_string('lastaccess'),
                             'lastip' => new lang_string('lastip'),
                             'mycourses' => new lang_string('mycourses'),
                             'groups' => new lang_string('groups'),
                             'suspended' => new lang_string('suspended', 'auth'),
                       )));

        // Select fields to display as part of user identity (only to those
        // with moodle/site:viewuseridentity).
        // Options include fields from the user table that might be helpful to
        // distinguish when adding or listing users ('I want to add the John
        // Smith from Science faculty') and any custom profile fields.
        $temp->add(new configmulticheckbox('showuseridentity',
                new lang_string('showuseridentity', 'admin'),
                new lang_string('showuseridentity_desc', 'admin'), ['email' => 1],
                function() {
                    global $CFG;
                    require_once($CFG->dirroot.'/user/profile/lib.php');

                    // Basic fields available in user table.
                    $fields = [
                        'username'    => new lang_string('username'),
                        'idnumber'    => new lang_string('idnumber'),
                        'email'       => new lang_string('email'),
                        'phone1'      => new lang_string('phone1'),
                        'phone2'      => new lang_string('phone2'),
                        'department'  => new lang_string('department'),
                        'institution' => new lang_string('institution'),
                        'city'        => new lang_string('city'),
                        'country'     => new lang_string('country'),
                    ];

                    // Custom profile fields.
                    $profilefields = profile_get_custom_fields();
                    foreach ($profilefields as $field) {
                        // Only reasonable-length text fields can be used as identity fields.
                        if ($field->param2 > 255 || $field->datatype != 'text') {
                            continue;
                        }
                        $fields['profile_field_' . $field->shortname] = format_string($field->name, true,
                            ['context' => system::instance()]) . ' *';
                    }

                    return $fields;
                }));
        $setting = new configtext('fullnamedisplay', new lang_string('fullnamedisplay', 'admin'),
            new lang_string('configfullnamedisplay', 'admin'), 'language', PARAM_TEXT, 50);
        $setting->set_force_ltr(true);
        $temp->add($setting);
        $temp->add(new configtext('alternativefullnameformat', new lang_string('alternativefullnameformat', 'admin'),
                new lang_string('alternativefullnameformat_desc', 'admin'),
                'language', PARAM_RAW, 50));
        $temp->add(new configtext('maxusersperpage', new lang_string('maxusersperpage','admin'), new lang_string('configmaxusersperpage','admin'), 100, PARAM_INT));
        $temp->add(new configcheckbox('enablegravatar', new lang_string('enablegravatar', 'admin'), new lang_string('enablegravatar_help', 'admin'), 0));
        $temp->add(new configtext('gravatardefaulturl', new lang_string('gravatardefaulturl', 'admin'), new lang_string('gravatardefaulturl_help', 'admin'), 'mm'));
    }

    $ADMIN->add('roles', $temp);

    if (is_siteadmin()) {
        $ADMIN->add('roles', new externalpage('admins', new lang_string('siteadministrators', 'role'), "$CFG->wwwroot/$CFG->admin/roles/admins.php"));
    }
    $ADMIN->add('roles', new externalpage('defineroles', new lang_string('defineroles', 'role'), "$CFG->wwwroot/$CFG->admin/roles/manage.php", 'moodle/role:manage'));
    $ADMIN->add('roles', new externalpage('assignroles', new lang_string('assignglobalroles', 'role'), "$CFG->wwwroot/$CFG->admin/roles/assign.php?contextid=".$systemcontext->id, 'moodle/role:assign'));
    $ADMIN->add('roles', new externalpage('checkpermissions', new lang_string('checkglobalpermissions', 'role'), "$CFG->wwwroot/$CFG->admin/roles/check.php?contextid=".$systemcontext->id, array('moodle/role:assign', 'moodle/role:safeoverride', 'moodle/role:override', 'moodle/role:manage')));

} // End of speedup.

// Privacy settings.
if ($hassiteconfig) {
    $temp = new settingpage('privacysettings', new lang_string('privacysettings', 'admin'));

    $options = array(
        0 => get_string('no'),
        1 => get_string('yes')
    );
    $url = new url('/admin/settings.php?section=supportcontact');
    $url = $url->out();
    $setting = new configselect('agedigitalconsentverification',
        new lang_string('agedigitalconsentverification', 'admin'),
        new lang_string('agedigitalconsentverification_desc', 'admin', $url), 0, $options);
    $setting->set_force_ltr(true);
    $temp->add($setting);

    // See {@link https://gdpr-info.eu/art-8-gdpr/}.
    // See {@link https://www.betterinternetforkids.eu/web/portal/practice/awareness/detail?articleId=3017751}.
    $ageofdigitalconsentmap = implode(PHP_EOL, [
        '*, 16',
        'AT, 14',
        'BE, 13',
        'BG, 14',
        'CY, 14',
        'CZ, 15',
        'DK, 13',
        'EE, 13',
        'ES, 14',
        'FI, 13',
        'FR, 15',
        'GB, 13',
        'GR, 15',
        'IT, 14',
        'LT, 14',
        'LV, 13',
        'MT, 13',
        'NO, 13',
        'PT, 13',
        'SE, 13',
        'US, 13'
    ]);
    $setting = new agedigitalconsentmap('agedigitalconsentmap',
        new lang_string('ageofdigitalconsentmap', 'admin'),
        new lang_string('ageofdigitalconsentmap_desc', 'admin'),
        $ageofdigitalconsentmap,
        PARAM_RAW
    );
    $temp->add($setting);

    $ADMIN->add('privacy', $temp);

    // Policy settings.
    $temp = new settingpage('policysettings', new lang_string('policysettings', 'admin'));
    $temp->add(new sitepolicy_handler_select('sitepolicyhandler', new lang_string('sitepolicyhandler', 'core_admin'),
        new lang_string('sitepolicyhandler_desc', 'core_admin')));
    $temp->add(new configtext('sitepolicy', new lang_string('sitepolicy', 'core_admin'),
        new lang_string('sitepolicy_help', 'core_admin'), '', PARAM_RAW));
    $temp->add(new configtext('sitepolicyguest', new lang_string('sitepolicyguest', 'core_admin'),
        new lang_string('sitepolicyguest_help', 'core_admin'), (isset($CFG->sitepolicy) ? $CFG->sitepolicy : ''), PARAM_RAW));

    $ADMIN->add('privacy', $temp);
}
