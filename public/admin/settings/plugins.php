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
 * Load all plugins into the admin tree.
 *
* Please note that is file is always loaded last - it means that you can inject entries into other categories too.
*
* @package    core
* @copyright  2007 Petr Skoda {@link http://skodak.org}
* @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/

use core\lang_string;
use core\output\html_writer;
use core\plugin_manager;
use core\url;
use core_admin\setting\page\manageblocks;
use core_admin\setting\page\managefilters;
use core_admin\setting\page\managemods;
use core_admin\setting\page\manageportfolios;
use core_admin\setting\page\manageqbehaviours;
use core_admin\setting\page\manageqtypes;
use core_admin\setting\page\managerepositories;
use core_admin\setting\page\pluginsoverview;
use core_admin\setting\setting\check;
use core_admin\setting\setting\configcheckbox;
use core_admin\setting\setting\configduration;
use core_admin\setting\setting\confightmleditor;
use core_admin\setting\setting\configmultiselect;
use core_admin\setting\setting\configselect;
use core_admin\setting\setting\configtext;
use core_admin\setting\setting\description;
use core_admin\setting\setting\heading;
use core_admin\setting\setting\manage_fileconverter_plugins;
use core_admin\setting\setting\manageantiviruses;
use core_admin\setting\setting\manageauths;
use core_admin\setting\setting\managecontentbankcontenttypes;
use core_admin\setting\setting\managecustomfields;
use core_admin\setting\setting\managedataformats;
use core_admin\setting\setting\manageenrols;
use core_admin\setting\setting\manageformats;
use core_admin\setting\setting\question_behaviour;
use core_admin\setting\setting\searchsetupinfo;
use core_admin\setting\settingpage\settingpage;
use core_admin\setting\tree\category;
use core_admin\setting\tree\externalpage;

$ADMIN->add('modules', new category('modsettings', new lang_string('activitymodules')));
$ADMIN->add('modules', new category('formatsettings', new lang_string('courseformats')));
$ADMIN->add('modules', new category('customfieldsettings', new lang_string('customfields', 'core_customfield')));
$ADMIN->add('modules', new category('blocksettings', new lang_string('blocks')));
$ADMIN->add('modules', new category('authsettings', new lang_string('authentication', 'admin')));
$ADMIN->add('modules', new category('enrolments', new lang_string('enrolments', 'enrol')));
$ADMIN->add('modules', new category('editorsettings', new lang_string('editors', 'editor')));
$ADMIN->add('modules', new category('antivirussettings', new lang_string('antiviruses', 'antivirus')));
$ADMIN->add('modules', new category('mlbackendsettings', new lang_string('mlbackendsettings', 'admin')));
$ADMIN->add('modules', new category('filtersettings', new lang_string('managefilters')));
$ADMIN->add('modules', new category('mediaplayers', new lang_string('type_media_plural', 'plugin')));
$ADMIN->add('modules', new category('fileconverterplugins', new lang_string('type_fileconverter_plural', 'plugin')));
$ADMIN->add('modules', new category('paymentgateways', new lang_string('type_paygw_plural', 'plugin')));
$ADMIN->add('modules', new category('dataformatsettings', new lang_string('dataformats')));
$ADMIN->add('modules', new category('portfoliosettings', new lang_string('portfolios', 'portfolio'),
    empty($CFG->enableportfolios)));
$ADMIN->add('modules', new category('repositorysettings', new lang_string('repositories', 'repository')));
$ADMIN->add('modules', new category('qbanksettings', new lang_string('type_qbank_plural', 'plugin')));
$ADMIN->add('modules', new category('qbehavioursettings', new lang_string('questionbehaviours', 'admin')));
$ADMIN->add('modules', new category('qtypesettings', new lang_string('questiontypes', 'admin')));
$ADMIN->add('modules', new category('plagiarism', new lang_string('plagiarism', 'plagiarism')));
$ADMIN->add('modules', new category('coursereports', new lang_string('coursereports')));
$ADMIN->add('modules', new category('reportplugins', new lang_string('reports')));
$ADMIN->add('modules', new category('searchplugins', new lang_string('search', 'admin')));
$ADMIN->add('modules', new category('tools', new lang_string('tools', 'admin')));
$ADMIN->add('modules', new category('cache', new lang_string('caching', 'cache')));
$ADMIN->add('cache', new category('cachestores', new lang_string('cachestores', 'cache')));
$ADMIN->add('modules', new category('calendartype', new lang_string('calendartypes', 'calendar')));
$ADMIN->add('modules', new category('communicationsettings', new lang_string('communication', 'core_communication')));
$ADMIN->add('modules', new category('sms', new lang_string('sms', 'core_sms')));
$ADMIN->add('modules', new category('contentbanksettings', new lang_string('contentbank')));
$ADMIN->add('modules', new category('localplugins', new lang_string('localplugins')));


if ($hassiteconfig) {
    /* @var admin_root $ADMIN */
    $ADMIN->locate('modules')->set_sorting(true);

    $ADMIN->add('modules', new pluginsoverview());

    // activity modules

    $ADMIN->add('modsettings', new managemods());

    $temp = new settingpage('managemodulescommon', new lang_string('commonactivitysettings', 'admin'));
    $temp->add(new configcheckbox('requiremodintro',
        get_string('requiremodintro', 'admin'), get_string('requiremodintro_desc', 'admin'), 0));
    $ADMIN->add('modsettings', $temp);

    $plugins = plugin_manager::instance()->get_plugins_of_type('mod');
    core_collator::asort_objects_by_property($plugins, 'displayname');
    foreach ($plugins as $plugin) {
        /** @var \core\plugininfo\mod $plugin */
        $plugin->load_settings($ADMIN, 'modsettings', $hassiteconfig);
    }

    // course formats
    $temp = new settingpage('manageformats', new lang_string('manageformats', 'core_admin'));
    $temp->add(new manageformats());
    $ADMIN->add('formatsettings', $temp);
    $plugins = plugin_manager::instance()->get_plugins_of_type('format');
    core_collator::asort_objects_by_property($plugins, 'displayname');
    foreach ($plugins as $plugin) {
        /** @var \core\plugininfo\format $plugin */
        $plugin->load_settings($ADMIN, 'formatsettings', $hassiteconfig);
    }

    // Custom fields.
    $temp = new settingpage('managecustomfields', new lang_string('managecustomfields', 'core_admin'));
    $temp->add(new managecustomfields());
    $ADMIN->add('customfieldsettings', $temp);
    $plugins = plugin_manager::instance()->get_plugins_of_type('customfield');
    core_collator::asort_objects_by_property($plugins, 'displayname');
    foreach ($plugins as $plugin) {
        /** @var \core\plugininfo\customfield $plugin */
        $plugin->load_settings($ADMIN, 'customfieldsettings', $hassiteconfig);
    }

    // blocks
    $ADMIN->add('blocksettings', new manageblocks());
    $plugins = plugin_manager::instance()->get_plugins_of_type('block');
    core_collator::asort_objects_by_property($plugins, 'displayname');
    foreach ($plugins as $plugin) {
        /** @var \core\plugininfo\block $plugin */
        $plugin->load_settings($ADMIN, 'blocksettings', $hassiteconfig);
    }

    // authentication plugins
    $temp = new settingpage('manageauths', new lang_string('authsettings', 'admin'));
    $temp->add(new manageauths());
    $temp->add(new heading('manageauthscommonheading', new lang_string('commonsettings', 'admin'), ''));
    $temp->add(new configcheckbox('allowaccountssameemail',
                    new lang_string('allowaccountssameemail', 'core_auth'),
                    new lang_string('allowaccountssameemail_desc', 'core_auth'), 0));
    $temp->add(new configcheckbox('authpreventaccountcreation', new lang_string('authpreventaccountcreation', 'admin'), new lang_string('authpreventaccountcreation_help', 'admin'), 0));
    $options = array(0 => get_string('no'), 1 => 1, 2 => 2, 3 => 3, 4 => 4, 5 => 5, 10 => 10, 20 => 20, 50 => 50);
    $temp->add(new configselect('limitconcurrentlogins',
        new lang_string('limitconcurrentlogins', 'core_auth'),
        new lang_string('limitconcurrentlogins_desc', 'core_auth'), 0, $options));
    $setting = new configtext('allowemailaddresses', new lang_string('allowemailaddresses', 'admin'),
        new lang_string('configallowemailaddresses', 'admin'), '', PARAM_NOTAGS);
    $setting->set_force_ltr(true);
    $temp->add($setting);
    $setting = new configtext('denyemailaddresses', new lang_string('denyemailaddresses', 'admin'),
        new lang_string('configdenyemailaddresses', 'admin'), '', PARAM_NOTAGS);
    $setting->set_force_ltr(true);
    $temp->add($setting);
    $temp->add(new configcheckbox('verifychangedemail', new lang_string('verifychangedemail', 'admin'), new lang_string('configverifychangedemail', 'admin'), 1));

    $ADMIN->add('authsettings', $temp);

    $temp = new externalpage('authtestsettings', get_string('testsettings', 'core_auth'), new url("/auth/test_settings.php"), 'moodle/site:config', true);
    $ADMIN->add('authsettings', $temp);

    $plugins = plugin_manager::instance()->get_plugins_of_type('auth');
    core_collator::asort_objects_by_property($plugins, 'displayname');
    foreach ($plugins as $plugin) {
        /** @var \core\plugininfo\auth $plugin */
        $plugin->load_settings($ADMIN, 'authsettings', $hassiteconfig);
    }

    // Enrolment plugins
    $temp = new settingpage('manageenrols', new lang_string('manageenrols', 'enrol'));
    $temp->add(new manageenrols());
    $ADMIN->add('enrolments', $temp);

    $temp = new externalpage('enroltestsettings', get_string('testsettings', 'core_enrol'), new url("/enrol/test_settings.php"), 'moodle/site:config', true);
    $ADMIN->add('enrolments', $temp);

    $plugins = plugin_manager::instance()->get_plugins_of_type('enrol');
    core_collator::asort_objects_by_property($plugins, 'displayname');
    foreach ($plugins as $plugin) {
        /** @var \core\plugininfo\enrol $plugin */
        $plugin->load_settings($ADMIN, 'enrolments', $hassiteconfig);
    }


/// Editor plugins
    $temp = new settingpage('manageeditors', new lang_string('editorsettings', 'editor'));
    $temp->add(new \core_admin\admin\admin_setting_plugin_manager(
        'editor',
        \core_admin\table\editor_management_table::class,
        'editorsui',
        get_string('editorsettings', 'editor'),
    ));
    $ADMIN->add('editorsettings', $temp);
    $plugins = plugin_manager::instance()->get_plugins_of_type('editor');
    core_collator::asort_objects_by_property($plugins, 'displayname');
    foreach ($plugins as $plugin) {
        /** @var \core\plugininfo\editor $plugin */
        $plugin->load_settings($ADMIN, 'editorsettings', $hassiteconfig);
    }

    // Antivirus plugins.
    $temp = new settingpage('manageantiviruses', new lang_string('antivirussettings', 'antivirus'));
    $temp->add(new manageantiviruses());

    // Status check.
    $temp->add(new heading('antivirus/statuschecks', new lang_string('statuschecks'), ''));
    $temp->add(new check('antivirus/checkantivirus', new \core\check\environment\antivirus()));

    // Common settings.
    $temp->add(new heading('antiviruscommonsettings', new lang_string('antiviruscommonsettings', 'antivirus'), ''));

    // Alert email.
    $temp->add(
        new configtext(
            'antivirus/notifyemail',
            new lang_string('notifyemail', 'antivirus'),
            new lang_string('notifyemail_help', 'antivirus'),
            '',
            PARAM_EMAIL
        )
    );

    // Notify level.
    $temp->add(new configselect('antivirus/notifylevel',
        get_string('notifylevel', 'antivirus'), '', core\antivirus\scanner::SCAN_RESULT_ERROR, [
            core\antivirus\scanner::SCAN_RESULT_ERROR => get_string('notifylevelerror', 'antivirus'),
            core\antivirus\scanner::SCAN_RESULT_FOUND => get_string('notifylevelfound', 'antivirus')
        ]),
    );

    // Threshold for check displayed on the /report/status/index.php page.
    $url = new url('/report/status/index.php');
    $link = html_writer::link($url, get_string('pluginname', 'report_status'));
    $temp->add(
        new configduration(
            'antivirus/threshold',
            new lang_string('threshold', 'antivirus'),
            get_string('threshold_desc', 'antivirus', $link),
            20 * MINSECS
        )
    );

    // Enable quarantine.
    $temp->add(
        new configcheckbox(
            'antivirus/enablequarantine',
            new lang_string('enablequarantine', 'antivirus'),
            new lang_string('enablequarantine_help', 'antivirus',
            \core\antivirus\quarantine::DEFAULT_QUARANTINE_FOLDER),
            0
        )
    );

    // Quarantine time.
    $temp->add(
        new configduration(
            'antivirus/quarantinetime',
            new lang_string('quarantinetime', 'antivirus'),
            new lang_string('quarantinetime_desc', 'antivirus'),
            \core\antivirus\quarantine::DEFAULT_QUARANTINE_TIME
        )
    );

    $ADMIN->add('antivirussettings', $temp);
    $plugins = plugin_manager::instance()->get_plugins_of_type('antivirus');
    core_collator::asort_objects_by_property($plugins, 'displayname');
    foreach ($plugins as $plugin) {
        /* @var \core\plugininfo\antivirus $plugin */
        $plugin->load_settings($ADMIN, 'antivirussettings', $hassiteconfig);
    }

    // Machine learning backend plugins.
    $plugins = plugin_manager::instance()->get_plugins_of_type('mlbackend');
    foreach ($plugins as $plugin) {
        $plugin->load_settings($ADMIN, 'mlbackendsettings', $hassiteconfig);
    }

/// Filter plugins

    $ADMIN->add('filtersettings', new managefilters());

    // "filtersettings" settingpage
    $temp = new settingpage('commonfiltersettings', new lang_string('commonfiltersettings', 'admin'));
    if ($ADMIN->fulltree) {
        $items = array();
        $items[] = new configselect('filteruploadedfiles', new lang_string('filteruploadedfiles', 'admin'), new lang_string('configfilteruploadedfiles', 'admin'), 0,
                array('0' => new lang_string('none'), '1' => new lang_string('allfiles'), '2' => new lang_string('htmlfilesonly')));
        $items[] = new configcheckbox('filtermatchoneperpage', new lang_string('filtermatchoneperpage', 'admin'), new lang_string('configfiltermatchoneperpage', 'admin'), 0);
        $items[] = new configcheckbox('filtermatchonepertext', new lang_string('filtermatchonepertext', 'admin'), new lang_string('configfiltermatchonepertext', 'admin'), 0);
        $items[] = new configcheckbox('filternavigationwithsystemcontext',
                new lang_string('filternavigationwithsystemcontext', 'admin'),
                new lang_string('configfilternavigationwithsystemcontext', 'admin'), 1);
        foreach ($items as $item) {
            $item->set_updatedcallback('reset_text_filters_cache');
            $temp->add($item);
        }
    }
    $ADMIN->add('filtersettings', $temp);

    $plugins = plugin_manager::instance()->get_plugins_of_type('filter');
    core_collator::asort_objects_by_property($plugins, 'displayname');
    foreach ($plugins as $plugin) {
        /** @var \core\plugininfo\filter $plugin */
        $plugin->load_settings($ADMIN, 'filtersettings', $hassiteconfig);
    }

    // Media players.
    $temp = new settingpage('managemediaplayers', new lang_string('managemediaplayers', 'media'));
    $temp->add(new heading('mediaformats', get_string('mediaformats', 'core_media'),
        format_text(get_string('mediaformats_desc', 'core_media'), FORMAT_MARKDOWN)));
    $temp->add(new \core_admin\admin\admin_setting_plugin_manager(
        'media',
        \core_admin\table\media_management_table::class,
        'managemediaplayers',
        new lang_string('managemediaplayers', 'core_media'),
    ));
    $temp->add(new heading('managemediaplayerscommonheading', new lang_string('commonsettings', 'admin'), ''));
    $temp->add(new configtext('media_default_width',
        new lang_string('defaultwidth', 'core_media'), new lang_string('defaultwidthdesc', 'core_media'),
        640, PARAM_INT, 10));
    $temp->add(new configtext('media_default_height',
        new lang_string('defaultheight', 'core_media'), new lang_string('defaultheightdesc', 'core_media'),
        360, PARAM_INT, 10));
    $ADMIN->add('mediaplayers', $temp);

    // Convert plugins.
    $temp = new settingpage('managefileconverterplugins', new lang_string('type_fileconvertermanage', 'plugin'));
    $temp->add(new manage_fileconverter_plugins());
    $ADMIN->add('fileconverterplugins', $temp);

    $plugins = plugin_manager::instance()->get_plugins_of_type('fileconverter');
    core_collator::asort_objects_by_property($plugins, 'displayname');
    foreach ($plugins as $plugin) {
        /** @var \core\plugininfo\media $plugin */
        $plugin->load_settings($ADMIN, 'fileconverterplugins', $hassiteconfig);
    }

    $plugins = plugin_manager::instance()->get_plugins_of_type('media');
    core_collator::asort_objects_by_property($plugins, 'displayname');
    foreach ($plugins as $plugin) {
        /** @var \core\plugininfo\media $plugin */
        $plugin->load_settings($ADMIN, 'mediaplayers', $hassiteconfig);
    }

    // Payment gateway plugins.
    $temp = new settingpage('managepaymentgateways', new lang_string('type_paygwmanage', 'plugin'));
    $temp->add(new \core_admin\local\settings\manage_payment_gateway_plugins());
    $temp->add(new description(
        'managepaymentgatewayspostfix',
        '',
        new lang_string('gotopaymentaccounts', 'payment',
            html_writer::link(new url('/payment/accounts.php'), get_string('paymentaccounts', 'payment')))
    ));
    $ADMIN->add('paymentgateways', $temp);

    $plugins = plugin_manager::instance()->get_plugins_of_type('paygw');
    core_collator::asort_objects_by_property($plugins, 'displayname');
    foreach ($plugins as $plugin) {
        /** @var \core\plugininfo\paygw $plugin */
        $plugin->load_settings($ADMIN, 'paymentgateways', $hassiteconfig);
    }

    // Data format settings.
    $temp = new settingpage('managedataformats', new lang_string('managedataformats'));
    $temp->add(new managedataformats());
    $ADMIN->add('dataformatsettings', $temp);

    $plugins = plugin_manager::instance()->get_plugins_of_type('dataformat');
    core_collator::asort_objects_by_property($plugins, 'displayname');
    foreach ($plugins as $plugin) {
        /** @var \core\plugininfo\dataformat $plugin */
        $plugin->load_settings($ADMIN, 'dataformatsettings', $hassiteconfig);
    }

    //== Portfolio settings ==
    require_once($CFG->libdir. '/portfoliolib.php');
    $manage = new lang_string('manageportfolios', 'portfolio');
    $url = "$CFG->wwwroot/$CFG->admin/portfolio.php";

    // Add manage page (with table)
    $temp = new manageportfolios();
    $ADMIN->add('portfoliosettings', $temp);

    // Add common settings page
    $temp = new settingpage('manageportfolioscommon', new lang_string('commonportfoliosettings', 'portfolio'));
    $temp->add(new heading('manageportfolioscommon', '', new lang_string('commonsettingsdesc', 'portfolio')));
    $fileinfo = portfolio_filesize_info(); // make sure this is defined in one place since its used inside portfolio too to detect insane settings
    $fileoptions = $fileinfo['options'];
    $temp->add(new configselect(
        'portfolio_moderate_filesize_threshold',
        new lang_string('moderatefilesizethreshold', 'portfolio'),
        new lang_string('moderatefilesizethresholddesc', 'portfolio'),
        $fileinfo['moderate'], $fileoptions));
    $temp->add(new configselect(
        'portfolio_high_filesize_threshold',
        new lang_string('highfilesizethreshold', 'portfolio'),
        new lang_string('highfilesizethresholddesc', 'portfolio'),
        $fileinfo['high'], $fileoptions));

    $temp->add(new configtext(
        'portfolio_moderate_db_threshold',
        new lang_string('moderatedbsizethreshold', 'portfolio'),
        new lang_string('moderatedbsizethresholddesc', 'portfolio'),
        20, PARAM_INT, 3));

    $temp->add(new configtext(
        'portfolio_high_db_threshold',
        new lang_string('highdbsizethreshold', 'portfolio'),
        new lang_string('highdbsizethresholddesc', 'portfolio'),
        50, PARAM_INT, 3));

    $ADMIN->add('portfoliosettings', $temp);
    $ADMIN->add('portfoliosettings', new externalpage('portfolionew', new lang_string('addnewportfolio', 'portfolio'), $url, 'moodle/site:config', true));
    $ADMIN->add('portfoliosettings', new externalpage('portfoliodelete', new lang_string('deleteportfolio', 'portfolio'), $url, 'moodle/site:config', true));
    $ADMIN->add('portfoliosettings', new externalpage('portfoliocontroller', new lang_string('manageportfolios', 'portfolio'), $url, 'moodle/site:config', true));

    foreach (portfolio_instances(false, false) as $portfolio) {
        require_once($CFG->dirroot . '/portfolio/' . $portfolio->get('plugin') . '/lib.php');
        $classname = 'portfolio_plugin_' . $portfolio->get('plugin');
        $ADMIN->add(
            'portfoliosettings',
            new externalpage(
                'portfoliosettings' . $portfolio->get('id'),
                $portfolio->get('name'),
                $url . '?action=edit&pf=' . $portfolio->get('id'),
                'moodle/site:config'
            )
        );
    }

    // repository setting
    require_once("$CFG->dirroot/repository/lib.php");
    $managerepo = new lang_string('manage', 'repository');
    $url = $CFG->wwwroot.'/'.$CFG->admin.'/repository.php';

    // Add main page (with table)
    $temp = new managerepositories();
    $ADMIN->add('repositorysettings', $temp);

    // Add common settings page
    $temp = new settingpage('managerepositoriescommon', new lang_string('commonrepositorysettings', 'repository'));
    $temp->add(new configtext('repositorycacheexpire', new lang_string('cacheexpire', 'repository'), new lang_string('configcacheexpire', 'repository'), 120, PARAM_INT));
    $temp->add(new configtext('repositorygetfiletimeout', new lang_string('getfiletimeout', 'repository'), new lang_string('configgetfiletimeout', 'repository'), 30, PARAM_INT));
    $temp->add(new configtext('repositorysyncfiletimeout', new lang_string('syncfiletimeout', 'repository'), new lang_string('configsyncfiletimeout', 'repository'), 1, PARAM_INT));
    $temp->add(new configtext('repositorysyncimagetimeout', new lang_string('syncimagetimeout', 'repository'), new lang_string('configsyncimagetimeout', 'repository'), 3, PARAM_INT));
    $temp->add(new configcheckbox('repositoryallowexternallinks', new lang_string('allowexternallinks', 'repository'), new lang_string('configallowexternallinks', 'repository'), 1));
    $temp->add(new configcheckbox('legacyfilesinnewcourses', new lang_string('legacyfilesinnewcourses', 'admin'), new lang_string('legacyfilesinnewcourses_help', 'admin'), 0));
    $temp->add(new configcheckbox('legacyfilesaddallowed', new lang_string('legacyfilesaddallowed', 'admin'), new lang_string('legacyfilesaddallowed_help', 'admin'), 1));
    $ADMIN->add('repositorysettings', $temp);
    $ADMIN->add('repositorysettings', new externalpage('repositorynew',
        new lang_string('addplugin', 'repository'), $url, 'moodle/site:config', true));
    $ADMIN->add('repositorysettings', new externalpage('repositorydelete',
        new lang_string('deleterepository', 'repository'), $url, 'moodle/site:config', true));
    $ADMIN->add('repositorysettings', new externalpage('repositorycontroller',
        new lang_string('manage', 'repository'), $url, 'moodle/site:config', true));
    $ADMIN->add('repositorysettings', new externalpage('repositoryinstancenew',
        new lang_string('createrepository', 'repository'), $url, 'moodle/site:config', true));
    $ADMIN->add('repositorysettings', new externalpage('repositoryinstanceedit',
        new lang_string('editrepositoryinstance', 'repository'), $url, 'moodle/site:config', true));
    $plugins = plugin_manager::instance()->get_plugins_of_type('repository');
    core_collator::asort_objects_by_property($plugins, 'displayname');
    foreach ($plugins as $plugin) {
        /** @var \core\plugininfo\repository $plugin */
        $plugin->load_settings($ADMIN, 'repositorysettings', $hassiteconfig);
    }
}

// Question bank settings.
if ($hassiteconfig || has_capability('moodle/question:config', $systemcontext)) {
    $temp = new settingpage('manageqbanks', new lang_string('manageqbanks', 'admin'));
    $temp->add(new \core_question\admin\manage_qbank_plugins_page());
    $ADMIN->add('qbanksettings', $temp);
    $plugins = plugin_manager::instance()->get_plugins_of_type('qbank');

    foreach ($plugins as $plugin) {
        /** @var \core\plugininfo\qbank $plugin */
        $plugin->load_settings($ADMIN, 'qbanksettings', $hassiteconfig);
    }
}

// Question type settings
if ($hassiteconfig || has_capability('moodle/question:config', $systemcontext)) {

    // Question behaviour settings.
    $ADMIN->add('qbehavioursettings', new manageqbehaviours());

    // Question type settings.
    $ADMIN->add('qtypesettings', new manageqtypes());

    // Question preview defaults.
    $settings = new settingpage('qdefaultsetting',
            get_string('questionpreviewdefaults', 'question'),
            'moodle/question:config');
    $ADMIN->add('qtypesettings', $settings);

    $settings->add(new heading('qdefaultsetting_preview_options',
            '', get_string('questionpreviewdefaults_desc', 'question')));

    // These keys are question_display_options::HIDDEN and VISIBLE.
    $hiddenofvisible = array(
        0 => get_string('notshown', 'question'),
        1 => get_string('shown', 'question'),
    );

    $settings->add(new question_behaviour('question_preview/behaviour',
            get_string('howquestionsbehave', 'question'), '',
                    'deferredfeedback'));

    $settings->add(new configselect('question_preview/correctness',
            get_string('whethercorrect', 'question'), '', 1, $hiddenofvisible));

    // These keys are question_display_options::HIDDEN, MARK_ONLY and MARK_AND_MAX.
    $marksoptions = array(
        0 => get_string('notshown', 'question'),
        1 => get_string('showmaxmarkonly', 'question'),
        2 => get_string('showmarkandmax', 'question'),
    );
    $settings->add(new configselect('question_preview/marks',
            get_string('marks', 'question'), '', 2, $marksoptions));

    $settings->add(new configselect('question_preview/markdp',
            get_string('decimalplacesingrades', 'question'), '', 2, array(0, 1, 2, 3, 4, 5, 6, 7)));

    $settings->add(new configselect('question_preview/feedback',
            get_string('specificfeedback', 'question'), '', 1, $hiddenofvisible));

    $settings->add(new configselect('question_preview/generalfeedback',
            get_string('generalfeedback', 'question'), '', 1, $hiddenofvisible));

    $settings->add(new configselect('question_preview/rightanswer',
            get_string('rightanswer', 'question'), '', 1, $hiddenofvisible));

    $settings->add(new configselect('question_preview/history',
            get_string('responsehistory', 'question'), '', 0, $hiddenofvisible));

    // Question editing settings.
    $settings = new settingpage('qediting',
            get_string('questionediting', 'question'),
            'moodle/question:config');
    $ADMIN->add('qtypesettings', $settings);

    $settings->add(new heading('qediting_options',
            '', get_string('questionediting_desc', 'question')));

    $settings->add(new configcheckbox('questiondefaultssave',
            get_string('questiondefaultssave', 'question'), get_string('questiondefaultssave_desc', 'question'), 1));

    // Settings for particular question types.
    $plugins = plugin_manager::instance()->get_plugins_of_type('qtype');
    core_collator::asort_objects_by_property($plugins, 'displayname');
    foreach ($plugins as $plugin) {
        /** @var \core\plugininfo\qtype $plugin */
        $plugin->load_settings($ADMIN, 'qtypesettings', $hassiteconfig);
    }

    // Settings for particular question behaviours.
    $plugins = plugin_manager::instance()->get_plugins_of_type('qbehaviour');
    core_collator::asort_objects_by_property($plugins, 'displayname');
    foreach ($plugins as $plugin) {
        /** @var \core\plugininfo\qtype $plugin */
        $plugin->load_settings($ADMIN, 'qbehavioursettings', $hassiteconfig);
    }
}

// Plagiarism plugin settings
if ($hassiteconfig && !empty($CFG->enableplagiarism)) {
    $ADMIN->add('plagiarism', new externalpage('manageplagiarismplugins', new lang_string('manageplagiarism', 'plagiarism'),
        $CFG->wwwroot . '/' . $CFG->admin . '/plagiarism.php'));

    $plugins = plugin_manager::instance()->get_plugins_of_type('plagiarism');
    core_collator::asort_objects_by_property($plugins, 'displayname');
    foreach ($plugins as $plugin) {
        /** @var \core\plugininfo\plagiarism $plugin */
        $plugin->load_settings($ADMIN, 'plagiarism', $hassiteconfig);
    }
}

// Comments report, note this page is really just a means to delete comments so check that.
$ADMIN->add('reports', new externalpage('comments', new lang_string('comments'), $CFG->wwwroot . '/comment/index.php',
    'moodle/comment:delete'));

// Course reports settings
if ($hassiteconfig) {
    $pages = array();
    foreach (core_component::get_plugin_list('coursereport') as $report => $path) {
        $file = $CFG->dirroot . '/course/report/' . $report . '/settings.php';
        if (file_exists($file)) {
            $settings = new settingpage('coursereport' . $report,
                    new lang_string('pluginname', 'coursereport_' . $report), 'moodle/site:config');
            // settings.php may create a subcategory or unset the settings completely
            include($file);
            if ($settings) {
                $pages[] = $settings;
            }
        }
    }
    if (!empty($pages)) {
        core_collator::asort_objects_by_property($pages, 'visiblename');
        foreach ($pages as $page) {
            $ADMIN->add('coursereports', $page);
        }
    }
    unset($pages);
}

// Now add reports
$pages = array();
foreach (core_component::get_plugin_list('report') as $report => $plugindir) {
    $settings_path = "$plugindir/settings.php";
    if (file_exists($settings_path)) {
        $settings = new settingpage('report' . $report,
                new lang_string('pluginname', 'report_' . $report), 'moodle/site:config');
        include($settings_path);
        if ($settings) {
            $pages[] = $settings;
        }
    }
}
$ADMIN->add('reportplugins', new externalpage('managereports', new lang_string('reportsmanage', 'admin'),
                                                    $CFG->wwwroot . '/' . $CFG->admin . '/reports.php'));
core_collator::asort_objects_by_property($pages, 'visiblename');
foreach ($pages as $page) {
    $ADMIN->add('reportplugins', $page);
}

if ($hassiteconfig) {
    // Global Search engine plugins.
    $temp = new settingpage('manageglobalsearch', new lang_string('globalsearchmanage', 'admin'));

    $pages = array();
    $engines = array();
    foreach (core_component::get_plugin_list('search') as $engine => $plugindir) {
        $engines[$engine] = new lang_string('pluginname', 'search_' . $engine);
        $settingspath = "$plugindir/settings.php";
        if (file_exists($settingspath)) {
            $settings = new settingpage('search' . $engine,
                    new lang_string('pluginname', 'search_' . $engine), 'moodle/site:config');
            include($settingspath);
            if ($settings) {
                $pages[] = $settings;
            }
        }
    }

    // Setup status.
    $temp->add(new searchsetupinfo());

    // Search engine selection.
    $temp->add(new heading('searchengineheading', new lang_string('searchengine', 'admin'), ''));
    $searchengineselect = new configselect('searchengine',
            new lang_string('selectsearchengine', 'admin'), '', 'simpledb', $engines);
    $searchengineselect->set_validate_function(function(string $value): string {
        global $CFG;

        // Check nobody's setting the indexing and query-only server to the same one.
        if (isset($CFG->searchenginequeryonly) && $CFG->searchenginequeryonly === $value) {
            return get_string('searchenginequeryonlysame', 'admin');
        } else {
            return '';
        }
    });
    $temp->add($searchengineselect);
    $temp->add(new heading('searchoptionsheading', new lang_string('searchoptions', 'admin'), ''));
    $temp->add(new configcheckbox('searchindexwhendisabled',
            new lang_string('searchindexwhendisabled', 'admin'), new lang_string('searchindexwhendisabled_desc', 'admin'),
            0));
    $temp->add(new configduration('searchindextime',
            new lang_string('searchindextime', 'admin'), new lang_string('searchindextime_desc', 'admin'),
            600));
    $temp->add(new heading('searchcoursesheading', new lang_string('searchablecourses', 'admin'), ''));
    $options = [
        0 => new lang_string('searchallavailablecourses_off', 'admin'),
        1 => new lang_string('searchallavailablecourses_on', 'admin')
    ];
    $temp->add(new configselect('searchallavailablecourses',
            new lang_string('searchallavailablecourses', 'admin'),
            new lang_string('searchallavailablecoursesdesc', 'admin'),
            0, $options));
    $temp->add(new configcheckbox('searchincludeallcourses',
        new lang_string('searchincludeallcourses', 'admin'), new lang_string('searchincludeallcourses_desc', 'admin'),
        0));

    // Search display options.
    $temp->add(new heading('searchdisplay', new lang_string('searchdisplay', 'admin'), ''));
    $temp->add(new configcheckbox('searchenablecategories',
        new lang_string('searchenablecategories', 'admin'),
        new lang_string('searchenablecategories_desc', 'admin'),
        0));
    $options = [];
    foreach (\core_search\manager::get_search_area_categories() as $category) {
        $options[$category->get_name()] = $category->get_visiblename();
    }
    $temp->add(new configselect('searchdefaultcategory',
        new lang_string('searchdefaultcategory', 'admin'),
        new lang_string('searchdefaultcategory_desc', 'admin'),
        \core_search\manager::SEARCH_AREA_CATEGORY_ALL, $options));
    $temp->add(new configcheckbox('searchhideallcategory',
        new lang_string('searchhideallcategory', 'admin'),
        new lang_string('searchhideallcategory_desc', 'admin'),
        0));

    // Top result options.
    $temp->add(new heading('searchtopresults', new lang_string('searchtopresults', 'admin'), ''));
    // Max Top results.
    $options = range(0, 10);
    $temp->add(new configselect('searchmaxtopresults',
        new lang_string('searchmaxtopresults', 'admin'),
        new lang_string('searchmaxtopresults_desc', 'admin'),
        3, $options));
    // Teacher roles.
    $options = [];
    foreach (role_get_names() as $role) {
        $options[$role->id] = $role->localname;
    }
    $temp->add(new configmultiselect('searchteacherroles',
        new lang_string('searchteacherroles', 'admin'),
        new lang_string('searchteacherroles_desc', 'admin'),
        [], $options));

    $temp->add(new heading('searchmanagement', new lang_string('searchmanagement', 'admin'),
            new lang_string('searchmanagement_desc', 'admin')));

    // Get list of search engines including those with alternate settings.
    $searchenginequeryonlyselect = new configselect('searchenginequeryonly',
            new lang_string('searchenginequeryonly', 'admin'),
            new lang_string('searchenginequeryonly_desc', 'admin'), '', function() use($engines) {
                $options = ['' => new lang_string('searchenginequeryonly_none', 'admin')];
                foreach ($engines as $name => $display) {
                    $options[$name] = $display;

                    $classname = '\search_' . $name . '\engine';
                    $engine = new $classname;
                    if ($engine->has_alternate_configuration()) {
                        $options[$name . '-alternate'] =
                                new lang_string('searchenginealternatesettings', 'admin', $display);
                    }
                }
                return $options;
            });
    $searchenginequeryonlyselect->set_validate_function(function(string $value): string {
        global $CFG;

        // Check nobody's setting the indexing and query-only server to the same one.
        if (isset($CFG->searchengine) && $CFG->searchengine === $value) {
            return get_string('searchenginequeryonlysame', 'admin');
        } else {
            return '';
        }
    });
    $temp->add($searchenginequeryonlyselect);
    $temp->add(new configcheckbox('searchbannerenable',
            new lang_string('searchbannerenable', 'admin'), new lang_string('searchbannerenable_desc', 'admin'),
            0));
    $temp->add(new confightmleditor('searchbanner',
            new lang_string('searchbanner', 'admin'), '', ''));

    $ADMIN->add('searchplugins', $temp);
    $ADMIN->add('searchplugins', new externalpage('searchareas', new lang_string('searchareas', 'admin'),
        new url('/admin/searchareas.php')));

    core_collator::asort_objects_by_property($pages, 'visiblename');
    foreach ($pages as $page) {
        $ADMIN->add('searchplugins', $page);
    }
}

/// Add all admin tools
if ($hassiteconfig) {
    $settingspage = new settingpage('toolsmanagement', new lang_string('toolsmanage', 'admin'));
    $ADMIN->add('tools', $settingspage);
    $settingspage->add(new \core_admin\admin\admin_setting_plugin_manager(
        'tool',
        \core_admin\table\tool_plugin_management_table::class,
        'managetools',
        new lang_string('toolsmanage', 'admin')
    ));
}

// Now add various admin tools.
$plugins = plugin_manager::instance()->get_plugins_of_type('tool');
core_collator::asort_objects_by_property($plugins, 'displayname');
foreach ($plugins as $plugin) {
    /** @var \core\plugininfo\tool $plugin */
    $plugin->load_settings($ADMIN, null, $hassiteconfig);
}

// Now add the Cache plugins
if ($hassiteconfig) {
    $ADMIN->add('cache', new externalpage('cacheconfig', new lang_string('cacheconfig', 'cache'), $CFG->wwwroot .'/cache/admin.php'));
    $ADMIN->add('cache', new externalpage('cachetestperformance', new lang_string('testperformance', 'cache'), $CFG->wwwroot . '/cache/testperformance.php'));
    $ADMIN->add('cache', new externalpage('cacheusage',
            new lang_string('cacheusage', 'cache'), $CFG->wwwroot . '/cache/usage.php'));
    $ADMIN->locate('cachestores')->set_sorting(true);
    foreach (core_component::get_plugin_list('cachestore') as $plugin => $path) {
        $settingspath = $path.'/settings.php';
        if (file_exists($settingspath)) {
            $settings = new settingpage('cachestore_'.$plugin.'_settings', new lang_string('pluginname', 'cachestore_'.$plugin), 'moodle/site:config');
            include($settingspath);
            $ADMIN->add('cachestores', $settings);
        }
    }
}

// Add Calendar type settings.
if ($hassiteconfig) {
    $plugins = plugin_manager::instance()->get_plugins_of_type('calendartype');
    core_collator::asort_objects_by_property($plugins, 'displayname');
    foreach ($plugins as $plugin) {
        /** @var \core\plugininfo\calendartype $plugin */
        $plugin->load_settings($ADMIN, 'calendartype', $hassiteconfig);
    }
}

// Communication plugins.
if ($hassiteconfig && core_communication\api::is_available()) {
    $temp = new settingpage('managecommunicationproviders',
        new lang_string('managecommunicationproviders', 'core_communication'));
    $temp->add(new \core_communication\admin\manage_communication_providers_page());
    $ADMIN->add('communicationsettings', $temp);
    $plugins = plugin_manager::instance()->get_plugins_of_type('communication');
    foreach ($plugins as $plugin) {
        /** @var \core\plugininfo\communication $plugin */
        $plugin->load_settings($ADMIN, 'communicationsettings', $hassiteconfig);
    }
}

// SMS plugins.
if ($hassiteconfig) {
    $ADMIN->add(
        'sms',
        new externalpage(
            'smsgateway',
            new lang_string('manage_sms_gateways', 'core_sms'),
            $CFG->wwwroot . '/sms/sms_gateways.php',
        ),
    );
    foreach (core_component::get_plugin_list('smsgateway') as $plugin => $path) {
        $settingspath = $path . '/settings.php';
        if (file_exists($settingspath)) {
            $settings = new settingpage(
                'smsgateway_' . $plugin . '_settings',
                new lang_string('pluginname', 'smsgateway_' . $plugin),
                'moodle/site:config',
            );
            include($settingspath);
            $ADMIN->add('smsgateway', $settings);
        }
    }
}

// Content bank content types.
if ($hassiteconfig) {
    $temp = new settingpage('managecontentbanktypes', new lang_string('managecontentbanktypes'));
    $temp->add(new managecontentbankcontenttypes());
    $ADMIN->add('contentbanksettings', $temp);
    $ADMIN->add('contentbanksettings',
        new externalpage('contentbank', new lang_string('contentbankcustomfields', 'contentbank'),
            $CFG->wwwroot . '/contentbank/customfield.php',
            'moodle/contentbank:configurecustomfields'
        )
    );
    $plugins = plugin_manager::instance()->get_plugins_of_type('contenttype');
    foreach ($plugins as $plugin) {
        /** @var \core\plugininfo\contentbank $plugin */
        $plugin->load_settings($ADMIN, 'contentbanksettings', $hassiteconfig);
    }
}

/// Add all local plugins - must be always last!
if ($hassiteconfig) {
    $ADMIN->add('localplugins', new externalpage('managelocalplugins', new lang_string('localpluginsmanage'),
                                                        $CFG->wwwroot . '/' . $CFG->admin . '/localplugins.php'));
}

// Extend settings for each local plugin. Note that their settings may be in any part of the
// settings tree and may be visible not only for administrators.
$plugins = plugin_manager::instance()->get_plugins_of_type('local');
core_collator::asort_objects_by_property($plugins, 'displayname');
foreach ($plugins as $plugin) {
    /** @var \core\plugininfo\local $plugin */
    $plugin->load_settings($ADMIN, null, $hassiteconfig);
}
