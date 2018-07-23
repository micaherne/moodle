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
 * The composer dependency manager for Moodle.
 *
 * @package    core
 * @copyright  2018 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core\composer;

// Note: Do not use any autoloading here.
// This file is used during installation and before autoloading is available.

/**
 * The composer dependency manager for Moodle.
 *
 * @package    core
 * @copyright  2018 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class manager {

    /**
     * @var string The authoritative URL to download Composer from.
     */
    protected static $composerurl = 'https://getcomposer.org/composer.phar';

    /**
     * Update the central lock file using other lock files distributed in Moodle.
     */
    protected static function update_locks(bool $includedev = false) {
        static::update_require_paths();

        $dirroot = static::get_dirroot();
        chdir($dirroot);

        // Update composer dependencies from the various lock files.
        $installcommand = [
            "php",
            static::get_composer_path(),
            "update",
            "--ansi",
        ];

        if (empty($includedev)) {
            // By default Composer installs dev dependencies.
            // Tell it not to.
            $installcommand[] = "--no-dev";
        }

        passthru(implode(" ", $installcommand), $code);
        if ($code != 0) {
            exit($code);
        }
    }

    /**
     * Update the require paths for composer from the list of installed plugins.
     */
    protected static function update_require_paths() {
        global $CFG;
        $jsonpath = static::get_composer_json();
        if (file_exists($jsonpath)) {
            $json = file_get_contents($jsonpath);
            $config = json_decode($json);
        } else {
            $config = (object) [
                'extra' => (object) [
                    'merge-plugin' => (object) [
                    ],
                ],
            ];
        }

        $config->extra->{'merge-plugin'}->require = [];
        $config->extra->{'merge-plugin'}->{'require-dev'} = [];

        // We have a core composer.json for core dependencies.
        $requirements = [];

        // Add an optional local composer.json file.
        if (static::is_valid_composer_json(static::get_dirroot() . '/lib/composer.json')) {
            $config->extra->{'merge-plugin'}->require[] = 'lib/composer.json';
        }

        // Add an optional local composer.json file.
        if (static::is_valid_composer_json(static::get_dirroot() . '/composer.local.json')) {
            $config->extra->{'merge-plugin'}->require[] = 'composer.local.json';
        }

        // Add the autoloading configuration file.
        if (static::is_valid_composer_json(static::get_autoload_json())) {
            $config->extra->{'merge-plugin'}->require[] = 'composer.autoload.json';
        }       

        require_once(static::get_dirroot() . "/lib/classes/component.php");
        // Add any plugin composer.json files.
        $plugintypes = \core_component::get_plugin_types();
        foreach (array_values($plugintypes) as $path) {
            $subpath = substr($path, strlen($CFG->dirroot) + 1);
            $subpathmatches = glob("{$subpath}/*/composer.json");
            foreach ($subpathmatches as $pluginjson) {
                if (static::is_valid_composer_json($pluginjson)) {
                    $config->extra->{'merge-plugin'}->require[] = $pluginjson;
                }
            }
        }

        $value = json_encode($config, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        file_put_contents($jsonpath, $value);
    }

    /**
     * Run the composer install.
     *
     * @param   bool    $includedev Whether to include developer dependencies or not.
     */
    public static function install(bool $includedev = false) {
        $dirroot = static::get_dirroot();
        chdir($dirroot);

        static::fetch_composer();
        static::update_locks($includedev);

        // Install compposer dependencies from the composer lock.
        $installcommand = [
            "php",
            static::get_composer_path(),
            "install",
            "--ansi",
            "--classmap-authoritative",
        ];

        if (empty($includedev)) {
            // By default Composer installs dev dependencies.
            // Tell it not to.
            $installcommand[] = "--no-dev";
        }

        passthru(implode(" ", $installcommand), $code);
        if ($code != 0) {
            exit($code);
        }
    }

    /**
     * Fetch the composer.phar file and install it into the dirroot.
     */
    protected static function fetch_composer() {
        $composerpath = static::get_composer_path();

        if (!file_exists($composerpath)) {
            $file = @fopen($composerpath, 'w');
            if ($file === false) {
                $errordetails = error_get_last();
                $error = sprintf("Unable to create composer.phar\nPHP error: %s",
                                $errordetails['message']);
                testing_error(TESTING_EXITCODE_COMPOSER, $error);
            }
            $curl = curl_init();

            curl_setopt($curl, CURLOPT_URL,  static::$composerurl);
            curl_setopt($curl, CURLOPT_FILE, $file);
            $result = curl_exec($curl);

            $curlerrno = curl_errno($curl);
            $curlerror = curl_error($curl);
            $curlinfo = curl_getinfo($curl);

            curl_close($curl);
            fclose($file);

            if (!$result) {
                $error = sprintf("Unable to download composer.phar\ncURL error (%d): %s",
                                $curlerrno, $curlerror);
                testing_error(TESTING_EXITCODE_COMPOSER, $error);
            } else if ($curlinfo['http_code'] === 404) {
                if (file_exists($composerpath)) {
                    // Deleting the resource as it would contain HTML.
                    unlink($composerpath);
                }
                $error = sprintf("Unable to download composer.phar\n" .
                                    "404 http status code fetching static::composerurl");
                testing_error(TESTING_EXITCODE_COMPOSER, $error);
            }
        }
    }

    /**
     * Run a self-udpate on composer.
     */
    protected static function update_composer() {
        $composerpath = static::get_composer_path();
        if (file_exists($composerpath)) {
            passthru("php {$composerpath} self-update --ansi", $code);
            if ($code != 0) {
                exit($code);
            }
        }
    }

    /**
     * Fetch the path on disk to the dirroot.
     *
     * @return  string
     */
    protected static function get_dirroot() : string {
        global $CFG;

        return dirname(dirname(dirname(__DIR__)));
    }

    /**
     * Fetch the path on disk to the composer.phar file.
     *
     * @return  string
     */
    protected static function get_composer_path() {
        return static::get_dirroot() . '/composer.phar';
    }

    /**
     * Fetch the path on disk to the composer configuration.
     *
     * @return  string
     */
    protected static function get_composer_json() {
        return static::get_dirroot() . '/composer.plugins.json';
    }

    /**
     * Check whether the supplied file contains a semi-valid composer.json.
     *
     * This checks whether it is a file, which is readable, and is json_decode-able.
     *
     * @param   string  $filepath
     * @return  bool
     */
    protected static function is_valid_composer_json($filepath) : bool {
        if (!file_exists($filepath)) {
            return false;
        }

        if (!is_readable($filepath)) {
            return false;
        }

        $json = file_get_contents($filepath);
        $config = @json_decode($json);
        if (empty($config)) {
            return false;
        }

        return true;
    }

    protected static function get_autoload_json() {
        return static::get_dirroot() . '/composer.autoload.json';
    }

    public static function generate_autoload_json() {
        global $CFG;

        $psr0 = [
            'Horde' => 'lib/horde/framework/Horde/',
            'Mustache' => 'lib/mustache/src/Mustache/'
        ];

        $psr4 = [
            'MaxMind\\' => 'lib/maxmind/MaxMind/',
            'GeoIp2\\' => 'lib/maxmind/GeoIp2/',
            'Sabberworm\\CSS\\' => 'lib/php-css-parser/',
            'MoodleHQ\\RTLCSS\\' => 'lib/rtlcss/',
            'Leafo\\ScssPhp\\' => 'lib/scssphp/',
            'Box\\Spout\\' => 'lib/spout/src/Spout/',
            'IMSGlobal\LTI\\' => 'lib/ltiprovider/src/',
            'Phpml\\' => 'lib/mlbackend/php/phpml/src/Phpml/',
            'RedeyeVentures\\GeoPattern\\' => 'lib/geopattern-php/GeoPattern/'
        ];

        // Find things like core_date.
        $psr0['core'] = 'lib/classes/';

        $psr4['core\\'] = 'lib/classes/';

        /* Note that the psr-0 paths below were an attempt to deal with the old-style non-namespaced Moodle classes, e.g.
           core_date, which happen to be PSR-0 compliant when there is no underscore in the class name. Due to a bug in
           composer's classmap generation this happens to map classes with underscores too at the moment, but shouldn't 
           be relied on (https://github.com/composer/composer/pull/7421).

           (In PSR-0 underscores should map to directory separators, so local_test_date_and_time should only be loaded
           from local/test/classes/date/and/time.php, but the old-style Moodle path is allowed by the bug: local/test/classes/date_and_time.php)
        */

        foreach (\core_component::get_core_subsystems() as $subsystem => $fulldir) {
            if (!$fulldir) {
                continue;
            }
            if (strpos($fulldir, $CFG->dirroot) === 0) {
                $psr0['core_' . $subsystem] = substr("$fulldir/classes/", strlen($CFG->dirroot) + 1);
                $psr4['core_' . $subsystem . '\\'] = substr("$fulldir/classes/", strlen($CFG->dirroot) + 1);
            } else {
                // Warn if admin directory has been moved.
                mtrace("WARNING: Component $subsystem is outside code directory. Autoloading may not work correctly.");
                $psr0['core_' . $subsystem0] = realpath("$fulldir/classes/") . '/';
                $psr4['core_' . $subsystem . '\\'] = realpath("$fulldir/classes/") . '/';
            }
        }

        foreach (\core_component::get_plugin_types() as $plugintype => $plugintypedir) {
            foreach (\core_component::get_plugin_list($plugintype) as $pluginname => $fulldir) {
                if (file_exists($fulldir . '/classes')) {
                    if (strpos($fulldir, $CFG->dirroot) === 0) {
                        $psr0[$plugintype . '_' . $pluginname] = substr("$fulldir/classes/", strlen($CFG->dirroot) + 1);
                        $psr4[$plugintype . '_' . $pluginname . '\\'] = substr("$fulldir/classes/", strlen($CFG->dirroot) + 1);
                    } else {
                        // Warn if theme directory has been moved.
                        mtrace("WARNING: Plugin {$plugintype}_{$pluginname} is outside code directory. Autoloading may not work correctly.");
                        $psr0[$plugintype . '_' . $pluginname] = realpath("$fulldir/classes") . '/';
                        $psr4[$plugintype . '_' . $pluginname . '\\'] = realpath("$fulldir/classes") . '/';
                    }
                }
            }
        }

        $config = (object) [
            'autoload' => [
                'psr-0' => $psr0,
                'psr-4' => $psr4
            ]
        ];

        file_put_contents(static::get_autoload_json(), json_encode($config, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    }
}
