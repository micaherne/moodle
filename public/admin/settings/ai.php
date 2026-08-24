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
 * Adds settings links to admin tree.
 *
 * AI gets top billing in general because it's the future.
 *
 * @package   core_admin
 * @copyright 2024 Matt Porritt <matt.porritt@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use core\lang_string;
use core\plugin_manager;
use core\url;
use core_admin\setting\setting\heading;
use core_admin\setting\settingpage\settingpage;
use core_admin\setting\tree\category;
use core_admin\setting\tree\externalpage;

defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {
    // Add settings page for AI provider settings.
    $providers = new settingpage('aiprovider', new lang_string('aiproviders', 'ai'));
    $providers->add(new heading('availableproviders',
        get_string('availableproviders', 'core_ai'),
        get_string('availableproviders_desc', 'core_ai')));

    if (!empty(plugin_manager::instance()->get_plugins_of_type("aiprovider"))) {
        // Add call to action to add a new provider.
        $providers->add(new \core_admin\admin\admin_setting_template_render(
            name: 'addnewprovider',
            templatename: 'core_ai/admin_add_provider',
            context: ['addnewproviderurl' => new url('/ai/configure.php')]
        ));

        $providers->add(new \core_ai\admin\admin_setting_provider_manager(
            'aiprovider',
            \core_ai\table\aiprovider_management_table::class,
            'manageaiproviders',
            new lang_string('manageaiproviders', 'core_ai'),
        ));
    } else {
        $providers->add(new \core_admin\admin\admin_setting_notification(
            name:'noproviderplugins',
            notification: get_string('noproviderplugins', 'core_ai'),
            type: 'danger'
        ));
    }

    $ADMIN->add('ai', $providers);

    // Add settings page for AI placement settings.
    $placements = new settingpage('aiplacement', new lang_string('aiplacements', 'ai'));
    $placements->add(new heading('availableplacements',
            get_string('availableplacements', 'core_ai'),
            get_string('availableplacements_desc', 'core_ai')));
    $placements->add(new \core_admin\admin\admin_setting_plugin_manager(
            'aiplacement',
            \core_ai\table\aiplacement_management_table::class,
            'manageaiplacements',
            new lang_string('manageaiplacements', 'core_ai'),
    ));
    $ADMIN->add('ai', $placements);

    // Load settings for all placements.
    $plugins = plugin_manager::instance()->get_plugins_of_type('aiplacement');
    foreach ($plugins as $plugin) {
        /** @var \core\plugininfo\aiprovider $plugin */
        $plugin->load_settings($ADMIN, 'ai', $hassiteconfig);
    }
}

// AI reports category.
$ADMIN->add('reports', new category('aireports', get_string('aireports', 'core_ai')));
// Add AI policy acceptance report.
$aipolicyacceptance = new externalpage(
    'aipolicyacceptancereport',
    get_string('aipolicyacceptance', 'core_ai'),
    new url('/ai/policy_acceptance_report.php'),
    'moodle/ai:viewaipolicyacceptancereport'
);
$ADMIN->add('aireports', $aipolicyacceptance);
// Add AI usage report.
$aiusage = new externalpage(
    'aiusagereport',
    get_string('aiusage', 'core_ai'),
    new url('/ai/usage_report.php'),
    'moodle/ai:viewaiusagereport',
);
$ADMIN->add('aireports', $aiusage);
