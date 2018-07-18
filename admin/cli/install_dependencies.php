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
 * This script manages and updates Composer dependencies for Moodle.
 *
 * @package    core
 * @subpackage cli
 * @copyright  2018 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// This is a CLI script.
define('CLI_SCRIPT', true);

// These values are hardcoded so that we don't require an installed site to install dependencies.
define('NO_DEBUG_DISPLAY', true);

// Used by library scripts to check they are being called by Moodle.
define('MOODLE_INTERNAL', true);

// Disables all caching.
define('CACHE_DISABLE_ALL', true);
define('PHPUNIT_TEST', false);
define('IGNORE_COMPONENT_CACHE', true);

// Hard code a minimal configuration.
global $CFG;
$CFG = new stdClass();
$CFG->dirroot = dirname(dirname(__DIR__));
$CFG->libdir = "{$CFG->dirroot}/lib";
$CFG->admin = 'admin';

require_once("{$CFG->libdir}/classes/component.php");
require_once("{$CFG->libdir}/clilib.php");

// Load the composer manager.
// We do not have autoloading capabilites.
require_once("{$CFG->libdir}/classes/composer/manager.php");

list($options, $unrecognized) = cli_get_params(
    array(
        'dev'       => false,
        'help'      => false,
    ),
    array(
        'h' => 'help',
        'd' => 'dev',
    )
);

$help = "
Composer Dependency wrapper for Moodle.

Usage:
  php admin/cli/install_dependencies.php [--dev] [--help]

Options:
-d, --dev       Enables installation of require-dev packages
-h, --help      Print out usage instructions

Example from Moodle root directory:
\$ php admin/cli/install_dependencies.php --dev
";

if (!empty($options['help'])) {
    echo $help;
    exit(1);
}

// Run the composer update --locks, and install..
\core\composer\manager::install(!empty($options['dev']));
