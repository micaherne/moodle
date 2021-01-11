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
 * Generate PHP for XMLDB structures.
 *
 * @package    tool_xmldb
 * @subpackage cli
 * @copyright  2021 Michael Aherne <michael.aherne@strath.ac.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('CLI_SCRIPT', 1);

require(__DIR__ . '/../../../../config.php');
require_once($CFG->libdir . '/clilib.php');

// Add required XMLDB action classes.
require_once($CFG->dirroot . '/admin/tool/xmldb/actions/XMLDBAction.class.php');
require_once($CFG->dirroot . '/admin/tool/xmldb/actions/XMLDBCheckAction.class.php');
require_once($CFG->dirroot . '/admin/tool/xmldb/actions/view_table_php/view_table_php.class.php');
require_once($CFG->dirroot . '/admin/tool/xmldb/actions/view_structure_php/view_structure_php.class.php');

$availableactions = [];
foreach ([view_table_php::class, view_structure_php::class] as $classname) {
    $reflectionclass = new ReflectionClass($classname);
    foreach ($reflectionclass->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
        if (substr($method->getName(), -4) === '_php') {
            $availableactions[substr($method->getName(), 0, -4)] = $method;
        }
    }
}

list($options, $unrecognized) = cli_get_params(['help' => false, 'list' => false, 'version' => false],
    ['h' => 'help', 'l' => 'list', 'v' => 'version']);

if ($options['list']) {

    $actions = [];

    foreach ($availableactions as $name => $method) {
        $desc = get_method_description($method);
        $actions[$name] = $desc;
    }

    $lengths = array_map('strlen', array_keys($actions));
    $padsize = max($lengths) + 1;
    foreach ($actions as $name => $desc) {
        echo str_pad($name, $padsize) . $desc . "\n";
    }
    exit;
}

$help =
"Generates PHP code for updating XMLDB structures.

Options:
-l, --list         List available actions
-v, --version      The version of the plugin for use in the savepoint
-h, --help         Print out this help, or action specific help if an action is given";

if (empty($unrecognized)) {
    if ($options['help']) {
        echo $help;
        echo "\n\n";
        exit;
    }
    die("Action name must be passed\n");
}

$version = null;
if ($options['version']) {
    if (preg_match('/^\\d{10}(\.\\d{2})?$/', $options['version'])) {
        $version = $options['version'];
    } else {
        die("Invalid version, should be ten digits with optional two decimal places");
    }
}

$action = array_shift($unrecognized);

if (!array_key_exists($action, $availableactions)) {
    die("Unknown action");
}

$actionmethod = $availableactions[$action];

$parameters = $actionmethod->getParameters();

if ($options['help']) {
    echo get_method_description($actionmethod) . "\n\n";
    echo "Usage: $action ";
    $parts = get_parameter_names($parameters);
    echo implode(' ', $parts) . "\n";
    exit;
}

$class = $actionmethod->getDeclaringClass()->newInstance();
$args = [];

$providedcount = count($unrecognized);
$expectedcount = count($parameters);
if ($providedcount !== $expectedcount) {
    die ("Incorrect parameter count, expected $expectedcount, got $providedcount");
}

foreach ($parameters as $parameter) {
    $value = array_shift($unrecognized);

    if ($parameter->getName() === 'structure') {

        $componentdirectory = core_component::get_component_directory($value);
        if (!$componentdirectory) {
            die("Invalid component\n");
        }

        $filepath = $componentdirectory . '/db/install.xml';

        $xmldbfile = new xmldb_file($filepath);
        $loadresult = $xmldbfile->loadXMLStructure();
        if (!$loadresult) {
            die ("Unable to load install.xml structure\n");
        }
        $structure = $xmldbfile->getStructure();
        $args[] = $structure;
    } else {
        $args[] = $value;
    }
}

$result = $actionmethod->invokeArgs($class, $args);
if (!empty($version)) {
    $result = str_replace('XXXXXXXXXX', $version, $result);
}
echo "$result\n";

/**
 * Get the names of the parameters for display.
 * @param ReflectionParameter[] $parameters
 * @return array the parameter names
 */
function get_parameter_names(array $parameters): array {
    $parts = [];
    foreach ($parameters as $parameter) {
        $name = $parameter->getName();
        if ($name === 'structure') {
            $name = 'component';
        }
        $parts[] = $name;
    }
    return $parts;
}

/**
 * Get the description of the method from the comments.
 *
 * @param ReflectionMethod $method
 * @return string the description of the method
 */
function get_method_description(ReflectionMethod $method): string {
    $comment = $method->getDocComment();
    $desclines = [];
    foreach (explode("\n", $comment) as $line) {
        $line = trim($line, ' /\\*');
        if (strlen($line) === 0 || strpos($line, '@') === 0) {
            continue;
        }
        $desclines[] = $line;
    }
    $desc = implode(' ', $desclines);
    $desc = preg_replace('/This function will generate (all )?the PHP code needed to /', '', $desc);
    $desc = preg_replace('/using XMLDB objects and functions/', '', $desc);
    // Remove unusual English construct.
    $desc = str_replace('one', 'a', $desc);
    $desc = str_replace('a index', 'an index', $desc);
    $desc = ucfirst($desc);
    return $desc;
}
